import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { optionalAuthMiddleware } from "./optional-auth";
import { ensureProfile, isProfileComplete, requireAdmin, type Profile } from "./profile";
import { maybeGrantProfileBonus } from "./wallet";

export type { Profile };

export const getMyProfile = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    return ensureProfile(context.userId);
  });

export const updateMyProfile = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        display_name: z.string().trim().max(80).optional(),
        phone: z.string().trim().max(20).optional(),
        university: z.string().trim().max(80).optional(),
        field: z.string().trim().max(80).optional(),
        degree: z.string().trim().max(40).optional(),
        city: z.string().trim().max(40).optional(),
        bio: z.string().trim().max(600).optional(),
        default_agent_id: z.string().trim().max(40).optional(),
        referral_code_in: z.string().trim().max(16).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    const current = await ensureProfile(context.userId);
    const sql = await getSql();
    const complete = isProfileComplete({
      display_name: data.display_name ?? current.display_name,
      phone: data.phone ?? current.phone,
      university: data.university ?? current.university,
      field: data.field ?? current.field,
    })
      ? 1
      : 0;
    if (data.referral_code_in && !current.referred_by) {
      const code = data.referral_code_in.trim().toUpperCase();
      const rows = await sql<{ user_id: string }>`select user_id from app_profiles where referral_code = ${code}`;
      if (rows[0] && rows[0].user_id !== context.userId) {
        await sql`update app_profiles set referred_by = ${rows[0].user_id} where user_id = ${context.userId}`;
      }
    }
    await sql.query(
      `update app_profiles set
        display_name = coalesce($1, display_name),
        phone = coalesce($2, phone),
        university = coalesce($3, university),
        field = coalesce($4, field),
        degree = coalesce($5, degree),
        city = coalesce($6, city),
        bio = coalesce($7, bio),
        default_agent_id = coalesce($8, default_agent_id),
        profile_complete = $9
       where user_id = $10`,
      [
        data.display_name ?? null,
        data.phone ?? null,
        data.university ?? null,
        data.field ?? null,
        data.degree ?? null,
        data.city ?? null,
        data.bio ?? null,
        data.default_agent_id ?? null,
        complete,
        context.userId,
      ],
    );
    const bonus = await maybeGrantProfileBonus(context.userId);
    const profile = await ensureProfile(context.userId);
    return { profile, bonus };
  });

export const listMyInquiries = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    return sql<{
      id: string;
      name: string;
      phone: string;
      service: string | null;
      message: string | null;
      created_at: string;
    }>`
      select id, name, phone, service, message, created_at::text as created_at
      from inquiries
      where user_id = ${context.userId}
      order by created_at desc
      limit 50
    `;
  });

export const listMyTickets = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    return sql<{
      id: string;
      subject: string;
      status: string;
      priority: string;
      created_at: string;
      updated_at: string;
    }>`
      select id, subject, status, priority, created_at::text as created_at, updated_at::text as updated_at
      from tickets
      where user_id = ${context.userId}
      order by updated_at desc
    `;
  });

export const createTicket = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z.object({
      subject: z.string().trim().min(4).max(160),
      body: z.string().trim().min(4).max(4000),
      priority: z.enum(["low", "normal", "high"]).optional(),
    }).parse(d),
  )
  .handler(async ({ context, data }) => {
    await ensureProfile(context.userId);
    const sql = await getSql();
    const id = crypto.randomUUID();
    const mid = crypto.randomUUID();
    await sql`
      insert into tickets (id, user_id, subject, priority)
      values (${id}, ${context.userId}, ${data.subject}, ${data.priority ?? "normal"})
    `;
    await sql`
      insert into ticket_messages (id, ticket_id, user_id, body, is_staff)
      values (${mid}, ${id}, ${context.userId}, ${data.body}, 0)
    `;
    return { id };
  });

export const getTicket = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ id: z.string() }).parse(d))
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const profile = await ensureProfile(context.userId);
    const tickets = await sql<{
      id: string;
      user_id: string;
      subject: string;
      status: string;
      priority: string;
      created_at: string;
    }>`
      select id, user_id, subject, status, priority, created_at::text as created_at
      from tickets where id = ${data.id}
    `;
    const ticket = tickets[0];
    if (!ticket) throw new Error("Not found");
    if (profile.role !== "admin" && ticket.user_id !== context.userId) throw new Error("Forbidden");
    const messages = await sql<{
      id: string;
      body: string;
      is_staff: number;
      created_at: string;
    }>`
      select id, body, is_staff, created_at::text as created_at
      from ticket_messages
      where ticket_id = ${data.id}
      order by created_at asc
    `;
    return { ticket, messages, isAdmin: profile.role === "admin" };
  });

export const replyTicket = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ id: z.string(), body: z.string().trim().min(1).max(4000) }).parse(d))
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const profile = await ensureProfile(context.userId);
    const tickets = await sql<{ user_id: string; status: string }>`select user_id, status from tickets where id = ${data.id}`;
    const ticket = tickets[0];
    if (!ticket) throw new Error("Not found");
    const isStaff = profile.role === "admin";
    if (!isStaff && ticket.user_id !== context.userId) throw new Error("Forbidden");
    const mid = crypto.randomUUID();
    await sql`
      insert into ticket_messages (id, ticket_id, user_id, body, is_staff)
      values (${mid}, ${data.id}, ${context.userId}, ${data.body}, ${isStaff ? 1 : 0})
    `;
    const nextStatus = isStaff ? "pending" : "open";
    await sql`update tickets set updated_at = now(), status = ${nextStatus} where id = ${data.id}`;
    return { ok: true as const };
  });

export const listAdminTickets = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{
      id: string;
      subject: string;
      status: string;
      priority: string;
      user_id: string;
      created_at: string;
      updated_at: string;
    }>`
      select id, subject, status, priority, user_id, created_at::text as created_at, updated_at::text as updated_at
      from tickets
      order by updated_at desc
      limit 100
    `;
  });

export const setTicketStatus = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z.object({ id: z.string(), status: z.enum(["open", "pending", "closed"]) }).parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    await sql`update tickets set status = ${data.status}, updated_at = now() where id = ${data.id}`;
    return { ok: true as const };
  });

export const listAdminInquiries = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{
      id: string;
      name: string;
      phone: string;
      email: string | null;
      service: string | null;
      field: string | null;
      message: string | null;
      created_at: string;
    }>`
      select id, name, phone, email, service, field, message, created_at::text as created_at
      from inquiries
      order by created_at desc
      limit 100
    `;
  });

export const trackPageView = createServerFn({ method: "POST" })
  .validator((d: unknown) => z.object({ path: z.string().max(200), referrer: z.string().max(300).optional() }).parse(d))
  .handler(async ({ data }) => {
    if (data.path.startsWith("/admin") || data.path.startsWith("/dashboard") || data.path.startsWith("/api")) {
      return { ok: true as const };
    }
    const sql = await getSql();
    await sql`
      insert into page_views (id, path, referrer)
      values (${crypto.randomUUID()}, ${data.path}, ${data.referrer || null})
    `;
    return { ok: true as const };
  });

export const trackClick = createServerFn({ method: "POST" })
  .validator((d: unknown) =>
    z
      .object({
        path: z.string().max(200),
        label: z.string().max(200),
        href: z.string().max(400).optional(),
        x: z.number().optional(),
        y: z.number().optional(),
      })
      .parse(d),
  )
  .handler(async ({ data }) => {
    if (data.path.startsWith("/admin") || data.path.startsWith("/dashboard")) return { ok: true as const };
    const sql = await getSql();
    await sql`
      insert into click_events (id, path, label, href, x, y)
      values (
        ${crypto.randomUUID()},
        ${data.path},
        ${data.label},
        ${data.href || null},
        ${data.x ?? null},
        ${data.y ?? null}
      )
    `;
    return { ok: true as const };
  });

export const getAnalytics = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const pages = await sql<{ path: string; views: number }>`
      select path, count(*)::int as views from page_views group by path order by views desc limit 12
    `;
    const clicks = await sql<{ label: string; n: number }>`
      select label, count(*)::int as n from click_events group by label order by n desc limit 12
    `;
    const daily = await sql<{ day: string; views: number }>`
      select substr(created_at::text, 1, 10) as day, count(*)::int as views
      from page_views
      group by 1
      order by 1 desc
      limit 14
    `;
    const totals = await sql<{ views: number; clicks: number; tickets: number; inquiries: number }>`
      select
        (select count(*)::int from page_views) as views,
        (select count(*)::int from click_events) as clicks,
        (select count(*)::int from tickets) as tickets,
        (select count(*)::int from inquiries) as inquiries
    `;
    const recentClicks = await sql<{ path: string; label: string; href: string | null; x: number | null; y: number | null; created_at: string }>`
      select path, label, href, x, y, created_at::text as created_at
      from click_events
      order by created_at desc
      limit 40
    `;
    return { pages, clicks, daily: daily.reverse(), totals: totals[0], recentClicks };
  });

function stripShortcodes(s: string) {
  return s.replace(/\[[^\]]+\]/g, " ").replace(/\s{2,}/g, " ").trim();
}

export const getPageField = createServerFn({ method: "GET" })
  .validator((d: unknown) => z.object({ slug: z.string() }).parse(d))
  .handler(async ({ data }) => {
    const sql = await getSql();
    const rows = await sql<{
      slug: string;
      eyebrow: string | null;
      title: string | null;
      lead: string | null;
      features: string | null;
      body: string | null;
      cta_text: string | null;
      cta_url: string | null;
    }>`select slug, eyebrow, title, lead, features, body, cta_text, cta_url from page_fields where slug = ${data.slug}`;
    const row = rows[0];
    if (!row) return null;
    return {
      ...row,
      title: row.title ? stripShortcodes(row.title) : row.title,
      lead: row.lead ? stripShortcodes(row.lead) : row.lead,
      features: row.features ? stripShortcodes(row.features) : row.features,
      body: row.body ? stripShortcodes(row.body) : row.body,
    };
  });

export const listPageFields = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{ slug: string; title: string | null; updated_at: string }>`
      select slug, title, updated_at::text as updated_at from page_fields order by slug
    `;
  });

export const savePageField = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        slug: z.string().min(1).max(80),
        eyebrow: z.string().max(80).optional(),
        title: z.string().max(200).optional(),
        lead: z.string().max(800).optional(),
        features: z.string().max(4000).optional(),
        body: z.string().max(8000).optional(),
        cta_text: z.string().max(80).optional(),
        cta_url: z.string().max(200).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    await sql`
      insert into page_fields (slug, eyebrow, title, lead, features, body, cta_text, cta_url, updated_at)
      values (
        ${data.slug},
        ${data.eyebrow ?? null},
        ${data.title ? stripShortcodes(data.title) : null},
        ${data.lead ? stripShortcodes(data.lead) : null},
        ${data.features ? stripShortcodes(data.features) : null},
        ${data.body ? stripShortcodes(data.body) : null},
        ${data.cta_text ?? null},
        ${data.cta_url ?? null},
        now()
      )
      on conflict (slug) do update set
        eyebrow = excluded.eyebrow,
        title = excluded.title,
        lead = excluded.lead,
        features = excluded.features,
        body = excluded.body,
        cta_text = excluded.cta_text,
        cta_url = excluded.cta_url,
        updated_at = now()
    `;
    return { ok: true as const };
  });

export const getSettings = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const rows = await sql<{ key: string; value: string }>`select key, value from site_settings`;
    const map: Record<string, string> = {};
    for (const r of rows) map[r.key] = r.value;
    return map;
  });

export const saveSettings = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ ga_id: z.string().max(40), clarity_id: z.string().max(40) }).parse(d))
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    await sql`
      insert into site_settings (key, value) values ('ga_id', ${data.ga_id})
      on conflict (key) do update set value = excluded.value
    `;
    await sql`
      insert into site_settings (key, value) values ('clarity_id', ${data.clarity_id})
      on conflict (key) do update set value = excluded.value
    `;
    return { ok: true as const };
  });

export const attachInquiryUser = optionalAuthMiddleware;
