import { useMemo, useState } from "react";
import {
  anova,
  chiSquare,
  cohensD,
  cronbach,
  fmt,
  histogram,
  linreg,
  mean,
  median,
  parseSeries,
  parseTable,
  pearson,
  pearsonInterpret,
  powerNTwoMean,
  quantile,
  sampleSizeMean,
  skewness,
  stdev,
  tStat,
} from "@/lib/stats";

function Field({
  label,
  value,
  onChange,
  hint,
}: {
  label: string;
  value: string;
  onChange: (v: string) => void;
  hint?: string;
}) {
  return (
    <div className="field">
      <label>{label}</label>
      <textarea value={value} onChange={(e) => onChange(e.target.value)} rows={4} />
      {hint ? <span className="text-xs text-muted">{hint}</span> : null}
    </div>
  );
}

function StatTable({ rows }: { rows: [string, string][] }) {
  return (
    <div className="stat-table">
      {rows.map(([k, v]) => (
        <div key={k} className="stat-row">
          <span>{k}</span>
          <strong dir="ltr">{v}</strong>
        </div>
      ))}
    </div>
  );
}

function CopyBtn({ text }: { text: string }) {
  return (
    <button
      type="button"
      className="btn-tz btn-light-tz"
      onClick={() => void navigator.clipboard.writeText(text).catch(() => {})}
    >
      کپی نتیجه
    </button>
  );
}

function Bars({ items }: { items: { label: string; n: number }[] }) {
  const max = Math.max(1, ...items.map((i) => i.n));
  return (
    <div className="mini-bars" aria-hidden>
      {items.map((i) => (
        <div key={i.label} className="mini-bar">
          <span style={{ height: `${(i.n / max) * 100}%` }} />
          <small>{i.n}</small>
        </div>
      ))}
    </div>
  );
}

export function DescriptiveCalc() {
  const [raw, setRaw] = useState("12, 15, 14, 18, 11, 16, 13, 17");
  const xs = useMemo(() => parseSeries(raw), [raw]);
  const ready = xs.length >= 2;
  const rows: [string, string][] = ready
    ? [
        ["تعداد", fmt(xs.length, 0)],
        ["میانگین", fmt(mean(xs))],
        ["میانه", fmt(median(xs))],
        ["انحراف معیار نمونه", fmt(stdev(xs))],
        ["چارک اول", fmt(quantile(xs, 0.25))],
        ["چارک سوم", fmt(quantile(xs, 0.75))],
        ["چولگی", fmt(skewness(xs))],
        ["کمینه / بیشینه", `${fmt(Math.min(...xs))} / ${fmt(Math.max(...xs))}`],
      ]
    : [];
  return (
    <div className="tool-card">
      <Field label="داده‌ها" value={raw} onChange={setRaw} hint="اعداد را با کاما یا فاصله جدا کنید." />
      {ready ? (
        <>
          <Bars items={histogram(xs)} />
          <StatTable rows={rows} />
          <CopyBtn text={rows.map((r) => r.join(": ")).join("\n")} />
        </>
      ) : (
        <p className="mt-3 text-sm text-muted">حداقل دو عدد وارد کنید.</p>
      )}
    </div>
  );
}

export function SampleSizeCalc() {
  const [sd, setSd] = useState("10");
  const [e, setE] = useState("2");
  const [cl, setCl] = useState("1.96");
  const n = sampleSizeMean(Number(cl), Number(sd), Number(e));
  const rows: [string, string][] = [["حجم نمونه پیشنهادی", Number.isFinite(n) ? fmt(n, 0) : "—"]];
  return (
    <div className="tool-card grid gap-3 sm:grid-cols-3">
      <div className="field">
        <label>انحراف معیار (σ)</label>
        <input value={sd} onChange={(e) => setSd(e.target.value)} dir="ltr" />
      </div>
      <div className="field">
        <label>خطای قابل قبول (E)</label>
        <input value={e} onChange={(e) => setE(e.target.value)} dir="ltr" />
      </div>
      <div className="field">
        <label>Z (۱.۹۶ ≈ ۹۵٪)</label>
        <input value={cl} onChange={(e) => setCl(e.target.value)} dir="ltr" />
      </div>
      <div className="sm:col-span-3">
        <StatTable rows={rows} />
        <p className="mt-2 text-xs text-muted">این برآورد برای میانگین یک جامعه است. ریزش نمونه را جداگانه اضافه کنید.</p>
      </div>
    </div>
  );
}

export function CronbachCalc() {
  const [raw, setRaw] = useState("4 5 4 3 5\n5 5 4 4 5\n3 4 3 3 4");
  const items = parseTable(raw);
  const a = items.length >= 2 ? cronbach(items) : NaN;
  return (
    <div className="tool-card">
      <Field label="گویه‌ها (هر سطر یک گویه)" value={raw} onChange={setRaw} hint="هر خط پاسخ‌های یک گویه برای افراد نمونه است." />
      <StatTable rows={[["آلفای کرونباخ", fmt(a, 3)]]} />
    </div>
  );
}

export function PearsonCalc() {
  const [x, setX] = useState("10 12 14 16 18");
  const [y, setY] = useState("22 24 25 29 31");
  const xs = parseSeries(x);
  const ys = parseSeries(y);
  const r = xs.length >= 2 && ys.length >= 2 ? pearson(xs, ys) : NaN;
  return (
    <div className="tool-card grid gap-3">
      <Field label="متغیر X" value={x} onChange={setX} />
      <Field label="متغیر Y" value={y} onChange={setY} />
      <StatTable
        rows={[
          ["r پیرسون", fmt(r, 3)],
          ["تفسیر", Number.isFinite(r) ? pearsonInterpret(r) : "—"],
        ]}
      />
    </div>
  );
}

export function TTestCalc() {
  const [raw, setRaw] = useState("18 20 19 22 21 17 20");
  const [mu, setMu] = useState("18");
  const xs = parseSeries(raw);
  const res = xs.length >= 2 ? tStat(xs, Number(mu)) : null;
  const d = xs.length >= 2 ? cohensD(xs, Number(mu)) : NaN;
  return (
    <div className="tool-card grid gap-3">
      <Field label="نمونه" value={raw} onChange={setRaw} />
      <div className="field">
        <label>مقدار مرجع (μ۰)</label>
        <input value={mu} onChange={(e) => setMu(e.target.value)} dir="ltr" />
      </div>
      {res ? (
        <StatTable
          rows={[
            ["میانگین", fmt(res.m)],
            ["انحراف معیار", fmt(res.s)],
            ["t", fmt(res.t)],
            ["df", fmt(res.df, 0)],
            ["d کوهن", fmt(d)],
          ]}
        />
      ) : null}
    </div>
  );
}

export function RegressionCalc() {
  const [x, setX] = useState("1 2 3 4 5 6");
  const [y, setY] = useState("2.1 4.0 5.8 8.3 9.9 12.2");
  const xs = parseSeries(x);
  const ys = parseSeries(y);
  const r = xs.length >= 2 ? linreg(xs, ys) : null;
  return (
    <div className="tool-card grid gap-3">
      <Field label="X" value={x} onChange={setX} />
      <Field label="Y" value={y} onChange={setY} />
      {r ? (
        <StatTable
          rows={[
            ["شیب", fmt(r.slope)],
            ["عرض از مبدأ", fmt(r.intercept)],
            ["R²", fmt(r.r2)],
            ["r", fmt(r.r)],
          ]}
        />
      ) : null}
    </div>
  );
}

export function AnovaCalc() {
  const [raw, setRaw] = useState("12 14 13 15\n18 19 17 21\n11 10 12 9");
  const groups = parseTable(raw);
  const res = anova(groups);
  return (
    <div className="tool-card">
      <Field label="گروه‌ها (هر سطر یک گروه)" value={raw} onChange={setRaw} />
      {res ? (
        <StatTable
          rows={[
            ["تعداد گروه", fmt(res.k, 0)],
            ["N", fmt(res.n, 0)],
            ["F", fmt(res.F)],
            ["df بین / درون", `${res.dfb} / ${res.dfw}`],
            ["MSB", fmt(res.msb)],
            ["MSW", fmt(res.msw)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل دو گروه با دو مشاهده لازم است.</p>
      )}
    </div>
  );
}

export function ChiSquareCalc() {
  const [raw, setRaw] = useState("20 15\n12 28");
  const table = parseTable(raw);
  const res = chiSquare(table);
  return (
    <div className="tool-card">
      <Field label="جدول فراوانی (هر سطر یک ردیف)" value={raw} onChange={setRaw} />
      {res ? (
        <StatTable
          rows={[
            ["χ²", fmt(res.x2)],
            ["df", fmt(res.df, 0)],
            ["N", fmt(res.total, 0)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل جدول ۲×۲ با اعداد نامنفی وارد کنید.</p>
      )}
    </div>
  );
}

export function PowerCalc() {
  const [d, setD] = useState("0.5");
  const n = powerNTwoMean(Number(d));
  return (
    <div className="tool-card grid gap-3">
      <div className="field">
        <label>اندازه اثر d (کوهن)</label>
        <input value={d} onChange={(e) => setD(e.target.value)} dir="ltr" />
      </div>
      <StatTable rows={[["n در هر گروه (α≈۰.۰۵، توان≈۰.۸۰)", Number.isFinite(n) ? fmt(n, 0) : "—"]]} />
    </div>
  );
}

export const CALC_MAP = {
  "descriptive-statistics": DescriptiveCalc,
  "sample-size": SampleSizeCalc,
  "cronbachs-alpha": CronbachCalc,
  "pearson-correlation": PearsonCalc,
  "t-test": TTestCalc,
  regression: RegressionCalc,
  anova: AnovaCalc,
  "chi-square": ChiSquareCalc,
  "power-analysis": PowerCalc,
} as const;
