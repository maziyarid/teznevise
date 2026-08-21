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

export function kurtosis(xs: number[]) {
  const n = xs.length;
  if (n < 4) return NaN;
  const m = mean(xs);
  const s = stdev(xs);
  if (!s) return NaN;
  const m4 = xs.reduce((a, x) => a + ((x - m) / s) ** 4, 0) / n;
  return m4 - 3;
}

export function mode(xs: number[]) {
  const freq = new Map<number, number>();
  for (const x of xs) freq.set(x, (freq.get(x) || 0) + 1);
  let best = xs[0];
  let n = 0;
  for (const [v, c] of freq) {
    if (c > n) {
      best = v;
      n = c;
    }
  }
  return { value: best, count: n };
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

export function cochranN(z: number, p: number, e: number, N?: number) {
  const q = 1 - p;
  const n0 = (z * z * p * q) / (e * e);
  if (!N || N <= 0) return Math.ceil(n0);
  return Math.ceil(n0 / (1 + (n0 - 1) / N));
}

/** Krejcie & Morgan (1970), 95% confidence, 5% margin. */
export function morganN(N: number) {
  if (!N || N <= 0) return 0;
  const chi2 = 3.841;
  const P = 0.5;
  const d = 0.05;
  return Math.ceil((chi2 * N * P * (1 - P)) / (d * d * (N - 1) + chi2 * P * (1 - P)));
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
  if (!Number.isFinite(d) || d === 0) return NaN;
  return Math.ceil(2 * ((zA + zB) / d) ** 2);
}

/** Two-sample t-test power (large-sample approximation), equal n per group. */
export function powerFromN(d: number, nPerGroup: number, zA = 1.96) {
  if (!Number.isFinite(d) || !Number.isFinite(nPerGroup) || nPerGroup < 2) return NaN;
  const zB = Math.abs(d) * Math.sqrt(nPerGroup / 2) - zA;
  return normCdf(zB);
}

export function erf(x: number) {
  const sign = x < 0 ? -1 : 1;
  const ax = Math.abs(x);
  const t = 1 / (1 + 0.3275911 * ax);
  const y =
    1 -
    (((((1.061405429 * t - 1.453152027) * t + 1.421413741) * t - 0.284496736) * t + 0.254829592) *
      t *
      Math.exp(-ax * ax));
  return sign * y;
}

export function normCdf(z: number) {
  return 0.5 * (1 + erf(z / Math.SQRT2));
}

/** Inverse standard normal (Acklam). */
export function normInv(p: number) {
  if (p <= 0) return -Infinity;
  if (p >= 1) return Infinity;
  const a = [
    -3.969683028665376e1, 2.209460984245205e2, -2.759285104469687e2, 1.383577459590407e2,
    -3.066479806614716e1, 2.506628277459239,
  ];
  const b = [
    -5.447609879822406e1, 1.615858368580409e2, -1.556989798598866e2, 6.680131188771972e1,
    -1.328068155288572e1,
  ];
  const c = [
    -7.784894002430293e-3, -3.223964580411365e-1, -2.400758277161838, -2.549732539343734,
    4.374664141464968, 2.938163982698783,
  ];
  const d = [7.784695709041462e-3, 3.224671290700398e-1, 2.445134137142996, 3.754408661907416];
  const plow = 0.02425;
  const phigh = 1 - plow;
  if (p < plow) {
    const q = Math.sqrt(-2 * Math.log(p));
    return (
      (((((c[0] * q + c[1]) * q + c[2]) * q + c[3]) * q + c[4]) * q + c[5]) /
      ((((d[0] * q + d[1]) * q + d[2]) * q + d[3]) * q + 1)
    );
  }
  if (p > phigh) {
    const q = Math.sqrt(-2 * Math.log(1 - p));
    return -(
      (((((c[0] * q + c[1]) * q + c[2]) * q + c[3]) * q + c[4]) * q + c[5]) /
      ((((d[0] * q + d[1]) * q + d[2]) * q + d[3]) * q + 1)
    );
  }
  const q = p - 0.5;
  const r = q * q;
  return (
    ((((((a[0] * r + a[1]) * r + a[2]) * r + a[3]) * r + a[4]) * r + a[5]) * q) /
    (((((b[0] * r + b[1]) * r + b[2]) * r + b[3]) * r + b[4]) * r + 1)
  );
}

export function zForAlpha(alpha: number, tails: "one" | "two") {
  const a = Math.min(Math.max(alpha, 1e-6), 0.5);
  return tails === "two" ? normInv(1 - a / 2) : normInv(1 - a);
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

/** Average ranks, 1-based; ties share the mean rank. */
export function ranksOf(xs: number[]): number[] {
  const idx = xs.map((v, i) => ({ v, i })).sort((a, b) => a.v - b.v);
  const out = Array(xs.length).fill(0);
  let i = 0;
  while (i < idx.length) {
    let j = i;
    while (j + 1 < idx.length && idx[j + 1].v === idx[i].v) j++;
    const avg = (i + 1 + j + 1) / 2;
    for (let k = i; k <= j; k++) out[idx[k].i] = avg;
    i = j + 1;
  }
  return out;
}

export function spearman(x: number[], y: number[]) {
  const n = Math.min(x.length, y.length);
  if (n < 2) return NaN;
  return pearson(ranksOf(x.slice(0, n)), ranksOf(y.slice(0, n)));
}

export function mannWhitney(a: number[], b: number[]) {
  if (a.length < 1 || b.length < 1) return null;
  const labels = [...a.map((v) => ({ v, g: 1 })), ...b.map((v) => ({ v, g: 2 }))];
  const r = ranksOf(labels.map((x) => x.v));
  const r1 = r.reduce((s, ri, i) => s + (labels[i].g === 1 ? ri : 0), 0);
  const n1 = a.length;
  const n2 = b.length;
  const u1 = n1 * n2 + (n1 * (n1 + 1)) / 2 - r1;
  const u2 = n1 * n2 - u1;
  const u = Math.min(u1, u2);
  const mu = (n1 * n2) / 2;
  const sigma = Math.sqrt((n1 * n2 * (n1 + n2 + 1)) / 12);
  const z = sigma ? (u - mu) / sigma : NaN;
  return { n1, n2, r1, u1, u2, u, z };
}

export function wilcoxon(before: number[], after: number[]) {
  const n = Math.min(before.length, after.length);
  const diffs: number[] = [];
  for (let i = 0; i < n; i++) {
    const d = after[i] - before[i];
    if (d !== 0) diffs.push(d);
  }
  if (diffs.length < 1) return null;
  const abs = diffs.map((d) => Math.abs(d));
  const r = ranksOf(abs);
  let tPos = 0;
  let tNeg = 0;
  diffs.forEach((d, i) => {
    if (d > 0) tPos += r[i];
    else tNeg += r[i];
  });
  const t = Math.min(tPos, tNeg);
  return { n: diffs.length, tPos, tNeg, t };
}

export function kruskalWallis(groups: number[][]) {
  const cleaned = groups.filter((g) => g.length >= 1);
  if (cleaned.length < 2) return null;
  const labels = cleaned.flatMap((g, gi) => g.map((v) => ({ v, g: gi })));
  const r = ranksOf(labels.map((x) => x.v));
  const n = labels.length;
  const k = cleaned.length;
  let sum = 0;
  for (let gi = 0; gi < k; gi++) {
    const idx = labels.map((x, i) => (x.g === gi ? i : -1)).filter((i) => i >= 0);
    const ri = idx.reduce((s, i) => s + r[i], 0);
    sum += (ri * ri) / idx.length;
  }
  const h = (12 / (n * (n + 1))) * sum - 3 * (n + 1);
  return { k, n, h, df: k - 1 };
}

export function cohensKappa(a: number, b: number, c: number, d: number) {
  const n = a + b + c + d;
  if (n <= 0) return null;
  const po = (a + d) / n;
  const pe = (((a + b) * (a + c)) / n + ((c + d) * (b + d)) / n) / n;
  const k = 1 - pe === 0 ? NaN : (po - pe) / (1 - pe);
  return { n, po, pe, k };
}

export function kr20(items: number[][]) {
  const k = items.length;
  if (k < 2) return NaN;
  const n = Math.min(...items.map((r) => r.length));
  const cols = items.map((r) => r.slice(0, n));
  const pq = cols.map((col) => {
    const p = mean(col);
    return p * (1 - p);
  });
  const totals = Array.from({ length: n }, (_, i) => cols.reduce((s, c) => s + c[i], 0));
  const vt = variance(totals);
  if (!vt) return NaN;
  return (k / (k - 1)) * (1 - pq.reduce((s, x) => s + x, 0) / vt);
}

export function kr21(k: number, m: number, vt: number) {
  if (k < 2 || vt <= 0) return NaN;
  return (k / (k - 1)) * (1 - (m * (1 - m / k)) / vt);
}

export function cvr(ne: number, n: number) {
  if (n <= 0) return NaN;
  return (ne - n / 2) / (n / 2);
}

export function itemCvi(nAgree: number, n: number) {
  if (n <= 0) return NaN;
  return nAgree / n;
}

export function goodnessOfFit(obs: number[], exp: number[]) {
  const n = Math.min(obs.length, exp.length);
  if (n < 2) return null;
  let x2 = 0;
  for (let i = 0; i < n; i++) {
    if (exp[i] <= 0) return null;
    x2 += (obs[i] - exp[i]) ** 2 / exp[i];
  }
  return { x2, df: n - 1 };
}

/** ICC(1,1) one-way random; each row is a subject, columns are raters. */
export function icc11(rows: number[][]) {
  const k = rows[0]?.length || 0;
  const n = rows.filter((r) => r.length === k).length;
  if (n < 2 || k < 2) return null;
  const data = rows.slice(0, n);
  const gm = mean(data.flat());
  const ssb = data.reduce((s, r) => s + k * (mean(r) - gm) ** 2, 0);
  const ssw = data.reduce((s, r) => s + r.reduce((b, x) => b + (x - mean(r)) ** 2, 0), 0);
  const msb = ssb / (n - 1);
  const msw = ssw / (n * (k - 1));
  const icc = (msb - msw) / (msb + (k - 1) * msw);
  return { n, k, msb, msw, icc };
}

export function independentT(a: number[], b: number[]) {
  if (a.length < 2 || b.length < 2) return null;
  const ma = mean(a);
  const mb = mean(b);
  const sa = variance(a);
  const sb = variance(b);
  const df = a.length + b.length - 2;
  const t = (ma - mb) / Math.sqrt(sa / a.length + sb / b.length);
  return { ma, mb, t, df, d: (ma - mb) / Math.sqrt(((a.length - 1) * sa + (b.length - 1) * sb) / df) };
}

export function pairedT(a: number[], b: number[]) {
  const n = Math.min(a.length, b.length);
  if (n < 2) return null;
  const d = Array.from({ length: n }, (_, i) => b[i] - a[i]);
  return { ...tStat(d, 0), n, meanDiff: mean(d) };
}
