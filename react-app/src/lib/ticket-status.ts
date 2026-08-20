export function statusFa(s: string) {
  if (s === "open") return "باز";
  if (s === "pending") return "در انتظار پاسخ";
  if (s === "closed") return "بسته";
  return s;
}

export function priorityFa(s: string) {
  if (s === "high") return "بالا";
  if (s === "low") return "پایین";
  if (s === "normal") return "عادی";
  return s;
}
