export function faNum(n: number) {
  return new Intl.NumberFormat("fa-IR").format(n);
}

export function faMoney(n: number) {
  return `${faNum(n)} تومان`;
}
