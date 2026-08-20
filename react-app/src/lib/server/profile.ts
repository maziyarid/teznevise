import { getSql } from "@/lib/db";

export type Profile = {
  user_id: string;
  role: string;
  display_name: string | null;
  phone: string | null;
  university: string | null;
  field: string | null;
  degree: string | null;
  city: string | null;
  bio: string | null;
  referral_code: string | null;
  referred_by: string | null;
  profile_complete: number;
  default_agent_id: string | null;
  bonus_granted: number;
};

const PROFILE_COLS =
  "user_id, role, display_name, phone, university, field, degree, city, bio, referral_code, referred_by, profile_complete, default_agent_id, bonus_granted";

export function isProfileComplete(p: Pick<Profile, "display_name" | "phone" | "university" | "field">) {
  return Boolean(p.display_name?.trim() && p.phone?.trim() && p.university?.trim() && p.field?.trim());
}

function makeReferralCode() {
  return crypto.randomUUID().replace(/-/g, "").slice(0, 8).toUpperCase();
}

export async function ensureProfile(userId: string, email?: string | null): Promise<Profile> {
  const sql = await getSql();
  const rows = await sql.query<Profile>(`select ${PROFILE_COLS} from app_profiles where user_id = $1`, [userId]);
  if (rows[0]) {
    if (!rows[0].referral_code) {
      const code = makeReferralCode();
      await sql.query(`update app_profiles set referral_code = $1 where user_id = $2 and referral_code is null`, [
        code,
        userId,
      ]);
      rows[0].referral_code = code;
    }
    await sql.query(`insert into wallets (user_id, balance) values ($1, 0) on conflict (user_id) do nothing`, [
      userId,
    ]);
    return rows[0];
  }
  const admins = await sql<{ n: number }>`select count(*)::int as n from app_profiles where role = 'admin'`;
  const role = (admins[0]?.n ?? 0) === 0 ? "admin" : "user";
  const name = email ? email.split("@")[0] : null;
  const code = makeReferralCode();
  await sql.query(
    `insert into app_profiles (user_id, role, display_name, referral_code)
     values ($1, $2, $3, $4)
     on conflict (user_id) do nothing`,
    [userId, role, name, code],
  );
  await sql.query(`insert into wallets (user_id, balance) values ($1, 0) on conflict (user_id) do nothing`, [userId]);
  const again = await sql.query<Profile>(`select ${PROFILE_COLS} from app_profiles where user_id = $1`, [userId]);
  return (
    again[0] ?? {
      user_id: userId,
      role,
      display_name: name,
      phone: null,
      university: null,
      field: null,
      degree: null,
      city: null,
      bio: null,
      referral_code: code,
      referred_by: null,
      profile_complete: 0,
      default_agent_id: null,
      bonus_granted: 0,
    }
  );
}

export async function requireAdmin(userId: string) {
  const p = await ensureProfile(userId);
  if (p.role !== "admin") {
    const err = new Error("Forbidden");
    (err as Error & { status?: number }).status = 403;
    throw err;
  }
  return p;
}

export async function getSetting(key: string, fallback = "") {
  const sql = await getSql();
  const rows = await sql<{ value: string }>`select value from site_settings where key = ${key}`;
  return rows[0]?.value ?? fallback;
}

export async function setSetting(key: string, value: string) {
  const sql = await getSql();
  await sql`
    insert into site_settings (key, value) values (${key}, ${value})
    on conflict (key) do update set value = excluded.value
  `;
}
