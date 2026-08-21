import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { grokChat, splitThinking } from "@/lib/ai";
import { optionalAuthMiddleware } from "./optional-auth";
import { creditWallet } from "./wallet";
import { ensureProfile, getSetting, requireAdmin } from "./profile";

export type AgentRow = {
  id: string;
  name: string;
  provider: string;
  model: string;
  system_prompt: string;
  active: number;
  sort: number;
  api_base?: string;
  api_key_setting?: string;
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
        api_base: z.string().max(200).optional(),
        api_key_setting: z.string().max(80).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const id = data.id ?? crypto.randomUUID();
    await sql`
      insert into ai_agents (id, name, provider, model, system_prompt, active, sort, api_base, api_key_setting)
      values (
        ${id},
        ${data.name},
        ${data.provider},
        ${data.model ?? ""},
        ${data.system_prompt ?? ""},
        ${data.active === false ? 0 : 1},
        ${data.sort ?? 0},
        ${data.api_base ?? ""},
        ${data.api_key_setting ?? ""}
      )
      on conflict (id) do update set
        name = excluded.name,
        provider = excluded.provider,
        model = excluded.model,
        system_prompt = excluded.system_prompt,
        active = excluded.active,
        sort = excluded.sort,
        api_base = excluded.api_base,
        api_key_setting = excluded.api_key_setting
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

async function loadAgentsByIds(ids: string[]) {
  const sql = await getSql();
  const all = await sql<AgentRow>`
    select id, name, provider, model, system_prompt, active, sort,
           coalesce(api_base, '') as api_base,
           coalesce(api_key_setting, '') as api_key_setting
    from ai_agents where active = 1 order by sort
  `.catch(async () =>
    sql<AgentRow>`select id, name, provider, model, system_prompt, active, sort from ai_agents where active = 1 order by sort`,
  );
  if (!ids.length) return all.slice(0, 1);
  const picked = all.filter((a) => ids.includes(a.id));
  return picked.length ? picked : all.slice(0, 1);
}

async function runAgent(agent: AgentRow, system: string, user: string, thinking: boolean) {
  const sys = thinking
    ? `${system}\n\nIf you reason, wrap the private reasoning in <think>...</think> then give the Persian answer.`
    : system;
  const keyName = agent.api_key_setting || (agent.provider === "openrouter" ? "openrouter_key" : "xai_api_key");
  const apiKey = (await getSetting(keyName, "")) || process.env.XAI_API_KEY || "";
  const apiBase =
    agent.api_base ||
    (agent.provider === "openrouter" ? "https://openrouter.ai/api/v1" : "") ||
    (await getSetting("openai_api_base", ""));
  let extra = "";
  if (agent.provider === "tavily") extra = await tavilySearch(user);
  if (agent.provider === "you") extra = await youSearch(user);
  const prompt = extra ? `${user}\n\nنتایج جستجو:\n${extra}` : user;
  if (agent.provider === "openrouter" && !agent.api_base) {
    const res = await openRouterChat(sys, prompt, agent.model);
    if (!res.ok) return { agentName: agent.name, thinking: "", content: res.error };
    const parts = splitThinking(res.text);
    return { agentName: agent.name, ...parts };
  }
  const res = await grokChat({
    system: sys,
    user: prompt,
    maxTokens: 800,
    model: agent.model || undefined,
    apiKey: apiKey || undefined,
    apiBase: apiBase || undefined,
  });
  if (!res.ok) return { agentName: agent.name, thinking: "", content: res.error };
  const parts = splitThinking(res.text);
  return { agentName: agent.name, ...parts };
}

export const getAiQuota = createServerFn({ method: "GET" })
  .middleware([optionalAuthMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    const guestLimit = Number(await getSetting("ai_guest_limit", "3")) || 3;
    const signedLimit = Number(await getSetting("ai_signed_in_limit", "20")) || 20;
    const cost = Number(await getSetting("ai_cost_per_message", "5")) || 5;
    const userId = context.userId;
    const limit = userId ? signedLimit : guestLimit;
    let used = 0;
    let credits = 0;
    if (userId) {
      const rows = await sql<{ n: number }>`
        select count(*)::int as n from ai_runs
        where user_id = ${userId} and created_at > now() - interval '1 day'
      `;
      used = rows[0]?.n ?? 0;
      const bal = await sql<{ balance: number }>`select balance from wallets where user_id = ${userId}`;
      credits = bal[0]?.balance ?? 0;
    }
    return { remaining: Math.max(0, limit - used), limit, cost, credits };
  });

export const listMyThreads = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ tool: z.string().max(80).optional() }).parse(d ?? {}))
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const threads = data.tool
      ? await sql<{ id: string; title: string }>`
          select id, title from ai_threads
          where user_id = ${context.userId} and tool = ${data.tool}
          order by updated_at desc limit 5
        `.catch(() => [])
      : await sql<{ id: string; title: string }>`
          select id, title from ai_threads
          where user_id = ${context.userId}
          order by updated_at desc limit 8
        `.catch(() => []);
    const out: { id: string; title: string; messages: { id: string; role: "user" | "assistant"; agentName: string; thinking?: string; content: string }[] }[] = [];
    for (const t of threads) {
      const messages = await sql<{ id: string; role: "user" | "assistant"; agent_name: string; thinking: string; content: string }>`
        select id, role, agent_name, thinking, content
        from ai_thread_messages where thread_id = ${t.id} order by created_at
      `.catch(() => []);
      out.push({
        id: t.id,
        title: t.title,
        messages: messages.map((m) => ({
          id: m.id,
          role: m.role,
          agentName: m.agent_name,
          thinking: m.thinking,
          content: m.content,
        })),
      });
    }
    return out;
  });

export const sendToolChat = createServerFn({ method: "POST" })
  .middleware([optionalAuthMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        tool: z.string().max(80),
        context: z.string().max(4000),
        question: z.string().trim().min(2).max(1500),
        agentIds: z.array(z.string().max(40)).max(4).optional(),
        mode: z.enum(["single", "collab", "reflect"]).optional(),
        thinking: z.boolean().optional(),
        threadId: z.string().max(60).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const userId = context.userId;
    const guestLimit = Number(await getSetting("ai_guest_limit", "3")) || 3;
    const signedLimit = Number(await getSetting("ai_signed_in_limit", "20")) || 20;
    const cost = Number(await getSetting("ai_cost_per_message", "5")) || 5;
    const limit = userId ? signedLimit : guestLimit;
    const uid = userId ?? "guest";
    const usedRows = await sql<{ n: number }>`
      select count(*)::int as n from ai_runs
      where user_id = ${uid} and created_at > now() - interval '1 day'
    `;
    const used = usedRows[0]?.n ?? 0;
    if (used >= limit) {
      return { ok: false as const, error: userId ? "سهمیه روزانه تمام شده است. تزکوین بخرید یا فردا برگردید." : "سهمیه مهمان تمام شد. وارد شوید تا ادامه دهید." };
    }
    if (userId && cost > 0) {
      const bal = await sql<{ balance: number }>`select balance from wallets where user_id = ${userId}`;
      const credits = bal[0]?.balance ?? 0;
      if (credits < cost) {
        return { ok: false as const, error: "اعتبار کافی نیست. از کیف پول تزکوین بخرید." };
      }
    }

    const agents = await loadAgentsByIds(data.agentIds ?? []);
    const mode = data.mode ?? (agents.length > 1 ? "collab" : "single");
    const thinking = Boolean(data.thinking);
    const skills = await sql<{ title: string; body_md: string }>`
      select title, body_md from tool_skills
      where tool_slug = ${data.tool} or tool_slug = '*'
      order by created_at desc limit 4
    `;
    const skillBlock = skills.map((s) => `### ${s.title}\n${s.body_md}`).join("\n\n");
    const userPrompt = `ابزار: ${data.tool}\nزمینه:\n${data.context}\n\n${skillBlock ? `مهارت‌های بارگذاری‌شده:\n${skillBlock}\n\n` : ""}درخواست کاربر:\n${data.question}`;

    const replies: { agentName: string; thinking: string; content: string }[] = [];
    if (mode === "single" || agents.length === 1) {
      replies.push(await runAgent(agents[0], agents[0].system_prompt || "پاسخ را فارسی، دقیق و بدون ارجاع جعلی بده.", userPrompt, thinking));
    } else {
      for (const agent of agents) {
        replies.push(await runAgent(agent, agent.system_prompt || "پاسخ را فارسی بده.", userPrompt, thinking));
      }
      if (mode === "reflect" || mode === "collab") {
        const synthesis = replies.map((r) => `## ${r.agentName}\n${r.content}`).join("\n\n");
        const last = agents[agents.length - 1];
        const reflect = await runAgent(
          last,
          "تو جمع‌بندی‌کننده هستی. پاسخ عامل‌های دیگر را مقایسه کن و یک جمع‌بندی فارسی روشن بده.",
          `سؤال:\n${data.question}\n\nپاسخ‌ها:\n${synthesis}`,
          thinking,
        );
        replies.push({ ...reflect, agentName: `${last.name} · جمع‌بندی` });
      }
    }

    await sql`insert into ai_runs (id, user_id, kind) values (${crypto.randomUUID()}, ${uid}, ${"chat"})`;
    if (userId && cost > 0) {
      await creditWallet(userId, -cost, "ai", "پیام دستیار هوش مصنوعی", data.tool);
    }

    let threadId = data.threadId || crypto.randomUUID();
    try {
      if (userId) {
        const existing = data.threadId
          ? await sql<{ id: string }>`select id from ai_threads where id = ${threadId} and user_id = ${userId}`
          : [];
        if (!existing[0]) {
          threadId = crypto.randomUUID();
          await sql`
            insert into ai_threads (id, user_id, tool, title)
            values (${threadId}, ${userId}, ${data.tool}, ${data.question.slice(0, 80)})
          `;
        } else {
          await sql`update ai_threads set updated_at = now() where id = ${threadId}`;
        }
        await sql`
          insert into ai_thread_messages (id, thread_id, role, agent_name, thinking, content)
          values (${crypto.randomUUID()}, ${threadId}, ${"user"}, ${"شما"}, ${""}, ${data.question})
        `;
        for (const r of replies) {
          await sql`
            insert into ai_thread_messages (id, thread_id, role, agent_name, thinking, content)
            values (${crypto.randomUUID()}, ${threadId}, ${"assistant"}, ${r.agentName}, ${r.thinking}, ${r.content})
          `;
        }
      }
    } catch {
      /* tables may not exist yet on first boot */
    }

    const bal = userId
      ? ((await sql<{ balance: number }>`select balance from wallets where user_id = ${userId}`)[0]?.balance ?? 0)
      : 0;
    return {
      ok: true as const,
      threadId,
      replies,
      quota: { remaining: Math.max(0, limit - used - 1), limit, cost, credits: bal },
    };
  });

