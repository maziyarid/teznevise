import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { optionalAuthMiddleware } from "./optional-auth";
import { getSetting, requireAdmin, setSetting } from "./profile";

export const SETTING_KEYS = [
  "ga_id",
  "clarity_id",
  "tezcoin_irr",
  "profile_bonus",
  "comment_reward",
  "share_reward",
  "referral_bonus",
  "zarinpal_merchant",
  "aqaye_pin",
  "gateway_mode",
  "openrouter_key",
  "youcom_key",
  "tavily_key",
  "enamad_code",
  "samandehi_code",
] as const;

const DEFAULT_SEARCH = ["پایان‌نامه", "پروپوزال", "SPSS", "حجم نمونه", "آلفای کرونباخ", "فصل چهارم", "روش تحقیق"];

export const getPublicSite = createServerFn({ method: "GET" }).handler(async () => {
  const sql = await getSql();
  const irr = Number(await getSetting("tezcoin_irr", "1000")) || 1000;
  const bonus = Number(await getSetting("profile_bonus", "1000")) || 1000;
  const nav = await sql<{ id: string; label: string; href: string; icon: string }>`
    select id, label, href, icon from bottom_nav_items where active = 1 order by sort
  `;
  const agents = await sql<{ id: string; name: string; provider: string }>`
    select id, name, provider from ai_agents where active = 1 order by sort
  `;
  const popular = await sql<{ query: string; n: number }>`
    select query, count(*)::int as n from search_log group by query order by n desc limit 8
  `;
  return {
    irr,
    bonus,
    nav,
    agents,
    popular: popular.length ? popular.map((p) => p.query) : DEFAULT_SEARCH,
  };
});

export const logSearch = createServerFn({ method: "POST" })
  .middleware([optionalAuthMiddleware])
  .validator((d: unknown) => z.object({ query: z.string().trim().min(2).max(80) }).parse(d))
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    await sql`
      insert into search_log (id, query, user_id)
      values (${crypto.randomUUID()}, ${data.query}, ${context.userId})
    `;
    return { ok: true as const };
  });

export const getAdminSettingsMap = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const rows = await sql<{ key: string; value: string }>`select key, value from site_settings`;
    const map: Record<string, string> = {};
    for (const r of rows) map[r.key] = r.value;
    return map;
  });

export const saveAdminSettingsMap = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.record(z.string(), z.string().max(4000)).parse(d))
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const allowed = new Set<string>(SETTING_KEYS);
    for (const [key, value] of Object.entries(data)) {
      if (!allowed.has(key)) continue;
      await setSetting(key, value);
    }
    return { ok: true as const };
  });

export const listBottomNav = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{ id: string; label: string; href: string; icon: string; sort: number; active: number }>`
      select id, label, href, icon, sort, active from bottom_nav_items order by sort
    `;
  });

export const saveBottomNav = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        items: z
          .array(
            z.object({
              id: z.string().min(1).max(40),
              label: z.string().trim().min(1).max(24),
              href: z.string().trim().min(1).max(120),
              icon: z.string().min(1).max(24),
              sort: z.number().int(),
              active: z.boolean(),
            }),
          )
          .min(2)
          .max(6),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    await sql`delete from bottom_nav_items`;
    for (const item of data.items) {
      await sql`
        insert into bottom_nav_items (id, label, href, icon, sort, active)
        values (${item.id}, ${item.label}, ${item.href}, ${item.icon}, ${item.sort}, ${item.active ? 1 : 0})
      `;
    }
    return { ok: true as const };
  });
