/**
 * TezNevise AI Skills - Skill definitions
 */

const tezneviseSkills = {
    general: {
        general_help: {
            name: 'General Help',
            description: 'Provides general assistance',
            prompt: 'You are a helpful AI assistant. Answer questions clearly and concisely.',
            temperature: 0.7,
            max_tokens: 1500,
        },
        explain_concepts: {
            name: 'Explain Concepts',
            description: 'Explains statistical concepts',
            prompt: 'You are an expert in statistics. Explain concepts in simple terms with examples.',
            temperature: 0.5,
            max_tokens: 2000,
        },
    },
    math: {
        solve_equations: {
            name: 'Solve Equations',
            description: 'Solves mathematical equations',
            prompt: 'You are a math expert. Solve equations step by step showing all work.',
            temperature: 0.3,
            max_tokens: 2000,
        },
        calculate_values: {
            name: 'Calculate Values',
            description: 'Performs calculations',
            prompt: 'You are a calculator. Perform calculations accurately and show the steps.',
            temperature: 0.2,
            max_tokens: 1000,
        },
    },
    stats: {
        interpret_results: {
            name: 'Interpret Results',
            description: 'Interprets statistical results',
            prompt: 'You are a statistics expert. Interpret p-values, confidence intervals, and effect sizes for non-technical users.',
            temperature: 0.4,
            max_tokens: 1800,
        },
        select_tests: {
            name: 'Select Tests',
            description: 'Helps select statistical tests',
            prompt: 'You are a statistics consultant. Help users select the appropriate statistical test for their data and research question.',
            temperature: 0.5,
            max_tokens: 1500,
        },
    },
    teznevise: {
        overview: { name: 'نمای کلی', description: 'نمای کلی مطلب', prompt: 'Write an 80–120 word Persian AI overview.', temperature: 0.4, max_tokens: 700 },
        synthesis: { name: 'جمع‌بندی مناظره', description: 'توافق و گام بعدی', prompt: 'Synthesize the panel in Persian.', temperature: 0.4, max_tokens: 800 },
        consult_plan: { name: 'برنامه مشاوره', description: 'مسیر مشاوره و ابزار', prompt: 'Propose a consulting plan. Never write the thesis.', temperature: 0.45, max_tokens: 800 },
    },
    christina: {
        outline_chapter: { name: 'طرح فصل', description: 'ساختار فصل', prompt: 'Chapter outline only. Never ghostwrite.', temperature: 0.5, max_tokens: 900 },
        academic_voice: { name: 'لحن دانشگاهی', description: 'ویرایش انسجام', prompt: 'Edit academic voice. Sample ≤80 words.', temperature: 0.5, max_tokens: 800 },
        abstract_edit: { name: 'چکیده', description: 'ساختار چکیده', prompt: 'Diagnose the abstract. Do not ghostwrite it.', temperature: 0.45, max_tokens: 700 },
    },
    ada: {
        stepwise_math: { name: 'ریاضی گام‌به‌گام', description: 'حل مرحله‌ای', prompt: 'Show each step. Never invent numbers.', temperature: 0.2, max_tokens: 1000 },
        code_explain: { name: 'توضیح کد', description: 'الگوریتم', prompt: 'Explain code. Do not claim local execution.', temperature: 0.25, max_tokens: 1000 },
        figure_describe: { name: 'توصیف نمودار', description: 'عنوان و محور', prompt: 'Describe figures in text. No dummy images.', temperature: 0.3, max_tokens: 700 },
    },
    professor: {
        research_design: { name: 'طرح پژوهش', description: 'متغیرها و طرح', prompt: 'Challenge design and operational definitions.', temperature: 0.4, max_tokens: 900 },
        proposal_structure: { name: 'ساختار پروپوزال', description: 'نقشه بخش‌ها', prompt: 'Proposal map. Never write the proposal.', temperature: 0.4, max_tokens: 800 },
        validity_check: { name: 'روایی و پایایی', description: 'تهدیدهای روایی', prompt: 'Review validity and reliability threats.', temperature: 0.35, max_tokens: 800 },
    },
    parantez: {
        select_test: { name: 'انتخاب آزمون', description: 'آزمون و پیش‌فرض', prompt: 'Recommend a test after stating assumptions. No invented p-values.', temperature: 0.3, max_tokens: 900 },
        interpret_stats: { name: 'تفسیر نتایج', description: 'اثر و فاصله اطمینان', prompt: 'Interpret effect size. Do not invent numbers.', temperature: 0.3, max_tokens: 900 },
        spss_plan: { name: 'مسیر SPSS/R', description: 'گام نرم‌افزار', prompt: 'SPSS/R path. No fabricated output.', temperature: 0.3, max_tokens: 900 },
    },
    elara: {
        qual_design: { name: 'طرح کیفی', description: 'تناسب رویکرد', prompt: 'Fit qualitative approach. Never fabricate quotes.', temperature: 0.45, max_tokens: 900 },
        coding_scheme: { name: 'طرح کدگذاری', description: 'کد و تم', prompt: 'Propose a coding scheme and trustworthiness checks.', temperature: 0.45, max_tokens: 900 },
        ethics_review: { name: 'اخلاق پژوهش', description: 'رضایت آگاهانه', prompt: 'Flag consent and identifiability risks.', temperature: 0.4, max_tokens: 800 },
    },
    cyrus: {
        claim_reason: { name: 'ادعا و دلیل', description: 'استدلال حقوقی', prompt: 'Claim–reason–rebuttal. Never invent statutes.', temperature: 0.35, max_tokens: 900 },
        policy_brief: { name: 'یادداشت سیاستی', description: 'قانون در برابر توصیه', prompt: 'Separate law from policy recommendation.', temperature: 0.35, max_tokens: 800 },
        integrity_arg: { name: 'صداقت علمی', description: 'استناد و تعارض', prompt: 'Advise on academic integrity.', temperature: 0.35, max_tokens: 800 },
    },
    mira: {
        clinical_safety: {
            name: 'ایمنی بالینی',
            description: 'شواهد و ایمنی؛ نه تشخیص',
            prompt: 'Discuss evidence and safety. Not a diagnosis. Never invent clinical data.',
            temperature: 0.25,
            max_tokens: 900,
        },
        stem_method: {
            name: 'روش STEM',
            description: 'طرح آزمایش و اندازه‌گیری',
            prompt: 'Review STEM method and error sources. No fabricated specs.',
            temperature: 0.3,
            max_tokens: 900,
        },
        evidence_grade: {
            name: 'درجه شواهد',
            description: 'سطح شواهد و سوگیری',
            prompt: 'Grade the evidence in plain Persian.',
            temperature: 0.3,
            max_tokens: 800,
        },
    },
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = tezneviseSkills;
}