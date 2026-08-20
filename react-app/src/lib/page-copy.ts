import { stripEmoji } from "./utils";
import type { ImportedBlock } from "./imported-pages";

export type FaqItem = { q: string; a: string };

export type PageCopy = {
  slug: string;
  eyebrow: string;
  title: string;
  lead: string;
  features: string[];
  body?: string[];
  faqs?: FaqItem[];
  ctaText?: string;
  ctaUrl?: string;
};

/** Split WP “question — answer” feature rows into FAQ cards. */
export function splitFaqs(features: string[]): { features: string[]; faqs: FaqItem[] } {
  const out: string[] = [];
  const faqs: FaqItem[] = [];
  for (const raw of features) {
    const t = stripEmoji(raw)
      .replace(/^[\s✦•●○*-]+/, "")
      .replace(/\s{2,}/g, " ")
      .trim();
    if (!t) continue;
    const m = t.match(/^(.*?[؟?])\s*[—–-]\s*(.+)$/s);
    if (m && m[1].length < 140) {
      faqs.push({ q: m[1].trim(), a: m[2].trim() });
      continue;
    }
    out.push(t.replace(/\s*[—–]\s*/g, " — "));
  }
  return { features: out, faqs };
}

export function importedToPage(imp: ImportedBlock): PageCopy {
  const { features, faqs } = splitFaqs(imp.features ?? []);
  const ctaRaw = imp.ctaText ? stripEmoji(imp.ctaText) : "";
  const ctaText =
    !ctaRaw || /تماس|0\d{10}|۰۹/.test(ctaRaw) ? "شروع مشاوره رایگان" : ctaRaw;
  return {
    slug: imp.slug,
    eyebrow: stripEmoji(imp.eyebrow),
    title: stripEmoji(imp.title),
    lead: stripEmoji(imp.lead),
    features,
    faqs: faqs.length ? faqs : undefined,
    body: imp.body?.map((p) => stripEmoji(p)).filter(Boolean),
    ctaText,
    ctaUrl: imp.ctaUrl || "/inquiry",
  };
}

export function absorbImported(
  target: Record<string, PageCopy>,
  src: Record<string, ImportedBlock>,
  skip: string[] = [],
) {
  for (const [key, block] of Object.entries(src)) {
    if (skip.includes(key)) continue;
    const next = importedToPage(block);
    const prev = target[key];
    target[key] = prev
      ? {
          ...prev,
          ...next,
          slug: prev.slug || next.slug,
          features: next.features.length ? next.features : prev.features,
          faqs: next.faqs?.length ? next.faqs : prev.faqs,
          body: next.body?.length ? next.body : prev.body,
        }
      : next;
  }
}
