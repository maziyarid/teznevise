import { IMPORTED_LEGAL } from "./imported-pages";

export type LegalPage = {
  slug: string;
  eyebrow: string;
  title: string;
  lead: string;
  sections: { title: string; body: string }[];
};

const IMPORT_MAP: Record<string, string> = {
  privacy: "privacy-policy",
  terms: "terms-and-conditions",
  cookies: "cookie-policy",
  refund: "refund-policy",
};

export const LEGAL_PAGES: LegalPage[] = Object.entries(IMPORT_MAP).map(([slug, imported]) => {
  const src = IMPORTED_LEGAL[imported];
  return {
    slug,
    eyebrow: slug === "privacy" ? "حریم خصوصی" : slug === "terms" ? "قوانین" : slug === "cookies" ? "کوکی" : "بازپرداخت",
    title: src.title,
    lead: src.lead,
    sections: src.sections,
  };
});

LEGAL_PAGES.push({
  slug: "rules",
  eyebrow: "آیین‌نامه",
  title: "آیین‌نامه پژوهشی و اخلاق علمی",
  lead: "همراهی تزنویسه جایگزین تفکر، مطالعه و دفاع دانشجو نیست.",
  sections: [
    {
      title: "اصالت پژوهش",
      body: "دانشجو موظف است منابع را ذکر کند، داده را جعل نکند و از اثر نهایی در جلسه دفاع دفاع کند. سفارش‌هایی که هدف آن‌ها تقلب در آزمون یا سرقت علمی باشد پذیرفته نمی‌شود.",
    },
    {
      title: "محرمانگی فایل",
      body: "فایل پرسشنامه، داده SPSS و پیشنویس فقط در انبار امن تیکت نگهداری می‌شود و پس از پایان پروژه طبق درخواست قابل حذف است.",
    },
  ],
});

export function legalBySlug(slug: string) {
  return LEGAL_PAGES.find((p) => p.slug === slug);
}
