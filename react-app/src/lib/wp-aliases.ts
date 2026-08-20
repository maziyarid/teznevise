import { resolveTool, SERVICE_PAGES, STATIC_PAGES, THESIS_PAGES, type PageBlock } from "./content";
import { GAMS_PAGE, IMPORTED_LEGAL, type LegalImported } from "./imported-pages";

export type AliasHit =
  | { kind: "tool"; slug: string }
  | { kind: "service"; page: PageBlock; fieldSlug: string }
  | { kind: "static"; slug: keyof typeof STATIC_PAGES }
  | { kind: "legal"; page: LegalImported }
  | { kind: "redirect"; to: string };

const STATIC_ALIAS: Record<string, keyof typeof STATIC_PAGES> = {
  "about-us": "about",
  about: "about",
  "our-team": "team",
  team: "team",
  careers: "careers",
  "join-us": "join-us",
  "our-story": "our-story",
  achievements: "achievements",
  testimonials: "testimonials",
  "case-studies": "case-studies",
  downloads: "downloads",
};

const SERVICE_ALIAS: Record<string, { key: keyof typeof SERVICE_PAGES; field: string }> = {
  "service-thesis": { key: "thesis", field: "thesis" },
  "service-proposal": { key: "proposal", field: "proposal" },
  "service-statistics": { key: "statistics", field: "statistics" },
  statistics: { key: "statistics", field: "statistics" },
  "service-simulation": { key: "simulation", field: "simulation" },
  simulation: { key: "simulation", field: "simulation" },
  article: { key: "article", field: "article" },
  paper: { key: "article", field: "article" },
  project: { key: "project", field: "project" },
  "student-project": { key: "project", field: "project" },
};

const REDIRECT: Record<string, string> = {
  "contact-us": "/contact",
  contact: "/contact",
  "online-calculation-tools": "/tools",
  tools: "/tools",
  account: "/dashboard",
  sitemap: "/sitemap",
  blog: "/blog",
  inquiry: "/inquiry",
  "research-rules": "/rules",
  cookies: "/cookies",
  privacy: "/privacy",
  terms: "/terms",
  refund: "/refund",
};

export function resolveWpSlug(slug: string): AliasHit | null {
  const tool = resolveTool(slug);
  if (tool) return { kind: "tool", slug: tool.slug };

  if (IMPORTED_LEGAL[slug]) return { kind: "legal", page: IMPORTED_LEGAL[slug] };

  if (slug === "gams") return { kind: "service", page: GAMS_PAGE, fieldSlug: "gams" };

  if (THESIS_PAGES[slug]) {
    return { kind: "service", page: THESIS_PAGES[slug], fieldSlug: `thesis-${slug}` };
  }

  if (SERVICE_ALIAS[slug]) {
    const a = SERVICE_ALIAS[slug];
    return { kind: "service", page: SERVICE_PAGES[a.key], fieldSlug: a.field };
  }

  if (STATIC_ALIAS[slug]) return { kind: "static", slug: STATIC_ALIAS[slug] };

  if (REDIRECT[slug]) return { kind: "redirect", to: REDIRECT[slug] };

  return null;
}
