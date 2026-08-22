import { IMPORTED_PROPOSAL, IMPORTED_THESIS, IMPORTED_TOOLS } from "./imported-pages";
import { absorbImported, importedToPage, splitFaqs, type FaqItem, type PageCopy } from "./page-copy";
import { TOOL_GUIDES } from "./tool-guides";

export type { FaqItem };

export type ArticleSection = { heading: string; paragraphs: string[] };
export type Article = {
  slug: string;
  title: string;
  excerpt: string;
  category: string;
  date: string;
  dateFa: string;
  cover: "mint" | "sand" | "forest";
  author: string;
  authorRole: string;
  sections: ArticleSection[];
};

export type PageBlock = PageCopy;

export function articleMinutes(article: Article) {
	const words = article.sections.flatMap((s) => [s.heading, ...s.paragraphs]).join(" ").split(/\s+/).filter(Boolean).length;
	return Math.max(4, Math.round(words / 180));
}
export const ARTICLES: Article[] = [
	{
		slug: "necessity-research",
		title: "اهمیت و ضرورت تحقیق در فصل اول را چگونه بنویسیم؟",
		excerpt: "بیان مسئله را نوشته‌اید و حالا به بخش اهمیت و ضرورت تحقیق رسیده‌اید. این بخش باید فاصله میان وضع موجود و وضع مطلوب را علمی نشان دهد.",
		category: "مسیر پایان‌نامه تا دفاع",
		date: "2026-08-19",
		dateFa: "۱۹ مرداد ۱۴۰۵",
		cover: "mint",
		author: "تحریریه تزنویسه",
		authorRole: "مشاور نگارش دانشگاهی",
		sections: [
			{
				heading: "این بخش چه کاری باید بکند؟",
				paragraphs: ["بخش اهمیت و ضرورت تحقیق، ادامهٔ منطقی بیان مسئله است؛ نه تکرار آن. اگر بیان مسئله «چه مشکلی وجود دارد» را روشن می‌کند، اهمیت تحقیق باید بگوید چرا حل این مسئله برای رشته، جامعه یا سازمان اهمیت دارد.", "داور این صفحه را با این سؤال می‌خواند: اگر این پژوهش انجام نشود، چه چیزی ناقص می‌ماند؟ پاسخ باید مشخص، قابل ارجاع و وابسته به همان مسئله باشد."]
			},
			{
				heading: "سه لایه اهمیت را جدا بنویسید",
				paragraphs: ["اهمیت نظری: شکاف دانش، نظریه ناقص یا اختلاف یافته‌های پیشین. اهمیت کاربردی: تصمیمی که بدون این داده گرفته می‌شود. اهمیت روش‌شناختی: فقط وقتی رویکرد شما تازگی واقعی دارد.", "هر ادعا را به منبع یا مشاهدهٔ قابل دفاع وصل کنید. جملهٔ «این موضوع بسیار مهم است» هیچ‌کدام از این لایه‌ها را پر نمی‌کند."]
			},
			{
				heading: "پیامد نپرداختن به مسئله",
				paragraphs: ["به‌جای کلی‌گویی، پیامد را مشخص کنید: چه سیاستی بدون شواهد جلو می‌رود؟ چه مداخله‌ای بی‌اثر می‌ماند؟ کدام مدل تصمیم‌گیری روی حدس می‌ماند؟", "در پایان، یک پاراگراف جمع‌بندی بنویسید که اهمیت تحقیق را به اهداف و سوال‌های فصل اول گره بزند تا داور احساس نکند این بخش مستقل از بقیهٔ فصل نوشته شده است."]
			}
		]
	},
	{
		slug: "statement-differences-proposal-first-chapter",
		title: "تفاوت بیان مسئله در پروپوزال و فصل اول چیست؟",
		excerpt: "پروپوزال شما تصویب شده و حالا فایل فصل اول را باز کرده‌اید. اولین سؤال معمولاً همین است: بیان مسئله پروپوزال را کپی کنم یا بازنویسی کنم؟",
		category: "مسیر پایان‌نامه تا دفاع",
		date: "2026-08-18",
		dateFa: "۱۸ مرداد ۱۴۰۵",
		cover: "sand",
		author: "تحریریه تزنویسه",
		authorRole: "مشاور روش تحقیق",
		sections: [{
			heading: "پروپوزال طرح است؛ فصل اول گزارش بالغ",
			paragraphs: ["بیان مسئلهٔ پروپوزال یک طرح اولیه است؛ بیان مسئلهٔ فصل اول باید همان ایده را با عمق بیشتر، منابع به‌روزتر و پیوند شفاف‌تر با اهداف و فرضیه‌ها بازنویسی کند.", "کپی کامل پروپوزال معمولاً دو مشکل می‌سازد: لحن پیشنهادی باقی می‌ماند، و اصلاحات استاد راهنما در متن جدید دیده نمی‌شود."]
		}, {
			heading: "ساختار پیشنهادی بازنویسی",
			paragraphs: ["زمینه و روند مسئله، شکاف دانش، پیامدهای عملی، و جمع‌بندی که به سوال اصلی می‌رسد. هر پاراگراف یک کار مشخص انجام دهد.", "اگر داده یا پیشینهٔ جدیدی بعد از تصویب پروپوزال پیدا کرده‌اید، همین‌جا وارد متن کنید. فصل اول باید نسخهٔ بالغ مسئله باشد، نه بایگانی پیشنهاد اولیه."]
		}]
	},
	{
		slug: "how-to-write-first-chapter-thesis",
		title: "آموزش نگارش فصل اول پایان‌نامه: تبدیل پروپوزال به فصل اول",
		excerpt: "پروپوزالتان تصویب شده و استاد گفته فصل اول را بنویسید، اما دقیقاً نمی‌دانید کدام بخش‌ها را می‌توانید از پروپوزال بردارید.",
		category: "مسیر پایان‌نامه تا دفاع",
		date: "2026-08-15",
		dateFa: "۱۵ مرداد ۱۴۰۵",
		cover: "forest",
		author: "گروه نگارش تزنویسه",
		authorRole: "مسیر پایان‌نامه",
		sections: [
			{
				heading: "قالب دانشگاه را اول بخوانید",
				paragraphs: ["فصل اول معمولاً شامل مقدمه، بیان مسئله، اهمیت و ضرورت، اهداف، سوال‌ها یا فرضیه‌ها، تعریف مفهومی و عملیاتی متغیرها و گاهی قلمرو پژوهش است. ترتیب بخش‌ها در آیین‌نامه‌ها فرق دارد."]
			},
			{
				heading: "چه چیزی از پروپوزال قابل انتقال است؟",
				paragraphs: ["از پروپوزال می‌توانید اسکلت را بردارید: عنوان، متغیرها، جامعه و روش کلی. آنچه باید بازنویسی شود لحن، عمق پیشینه و انسجام منطقی میان مسئله و اهداف است.", "یک روش عملی: هر بخش پروپوزال را در یک ستون بنویسید و در ستون روبه‌رو نسخهٔ فصل اول را با این پرسش بنویسید: «این جمله چه چیزی به داور اضافه می‌کند؟»"]
			},
			{
				heading: "آزمون انسجام از آخر به اول",
				paragraphs: ["پس از اتمام پیش‌نویس، یک‌بار از آخر به اول بخوانید: آیا سوال‌ها از اهداف می‌آیند؟ آیا اهداف مسئله را پوشش می‌دهند؟ اگر حلقه‌ای باز مانده باشد، فصل اول هنوز آمادهٔ ارسال نیست."]
			}
		]
	},
	{
		slug: "independent-t-test-spss",
		title: "آموزش گام‌به‌گام آزمون تی مستقل در SPSS",
		excerpt: "اجرای آزمون، بررسی پیش‌فرض‌ها و تفسیر خروجی را به زبان ساده یاد بگیرید تا در فصل چهارم و جلسه دفاع قابل توضیح باشد.",
		category: "تحلیل آماری",
		date: "2026-08-02",
		dateFa: "۲ مرداد ۱۴۰۵",
		cover: "mint",
		author: "گروه آمار تزنویسه",
		authorRole: "تحلیل کمی",
		sections: [
			{
				heading: "چه زمانی t مستقل مناسب است؟",
				paragraphs: ["آزمون t مستقل وقتی مناسب است که یک متغیر وابسته کمی و یک عامل گروهی دو سطحی داشته باشید و مشاهدات مستقل باشند. قبل از اجرا، نرمال بودن تقریبی در هر گروه و همگنی واریانس را بررسی کنید."]
			},
			{
				heading: "مسیر اجرا در SPSS",
				paragraphs: ["مسیر Analyze → Compare Means → Independent-Samples T Test را بروید، متغیر وابسته را در Test Variable و گروه را در Grouping Variable بگذارید و کد دو گروه را تعریف کنید.", "خروجی Levene را اول بخوانید. اگر معنادار نبود، ردیف Equal variances assumed را گزارش کنید؛ در غیر این صورت از ردیف دوم استفاده کنید."]
			},
			{
				heading: "تفسیر به زبان پژوهش",
				paragraphs: ["مقدار t، درجه آزادی، Sig و فاصله اطمینان را بنویسید. تفسیر باید تفاوت میانگین دو گروه، جهت تفاوت و اندازه اثر را بگوید — نه فقط «معنادار شد یا نشد»."]
			}
		]
	},
	{
		slug: "thematic-analysis-steps",
		title: "تحلیل مضمون را مرحله‌به‌مرحله برای فصل چهارم بنویسید",
		excerpt: "کدگذاری انجام شده اما نمی‌دانید چگونه مضامین را به روایتی قابل دفاع برای داور تبدیل کنید.",
		category: "روش کیفی",
		date: "2026-07-21",
		dateFa: "۳۰ تیر ۱۴۰۵",
		cover: "sand",
		author: "دکتر لیلا احمدی",
		authorRole: "روش کیفی",
		sections: [{
			heading: "از کد به مضمون، نه از مضمون به کد",
			paragraphs: ["مضمون از قبل در ذهن پژوهشگر نباید قالب‌گیری شود. ابتدا کدهای توصیفی، سپس گروه‌های معنایی، و در نهایت مضامین اصلی که به سوال پژوهش جواب می‌دهند.", "اگر مضمونی نقل‌قول پشتیبان ندارد، یا داده کم است یا مضمون زودرس ساخته شده است."]
		}, {
			heading: "جدول شواهد را از متن جدا نکنید",
			paragraphs: ["برای هر مضمون یک تعریف کوتاه، چند نقل‌قول نماینده و توضیح تمایز با مضامین مجاور بنویسید. داور باید بفهمد چرا این برچسب و نه برچسب دیگر."]
		}]
	},
	{
		slug: "sample-size-common-mistakes",
		title: "پنج اشتباه رایج در تعیین حجم نمونه پژوهش کمی",
		excerpt: "عدد n را از مقاله‌ای مشابه برداشته‌اید؟ کمیته روش معمولاً دقیقاً همان‌جا ایراد می‌گیرد.",
		category: "تحلیل آماری",
		date: "2026-07-08",
		dateFa: "۱۷ تیر ۱۴۰۵",
		cover: "forest",
		author: "گروه آمار تزنویسه",
		authorRole: "طراحی پژوهش",
		sections: [
			{
				heading: "کپی n از مقاله مشابه",
				paragraphs: ["حجم نمونه تابع توان آزمون، اندازه اثر مورد انتظار، سطح معناداری و طرح تحلیل است. مقاله مشابه ممکن است آزمون دیگری داشته باشد."]
			},
			{
				heading: "نادیده گرفتن ریزش و خوشه‌ای بودن",
				paragraphs: ["در مطالعات طولی و خوشه‌ای، n نهایی با n محاسبه‌شده یکی نیست. درصد ریزش و اثر طرح را از ابتدا وارد فرمول کنید تا در فصل روش غافلگیر نشوید."]
			},
			{
				heading: "ابزار آنلاین بدون توضیح",
				paragraphs: ["ماشین‌حساب حجم نمونه کمکتان می‌کند، اما در پروپوزال باید مفروضات (d، توان، α) را بنویسید. عدد بدون مفروضات برای داور قابل دفاع نیست."]
			}
		]
	}
];
export const SERVICES = [
	{
		to: "/thesis",
		title: "مشاوره انجام پایان‌نامه",
		text: "از انتخاب موضوع تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع برای ارشد و دکتری.",
		icon: "grad"
	},
	{
		to: "/proposal",
		title: "مشاوره انجام پروپوزال",
		text: "بیان مسئله، مرور ادبیات، اهداف، فرضیه‌ها و روش‌شناسی با ساختار علمی و منابع به‌روز.",
		icon: "file"
	},
	{
		to: "/statistics",
		title: "تحلیل آماری",
		text: "تحلیل داده‌ها با SPSS، R، Python، LISREL، Matlab و AMOS همراه با تفسیر علمی نتایج.",
		icon: "chart"
	},
	{
		to: "/tools",
		title: "ابزارهای آنلاین",
		text: "ماشین‌حساب‌های آماری رایگان برای آمار توصیفی، همبستگی و آزمون‌های پرکاربرد.",
		icon: "calc"
	},
	{
		to: "/blog",
		title: "مرکز دانش",
		text: "راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی، تحلیل آماری و ابزارهای پژوهشی.",
		icon: "idea"
	},
	{
		to: "/simulation",
		title: "شبیه‌سازی",
		text: "مدل‌سازی عددی، شبیه‌سازی سیستم‌ها و تحلیل‌های مهندسی با MATLAB و Python.",
		icon: "sim"
	},
	{
		to: "/proposal/qualitative",
		title: "تحلیل کیفی",
		text: "تحلیل مضمون، گراندد تئوری و پدیدارشناسی با کدگذاری منظم و قابل دفاع.",
		icon: "qual"
	},
	{
		to: "/project",
		title: "انجام پروژه دانشجویی",
		text: "پروژه‌های درسی، کارورزی و گزارش‌های دانشگاهی با ساختار علمی و تحویل مرحله‌ای.",
		icon: "proj"
	},
	{
		to: "/article",
		title: "انجام مقاله",
		text: "نگارش، استخراج و آماده‌سازی مقاله علمی از پایان‌نامه برای مجلات داخلی و ISI.",
		icon: "paper"
	}
] as const;
export const REASONS = [
	{
		title: "محرمانگی کامل",
		text: "اطلاعات و فایل‌های پروژه با رویکرد محرمانه مدیریت می‌شوند.",
		icon: "shield"
	},
	{
		title: "روش‌مندی علمی",
		text: "ساختار پژوهش بر اساس متدولوژی استاندارد و منابع علمی به‌روز پیش می‌رود.",
		icon: "compass"
	},
	{
		title: "پاسخ‌گویی سریع",
		text: "درخواست اولیه شما در کوتاه‌ترین زمان بررسی و مسیر بعدی شفاف می‌شود.",
		icon: "bolt"
	},
	{
		title: "خلاقیت آکادمیک",
		text: "مسیر پروژه منظم، قابل پیگیری و متناسب با استاندارد دانشگاهی پیش می‌رود.",
		icon: "pen"
	}
] as const;
export const STEPS = [
	{
		title: "مشاوره رایگان",
		text: "موضوع، نیاز، زمان و بودجه بررسی می‌شود و برآورد اولیه دریافت می‌کنید."
	},
	{
		title: "طرح و پروپوزال",
		text: "ساختار پژوهش، پرسش‌ها، فرضیه‌ها و روش اجرا منسجم می‌شود."
	},
	{
		title: "گردآوری داده",
		text: "ابزار، نمونه و پروتکل اجرا مشخص می‌شود تا داده قابل‌اتکا باشد."
	},
	{
		title: "اجرا و تحلیل",
		text: "داده‌ها با روش مناسب تحلیل و نتایج به‌صورت علمی تفسیر می‌شوند."
	},
	{
		title: "نگارش فصل‌ها",
		text: "فصل‌ها تکمیل، انسجام منطقی حفظ و اصلاحات استاد اعمال می‌شود."
	},
	{
		title: "دفاع و تحویل",
		text: "آمادگی جلسه دفاع، فایل نهایی و پشتیبانی تا تأیید دانشگاه."
	}
];
export const SERVICE_PAGES: Record<string, PageBlock> = {
	thesis: {
		slug: "thesis",
		eyebrow: "خدمات پژوهشی",
		title: "مشاوره انجام پایان‌نامه ارشد و دکتری",
		lead: "از انتخاب موضوع تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع؛ مسیر پایان‌نامه را مرحله‌به‌مرحله و قابل پیگیری پیش می‌بریم.",
		features: [
			"انتخاب و تثبیت موضوع قابل دفاع",
			"نگارش فصل‌به‌فصل با قالب دانشگاه",
			"هماهنگی روش تحقیق و ابزار گردآوری داده",
			"آمادگی جلسه دفاع و اصلاحات استاد"
		],
		body: ["مسیر پایان‌نامه وقتی قابل دفاع می‌شود که موضوع، مسئله، روش و نگارش از یک منطق واحد پیروی کنند. تزنویسه این حلقه را برای ارشد و دکتری، با پژوهشگر متناسب رشته شما، مرحله‌به‌مرحله جلو می‌برد.", "هر تحویل با توضیح تغییرات همراه است تا شما بتوانید در جلسه استاد راهنما از متن دفاع کنید، نه فقط فایل را ارسال کنید."]
	},
	proposal: {
		slug: "proposal",
		eyebrow: "خدمات پژوهشی",
		title: "مشاوره انجام پروپوزال تضمینی",
		lead: "بیان مسئله، پیشینه، اهداف و روش‌شناسی را طوری می‌نویسیم که ارتباط منطقی پژوهش از همان صفحه اول روشن باشد.",
		features: [
			"تدوین بیان مسئله و شکاف دانش",
			"مرور ادبیات هدفمند و منابع به‌روز",
			"طراحی سوال، فرضیه و مدل مفهومی",
			"روش اجرا متناسب با نوع پژوهش"
		],
		body: ["پروپوزال ضعیف معمولاً عنوان جذابی دارد اما مسئله، اهداف و روش با هم حرف نمی‌زنند. کار ما همین انسجام است تا در کمیته مجبور به بازنویسی کامل نشوید."]
	},
	statistics: {
		slug: "statistics",
		eyebrow: "خدمات تحلیل آماری",
		title: "تحلیل آماری تخصصی پژوهش",
		lead: "مستقل‌ترین متخصصان، داده‌های شما را با دقت تحلیل و نتایج را به‌صورت قابل‌دفاع تفسیر می‌کنند. از ابزارهای آماری رایگان ما نیز می‌توانید استفاده کنید.",
		features: [
			"آمار توصیفی و استنباطی با SPSS",
			"مدل‌سازی معادلات ساختاری با AMOS و Smart PLS",
			"تحلیل عاملی تأییدی و اکتشافی",
			"آزمون‌های ناپارامتری و رگرسیون پیشرفته با R",
			"تفسیر کامل خروجی‌ها به زبان فارسی"
		]
	},
	simulation: {
		slug: "simulation",
		eyebrow: "خدمات تخصصی",
		title: "شبیه‌سازی و مدل‌سازی",
		lead: "شبیه‌سازی سیستم‌ها، مدل‌های عامل‌محور و تحلیل‌های عددی برای پروژه‌های مهندسی و کاربردی.",
		features: [
			"مدل‌سازی ریاضی و عددی",
			"شبیه‌سازی عامل‌محور",
			"MATLAB، Python و ابزارهای تخصصی",
			"گزارش روش و تفسیر نتایج"
		]
	},
	project: {
		slug: "project",
		eyebrow: "خدمات دانشگاهی",
		title: "انجام پروژه دانشجویی",
		lead: "پروژه‌های درسی، کارورزی و گزارش‌های عملی را با ساختار علمی، زمان‌بندی شفاف و پژوهشگر هم‌رشته پیش می‌بریم.",
		features: [
			"تعریف مسئله و خروجی مورد انتظار استاد",
			"ساختار گزارش مطابق قالب دانشگاه",
			"تحلیل داده یا شبیه‌سازی در صورت نیاز",
			"تحویل مرحله‌ای و پشتیبانی اصلاحات"
		],
		body: ["پروژه دانشجویی وقتی قابل دفاع است که مسئله، روش و خروجی با هم بخوانند. مسیر تزنویسه همین انسجام را از موضوع تا فایل نهایی حفظ می‌کند."]
	},
	article: {
		slug: "article",
		eyebrow: "نگارش علمی",
		title: "انجام و استخراج مقاله",
		lead: "از استخراج مقاله از پایان‌نامه تا نگارش مستقل برای مجلات داخلی و بین‌المللی، با ساختار استاندارد و منابع به‌روز.",
		features: [
			"استخراج مقاله از پایان‌نامه یا رساله",
			"ساختار IMRaD و قالب مجله هدف",
			"ویرایش علمی فارسی و انگلیسی",
			"آماده‌سازی ارسال و پاسخ به داور"
		],
		body: ["مقاله قوی تکرار پایان‌نامه نیست؛ یک پرسش مشخص، یافته متمرکز و بحث قابل استناد است. ما همان منطق را برای مجله هدف شما می‌سازیم."]
	}
};
export const THESIS_PAGES: Record<string, PageBlock> = {
	"chapter-one": {
		slug: "chapter-one",
		eyebrow: "نگارش فصل‌ها",
		title: "نگارش فصل اول پایان‌نامه",
		lead: "کلیات پژوهش: بیان مسئله، اهمیت، اهداف، سوال‌ها و تعریف متغیرها را منسجم می‌کنیم.",
		features: [
			"تبدیل پروپوزال به فصل اول",
			"انسجام مسئله و اهداف",
			"تعاریف مفهومی و عملیاتی"
		]
	},
	"chapter-two": {
		slug: "chapter-two",
		eyebrow: "نگارش فصل‌ها",
		title: "نگارش فصل دوم پایان‌نامه",
		lead: "مرور ادبیات هدفمند، دسته‌بندی نظریه‌ها و استخراج چارچوب مفهومی.",
		features: [
			"پیشینه داخلی و خارجی",
			"نقد منابع",
			"مدل مفهومی"
		]
	},
	"chapter-three": {
		slug: "chapter-three",
		eyebrow: "نگارش فصل‌ها",
		title: "نگارش فصل سوم پایان‌نامه",
		lead: "روش تحقیق، جامعه، نمونه، ابزار و روایی-پایایی را شفاف و قابل دفاع می‌نویسیم.",
		features: [
			"طرح پژوهش",
			"نمونه‌گیری",
			"ابزار اندازه‌گیری"
		]
	},
	"chapter-four": {
		slug: "chapter-four",
		eyebrow: "نگارش فصل‌ها",
		title: "نگارش فصل چهارم پایان‌نامه",
		lead: "تجزیه و تحلیل داده با جداول، نمودار و تفسیر متناسب با سوال‌های پژوهش.",
		features: [
			"آمار توصیفی",
			"آزمون فرضیه‌ها",
			"تفسیر فارسی خروجی"
		]
	},
	"chapter-five": {
		slug: "chapter-five",
		eyebrow: "نگارش فصل‌ها",
		title: "نگارش فصل پنجم پایان‌نامه",
		lead: "بحث، نتیجه‌گیری، پیشنهادها و محدودیت‌ها را به یافته‌ها گره می‌زنیم.",
		features: [
			"بحث یافته‌ها",
			"پیشنهادهای کاربردی",
			"پیشنهاد پژوهش‌های بعدی"
		]
	},
	humanities: {
		slug: "humanities",
		eyebrow: "بر اساس رشته",
		title: "مشاوره انجام پایان‌نامه علوم انسانی",
		lead: "مدیریت، روانشناسی، حقوق، علوم اجتماعی و رشته‌های وابسته با روش کمی، کیفی یا ترکیبی.",
		features: [
			"روش متناسب با پارادایم",
			"منابع فارسی و انگلیسی",
			"ویرایش علمی"
		]
	},
	engineering: {
		slug: "engineering",
		eyebrow: "بر اساس رشته",
		title: "مشاوره انجام پایان‌نامه فنی و مهندسی",
		lead: "پروژه‌های مهندسی با مدل‌سازی، شبیه‌سازی، داده آزمایشگاهی یا مطالعه موردی.",
		features: [
			"روش عددی و آزمایشگاهی",
			"نرم‌افزارهای تخصصی",
			"گزارش فنی استاندارد"
		]
	},
	"pure-science": {
		slug: "pure-science",
		eyebrow: "بر اساس رشته",
		title: "مشاوره انجام پایان‌نامه علوم پایه",
		lead: "پژوهش‌های علوم پایه با تأکید بر دقت روش، داده و استناد علمی.",
		features: [
			"طراحی آزمایش",
			"تحلیل داده",
			"نگارش علمی"
		]
	},
	"medical-health": {
		slug: "medical-health",
		eyebrow: "بر اساس رشته",
		title: "مشاوره انجام پایان‌نامه علوم پزشکی و سلامت",
		lead: "مطالعات بالینی، اپیدمیولوژی و سلامت با رعایت اخلاق پژوهش و تحلیل زیست‌آمار.",
		features: [
			"زیست‌آمار",
			"پرسشنامه و مقیاس",
			"گزارش مطابق قالب دانشگاه علوم پزشکی"
		]
	},
	"art-architecture-media": {
		slug: "art-architecture-media",
		eyebrow: "بر اساس رشته",
		title: "مشاوره انجام پایان‌نامه هنر، معماری و رسانه",
		lead: "پژوهش‌های طراحی‌محور، کیفی و بین‌رشته‌ای در هنر و معماری.",
		features: [
			"مطالعه موردی",
			"تحلیل محتوا",
			"مستندسازی فرآیند طراحی"
		]
	},
	"agriculture-natural-resources": {
		slug: "agriculture-natural-resources",
		eyebrow: "بر اساس رشته",
		title: "کشاورزی و منابع طبیعی",
		lead: "پژوهش‌های مزرعه‌ای، آزمایشگاهی و مدل‌سازی در کشاورزی و منابع طبیعی.",
		features: [
			"طرح آزمایشی",
			"تحلیل واریانس",
			"گزارش علمی"
		]
	},
	phd: {
		slug: "phd",
		eyebrow: "سایر",
		title: "انجام رساله دکتری",
		lead: "همراهی عمیق‌تر برای نوآوری نظری، روش پیشرفته و دفاع در کمیته رساله.",
		features: [
			"شکاف دانش اصیل",
			"روش پیشرفته",
			"آمادگی جلسه دفاع"
		]
	},
	international: {
		slug: "international",
		eyebrow: "سایر",
		title: "نگارش پایان‌نامه بین‌المللی",
		lead: "نگارش و ویرایش علمی انگلیسی برای دانشجویان خارج از کشور.",
		features: [
			"نگارش آکادمیک انگلیسی",
			"استاندارد ژورنال",
			"ویرایش native-like"
		]
	},
	interdisciplinary: {
		slug: "interdisciplinary",
		eyebrow: "سایر",
		title: "علوم بین‌رشته‌ای و کاربردی",
		lead: "پروژه‌هایی که چند حوزه را ترکیب می‌کنند و نیاز به چارچوب ترکیبی دارند.",
		features: [
			"چارچوب ترکیبی",
			"روش مختلط",
			"انسجام میان‌رشته‌ای"
		]
	}
};
export const PROPOSAL_PAGES: Record<string, PageBlock> = {
	phd: {
		slug: "phd",
		eyebrow: "پروپوزال",
		title: "مشاوره انجام پروپوزال دکتری",
		lead: "پیشنهاد پژوهشی با نوآوری مشخص، پیشینه عمیق و روش قابل دفاع در کمیته.",
		features: [
			"نوآوری و سهم دانش",
			"مدل مفهومی",
			"زمان‌بندی اجرایی"
		]
	},
	project: {
		slug: "project",
		eyebrow: "پروپوزال",
		title: "پروپوزال کلاسی درس روش تحقیق",
		lead: "قالب کوتاه کلاسی با بیان مسئله، اهداف و روش متناسب با نمره درس.",
		features: [
			"قالب استاد درس",
			"منابع کافی",
			"روش ساده و روشن"
		]
	},
	english: {
		slug: "english",
		eyebrow: "پروپوزال",
		title: "مشاوره انجام پروپوزال انگلیسی",
		lead: "نگارش پروپوزال به زبان انگلیسی با ساختار دانشگاه‌های خارج از کشور.",
		features: [
			"Academic English",
			"ساختار IMRaD",
			"ویرایش علمی"
		]
	},
	qualitative: {
		slug: "qualitative",
		eyebrow: "پروپوزال",
		title: "نگارش پروپوزال پژوهش کیفی",
		lead: "پدیدارشناسی، گراندد تئوری، تحلیل مضمون و سایر طرح‌های کیفی.",
		features: [
			"سوال کیفی",
			"نمونه‌گیری هدفمند",
			"معیار اعتمادپذیری"
		]
	},
	quantitative: {
		slug: "quantitative",
		eyebrow: "پروپوزال",
		title: "نگارش پروپوزال پژوهش کمی",
		lead: "فرضیه‌ها، متغیرها، ابزار و آزمون آماری از ابتدا هماهنگ می‌شوند.",
		features: [
			"مدل و فرضیه",
			"حجم نمونه",
			"آزمون پیشنهادی"
		]
	},
	"applied-research": {
		slug: "applied-research",
		eyebrow: "پروپوزال",
		title: "پروپوزال تحقیق کاربردی",
		lead: "مسئله سازمانی یا صنعتی با خروجی تصمیم‌یار و روش اجرایی.",
		features: [
			"مسئله واقعی",
			"مدل کاربردی",
			"پیشنهاد عملی"
		]
	},
	medical: {
		slug: "medical",
		eyebrow: "پروپوزال",
		title: "نگارش پروپوزال پزشکی و طرح تحقیقاتی",
		lead: "طرح‌های بالینی و سلامت با ملاحظات اخلاق و زیست‌آمار.",
		features: [
			"پروتکل پژوهش",
			"حجم نمونه",
			"اخلاق پژوهش"
		]
	}
};
export const STATIC_PAGES: Record<string, { eyebrow: string; title: string; lead: string; sections: { title: string; body: string }[] }> = {
	about: {
		eyebrow: "درباره ما",
		title: "تزنویسه؛ همراه پژوهشی از ایده تا دفاع",
		lead: "مسیری شفاف، تخصصی و محرمانه برای دانشجویان و پژوهشگران.",
		sections: [
			{
				title: "چگونه تزنویسه شکل گرفت؟",
				body: "تزنویسه از نیاز واقعی دانشجویان به همراهی علمی منظم متولد شد؛ جایی که انتخاب موضوع، نگارش، تحلیل و آمادگی دفاع در یک مسیر واحد و قابل اتکا قرار بگیرد. تمرکز ما بر کیفیت علمی، شفافیت فرآیند و احترام به محرمانگی است."
			},
			{
				title: "مأموریت",
				body: "ارائه خدمات پژوهشی استاندارد با راهنمایی تخصصی، زمان‌بندی شفاف و پشتیبانی مستمر تا رسیدن به نتیجه قابل دفاع."
			},
			{
				title: "چشم‌انداز",
				body: "تبدیل شدن به مرجع قابل اعتماد همراهی پژوهشی برای دانشجویان فارسی‌زبان در مسیر پایان‌نامه و پروپوزال."
			}
		]
	},
	team: {
		eyebrow: "تیم",
		title: "تیم پژوهشگران تزنویسه",
		lead: "ترکیبی از مشاوران پژوهشی، تحلیل‌گران آماری و ویراستاران علمی.",
		sections: [{
			title: "چگونه پژوهشگر مناسب انتخاب می‌شود؟",
			body: "رشته، مقطع، روش تحقیق و مرحله فعلی پروژه بررسی می‌شود تا پژوهشگر با تجربه نزدیک به موضوع شما همراه شود."
		}]
	},
	careers: {
		eyebrow: "همکاری",
		title: "موقعیت‌های شغلی",
		lead: "اگر در نگارش علمی، آمار یا مشاوره پژوهشی تجربه دارید، به تیم تزنویسه بپیوندید.",
		sections: [{
			title: "چه کسانی را می‌جوییم؟",
			body: "پژوهشگران ارشد و دکتری با سابقه نگارش دانشگاهی، تحلیل‌گران SPSS/R/Python و ویراستاران علمی فارسی و انگلیسی."
		}]
	},
	"join-us": {
		eyebrow: "همکاری",
		title: "همکاری با تزنویسه",
		lead: "شبکه پژوهشگران ما در رشته‌های مختلف در حال گسترش است.",
		sections: [{
			title: "مسیر همکاری",
			body: "رزومه، نمونه کار و حوزه تخصصی خود را از صفحه تماس یا ثبت درخواست ارسال کنید تا بررسی اولیه انجام شود."
		}]
	},
	"our-story": {
		eyebrow: "داستان ما",
		title: "مسیر شکل‌گیری تزنویسه",
		lead: "از مشاوره موضوع تا یک مسیر کامل پژوهشی.",
		sections: [{
			title: "خط زمانی",
			body: "۱۳۹۸ شروع مشاوره تخصصی، ۱۴۰۰ گسترش خدمات آماری، ۱۴۰۲ پوشش مسیر کامل پایان‌نامه، ۱۴۰۴ ابزارهای آنلاین و منابع رایگان."
		}]
	},
	achievements: {
		eyebrow: "افتخارات",
		title: "کارنامه‌ای ساخته‌شده با اعتماد متقاضیان",
		lead: "آمار ما بر اساس پروژه‌های واقعی و قابل پیگیری است.",
		sections: [{
			title: "آنچه به آن افتخار می‌کنیم",
			body: "هزاران پروژه در علوم انسانی، مهندسی، پزشکی و علوم پایه؛ رضایت متقاضیان و انتشار بخشی از پژوهش‌ها در ژورنال‌های معتبر."
		}]
	},
	testimonials: {
		eyebrow: "بازخورد",
		title: "نظرات و رضایت متقاضیان",
		lead: "صدای کسانی که مسیر پژوهشی خود را به تزنویسه سپرده‌اند.",
		sections: [{
			title: "آنچه معمولاً می‌شنویم",
			body: "شفافیت زمان‌بندی، پاسخ‌گویی منظم و قابل دفاع بودن خروجی در جلسه استاد راهنما، پرتکرارترین نکته‌های بازخورد هستند."
		}]
	},
	"case-studies": {
		eyebrow: "مطالعات موردی",
		title: "نمونه‌هایی از مسیر پروژه‌ها",
		lead: "بدون افشای هویت، منطق کار روی چند نوع پروژه را نشان می‌دهیم.",
		sections: [{
			title: "رویکرد",
			body: "هر مطالعه موردی مسئله، روش، چالش و نتیجه را خلاصه می‌کند تا ببینید مسیر کار چگونه پیش می‌رود."
		}]
	},
	privacy: {
		eyebrow: "حریم خصوصی",
		title: "سیاست حریم خصوصی و امنیت داده‌ها",
		lead: "فایل‌ها و اطلاعات هویتی شما فقط برای بررسی و انجام درخواست استفاده می‌شود.",
		sections: [{
			title: "چه داده‌ای جمع می‌کنیم؟",
			body: "نام، شماره تماس، رشته، مقطع و فایل‌های پژوهشی که خودتان ارسال می‌کنید. از این داده‌ها برای مشاوره، برآورد و اجرای پروژه استفاده می‌شود."
		}, {
			title: "نگهداری و دسترسی",
			body: "دسترسی به فایل پروژه محدود به پژوهشگر مسئول و هماهنگی داخلی است. اطلاعات با اشخاص ثالث فروخته نمی‌شود."
		}]
	}
};

export type ToolTier = "free" | "pro";
export type ToolKind = "calc" | "ai";
export type ToolDef = {
  slug: string;
  title: string;
  text: string;
  tier: ToolTier;
  kind: ToolKind;
  group: string;
};

export const TOOLS: ToolDef[] = [
  { slug: "descriptive-statistics", title: "آمار توصیفی", text: "میانگین، میانه، انحراف معیار، چارک‌ها، چولگی و نمودار توزیع.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "sample-size", title: "حجم نمونه", text: "برآورد n با فرمول کوکران و مفروضات سطح اطمینان.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "cronbachs-alpha", title: "آلفای کرونباخ", text: "پایایی پرسشنامه از روی واریانس گویه‌ها.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "pearson-correlation", title: "همبستگی پیرسون", text: "ضریب r و تفسیر شدت رابطه دو متغیر کمی.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "spearman", title: "همبستگی اسپیرمن", text: "همبستگی رتبه‌ای برای داده‌های غیرنرمال یا ترتیبی.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "t-test", title: "آزمون t", text: "t تک‌نمونه‌ای، مستقل و زوجی با تفسیر معناداری.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "regression", title: "رگرسیون", text: "شیب، عرض از مبدأ و R² برای پیش‌بین کمی.", tier: "free", kind: "calc", group: "آمار پایه" },
  { slug: "anova", title: "تحلیل واریانس", text: "مقایسه میانگین چند گروه و آماره F.", tier: "free", kind: "calc", group: "آمار پیشرفته" },
  { slug: "chi-square", title: "خی‌دو", text: "آزمون استقلال در جدول توافقی.", tier: "free", kind: "calc", group: "آمار پیشرفته" },
  { slug: "power-analysis", title: "توان آزمون", text: "برآورد حجم نمونه برای اختلاف دو میانگین.", tier: "free", kind: "calc", group: "آمار پیشرفته" },
  { slug: "content-validity", title: "روایی محتوا", text: "محاسبه CVR و CVI بر اساس نظر متخصصان.", tier: "free", kind: "calc", group: "روایی و پایایی" },
  { slug: "kr20", title: "KR-20 / KR-21", text: "پایایی آزمون‌های دوحالتی صحیح/غلط.", tier: "free", kind: "calc", group: "روایی و پایایی" },
  { slug: "cohens-kappa", title: "کاپای کوهن", text: "توافق بین دو ارزیاب برای داده‌های رده‌ای.", tier: "free", kind: "calc", group: "روایی و پایایی" },
  { slug: "icc", title: "ICC", text: "همبستگی درون‌کلاسی برای اندازه‌گیری‌های تکراری.", tier: "free", kind: "calc", group: "روایی و پایایی" },
  { slug: "mann-whitney", title: "من-ویتنی", text: "آزمون ناپارامتری دو گروه مستقل.", tier: "free", kind: "calc", group: "ناپارامتری" },
  { slug: "wilcoxon", title: "ویلکاکسون", text: "آزمون ناپارامتری زوجی پیش‌آزمون و پس‌آزمون.", tier: "free", kind: "calc", group: "ناپارامتری" },
  { slug: "kruskal-wallis", title: "کروسکال-والیس", text: "جایگزین ناپارامتری ANOVA برای چند گروه.", tier: "free", kind: "calc", group: "ناپارامتری" },
  { slug: "goodness-of-fit", title: "نیکویی برازش", text: "آزمون تطابق توزیع مشاهده‌شده با توزیع مورد انتظار.", tier: "free", kind: "calc", group: "آمار پیشرفته" },
  { slug: "price", title: "برآورد هزینه", text: "برآورد اولیه هزینه خدمت بر اساس مقطع و نوع پروژه.", tier: "free", kind: "calc", group: "دستیار پژوهشی" },
  { slug: "method-advisor", title: "مشاور روش تحقیق", text: "با هوش مصنوعی روش کمی، کیفی یا ترکیبی پیشنهاد بگیرید.", tier: "pro", kind: "ai", group: "دستیار پژوهشی" },
  { slug: "apa-citation", title: "ساخت ارجاع APA", text: "مشخصات منبع را بدهید؛ قالب APA ساخته می‌شود.", tier: "pro", kind: "ai", group: "دستیار پژوهشی" },
  { slug: "theme-extractor", title: "استخراج مضمون کیفی", text: "متن مصاحبه را بگذارید تا مضامین اولیه پیشنهاد شود.", tier: "pro", kind: "ai", group: "دستیار پژوهشی" },
];

export const TOOL_GROUPS = ["آمار پایه", "روایی و پایایی", "آمار پیشرفته", "ناپارامتری", "دستیار پژوهشی"] as const;

export const TOOL_ALIASES: Record<string, string> = {
  "descriptive-statistics-calculator": "descriptive-statistics",
  "sample-size-calculator": "sample-size",
  "cronbachs-alpha-calculator": "cronbachs-alpha",
  "pearson-correlation-calculator": "pearson-correlation",
  "spearman-correlation-calculator": "spearman",
  "spearman-correlation": "spearman",
  "t-test-calculator": "t-test",
  "regression-calculator": "regression",
  "anova-calculator": "anova",
  "chi-square-calculator": "chi-square",
  "power-analysis-calculator": "power-analysis",
  "content-validity-calculator": "content-validity",
  "kr20-kr21-calculator": "kr20",
  "kr20-kr21": "kr20",
  "cohens-kappa-calculator": "cohens-kappa",
  "icc-calculator": "icc",
  "mann-whitney-calculator": "mann-whitney",
  "wilcoxon-calculator": "wilcoxon",
  "kruskal-wallis-calculator": "kruskal-wallis",
  "goodness-of-fit-calculator": "goodness-of-fit",
  "price-calculator": "price",
};

export function resolveTool(slug: string) {
  const canon = TOOL_ALIASES[slug] || slug.replace(/-calculator$/, "");
  return TOOLS.find((t) => t.slug === canon || t.slug === slug) ?? null;
}

export function importedToolCopy(slug: string) {
  const keys = [
    slug,
    `${slug}-calculator`,
    ...Object.entries(TOOL_ALIASES)
      .filter(([, v]) => v === slug)
      .map(([k]) => k),
  ];
  for (const k of keys) {
    if (IMPORTED_TOOLS[k]) return IMPORTED_TOOLS[k];
  }
  return null;
}

export function toolPageCopy(slug: string) {
  const canon = TOOL_ALIASES[slug] || slug.replace(/-calculator$/, "");
  const imp = importedToolCopy(canon) || importedToolCopy(slug);
  const guide = TOOL_GUIDES[canon] || TOOL_GUIDES[slug];
  const rawFeatures = (guide?.features?.length ? guide.features : imp?.features) ?? [];
  const split = splitFaqs(rawFeatures);
  return {
    heroTitle: guide?.heroTitle || imp?.heroTitle || imp?.title || "",
    title: guide?.heroTitle || imp?.title || "",
    lead: guide?.lead || imp?.lead || "",
    features: split.features,
    faqs: guide?.faqs?.length ? guide.faqs : split.faqs,
    body: guide?.body ?? [],
    sections: guide?.sections ?? [],
    ctaTitle: guide?.ctaTitle,
    ctaText: guide?.ctaText,
  };
}

for (const t of TOOLS) {
  const copy = toolPageCopy(t.slug);
  if (copy.heroTitle) t.title = copy.heroTitle;
  if (copy.lead) t.text = copy.lead;
}

absorbImported(THESIS_PAGES, IMPORTED_THESIS);
absorbImported(PROPOSAL_PAGES, IMPORTED_PROPOSAL, ["hub"]);

if (IMPORTED_PROPOSAL.hub) {
  const hub = importedToPage({ ...IMPORTED_PROPOSAL.hub, slug: "proposal" });
  SERVICE_PAGES.proposal = {
    ...SERVICE_PAGES.proposal,
    ...hub,
    slug: "proposal",
    features: hub.features.length ? hub.features : SERVICE_PAGES.proposal.features,
    faqs: hub.faqs?.length ? hub.faqs : SERVICE_PAGES.proposal.faqs,
  };
}

if (!SERVICE_PAGES.thesis.faqs?.length && IMPORTED_THESIS.humanities) {
  SERVICE_PAGES.thesis.faqs = importedToPage(IMPORTED_THESIS.humanities).faqs;
}
if (!SERVICE_PAGES.statistics.faqs?.length && IMPORTED_TOOLS["online-calculation-tools"]) {
  SERVICE_PAGES.statistics.faqs = splitFaqs(IMPORTED_TOOLS["online-calculation-tools"].features).faqs;
}
