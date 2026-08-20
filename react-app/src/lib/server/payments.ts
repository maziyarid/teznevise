import { createServerFn } from "@tanstack/react-start";
import { z } from "zod";
import { getSql } from "@/lib/db";
import { authMiddleware } from "@/lib/auth/middleware";
import { ensureProfile, getSetting } from "./profile";
import { creditWallet } from "./wallet";

type Pack = { id: string; title: string; coins: number; irr_price: number };

async function getPack(id: string): Promise<Pack> {
  const sql = await getSql();
  const rows = await sql<Pack>`select id, title, coins, irr_price from coin_packs where id = ${id} and active = 1`;
  if (!rows[0]) throw new Error("بسته اعتبار یافت نشد");
  return rows[0];
}

function sandboxEnabled(merchant: string, pin: string, mode: string) {
  return mode !== "live" || (!merchant && !pin);
}

export const startPayment = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        packId: z.string().min(1).max(40),
        gateway: z.enum(["zarinpal", "aqaye", "sandbox"]),
        origin: z.string().url(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    await ensureProfile(context.userId);
    const pack = await getPack(data.packId);
    const merchant = await getSetting("zarinpal_merchant");
    const pin = await getSetting("aqaye_pin");
    const mode = await getSetting("gateway_mode", "sandbox");
    const sql = await getSql();
    const id = crypto.randomUUID();
    const callback = `${data.origin.replace(/\/$/, "")}/pay/callback`;

    if (data.gateway === "sandbox" || sandboxEnabled(merchant, pin, mode)) {
      await sql`
        insert into payments (id, user_id, pack_id, gateway, amount_irr, coins, status, authority, paid_at)
        values (${id}, ${context.userId}, ${pack.id}, ${"sandbox"}, ${pack.irr_price}, ${pack.coins}, ${"paid"}, ${id}, now())
      `;
      await creditWallet(context.userId, pack.coins, "purchase", `خرید ${pack.title}`, id);
      return { ok: true as const, mode: "sandbox" as const, paymentId: id, redirect: `/dashboard/wallet?paid=${id}` };
    }

    if (data.gateway === "zarinpal") {
      const res = await fetch("https://api.zarinpal.com/pg/v4/payment/request.json", {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({
          merchant_id: merchant,
          amount: pack.irr_price,
          callback_url: callback,
          description: `Tezcoin ${pack.title}`,
          metadata: { payment_id: id },
        }),
      });
      const body = (await res.json()) as { data?: { authority?: string; code?: number }; errors?: unknown };
      const authority = body.data?.authority;
      if (!authority) throw new Error("زرین‌پال درخواست را نپذیرفت");
      await sql`
        insert into payments (id, user_id, pack_id, gateway, amount_irr, coins, status, authority)
        values (${id}, ${context.userId}, ${pack.id}, ${"zarinpal"}, ${pack.irr_price}, ${pack.coins}, ${"pending"}, ${authority})
      `;
      return {
        ok: true as const,
        mode: "live" as const,
        paymentId: id,
        redirect: `https://www.zarinpal.com/pg/StartPay/${authority}`,
      };
    }

    const res = await fetch("https://panel.aqayepardakht.ir/api/v2/create", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        pin,
        amount: pack.irr_price,
        callback,
        invoice_id: id,
      }),
    });
    const body = (await res.json()) as { transid?: string; status?: string };
    if (!body.transid) throw new Error("آقای پرداخت درخواست را نپذیرفت");
    await sql`
      insert into payments (id, user_id, pack_id, gateway, amount_irr, coins, status, authority)
      values (${id}, ${context.userId}, ${pack.id}, ${"aqaye"}, ${pack.irr_price}, ${pack.coins}, ${"pending"}, ${body.transid})
    `;
    return {
      ok: true as const,
      mode: "live" as const,
      paymentId: id,
      redirect: `https://panel.aqayepardakht.ir/startpay/${body.transid}`,
    };
  });

export const verifyPayment = createServerFn({ method: "POST" })
  .middleware([authMiddleware])
  .validator((d: unknown) =>
    z
      .object({
        authority: z.string().min(1).max(80).optional(),
        transid: z.string().min(1).max(80).optional(),
        status: z.string().max(20).optional(),
      })
      .parse(d),
  )
  .handler(async ({ context, data }) => {
    const sql = await getSql();
    const key = data.authority || data.transid;
    if (!key) throw new Error("کد پیگیری موجود نیست");
    const rows = await sql<{
      id: string;
      user_id: string;
      gateway: string;
      amount_irr: number;
      coins: number;
      status: string;
      authority: string | null;
    }>`
      select id, user_id, gateway, amount_irr, coins, status, authority
      from payments where authority = ${key} and user_id = ${context.userId}
    `;
    const pay = rows[0];
    if (!pay) throw new Error("پرداخت پیدا نشد");
    if (pay.status === "paid") return { ok: true as const, already: true as const, coins: pay.coins };

    if (data.status && data.status.toUpperCase() !== "OK") {
      await sql`update payments set status = 'failed' where id = ${pay.id}`;
      throw new Error("پرداخت لغو شد");
    }

    const merchant = await getSetting("zarinpal_merchant");
    const pin = await getSetting("aqaye_pin");

    if (pay.gateway === "zarinpal") {
      const res = await fetch("https://api.zarinpal.com/pg/v4/payment/verify.json", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ merchant_id: merchant, amount: pay.amount_irr, authority: pay.authority }),
      });
      const body = (await res.json()) as { data?: { code?: number; ref_id?: number } };
      if (body.data?.code !== 100 && body.data?.code !== 101) throw new Error("تأیید زرین‌پال ناموفق بود");
      await sql`update payments set status = 'paid', ref_id = ${String(body.data.ref_id ?? "")}, paid_at = now() where id = ${pay.id}`;
    } else if (pay.gateway === "aqaye") {
      const res = await fetch("https://panel.aqayepardakht.ir/api/v2/verify", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ pin, amount: pay.amount_irr, transid: pay.authority }),
      });
      const body = (await res.json()) as { status?: string | number };
      if (String(body.status) !== "1" && String(body.status) !== "success") throw new Error("تأیید آقای پرداخت ناموفق بود");
      await sql`update payments set status = 'paid', paid_at = now() where id = ${pay.id}`;
    } else {
      await sql`update payments set status = 'paid', paid_at = now() where id = ${pay.id}`;
    }

    await creditWallet(context.userId, pay.coins, "purchase", "خرید تزکوین", pay.id);
    return { ok: true as const, already: false as const, coins: pay.coins };
  });

export const listMyPayments = createServerFn({ method: "GET" })
  .middleware([authMiddleware])
  .handler(async ({ context }) => {
    const sql = await getSql();
    return sql<{
      id: string;
      gateway: string;
      amount_irr: number;
      coins: number;
      status: string;
      created_at: string;
    }>`
      select id, gateway, amount_irr, coins, status, created_at::text as created_at
      from payments where user_id = ${context.userId}
      order by created_at desc limit 30
    `;
  });
