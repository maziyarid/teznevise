<?php
/**
 * Per-agent skill catalog. Seeded INSERT-if-missing into the skills table.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Named-agent skills. Keys are agent_id.
 *
 * @param string $agent_id Optional agent id; empty returns the full map.
 * @return array<string,array<int,array<string,mixed>>>|array<int,array<string,mixed>>
 */
function teznevise_core_agent_skills( $agent_id = '' ) {
	$catalog = array(
		'teznevise' => array(
			array(
				'skill_id'    => 'overview',
				'name'        => 'نمای کلی',
				'description' => 'نمای کلی ۸۰–۱۲۰ کلمه‌ای برای مطلب و SERP',
				'prompt'      => 'Write an 80–120 word Persian AI overview of the article. Cite the research brief as [n] only. Consulting voice. No ghostwriting.',
				'temperature' => 0.4,
				'max_tokens'  => 700,
			),
			array(
				'skill_id'    => 'synthesis',
				'name'        => 'جمع‌بندی مناظره',
				'description' => 'توافق، اختلاف، یک گام بعدی عملی',
				'prompt'      => 'Synthesize the panel: agreements, remaining disagreement, one practical next step. Persian. Consulting only.',
				'temperature' => 0.4,
				'max_tokens'  => 800,
			),
			array(
				'skill_id'    => 'consult_plan',
				'name'        => 'برنامه مشاوره',
				'description' => 'مسیر مشاوره و ابزار مناسب، نه نوشتن پایان‌نامه',
				'prompt'      => 'Propose a consulting plan and which Teznevise tools help. Never offer to write the thesis.',
				'temperature' => 0.45,
				'max_tokens'  => 800,
			),
		),
		'christina' => array(
			array(
				'skill_id'    => 'outline_chapter',
				'name'        => 'طرح فصل',
				'description' => 'ساختار فصل و جمله موضوع، بدون نگارش کامل',
				'prompt'      => 'Produce a chapter outline with H2/H3 and one topic sentence each. Never ghostwrite the chapter.',
				'temperature' => 0.5,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'academic_voice',
				'name'        => 'لحن دانشگاهی',
				'description' => 'ویرایش انسجام، کلیشه، صدای علمی',
				'prompt'      => 'Edit for academic voice and cohesion. Return revision notes plus at most one 80-word sample paragraph.',
				'temperature' => 0.5,
				'max_tokens'  => 800,
			),
			array(
				'skill_id'    => 'abstract_edit',
				'name'        => 'چکیده',
				'description' => 'ساختار چکیده و نقد، نه چکیده کامل سفارشی',
				'prompt'      => 'Diagnose the abstract (purpose, method, findings, implication). Offer a skeleton, not a finished ghostwritten abstract.',
				'temperature' => 0.45,
				'max_tokens'  => 700,
			),
		),
		'ada'       => array(
			array(
				'skill_id'    => 'stepwise_math',
				'name'        => 'ریاضی گام‌به‌گام',
				'description' => 'حل مرحله‌ای بدون اختراع عدد',
				'prompt'      => 'Show each algebraic step. Never invent measurements. Stop if data is missing.',
				'temperature' => 0.2,
				'max_tokens'  => 1000,
			),
			array(
				'skill_id'    => 'code_explain',
				'name'        => 'توضیح کد',
				'description' => 'الگوریتم، پایتون/متلب، بدون اجرای ادعایی',
				'prompt'      => 'Explain or sketch code/algorithms. Label assumptions. Do not claim you executed code on the user’s machine.',
				'temperature' => 0.25,
				'max_tokens'  => 1000,
			),
			array(
				'skill_id'    => 'figure_describe',
				'name'        => 'توصیف نمودار',
				'description' => 'عنوان، محور، آنچه شکل نشان می‌دهد — بدون تصویر ساختگی',
				'prompt'      => 'Describe 1–2 figures in text (title, axes, what it shows). No image URLs, no dummy data plots.',
				'temperature' => 0.3,
				'max_tokens'  => 700,
			),
		),
		'professor' => array(
			array(
				'skill_id'    => 'research_design',
				'name'        => 'طرح پژوهش',
				'description' => 'طرح، متغیرها، عملیاتی‌سازی',
				'prompt'      => 'Challenge design, variables, and operational definitions. Consulting structure only.',
				'temperature' => 0.4,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'proposal_structure',
				'name'        => 'ساختار پروپوزال',
				'description' => 'فهرست بخش‌های پروپوزال، نه متن آماده',
				'prompt'      => 'Give a proposal section map and what each section must answer. Never write the proposal.',
				'temperature' => 0.4,
				'max_tokens'  => 800,
			),
			array(
				'skill_id'    => 'validity_check',
				'name'        => 'روایی و پایایی',
				'description' => 'روایی محتوا/سازه، پایایی، تهدیدها',
				'prompt'      => 'Review validity and reliability threats. Suggest checks the student can run.',
				'temperature' => 0.35,
				'max_tokens'  => 800,
			),
		),
		'parantez'  => array(
			array(
				'skill_id'    => 'select_test',
				'name'        => 'انتخاب آزمون',
				'description' => 'آزمون مناسب با پیش‌فرض‌ها',
				'prompt'      => 'Recommend a statistical test only after stating scale, design, and assumptions. Never invent p-values.',
				'temperature' => 0.3,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'interpret_stats',
				'name'        => 'تفسیر نتایج',
				'description' => 'اثر، فاصله اطمینان، نه فقط معناداری',
				'prompt'      => 'Interpret effect size and confidence, not just p. If numbers are missing, ask; do not invent them.',
				'temperature' => 0.3,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'spss_plan',
				'name'        => 'مسیر SPSS/R',
				'description' => 'گام‌های نرم‌افزار به زبان مشاوره',
				'prompt'      => 'Give an SPSS or R click/code path at consulting level. No fabricated output tables.',
				'temperature' => 0.3,
				'max_tokens'  => 900,
			),
		),
		'elara'     => array(
			array(
				'skill_id'    => 'qual_design',
				'name'        => 'طرح کیفی',
				'description' => 'پدیدارشناسی، گراندد، تناسب سؤال',
				'prompt'      => 'Fit qualitative approach to the question. Never fabricate participant quotes.',
				'temperature' => 0.45,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'coding_scheme',
				'name'        => 'طرح کدگذاری',
				'description' => 'کد، تم، اعتمادپذیری',
				'prompt'      => 'Propose a coding scheme and trustworthiness checks (peer debrief, audit trail).',
				'temperature' => 0.45,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'ethics_review',
				'name'        => 'اخلاق پژوهش',
				'description' => 'رضایت، شناسایی‌پذیری، نقش دوگانه',
				'prompt'      => 'Flag consent, identifiability, and dual-role risks. Practical ethics, not legal advice.',
				'temperature' => 0.4,
				'max_tokens'  => 800,
			),
		),
		'cyrus'     => array(
			array(
				'skill_id'    => 'claim_reason',
				'name'        => 'ادعا و دلیل',
				'description' => 'ادعا، دلیل، رد، محدودیت',
				'prompt'      => 'Build claim–reason–rebuttal. Never invent statutes or case numbers.',
				'temperature' => 0.35,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'policy_brief',
				'name'        => 'یادداشت سیاستی',
				'description' => 'جدا کردن قانون از توصیه سیاستی',
				'prompt'      => 'Separate legal constraint from policy recommendation. Cite only the brief.',
				'temperature' => 0.35,
				'max_tokens'  => 800,
			),
			array(
				'skill_id'    => 'integrity_arg',
				'name'        => 'صداقت علمی',
				'description' => 'سرقت علمی، استناد، تعارض منافع',
				'prompt'      => 'Advise on academic integrity, citation, and conflict of interest. No accusations without evidence.',
				'temperature' => 0.35,
				'max_tokens'  => 800,
			),
		),
		'mira'      => array(
			array(
				'skill_id'    => 'clinical_safety',
				'name'        => 'ایمنی بالینی',
				'description' => 'شواهد و ایمنی؛ نه تشخیص و نه نسخه',
				'prompt'      => 'Discuss evidence and safety. Not a diagnosis or prescription. Never invent clinical data.',
				'temperature' => 0.25,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'stem_method',
				'name'        => 'روش STEM',
				'description' => 'طرح آزمایش/مهندسی و اندازه‌گیری',
				'prompt'      => 'Review STEM/engineering method, measurement, and error sources. No fabricated device specs.',
				'temperature' => 0.3,
				'max_tokens'  => 900,
			),
			array(
				'skill_id'    => 'evidence_grade',
				'name'        => 'درجه شواهد',
				'description' => 'سطح شواهد، سوگیری، تعمیم',
				'prompt'      => 'Grade the evidence (design, bias, generalisability) in plain Persian.',
				'temperature' => 0.3,
				'max_tokens'  => 800,
			),
		),
	);
	$catalog = apply_filters( 'teznevise_core_agent_skills', $catalog );
	$agent_id = sanitize_key( $agent_id );
	if ( '' === $agent_id ) {
		return $catalog;
	}
	return isset( $catalog[ $agent_id ] ) ? $catalog[ $agent_id ] : array();
}
