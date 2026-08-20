import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { grokChat } from "@/lib/ai";
import { ensureProfile, getSetting, requireAdmin } from "./profile";

export type AgentRow = {
  id: string;
  name: string;
  provider: string;
  model: string;
  system_prompt: string;
  active: number;
  sort: number;
};

async function rateLimit(userId: string, kind: string, max = 20) {
  const sql = await getSql();
  const rows = await sql<{ n: number }>`
    select count(*)::int as n from ai_runs
    where user_id = ${userId} and created_at > now() - interval '1 day'
  `;
  if ((rows[0]?.n ?? 0) >= max) {
    throw new Error("سهمیه روزانه پرسش از هوش مصنوعی تمام شده است");
  }
  await sql`insert into ai_runs (id, user_id, kind) values (${crypto.randomUUID()}, ${userId}, ${kind})`;
}

async function openRouterChat(system: string, user: string, model: string) {
  const key = await getSetting("openrouter_key");
  if (!key) return grokChat({ system, user, maxTokens: 700 });
  const res = await fetch("https://openrouter.ai/api/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${key}`,
    },
    body: JSON.stringify({
      model: model || "openai/gpt-4o-mini",
      max_tokens: 700,
      messages: [
        { role: "system", content: system },
        { role: "user", content: user },
      ],
    }),
  });
  if (!res.ok) return grokChat({ system, user, maxTokens: 700 });
  const body = (await res.json()) as { choices?: { message?: { content?: string } }[] };
  const text = body.choices?.[0]?.message?.content?.trim() ?? "";
  if (!text) return { ok: false as const, error: "پاسخ خالی بود" };
  return { ok: true as const, text };
}

async function tavilySearch(query: string) {
  const key = await getSetting("tavily_key");
  if (!key) return "";
  const res = await fetch("https://api.tavily.com/search", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ api_key: key, query, max_results: 5, search_depth: "basic" }),
  });
  if (!res.ok) return "";
  const body = (await res.json()) as { results?: { title?: string; url?: string; content?: string }[] };
  return (body.results ?? [])
    .slice(0, 5)
    .map((r) => `- ${r.title ?? ""} (${r.url ?? ""}): ${r.content ?? ""}`)
    .join("\n");
}

async function youSearch(query: string) {
  const key = await getSetting("youcom_key");
  if (!key) return "";
  const url = `https://api.ydc-index.io/v1/search?query=${encodeURIComponent(query)}`;
  const res = await fetch(url, { headers: { "X-API-Key": key } });
  if (!res.ok) return "";
  const body = (await res.json()) as { hits?: { title?: string; url?: string; description?: string }[] };
  return (body.hits ?? [])
    .slice(0, 5)
    .map((r) => `- ${r.title ?? ""} (${r.url ?? ""}): ${r.description ?? ""}`)
    .join("\n");
}

export const listAgents = createServerFn({ method: "GET" }).handler(async () => {
  const sql = await getSql();
  return sql<{ id: string; name: string; provider: string }>`
    select id, name, provider from ai_agents where active = 1 order by sort
  `;
});

export const listAdminAgents = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<AgentRow>`
      select id, name, provider, model, system_prompt, active, sort from ai_agents order by sort
    `;
  });

export const saveAgent = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        id: z.string().min(1).max(40).optional(),
        name: z.string().trim().min(2).max(80),
        provider: z.enum(["xai", "openrouter", "tavily", "you"]),
        model: z.string().max(80).optional(),
        system_prompt: z.string().max(4000).optional(),
        active: z.boolean().optional(),
        sort: z.number().int().optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const id = data.id ?? crypto.randomUUID();
    await sql`
      insert into ai_agents (id, name, provider, model, system_prompt, active, sort)
      values (
        ${id},
        ${data.name},
        ${data.provider},
        ${data.model ?? ""},
        ${data.system_prompt ?? ""},
        ${data.active === false ? 0 : 1},
        ${data.sort ?? 0}
      )
      on conflict (id) do update set
        name = excluded.name,
        provider = excluded.provider,
        model = excluded.model,
        system_prompt = excluded.system_prompt,
        active = excluded.active,
        sort = excluded.sort
    `;
    return { ok: true as const, id };
  });

export const listSkills = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    return sql<{ id: string; tool_slug: string; title: string; created_at: string }>`
      select id, tool_slug, title, created_at::text as created_at from tool_skills order by created_at desc
    `;
  });

export const saveSkill = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        tool_slug: z.string().min(1).max(80),
        title: z.string().trim().min(2).max(80),
        body_md: z.string().min(8).max(20000),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    await sql`
      insert into tool_skills (id, tool_slug, title, body_md)
      values (${crypto.randomUUID()}, ${data.tool_slug}, ${data.title}, ${data.body_md})
    `;
    return { ok: true as const };
  });

export const askAgent = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        tool: z.string().max(80),
        context: z.string().max(4000),
        question: z.string().trim().min(4).max(1500),
        agentId: z.string().max(40).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await rateLimit(context.userId, "tool");
    const me = await ensureProfile(context.userId);
    const sql = await getSql();
    const agentId = data.agentId || me.default_agent_id || "method";
    const agents = await sql<AgentRow>`select id, name, provider, model, system_prompt, active, sort from ai_agents where id = ${agentId}`;
    const agent = agents[0];
    const skills = await sql<{ title: string; body_md: string }>`
      select title, body_md from tool_skills
      where tool_slug = ${data.tool} or tool_slug = '*'
      order by created_at desc limit 4
    `;
    const skillBlock = skills.map((s) => `### ${s.title}\n${s.body_md}`).join("\n\n");
    const system =
      agent?.system_prompt ||
      "You are a Persian research-methods and statistics tutor for Teznevise. Answer in clear Persian. Show formulas when useful. Never invent p-values.";
    let extra = "";
    if (agent?.provider === "tavily") extra = await tavilySearch(data.question);
    if (agent?.provider === "you") extra = await youSearch(data.question);
    const user = `ابزار: ${data.tool}\nزمینه:\n${data.context}\n\n${skillBlock ? `مهارت‌های بارگذاری‌شده:\n${skillBlock}\n\n` : ""}${extra ? `نتایج جستجو:\n${extra}\n\n` : ""}درخواست کاربر:\n${data.question}`;

    if (agent?.provider === "openrouter") return openRouterChat(system, user, agent.model);
    return grokChat({ system, user, maxTokens: 700 });
  });

export const setMyAgent = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ agentId: z.string().min(1).max(40) }).parse(d))
  .handler(async ({ context, data }) => {
    await ensureProfile(context.userId);
    const sql = await getSql();
    await sql`update app_profiles set default_agent_id = ${data.agentId} where user_id = ${context.userId}`;
    return { ok: true as const };
  });
