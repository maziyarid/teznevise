const MODEL = "grok-4.5";

export async function grokChat(opts: {
  system: string;
  user: string;
  maxTokens?: number;
}): Promise<{ ok: true; text: string } | { ok: false; error: string }> {
  const apiKey = process.env.XAI_API_KEY;
  if (!apiKey) return { ok: false, error: "AI is not available" };

  const res = await fetch("https://api.x.ai/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${apiKey}`,
    },
    body: JSON.stringify({
      model: MODEL,
      temperature: 0.2,
      max_tokens: opts.maxTokens ?? 500,
      messages: [
        { role: "system", content: opts.system },
        { role: "user", content: opts.user },
      ],
    }),
  });
  if (!res.ok) return { ok: false, error: `xAI API error ${res.status}` };
  const body = (await res.json()) as { choices?: { message?: { content?: string } }[] };
  const text = body.choices?.[0]?.message?.content?.trim() ?? "";
  if (!text) return { ok: false, error: "پاسخ خالی بود" };
  return { ok: true, text };
}

export async function moderateComment(body: string, name: string) {
  const result = await grokChat({
    system:
      "You moderate Persian academic-site comments. Reply with exactly one line: APPROVE or HOLD, then a short Persian reason after a pipe. HOLD spam, ads, abuse, insults, sexual content, or off-topic promotions. APPROVE genuine questions about thesis, statistics, or the article.",
    user: `نام: ${name}\nنظر: ${body}`,
    maxTokens: 80,
  });
  if (!result.ok) {
    const spammy = /https?:\/\/|telegram|واتساپ|فالو|تبلیغ|crypto|xxx/i.test(body);
    return { status: spammy ? "held" : "approved", reason: "بررسی قاعده‌ای (AI در دسترس نبود)" };
  }
  const upper = result.text.toUpperCase();
  const held = upper.startsWith("HOLD");
  const reason = result.text.split("|")[1]?.trim() || result.text.slice(0, 120);
  return { status: held ? "held" : "approved", reason };
}
