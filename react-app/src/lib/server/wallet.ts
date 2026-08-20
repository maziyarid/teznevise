import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { optionalAuthMiddleware } from "./optional-auth";
import { ensureProfile, getSetting, isProfileComplete, requireAdmin } from "./profile";

export type LedgerRow = {
  id: string;
  amount: number;
  kind: string;
  reason: string;
  ref: string | null;
  created_at: string;
};

export async function creditWallet(
  userId: string,
  amount: number,
  kind: string,
  reason: string,
  ref?: string | null,
) {
  if (!amount) return;
  const sql = await getSql();
  await sql.query(
    `insert into wallets (user_id, balance) values ($1, 0) on conflict (user_id) do nothing`,
    [userId],
  );
  await sql.query(`update wallets set balance = balance + $1, updated_at = now() where user_id = $2`, [
    amount,
    userId,
  ]);
  await sql.query(
    `insert into wallet_ledger (id, user_id, amount, kind, reason, ref) values ($1, $2, $3, $4, $5, $6)`,
    [crypto.randomUUID(), userId, amount, kind, reason, ref ?? null],
  );
}

export async function maybeGrantProfileBonus(userId: string) {
  const p = await ensureProfile(userId);
  if (p.bonus_granted) return { granted: false as const, coins: 0 };
  if (!isProfileComplete(p)) return { granted: false as const, coins: 0 };
  const coins = Number(await getSetting("profile_bonus", "1000")) || 1000;
  const sql = await getSql();
  await sql.query(`update app_profiles set bonus_granted = 1, profile_complete = 1 where user_id = $1 and bonus_granted = 0`, [
    userId,
  ]);
  await creditWallet(userId, coins, "bonus", "هدیه تکمیل پروفایل", "profile");
  if (p.referred_by) {
    const extra = Number(await getSetting("referral_bonus", "200")) || 200;
    await creditWallet(p.referred_by, extra, "referral", "پاداش معرفی دوست", userId);
    await creditWallet(userId, Math.round(extra / 2), "referral", "پاداش استفاده از کد معرفی", p.referred_by);
  }
  return { granted: true as const, coins };
}

export const getMyWallet = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await ensureProfile(context.userId);
    const sql = await getSql();
    const bal = await sql<{ balance: number }>`select balance from wallets where user_id = ${context.userId}`;
    const ledger = await sql<LedgerRow>`
      select id, amount, kind, reason, ref, created_at::text as created_at
      from wallet_ledger where user_id = ${context.userId}
      order by created_at desc limit 40
    `;
    const packs = await sql<{ id: string; title: string; coins: number; irr_price: number }>`
      select id, title, coins, irr_price from coin_packs where active = 1 order by sort
    `;
    const irr = Number(await getSetting("tezcoin_irr", "1000")) || 1000;
    const profile = await ensureProfile(context.userId);
    return {
      balance: bal[0]?.balance ?? 0,
      ledger,
      packs,
      irr,
      referral_code: profile.referral_code,
      bonus_granted: profile.bonus_granted,
      profile_complete: isProfileComplete(profile),
    };
  });

export const getHeaderCredits = createServerFn({ method: "GET" })
  .middleware([optionalAuthMiddleware])
  .handler(async ({ context }) => {
    if (!context.userId) return { balance: null as number | null, bonus: 1000 };
    await ensureProfile(context.userId);
    const sql = await getSql();
    const bal = await sql<{ balance: number }>`select balance from wallets where user_id = ${context.userId}`;
    const bonus = Number(await getSetting("profile_bonus", "1000")) || 1000;
    return { balance: bal[0]?.balance ?? 0, bonus };
  });

export const listCoinPacks = createServerFn({ method: "GET" }).handler(async () => {
  const sql = await getSql();
  return sql<{ id: string; title: string; coins: number; irr_price: number; active: number; sort: number }>`
    select id, title, coins, irr_price, active, sort from coin_packs order by sort
  `;
});

export const saveCoinPack = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        id: z.string().min(1).max(40).optional(),
        title: z.string().trim().min(2).max(80),
        coins: z.number().int().min(1).max(1_000_000),
        irr_price: z.number().int().min(1000).max(1_000_000_000),
        active: z.boolean().optional(),
        sort: z.number().int().min(0).max(99).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const id = data.id ?? crypto.randomUUID();
    await sql`
      insert into coin_packs (id, title, coins, irr_price, active, sort)
      values (${id}, ${data.title}, ${data.coins}, ${data.irr_price}, ${data.active === false ? 0 : 1}, ${data.sort ?? 0})
      on conflict (id) do update set
        title = excluded.title,
        coins = excluded.coins,
        irr_price = excluded.irr_price,
        active = excluded.active,
        sort = excluded.sort
    `;
    return { ok: true as const, id };
  });

export const applyReferral = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) => z.object({ code: z.string().trim().min(4).max(16) }).parse(d))
  .handler(async ({ context, data }) => {
    const me = await ensureProfile(context.userId);
    if (me.referred_by) throw new Error("کد معرفی قبلاً ثبت شده است");
    const sql = await getSql();
    const code = data.code.trim().toUpperCase();
    if (me.referral_code === code) throw new Error("نمی‌توانید کد خودتان را وارد کنید");
    const rows = await sql<{ user_id: string }>`select user_id from app_profiles where referral_code = ${code}`;
    const ref = rows[0];
    if (!ref) throw new Error("کد معرفی معتبر نیست");
    await sql`update app_profiles set referred_by = ${ref.user_id} where user_id = ${context.userId}`;
    const bonus = await maybeGrantProfileBonus(context.userId);
    return { ok: true as const, bonus };
  });

export const claimShareReward = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z.object({ slug: z.string().min(1).max(80), network: z.enum(["telegram", "whatsapp", "x", "linkedin"]) }).parse(d),
  )
  .handler(async ({ context, data }) => {
    await ensureProfile(context.userId);
    const coins = Number(await getSetting("share_reward", "40")) || 40;
    const sql = await getSql();
    const id = crypto.randomUUID();
    try {
      await sql`
        insert into share_rewards (id, user_id, slug, network, coins)
        values (${id}, ${context.userId}, ${data.slug}, ${data.network}, ${coins})
      `;
    } catch {
      return { ok: true as const, awarded: false as const, coins: 0 };
    }
    await creditWallet(context.userId, coins, "share", `اشتراک مطلب در ${data.network}`, data.slug);
    return { ok: true as const, awarded: true as const, coins };
  });

export async function awardCommentCoins(userId: string, slug: string) {
  const coins = Number(await getSetting("comment_reward", "25")) || 25;
  const sql = await getSql();
  try {
    await sql`
      insert into comment_rewards (user_id, post_slug, coins)
      values (${userId}, ${slug}, ${coins})
    `;
  } catch {
    return 0;
  }
  await creditWallet(userId, coins, "comment", "پاداش نظر پژوهشی", slug);
  return coins;
}

export const adminAccounting = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    await requireAdmin(context.userId);
    const sql = await getSql();
    const totals = await sql<{ wallets: number; coins: number; paid: number }>`
      select
        (select count(*)::int from wallets) as wallets,
        (select coalesce(sum(balance),0)::int from wallets) as coins,
        (select coalesce(sum(amount_irr),0)::int from payments where status = 'paid') as paid
    `;
    const recent = await sql<LedgerRow & { user_id: string }>`
      select id, user_id, amount, kind, reason, ref, created_at::text as created_at
      from wallet_ledger order by created_at desc limit 50
    `;
    return { totals: totals[0], recent };
  });
