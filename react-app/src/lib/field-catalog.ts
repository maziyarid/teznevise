import { PROPOSAL_PAGES, SERVICE_PAGES, STATIC_PAGES, THESIS_PAGES } from "./content";

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
  ...Object.values(THESIS_PAGES).map((p) => ({
    slug: `thesis-${p.slug}`,
    path: `/thesis/${p.slug}`,
    title: p.title,
    group: "پایان‌نامه",
  })),
  ...Object.values(PROPOSAL_PAGES).map((p) => ({
    slug: `proposal-${p.slug}`,
    path: `/proposal/${p.slug}`,
    title: p.title,
    group: "پروپوزال",
  })),
  ...Object.entries(STATIC_PAGES).map(([slug, p]) => ({
    slug,
    path: `/${slug}`,
    title: p.title,
    group: "صفحات ثابت",
  })),
];

export const FIELD_GROUPS = ["خدمات اصلی", "پایان‌نامه", "پروپوزال", "صفحات ثابت"] as const;
