import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { moderateComment } from "@/lib/ai";
import { ensureProfile } from "@/lib/server/profile";
import { awardCommentCoins } from "@/lib/server/wallet";
import { askAgent } from "@/lib/server/ai-hub";

export type CommentRow = {
  id: string;
  post_slug: string;
  name: string;
  body: string;
  status: string;
  ai_reason: string | null;
  created_at: string;
};

export const listApprovedComments = createServerFn({ method: "GET" })
  .validator((d: unknown) => z.object({ slug: z.string().min(1).max(80) }).parse(d))
  .handler(async ({ data }) => {
    const sql = await getSql();
    return sql<CommentRow>`
      select id, post_slug, name, body, status, ai_reason, created_at::text as created_at
      from post_comments
      where post_slug = ${data.slug} and status = 'approved'
      order by created_at asc
      limit 80
    `;
  });

export const submitComment = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        slug: z.string().min(1).max(80),
        body: z.string().trim().min(8).max(1200),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    const profile = await ensureProfile(context.userId);
    const name = profile.display_name?.trim() || "عضو تزنویسه";
    const phone = profile.phone?.trim() || "logged-in";
    const mod = await moderateComment(data.body, name);
    const sql = await getSql();
    const id = crypto.randomUUID();
    await sql`
      insert into post_comments (id, post_slug, name, phone, body, status, ai_reason, user_id)
      values (
        ${id},
        ${data.slug},
        ${name},
        ${phone},
        ${data.body},
        ${mod.status},
        ${mod.reason},
        ${context.userId}
      )
    `;
    let coins = 0;
    if (mod.status === "approved") {
      coins = await awardCommentCoins(context.userId, data.slug);
    }
    return { ok: true as const, status: mod.status, reason: mod.reason, coins };
  });

export const listAdminComments = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    const me = await sql<{ role: string }>`select role from app_profiles where user_id = ${context.userId}`;
    if (me[0]?.role !== "admin") throw new Error("Forbidden");
    return sql<CommentRow & { phone: string }>`
      select id, post_slug, name, phone, body, status, ai_reason, created_at::text as created_at
      from post_comments
      order by created_at desc
      limit 120
    `;
  });

export const setCommentStatus = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z.object({ id: z.string(), status: z.enum(["approved", "held"]) }).parse(d),
  )
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const me = await sql<{ role: string }>`select role from app_profiles where user_id = ${context.userId}`;
    if (me[0]?.role !== "admin") throw new Error("Forbidden");
    await sql`update post_comments set status = ${data.status} where id = ${data.id}`;
    return { ok: true as const };
  });

export const askToolAi = askAgent;
