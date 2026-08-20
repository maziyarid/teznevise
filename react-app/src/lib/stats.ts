export function parseSeries(raw: string): number[] {
  return raw
    .split(/[\s,;،]+/)
    .map((t) => t.trim())
    .filter(Boolean)
    .map((t) => Number(t.replace(/٫/g, ".")))
    .filter((n) => Number.isFinite(n));
}

export function parseTable(raw: string): number[][] {
  return raw
    .split(/\n+/)
    .map((line) => parseSeries(line))
    .filter((r) => r.length);
}

export function mean(xs: number[]) {
  return xs.reduce((a, b) => a + b, 0) / xs.length;
}

export function variance(xs: number[], sample = true) {
  const m = mean(xs);
  const d = xs.reduce((a, x) => a + (x - m) ** 2, 0);
  return d / (sample ? xs.length - 1 : xs.length);
}

export function stdev(xs: number[], sample = true) {
  return Math.sqrt(variance(xs, sample));
}

export function median(xs: number[]) {
  const s = [...xs].sort((a, b) => a - b);
  const n = s.length;
  return n % 2 ? s[(n - 1) / 2] : (s[n / 2 - 1] + s[n / 2]) / 2;
}

export function quantile(xs: number[], p: number) {
  const s = [...xs].sort((a, b) => a - b);
  const idx = (s.length - 1) * p;
  const lo = Math.floor(idx);
  const hi = Math.ceil(idx);
  if (lo === hi) return s[lo];
  return s[lo] * (hi - idx) + s[hi] * (idx - lo);
}

export function skewness(xs: number[]) {
  const m = mean(xs);
  const s = stdev(xs);
  const n = xs.length;
  return (n / ((n - 1) * (n - 2))) * xs.reduce((a, x) => a + ((x - m) / s) ** 3, 0);
}

export function pearson(x: number[], y: number[]) {
  const n = Math.min(x.length, y.length);
  const xs = x.slice(0, n);
  const ys = y.slice(0, n);
  const mx = mean(xs);
  const my = mean(ys);
  let num = 0;
  let dx = 0;
  let dy = 0;
  for (let i = 0; i < n; i++) {
    const a = xs[i] - mx;
    const b = ys[i] - my;
    num += a * b;
    dx += a * a;
    dy += b * b;
  }
  return num / Math.sqrt(dx * dy);
}

export function cronbach(items: number[][]) {
  const k = items.length;
  if (k < 2) return NaN;
  const n = Math.min(...items.map((r) => r.length));
  const cols = items.map((r) => r.slice(0, n));
  const itemVars = cols.map((c) => variance(c));
  const totals = Array.from({ length: n }, (_, i) => cols.reduce((a, c) => a + c[i], 0));
  const totalVar = variance(totals);
  return (k / (k - 1)) * (1 - itemVars.reduce((a, b) => a + b, 0) / totalVar);
}

export function sampleSizeMean(z: number, sd: number, e: number) {
  return Math.ceil(((z * sd) / e) ** 2);
}

export function tStat(xs: number[], mu0: number) {
  const m = mean(xs);
  const s = stdev(xs);
  const t = (m - mu0) / (s / Math.sqrt(xs.length));
  return { m, s, t, df: xs.length - 1 };
}

export function linreg(x: number[], y: number[]) {
  const n = Math.min(x.length, y.length);
  const xs = x.slice(0, n);
  const ys = y.slice(0, n);
  const mx = mean(xs);
  const my = mean(ys);
  let sxx = 0;
  let sxy = 0;
  for (let i = 0; i < n; i++) {
    sxx += (xs[i] - mx) ** 2;
    sxy += (xs[i] - mx) * (ys[i] - my);
  }
  const slope = sxy / sxx;
  const intercept = my - slope * mx;
  const r = pearson(xs, ys);
  return { slope, intercept, r2: r * r, r };
}

export function fmt(n: number, d = 3) {
  if (!Number.isFinite(n)) return "—";
  return n.toLocaleString("fa-IR", { maximumFractionDigits: d, minimumFractionDigits: 0 });
}

export function anova(groups: number[][]) {
  const cleaned = groups.filter((g) => g.length >= 2);
  const k = cleaned.length;
  if (k < 2) return null;
  const n = cleaned.reduce((a, g) => a + g.length, 0);
  const gm = mean(cleaned.flat());
  const ssb = cleaned.reduce((a, g) => a + g.length * (mean(g) - gm) ** 2, 0);
  const ssw = cleaned.reduce((a, g) => a + g.reduce((b, x) => b + (x - mean(g)) ** 2, 0), 0);
  const dfb = k - 1;
  const dfw = n - k;
  if (dfw <= 0) return null;
  const msb = ssb / dfb;
  const msw = ssw / dfw;
  return { k, n, dfb, dfw, msb, msw, F: msb / msw };
}

export function chiSquare(table: number[][]) {
  if (table.length < 2 || table[0].length < 2) return null;
  const rows = table.length;
  const cols = table[0].length;
  if (table.some((r) => r.length !== cols || r.some((x) => x < 0))) return null;
  const rowSum = table.map((r) => r.reduce((a, b) => a + b, 0));
  const colSum = table[0].map((_, j) => table.reduce((a, r) => a + r[j], 0));
  const total = rowSum.reduce((a, b) => a + b, 0);
  if (total <= 0) return null;
  let x2 = 0;
  const expected: number[][] = [];
  for (let i = 0; i < rows; i++) {
    expected[i] = [];
    for (let j = 0; j < cols; j++) {
      const e = (rowSum[i] * colSum[j]) / total;
      expected[i][j] = e;
      if (e <= 0) return null;
      x2 += (table[i][j] - e) ** 2 / e;
    }
  }
  return { x2, df: (rows - 1) * (cols - 1), expected, total };
}

export function cohensD(xs: number[], mu0: number) {
  const s = stdev(xs);
  if (!s) return NaN;
  return (mean(xs) - mu0) / s;
}

export function powerNTwoMean(d: number, zA = 1.96, zB = 0.84) {
  if (d === 0) return NaN;
  return Math.ceil(2 * ((zA + zB) / d) ** 2);
}

export function histogram(xs: number[], bins = 6) {
  if (!xs.length) return [];
  const lo = Math.min(...xs);
  const hi = Math.max(...xs);
  if (lo === hi) return [{ label: fmt(lo, 1), n: xs.length }];
  const width = (hi - lo) / bins;
  return Array.from({ length: bins }, (_, i) => {
    const a = lo + i * width;
    const b = i === bins - 1 ? hi + 1e-9 : lo + (i + 1) * width;
    return {
      label: `${fmt(a, 1)}–${fmt(b, 1)}`,
      n: xs.filter((x) => x >= a && x < b).length,
    };
  });
}

export function pearsonInterpret(r: number) {
  const a = Math.abs(r);
  if (a < 0.1) return "تقریباً بدون رابطه";
  if (a < 0.3) return "رابطه ضعیف";
  if (a < 0.5) return "رابطه متوسط";
  if (a < 0.7) return "رابطه قوی";
  return "رابطه خیلی قوی";
}
