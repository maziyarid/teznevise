/** Iranian mobile helpers — used for login identity (mapped to email internally). */
export function digitsOnly(raw: string): string {
  return raw.replace(/\D/g, "");
}

export function normalizePhone(raw: string): string {
  let d = digitsOnly(raw);
  if (d.startsWith("0098")) d = d.slice(4);
  else if (d.startsWith("98")) d = d.slice(2);
  if (d.startsWith("0")) d = d.slice(1);
  return d;
}

export function isValidIrMobile(raw: string): boolean {
  return /^9\d{9}$/.test(normalizePhone(raw));
}

export function phoneToEmail(raw: string): string {
  return `${normalizePhone(raw)}@phone.teznevise.ir`;
}

export function formatPhoneFa(raw: string): string {
  const n = normalizePhone(raw);
  if (!/^9\d{9}$/.test(n)) return raw;
  return `0${n}`;
}
