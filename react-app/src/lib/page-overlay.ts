import { useEffect, useState } from "react";
import type { PageBlock } from "@/lib/content";
import { splitFaqs } from "./page-copy";
import { getPageField } from "@/lib/server/app";

export type PageFieldRow = {
  slug: string;
  eyebrow: string | null;
  title: string | null;
  lead: string | null;
  features: string | null;
  body: string | null;
  cta_text: string | null;
  cta_url: string | null;
};

export function mergePageBlock(page: PageBlock, overlay: PageFieldRow | null | undefined): PageBlock & {
  ctaText?: string;
  ctaUrl?: string;
} {
  if (!overlay) return page;
  const features = overlay.features
    ? overlay.features
        .split(/\n+/)
        .map((s) => s.trim())
        .filter(Boolean)
    : page.features;
  const body = overlay.body
    ? overlay.body
        .split(/\n{2,}/)
        .map((s) => s.trim())
        .filter(Boolean)
    : page.body;
  const split = overlay.features ? splitFaqs(features) : { features, faqs: page.faqs ?? [] };
  return {
    ...page,
    eyebrow: overlay.eyebrow?.trim() || page.eyebrow,
    title: overlay.title?.trim() || page.title,
    lead: overlay.lead?.trim() || page.lead,
    features: overlay.features ? split.features : page.features,
    faqs: overlay.features ? (split.faqs.length ? split.faqs : page.faqs) : page.faqs,
    body,
    ctaText: overlay.cta_text?.trim() || page.ctaText,
    ctaUrl: overlay.cta_url?.trim() || page.ctaUrl,
  };
}

export function usePageOverlay(slug: string) {
  const [row, setRow] = useState<PageFieldRow | null>(null);
  useEffect(() => {
    let live = true;
    void getPageField({ data: { slug } })
      .then((data) => {
        if (live) setRow(data);
      })
      .catch(() => {
        if (live) setRow(null);
      });
    return () => {
      live = false;
    };
  }, [slug]);
  return row;
}
