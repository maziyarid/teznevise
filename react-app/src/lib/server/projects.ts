import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { ensureProfile, requireAdmin } from "./profile";

export const PROJECT_STATUSES = [
  "intake",
  "brief",
  "drafting",
  "analysis",
  "revision",
  "defense",
  "delivered",
] as const;

export type ProjectStatus = (typeof PROJECT_STATUSES)[number];

export const PROJECT_STATUS_FA: Record<string, string> = {
  intake: "ثبت اولیه",
  brief: "بررسی موضوع",
  drafting: "نگارش",
  analysis: "تحلیل آماری",
  revision: "اصلاحات",
  defense: "آمادگی دفاع",
  delivered: "تحویل نهایی",
};

const progressFor: Record<ProjectStatus, number> = {
  intake: 8,
  brief: 18,
  drafting: 40,
  analysis: 62,
  revision: 78,
  defense: 90,
  delivered: 100,
};

export const listMyProjects = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    return sql<{
      id: string;
      title: string;
      service: string;
      status: string;
      progress: number;
      due_at: string | null;
      updated_at: string;
    }>`
      select id, title, service, status, progress, due_at, updated_at::text as updated_at
      from projects where user_id = ${context.userId}
      order by updated_at desc
    `;
  });

export const createProject = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        title: z.string().trim().min(4).max(160),
        service: z.string().trim().min(2).max(80),
        notes: z.string().trim().max(2000).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await ensureProfile(context.userId);
    const sql = await getSql();
    const id = crypto.randomUUID();
    await sql`
      insert into projects (id, user_id, title, service, notes, status, progress)
      values (${id}, ${context.userId}, ${data.title}, ${data.service}, ${data.notes ?? null}, ${"intake"}, ${8})
    `;
    await sql`
      insert into project_events (id, project_id, status, note)
      values (${crypto.randomUUID()}, ${id}, ${"intake"}, ${"پروژه ثبت شد و در صف بررسی مشاور قرار گرفت."})
    `;
    return { id };
  });

export const getProject = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ id: z.string() }).parse(d))
  .handler(async ({ context, data }) => {
    const profile = await ensureProfile(context.userId);
    const sql = await getSql();
    const rows = await sql<{
      id: string;
      user_id: string;
      title: string;
      service: string;
      status: string;
      progress: number;
      due_at: string | null;
      notes: string | null;
      created_at: string;
    }>`
      select id, user_id, title, service, status, progress, due_at, notes, created_at::text as created_at
      from projects where id = ${data.id}
    `;
    const project = rows[0];
    if (!project) throw new Error("Not found");
    if (profile.role !== "admin" && project.user_id !== context.userId) throw new Error("Forbidden");
    const events = await sql<{ id: string; status: string; note: string | null; created_at: string }>`
      select id, status, note, created_at::text as created_at
      from project_events where project_id = ${data.id} order by created_at asc
    `;
    return { project, events, isAdmin: profile.role === "admin" };
  });

export const listAdminProjects = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{
      id: string;
      title: string;
      service: string;
      status: string;
      progress: number;
      user_id: string;
      updated_at: string;
    }>`
      select id, title, service, status, progress, user_id, updated_at::text as updated_at
      from projects order by updated_at desc limit 80
    `;
  });

export const setProjectStatus = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        id: z.string(),
        status: z.enum(PROJECT_STATUSES),
        note: z.string().trim().max(400).optional(),
        due_at: z.string().max(40).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const progress = progressFor[data.status];
    await sql`
      update projects
      set status = ${data.status}, progress = ${progress}, due_at = coalesce(${data.due_at ?? null}, due_at), updated_at = now()
      where id = ${data.id}
    `;
    await sql`
      insert into project_events (id, project_id, status, note)
      values (${crypto.randomUUID()}, ${data.id}, ${data.status}, ${data.note ?? PROJECT_STATUS_FA[data.status]})
    `;
    return { ok: true as const };
  });
