import { useMemo, useState } from "react";
import { Link } from "@tanstack/react-router";
import {
  anova,
  chiSquare,
  cochranN,
  cohensD,
  cohensKappa,
  cronbach,
  cvr,
  fmt,
  goodnessOfFit,
  histogram,
  icc11,
  independentT,
  itemCvi,
  kr20,
  kr21,
  kruskalWallis,
  kurtosis,
  linreg,
  mannWhitney,
  mean,
  median,
  mode,
  pairedT,
  parseSeries,
  parseTable,
  pearson,
  pearsonInterpret,
  powerNTwoMean,
  quantile,
  sampleSizeMean,
  skewness,
  spearman,
  stdev,
  tStat,
  variance,
  wilcoxon,
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
  const md = ready ? mode(xs) : null;
  const rows: [string, string][] = ready
    ? [
        ["تعداد", fmt(xs.length, 0)],
        ["میانگین", fmt(mean(xs))],
        ["میانه", fmt(median(xs))],
        ["نما", md ? `${fmt(md.value)} (تکرار ${fmt(md.count, 0)})` : "—"],
        ["انحراف معیار نمونه", fmt(stdev(xs))],
        ["واریانس", fmt(variance(xs))],
        ["چارک اول", fmt(quantile(xs, 0.25))],
        ["چارک سوم", fmt(quantile(xs, 0.75))],
        ["دامنه بین‌چارکی", fmt(quantile(xs, 0.75) - quantile(xs, 0.25))],
        ["چولگی", fmt(skewness(xs))],
        ["کشیدگی", fmt(kurtosis(xs))],
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
  const [kind, setKind] = useState<"mean" | "cochran">("cochran");
  const [sd, setSd] = useState("10");
  const [e, setE] = useState("0.05");
  const [cl, setCl] = useState("1.96");
  const [p, setP] = useState("0.5");
  const [N, setN] = useState("");
  const nMean = sampleSizeMean(Number(cl), Number(sd), Number(e));
  const nCochran = cochranN(Number(cl), Number(p), Number(e), N ? Number(N) : undefined);
  return (
    <div className="tool-card grid gap-3">
      <div className="flex flex-wrap gap-2">
        <button type="button" className={`btn-tz ${kind === "cochran" ? "btn-primary-tz" : "btn-light-tz"}`} onClick={() => setKind("cochran")}>
          کوکران
        </button>
        <button type="button" className={`btn-tz ${kind === "mean" ? "btn-primary-tz" : "btn-light-tz"}`} onClick={() => setKind("mean")}>
          میانگین جامعه
        </button>
      </div>
      {kind === "cochran" ? (
        <div className="grid gap-3 sm:grid-cols-2">
          <div className="field">
            <label>Z (۱.۹۶ ≈ ۹۵٪)</label>
            <input value={cl} onChange={(e) => setCl(e.target.value)} dir="ltr" />
          </div>
          <div className="field">
            <label>نسبت p (معمولاً ۰.۵)</label>
            <input value={p} onChange={(e) => setP(e.target.value)} dir="ltr" />
          </div>
          <div className="field">
            <label>حاشیه خطا e</label>
            <input value={e} onChange={(e) => setE(e.target.value)} dir="ltr" />
          </div>
          <div className="field">
            <label>حجم جامعه N (اختیاری)</label>
            <input value={N} onChange={(e) => setN(e.target.value)} dir="ltr" placeholder="نامحدود" />
          </div>
          <div className="sm:col-span-2">
            <StatTable rows={[["حجم نمونه", Number.isFinite(nCochran) ? fmt(nCochran, 0) : "—"]]} />
          </div>
        </div>
      ) : (
        <div className="grid gap-3 sm:grid-cols-3">
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
            <StatTable rows={[["حجم نمونه پیشنهادی", Number.isFinite(nMean) ? fmt(nMean, 0) : "—"]]} />
          </div>
        </div>
      )}
      <p className="text-xs text-muted">ریزش نمونه را جداگانه اضافه کنید. این برآورد جایگزین نظر کمیته روش نیست.</p>
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
  const [kind, setKind] = useState<"one" | "ind" | "pair">("one");
  const [raw, setRaw] = useState("18 20 19 22 21 17 20");
  const [rawB, setRawB] = useState("16 17 18 15 19 16 18");
  const [mu, setMu] = useState("18");
  const xs = parseSeries(raw);
  const ys = parseSeries(rawB);
  const one = xs.length >= 2 ? tStat(xs, Number(mu)) : null;
  const d = xs.length >= 2 ? cohensD(xs, Number(mu)) : NaN;
  const ind = independentT(xs, ys);
  const pair = pairedT(xs, ys);
  return (
    <div className="tool-card grid gap-3">
      <div className="flex flex-wrap gap-2">
        {([
          ["one", "تک‌نمونه‌ای"],
          ["ind", "مستقل"],
          ["pair", "زوجی"],
        ] as const).map(([k, label]) => (
          <button key={k} type="button" className={`btn-tz ${kind === k ? "btn-primary-tz" : "btn-light-tz"}`} onClick={() => setKind(k)}>
            {label}
          </button>
        ))}
      </div>
      <Field label={kind === "ind" ? "گروه اول" : kind === "pair" ? "پیش‌آزمون" : "نمونه"} value={raw} onChange={setRaw} />
      {kind !== "one" ? (
        <Field label={kind === "ind" ? "گروه دوم" : "پس‌آزمون"} value={rawB} onChange={setRawB} />
      ) : (
        <div className="field">
          <label>مقدار مرجع (μ۰)</label>
          <input value={mu} onChange={(e) => setMu(e.target.value)} dir="ltr" />
        </div>
      )}
      {kind === "one" && one ? (
        <StatTable
          rows={[
            ["میانگین", fmt(one.m)],
            ["انحراف معیار", fmt(one.s)],
            ["t", fmt(one.t)],
            ["df", fmt(one.df, 0)],
            ["d کوهن", fmt(d)],
          ]}
        />
      ) : null}
      {kind === "ind" && ind ? (
        <StatTable
          rows={[
            ["میانگین گروه ۱", fmt(ind.ma)],
            ["میانگین گروه ۲", fmt(ind.mb)],
            ["t", fmt(ind.t)],
            ["df", fmt(ind.df, 0)],
            ["d کوهن", fmt(ind.d)],
          ]}
        />
      ) : null}
      {kind === "pair" && pair ? (
        <StatTable
          rows={[
            ["میانگین تفاوت", fmt(pair.meanDiff)],
            ["t", fmt(pair.t)],
            ["df", fmt(pair.df, 0)],
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

export function SpearmanCalc() {
  const [x, setX] = useState("10 12 14 16 18");
  const [y, setY] = useState("22 24 25 29 31");
  const xs = parseSeries(x);
  const ys = parseSeries(y);
  const r = xs.length >= 2 && ys.length >= 2 ? spearman(xs, ys) : NaN;
  return (
    <div className="tool-card grid gap-3">
      <Field label="متغیر X" value={x} onChange={setX} />
      <Field label="متغیر Y" value={y} onChange={setY} />
      <StatTable
        rows={[
          ["rₛ اسپیرمن", fmt(r, 3)],
          ["تفسیر", Number.isFinite(r) ? pearsonInterpret(r) : "—"],
        ]}
      />
    </div>
  );
}

export function ContentValidityCalc() {
  const [n, setN] = useState("8");
  const [ne, setNe] = useState("7");
  const [agree, setAgree] = useState("7");
  const N = Number(n);
  const ratio = cvr(Number(ne), N);
  const iCvi = itemCvi(Number(agree), N);
  return (
    <div className="tool-card grid gap-3 sm:grid-cols-3">
      <div className="field">
        <label>تعداد متخصصان (n)</label>
        <input value={n} onChange={(e) => setN(e.target.value)} dir="ltr" />
      </div>
      <div className="field">
        <label>تعداد «ضروری» (Ne)</label>
        <input value={ne} onChange={(e) => setNe(e.target.value)} dir="ltr" />
      </div>
      <div className="field">
        <label>تعداد موافق مرتبط بودن</label>
        <input value={agree} onChange={(e) => setAgree(e.target.value)} dir="ltr" />
      </div>
      <div className="sm:col-span-3">
        <StatTable
          rows={[
            ["CVR لاوشه", fmt(ratio, 3)],
            ["I-CVI", fmt(iCvi, 3)],
            ["تفسیر I-CVI", iCvi >= 0.79 ? "گویه مناسب است" : iCvi >= 0.7 ? "نیاز به بازنگری" : "حذف گویه"],
          ]}
        />
      </div>
    </div>
  );
}

export function Kr20Calc() {
  const [raw, setRaw] = useState("1 1 0 1 1\n1 0 0 1 1\n1 1 1 1 0");
  const [k, setK] = useState("20");
  const [m, setM] = useState("14");
  const [vt, setVt] = useState("12");
  const items = parseTable(raw);
  const a = items.length >= 2 ? kr20(items) : NaN;
  const b = kr21(Number(k), Number(m), Number(vt));
  return (
    <div className="tool-card grid gap-3">
      <Field label="ماتریس ۰/۱ (هر سطر یک سؤال)" value={raw} onChange={setRaw} hint="برای KR-20: هر خط پاسخ‌های یک سؤال برای افراد نمونه است." />
      <StatTable rows={[["KR-20", fmt(a, 3)]]} />
      <p className="text-sm font-bold">KR-21 از آمار خلاصه</p>
      <div className="grid gap-3 sm:grid-cols-3">
        <div className="field">
          <label>تعداد سؤال k</label>
          <input value={k} onChange={(e) => setK(e.target.value)} dir="ltr" />
        </div>
        <div className="field">
          <label>میانگین نمرات</label>
          <input value={m} onChange={(e) => setM(e.target.value)} dir="ltr" />
        </div>
        <div className="field">
          <label>واریانس نمرات کل</label>
          <input value={vt} onChange={(e) => setVt(e.target.value)} dir="ltr" />
        </div>
      </div>
      <StatTable rows={[["KR-21", fmt(b, 3)]]} />
    </div>
  );
}

export function KappaCalc() {
  const [a, setA] = useState("40");
  const [b, setB] = useState("10");
  const [c, setC] = useState("8");
  const [d, setD] = useState("42");
  const res = cohensKappa(Number(a), Number(b), Number(c), Number(d));
  return (
    <div className="tool-card grid gap-3">
      <p className="text-sm text-muted">جدول ۲×۲ توافق دو ارزیاب: a توافق مثبت، d توافق منفی، b و c اختلاف.</p>
      <div className="grid gap-3 sm:grid-cols-2">
        <div className="field">
          <label>a (هر دو مثبت)</label>
          <input value={a} onChange={(e) => setA(e.target.value)} dir="ltr" />
        </div>
        <div className="field">
          <label>b (فقط ارزیاب ۱ مثبت)</label>
          <input value={b} onChange={(e) => setB(e.target.value)} dir="ltr" />
        </div>
        <div className="field">
          <label>c (فقط ارزیاب ۲ مثبت)</label>
          <input value={c} onChange={(e) => setC(e.target.value)} dir="ltr" />
        </div>
        <div className="field">
          <label>d (هر دو منفی)</label>
          <input value={d} onChange={(e) => setD(e.target.value)} dir="ltr" />
        </div>
      </div>
      {res ? (
        <StatTable
          rows={[
            ["N", fmt(res.n, 0)],
            ["Pₒ توافق مشاهده‌شده", fmt(res.po, 3)],
            ["Pₑ توافق تصادفی", fmt(res.pe, 3)],
            ["κ کاپا", fmt(res.k, 3)],
          ]}
        />
      ) : null}
    </div>
  );
}

export function IccCalc() {
  const [raw, setRaw] = useState("4 5 4\n5 5 4\n3 4 3\n4 4 5");
  const rows = parseTable(raw);
  const res = icc11(rows);
  return (
    <div className="tool-card">
      <Field label="هر سطر یک آزمودنی، ستون‌ها ارزیاب‌ها" value={raw} onChange={setRaw} />
      {res ? (
        <StatTable
          rows={[
            ["تعداد آزمودنی", fmt(res.n, 0)],
            ["تعداد ارزیاب", fmt(res.k, 0)],
            ["MSB", fmt(res.msb)],
            ["MSW", fmt(res.msw)],
            ["ICC(1,1)", fmt(res.icc, 3)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل دو آزمودنی و دو ارزیاب لازم است.</p>
      )}
    </div>
  );
}

export function MannWhitneyCalc() {
  const [a, setA] = useState("12 15 14 18 11");
  const [b, setB] = useState("20 22 19 25 21");
  const res = mannWhitney(parseSeries(a), parseSeries(b));
  return (
    <div className="tool-card grid gap-3">
      <Field label="گروه ۱" value={a} onChange={setA} />
      <Field label="گروه ۲" value={b} onChange={setB} />
      {res ? (
        <StatTable
          rows={[
            ["n₁ / n₂", `${fmt(res.n1, 0)} / ${fmt(res.n2, 0)}`],
            ["U₁", fmt(res.u1)],
            ["U₂", fmt(res.u2)],
            ["U", fmt(res.u)],
            ["z تقریبی", fmt(res.z)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">هر گروه حداقل یک مشاهده لازم دارد.</p>
      )}
    </div>
  );
}

export function WilcoxonCalc() {
  const [a, setA] = useState("12 15 14 18 11 16");
  const [b, setB] = useState("14 16 13 20 12 18");
  const res = wilcoxon(parseSeries(a), parseSeries(b));
  return (
    <div className="tool-card grid gap-3">
      <Field label="پیش‌آزمون" value={a} onChange={setA} />
      <Field label="پس‌آزمون" value={b} onChange={setB} />
      {res ? (
        <StatTable
          rows={[
            ["جفت‌های غیرصفر", fmt(res.n, 0)],
            ["T₊", fmt(res.tPos)],
            ["T₋", fmt(res.tNeg)],
            ["T", fmt(res.t)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل یک تفاوت غیرصفر لازم است.</p>
      )}
    </div>
  );
}

export function KruskalCalc() {
  const [raw, setRaw] = useState("12 14 13 15\n18 19 17 21\n11 10 12 9");
  const res = kruskalWallis(parseTable(raw));
  return (
    <div className="tool-card">
      <Field label="گروه‌ها (هر سطر یک گروه)" value={raw} onChange={setRaw} />
      {res ? (
        <StatTable
          rows={[
            ["تعداد گروه", fmt(res.k, 0)],
            ["N", fmt(res.n, 0)],
            ["H", fmt(res.h)],
            ["df", fmt(res.df, 0)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل دو گروه لازم است.</p>
      )}
    </div>
  );
}

export function GoodnessCalc() {
  const [obs, setObs] = useState("18 22 20 15 25");
  const [exp, setExp] = useState("20 20 20 20 20");
  const res = goodnessOfFit(parseSeries(obs), parseSeries(exp));
  return (
    <div className="tool-card grid gap-3">
      <Field label="فراوانی مشاهده‌شده" value={obs} onChange={setObs} />
      <Field label="فراوانی مورد انتظار" value={exp} onChange={setExp} />
      {res ? (
        <StatTable
          rows={[
            ["χ²", fmt(res.x2)],
            ["df", fmt(res.df, 0)],
          ]}
        />
      ) : (
        <p className="text-sm text-muted">حداقل دو طبقه با انتظار مثبت وارد کنید.</p>
      )}
    </div>
  );
}

export function PriceCalc() {
  const [service, setService] = useState("انجام پایان‌نامه");
  const [degree, setDegree] = useState("کارشناسی ارشد");
  const extras = [
    { id: "en", label: "نگارش یا ویرایش انگلیسی" },
    { id: "rush", label: "تحویل فوری" },
    { id: "stat", label: "تحلیل آماری همراه پروژه" },
  ];
  const [on, setOn] = useState<Record<string, boolean>>({});
  const note = [
    `خدمت: ${service}`,
    `مقطع: ${degree}`,
    ...extras.filter((x) => on[x.id]).map((x) => x.label),
  ].join(" — ");
  return (
    <div className="tool-card grid gap-4">
      <p className="text-sm text-muted mb-0">
        قیمت نهایی پس از بررسی دقیق پروژه توسط کارشناسان اعلام می‌شود. این فرم فقط محدوده خدمت را مشخص می‌کند.
      </p>
      <div className="field">
        <label>خدمت</label>
        <select value={service} onChange={(e) => setService(e.target.value)}>
          {["انجام پایان‌نامه", "انجام پروپوزال", "تحلیل آماری", "شبیه‌سازی", "تحلیل کیفی", "پروژه دانشجویی", "انجام مقاله", "پروژه GAMS"].map((s) => (
            <option key={s} value={s}>{s}</option>
          ))}
        </select>
      </div>
      <div className="field">
        <label>مقطع</label>
        <select value={degree} onChange={(e) => setDegree(e.target.value)}>
          {["کارشناسی", "کارشناسی ارشد", "دکتری"].map((s) => (
            <option key={s} value={s}>{s}</option>
          ))}
        </select>
      </div>
      <div className="grid gap-2">
        {extras.map((x) => (
          <label key={x.id} className="flex items-center gap-2 text-sm">
            <input type="checkbox" checked={!!on[x.id]} onChange={(e) => setOn((p) => ({ ...p, [x.id]: e.target.checked }))} />
            {x.label}
          </label>
        ))}
      </div>
      <StatTable rows={[["خلاصه انتخاب", note], ["برآورد", "پس از بررسی پروژه اعلام می‌شود"]]} />
      <Link to="/inquiry" className="btn-tz btn-primary-tz">
        ثبت درخواست برآورد دقیق
      </Link>
    </div>
  );
}

export const CALC_MAP = {
  "descriptive-statistics": DescriptiveCalc,
  "sample-size": SampleSizeCalc,
  "cronbachs-alpha": CronbachCalc,
  "pearson-correlation": PearsonCalc,
  spearman: SpearmanCalc,
  "t-test": TTestCalc,
  regression: RegressionCalc,
  anova: AnovaCalc,
  "chi-square": ChiSquareCalc,
  "power-analysis": PowerCalc,
  "content-validity": ContentValidityCalc,
  kr20: Kr20Calc,
  "cohens-kappa": KappaCalc,
  icc: IccCalc,
  "mann-whitney": MannWhitneyCalc,
  wilcoxon: WilcoxonCalc,
  "kruskal-wallis": KruskalCalc,
  "goodness-of-fit": GoodnessCalc,
  price: PriceCalc,
} as const;
