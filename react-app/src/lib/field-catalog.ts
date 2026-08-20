import { PROPOSAL_PAGES, SERVICE_PAGES, STATIC_PAGES, THESIS_PAGES, TOOLS } from "./content";
import { CASE_STUDIES, DOWNLOADS, GAMS_PAGE, IMPORTED_LEGAL } from "./imported-pages";

export type FieldCatalogItem = {
  slug: string;
  path: string;
  title: string;
  group: string;
};

export const FIELD_CATALOG: FieldCatalogItem[] = [
  ...Object.values(SERVICE_PAGES).map((p) => ({
    slug: p.slug,
    path: `/${p.slug}`,
    title: p.title,
    group: "خدمات اصلی",
  })),
  {
    slug: "gams",
    path: "/gams",
    title: GAMS_PAGE.title,
    group: "خدمات اصلی",
  },
  ...Object.values(THESIS_PAGES).map((p) => ({
    slug: `thesis-${p.slug}`,
    path: p.slug.match(/^(psychology|law|management|philosophy|history|social-sciences)$/)
      ? `/thesis/humanities/${p.slug}`
      : `/thesis/${p.slug}`,
    title: p.title,
    group: "پایان‌نامه",
  })),
  ...Object.values(PROPOSAL_PAGES).map((p) => ({
    slug: `proposal-${p.slug}`,
    path: `/proposal/${p.slug}`,
    title: p.title,
    group: "پروپوزال",
  })),
  ...TOOLS.map((t) => ({
    slug: `tool-${t.slug}`,
    path: `/tools/${t.slug}`,
    title: t.title,
    group: "ابزارها",
  })),
  ...Object.entries(STATIC_PAGES).map(([slug, p]) => ({
    slug,
    path: `/${slug}`,
    title: p.title,
    group: "صفحات ثابت",
  })),
  ...Object.values(IMPORTED_LEGAL).map((p) => ({
    slug: `legal-${p.slug}`,
    path: `/${p.slug}`,
    title: p.title,
    group: "قوانین",
  })),
  ...DOWNLOADS.map((d) => ({
    slug: `download-${d.slug}`,
    path: `/download/${d.slug}`,
    title: d.title,
    group: "دانلودها",
  })),
  ...CASE_STUDIES.map((c) => ({
    slug: `case-${c.slug}`,
    path: `/case-study/${c.slug}`,
    title: c.title,
    group: "مطالعات موردی",
  })),
];

export const FIELD_GROUPS = [
  "خدمات اصلی",
  "پایان‌نامه",
  "پروپوزال",
  "ابزارها",
  "صفحات ثابت",
  "قوانین",
  "دانلودها",
  "مطالعات موردی",
] as const;
