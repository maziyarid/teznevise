export const SITE = {
  name: "تزنویسه",
  tagline: "پژوهش بهتر، آینده روشن‌تر.",
  description:
    "تزنویسه برای پایان‌نامه، پروپوزال، پروژه دانشگاهی و تحلیل آماری یک مسیر منظم، خلاقانه و قابل اتکا می‌سازد.",
  phoneDisplay: "۰۹۳۰۲۸۲۲۰۹۱",
  phoneIntl: "+989302822091",
  hours: "شنبه تا پنجشنبه، ۹ تا ۲۱",
  email: "teznevisan@gmail.com",
  address: "تهران، انقلاب، خیابان ۱۲ فروردین",
  telegram: "https://t.me/Teznevise",
  whatsapp: "https://wa.me/989302822091",
  bale: "https://ble.ir/teznevise",
} as const;

export type NavChild = { label: string; to: string };
export type NavItem = {
  label: string;
  to: string;
  children?: { heading?: string; items: NavChild[] }[];
};

export const PRIMARY_NAV: NavItem[] = [
  { label: "خانه", to: "/" },
  {
    label: "پایان‌نامه",
    to: "/thesis",
    children: [
      {
        heading: "فصل به فصل",
        items: [
          { label: "نگارش فصل اول", to: "/thesis/chapter-one" },
          { label: "نگارش فصل دوم", to: "/thesis/chapter-two" },
          { label: "نگارش فصل سوم", to: "/thesis/chapter-three" },
          { label: "نگارش فصل چهارم", to: "/thesis/chapter-four" },
          { label: "نگارش فصل پنجم", to: "/thesis/chapter-five" },
        ],
      },
      {
        heading: "بر اساس رشته",
        items: [
          { label: "علوم انسانی", to: "/thesis/humanities" },
          { label: "فنی و مهندسی", to: "/thesis/engineering" },
          { label: "علوم پایه", to: "/thesis/pure-science" },
          { label: "علوم پزشکی", to: "/thesis/medical-health" },
          { label: "هنر و معماری", to: "/thesis/art-architecture-media" },
          { label: "کشاورزی و منابع طبیعی", to: "/thesis/agriculture-natural-resources" },
          { label: "علوم دامی و دامپزشکی", to: "/thesis/animal-science-veterinary" },
          { label: "علوم بین‌رشته‌ای", to: "/thesis/interdisciplinary" },
        ],
      },
      {
        heading: "علوم انسانی",
        items: [
          { label: "روانشناسی", to: "/thesis/humanities/psychology" },
          { label: "حقوق", to: "/thesis/humanities/law" },
          { label: "مدیریت", to: "/thesis/humanities/management" },
          { label: "علوم اجتماعی", to: "/thesis/humanities/social-sciences" },
          { label: "فلسفه", to: "/thesis/humanities/philosophy" },
          { label: "تاریخ", to: "/thesis/humanities/history" },
        ],
      },
      {
        heading: "سایر خدمات",
        items: [
          { label: "رساله دکتری", to: "/thesis/phd" },
          { label: "پایان‌نامه بین‌المللی", to: "/thesis/international" },
        ],
      },
    ],
  },
  {
    label: "پروپوزال",
    to: "/proposal",
    children: [
      {
        heading: "انواع پروپوزال",
        items: [
          { label: "پروپوزال دکتری", to: "/proposal/phd" },
          { label: "پروپوزال کلاسی", to: "/proposal/project" },
          { label: "پروپوزال انگلیسی", to: "/proposal/english" },
          { label: "پروپوزال پزشکی", to: "/proposal/medical" },
        ],
      },
      {
        heading: "روش پژوهش",
        items: [
          { label: "پژوهش کیفی", to: "/proposal/qualitative" },
          { label: "پژوهش کمی", to: "/proposal/quantitative" },
          { label: "تحقیق کاربردی", to: "/proposal/applied-research" },
        ],
      },
    ],
  },
  { label: "بلاگ", to: "/blog" },
  {
    label: "ابزار",
    to: "/tools",
    children: [
      {
        heading: "آمار پایه",
        items: [
          { label: "آمار توصیفی", to: "/tools/descriptive-statistics" },
          { label: "حجم نمونه", to: "/tools/sample-size" },
          { label: "آزمون t", to: "/tools/t-test" },
          { label: "رگرسیون", to: "/tools/regression" },
        ],
      },
      {
        heading: "روایی و پایایی",
        items: [
          { label: "آلفای کرونباخ", to: "/tools/cronbachs-alpha" },
          { label: "همبستگی پیرسون", to: "/tools/pearson-correlation" },
          { label: "همبستگی اسپیرمن", to: "/tools/spearman" },
        ],
      },
      {
        heading: "پیشرفته",
        items: [
          { label: "ANOVA", to: "/tools/anova" },
          { label: "خی‌دو", to: "/tools/chi-square" },
          { label: "برآورد هزینه", to: "/tools/price" },
          { label: "دستیار هوش مصنوعی", to: "/tools/method-advisor" },
        ],
      },
    ],
  },
  { label: "تماس", to: "/contact" },
];

export const UTILITY_LINKS = [
  { label: "حریم خصوصی", to: "/privacy" },
  { label: "بازخورد مشتریان", to: "/testimonials" },
  { label: "ثبت درخواست", to: "/inquiry" },
] as const;

export const FOOTER_SERVICES = [
  { label: "مشاوره انجام پایان‌نامه", to: "/thesis" },
  { label: "مشاوره انجام پروپوزال", to: "/proposal" },
  { label: "تحلیل آماری", to: "/statistics" },
  { label: "شبیه‌سازی", to: "/simulation" },
  { label: "تحلیل کیفی", to: "/proposal/qualitative" },
  { label: "پروژه دانشجویی", to: "/project" },
  { label: "مشاوره نگارش مقاله", to: "/article" },
  { label: "ابزارهای آنلاین", to: "/tools" },
] as const;

export const FOOTER_NAV = [
  { label: "تیم پژوهشگران ما", to: "/team" },
  { label: "موقعیت‌های شغلی", to: "/careers" },
  { label: "همکاری با تزنویسه", to: "/join-us" },
  { label: "داستان ما", to: "/our-story" },
  { label: "افتخارات و سوابق", to: "/achievements" },
  { label: "نظرات متقاضیان", to: "/testimonials" },
  { label: "مطالعات موردی", to: "/case-studies" },
  { label: "دانلودها", to: "/downloads" },
] as const;

export const FOOTER_QUICK = [
  { label: "درباره ما", to: "/about" },
  { label: "تماس با ما", to: "/contact" },
  { label: "ثبت سفارش", to: "/inquiry" },
  { label: "بلاگ", to: "/blog" },
  { label: "حریم خصوصی", to: "/privacy" },
  { label: "نقشه سایت", to: "/sitemap" },
] as const;

export const BOTTOM_NAV = [
  { label: "خانه", to: "/", icon: "home" as const, kind: "route" as const },
  { label: "ابزارها", to: "/tools", icon: "tools" as const, kind: "route" as const },
  { label: "بلاگ", to: "/blog", icon: "blog" as const, kind: "route" as const },
  { label: "تماس", to: `tel:${SITE.phoneIntl}`, icon: "phone" as const, kind: "tel" as const },
];

