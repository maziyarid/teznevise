<?php
/**
 * T-Test AI Configuration
 */

return [
    'id' => 't-test',
    'name' => 'T-Test',
    'description' => 'Parametric test for comparing means',
    'default_agent' => 'math',
    'collaboration_mode' => 'single',
    'thinking_enabled' => true,
    'initial_message' => 'اگه نتونستی با ابزار T-Test کار کنی میتونی از من کمک بگیری',
    'free_tier_limit' => 10,
    'signed_in_limit' => 100,
    'cost_per_message' => 0.01,
    'recommended_agents' => ['math', 'stats', 'general'],
    'skills' => [
        'interpret_results' => [
            'name' => 'Interpret T-Test Results',
            'description' => 'Explains t-statistic, p-value, and confidence intervals',
            'prompt' => 'You are a statistics expert specializing in t-tests. When a user provides results from a t-test, explain: 1) What the t-statistic means, 2) How to interpret the p-value, 3) What the confidence interval tells us, 4) Practical implications. Always respond in Persian if the user writes in Persian, otherwise use English.',
            'temperature' => 0.4,
            'max_tokens' => 2000,
        ],
        'select_test_type' => [
            'name' => 'Select Test Type',
            'description' => 'Helps user choose between independent and paired t-test',
            'prompt' => 'You are a statistics consultant. Help the user determine whether they need an independent samples t-test or a paired samples t-test based on their study design. Ask clarifying questions if needed. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 1500,
        ],
        'check_assumptions' => [
            'name' => 'Check Assumptions',
            'description' => 'Guides user through checking t-test assumptions',
            'prompt' => 'You are a statistics expert. Guide the user through checking the assumptions of the t-test: 1) Normality of data, 2) Equality of variances, 3) Independence of observations. Explain how to check each assumption and what to do if assumptions are violated. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 2000,
        ],
    ],
    'context' => [
        'test_type' => 'parametric',
        'purpose' => 'compare means between groups',
        'types' => [
            'independent' => 'Compare two independent groups',
            'paired' => 'Compare the same group at two time points or under two conditions',
        ],
        'alternative' => 'Mann-Whitney U test (if data is not normal)',
        'assumptions' => [
            'Normal distribution of data',
            'Equal variances (for independent t-test)',
            'Independent observations',
            'Continuous data',
        ],
        'output' => [
            't-statistic' => 'Test statistic value',
            'p-value' => 'Probability of observing the data if null hypothesis is true',
            'confidence_interval' => 'Range of values likely to contain the true difference',
            'effect_size' => 'Cohen\'s d - measure of effect magnitude',
        ],
    ],
];