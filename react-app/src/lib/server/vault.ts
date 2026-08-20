import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { ensureProfile } from "./profile";

const ALLOWED = new Set([
  "application/pdf",
  "image/png",
  "image/jpeg",
  "image/webp",
  "application/zip",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  "application/msword",
  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  "text/plain",
  "text/markdown",
]);

const MAX = 2_000_000;

function safeName(name: string) {
  return name.replace(/[^\u0600-\u06FFa-zA-Z0-9._-]+/g, "_").slice(0, 80) || "file";
}

export const listVaultFiles = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ ticketId: z.string() }).parse(d))
  .handler(async ({ context, data }) => {
    const profile = await ensureProfile(context.userId);
    const sql = await getSql();
    const tickets = await sql<{ user_id: string }>`select user_id from tickets where id = ${data.ticketId}`;
    const t = tickets[0];
    if (!t) throw new Error("Not found");
    if (profile.role !== "admin" && t.user_id !== context.userId) throw new Error("Forbidden");
    return sql<{ id: string; filename: string; mime: string; byte_size: number; created_at: string }>`
      select id, filename, mime, byte_size, created_at::text as created_at
      from secure_vault where ticket_id = ${data.ticketId}
      order by created_at desc
    `;
  });

export const uploadVaultFile = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        ticketId: z.string(),
        filename: z.string().min(1).max(120),
        mime: z.string().min(3).max(120),
        base64: z.string().min(8).max(3_000_000),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    const profile = await ensureProfile(context.userId);
    if (!ALLOWED.has(data.mime)) throw new Error("این نوع فایل مجاز نیست");
    const sql = await getSql();
    const tickets = await sql<{ user_id: string }>`select user_id from tickets where id = ${data.ticketId}`;
    const t = tickets[0];
    if (!t) throw new Error("Not found");
    if (profile.role !== "admin" && t.user_id !== context.userId) throw new Error("Forbidden");
    const raw = data.base64.replace(/^data:[^;]+;base64,/, "");
    const size = Math.ceil((raw.length * 3) / 4);
    if (size > MAX) throw new Error("حجم فایل حداکثر ۲ مگابایت است");
    const id = crypto.randomUUID();
    await sql`
      insert into secure_vault (id, ticket_id, user_id, filename, mime, byte_size, payload)
      values (
        ${id},
        ${data.ticketId},
        ${context.userId},
        ${safeName(data.filename)},
        ${data.mime},
        ${size},
        ${raw}
      )
    `;
    return { id };
  });

export const downloadVaultFile = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ id: z.string() }).parse(d))
  .handler(async ({ context, data }) => {
    const profile = await ensureProfile(context.userId);
    const sql = await getSql();
    const rows = await sql<{
      ticket_id: string;
      filename: string;
      mime: string;
      payload: string;
    }>`select ticket_id, filename, mime, payload from secure_vault where id = ${data.id}`;
    const file = rows[0];
    if (!file) throw new Error("Not found");
    const tickets = await sql<{ user_id: string }>`select user_id from tickets where id = ${file.ticket_id}`;
    const t = tickets[0];
    if (!t) throw new Error("Not found");
    if (profile.role !== "admin" && t.user_id !== context.userId) throw new Error("Forbidden");
    return { filename: file.filename, mime: file.mime, base64: file.payload };
  });
