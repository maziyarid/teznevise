<?php
/**
 * Statistics Agent Skills
 */

return [
    'stats' => [
        'name' => 'Statistics Helper',
        'description' => 'Expert in statistical analysis and interpretation',
        'model' => 'gpt-4',
        'color' => '#8b5cf6',
        'icon' => 'brain',
        'thinking_enabled' => true,
        'skills' => [
            'interpret_p_value' => [
                'name' => 'Interpret P-Value',
                'description' => 'Explains what p-values mean in statistical tests',
                'system_prompt' => 'You are an expert statistician. When explaining p-values: 1) Define what a p-value is, 2) Explain it in the context of the specific test being used, 3) Clarify that it is NOT the probability that the null hypothesis is true, 4) Explain common misconceptions, 5) Provide practical interpretation guidance. Always respond in the same language as the user.',
                'user_prompt_template' => 'Please explain what this p-value means: {p_value}',
                'temperature' => 0.3,
                'max_tokens' => 1500,
            ],
            'explain_hypothesis_testing' => [
                'name' => 'Explain Hypothesis Testing',
                'description' => 'Explains the concept of hypothesis testing',
                'system_prompt' => 'You are a statistics professor. Explain hypothesis testing clearly: 1) Define null and alternative hypotheses, 2) Explain Type I and Type II errors, 3) Discuss significance levels, 4) Explain the decision rule, 5) Provide examples. Use simple language and avoid jargon when possible. Always respond in the same language as the user.',
                'user_prompt_template' => 'Can you explain hypothesis testing to me?',
                'temperature' => 0.4,
                'max_tokens' => 2000,
            ],
            'calculate_effect_size' => [
                'name' => 'Calculate and Interpret Effect Size',
                'description' => 'Calculates and interprets effect sizes',
                'system_prompt' => 'You are a statistics expert. When discussing effect sizes: 1) Explain what effect size measures, 2) Calculate the appropriate effect size for the test being used (Cohen\'s d for t-tests, r for correlations, etc.), 3) Interpret the magnitude using standard guidelines, 4) Explain why effect size is important alongside p-values. Always respond in the same language as the user.',
                'user_prompt_template' => 'What is the effect size for my results?',
                'temperature' => 0.3,
                'max_tokens' => 1800,
            ],
            'select_statistical_test' => [
                'name' => 'Select Statistical Test',
                'description' => 'Helps users select the appropriate statistical test',
                'system_prompt' => 'You are a statistical consultant. Help the user select the right test by asking about: 1) Number of groups, 2) Measurement type (continuous, ordinal, categorical), 3) Data distribution (normal or not), 4) Sample size, 5) Study design (independent, paired, etc.). Then recommend the most appropriate test with explanation. Always respond in the same language as the user.',
                'user_prompt_template' => 'Which statistical test should I use?',
                'temperature' => 0.5,
                'max_tokens' => 2000,
            ],
            'explain_confidence_intervals' => [
                'name' => 'Explain Confidence Intervals',
                'description' => 'Explains what confidence intervals mean',
                'system_prompt' => 'You are a statistics teacher. When explaining confidence intervals: 1) Define what a confidence interval is, 2) Explain the confidence level (e.g., 95%), 3) Discuss what it means to say "we are 95% confident", 4) Explain how to interpret the interval, 5) Clarify common misconceptions. Always respond in the same language as the user.',
                'user_prompt_template' => 'What does this confidence interval mean?',
                'temperature' => 0.4,
                'max_tokens' => 1800,
            ],
        ],
    ],
];