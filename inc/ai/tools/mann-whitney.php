<?php
/**
 * Mann-Whitney U Test AI Configuration
 */

return [
    'id' => 'mann-whitney',
    'name' => 'Mann-Whitney U Test',
    'description' => 'Non-parametric test for comparing two independent samples',
    'default_agent' => 'stats',
    'collaboration_mode' => 'single',
    'thinking_enabled' => true,
    'initial_message' => 'اگه نتونستی با ابزار Mann-Whitney کار کنی میتونی از من کمک بگیری',
    'free_tier_limit' => 10,
    'signed_in_limit' => 100,
    'cost_per_message' => 0.01,
    'recommended_agents' => ['stats', 'general'],
    'skills' => [
        'interpret_results' => [
            'name' => 'Interpret Mann-Whitney Results',
            'description' => 'Explains U statistic, p-value, and effect size',
            'prompt' => 'You are a statistics expert specializing in the Mann-Whitney U test. When a user provides results from this test, explain: 1) What the U statistic means, 2) How to interpret the p-value, 3) What the effect size indicates, 4) Practical implications of the results. Always respond in Persian if the user writes in Persian, otherwise use English.',
            'temperature' => 0.4,
            'max_tokens' => 2000,
        ],
        'explain_test' => [
            'name' => 'Explain Mann-Whitney Test',
            'description' => 'Explains what the Mann-Whitney test does',
            'prompt' => 'You are a statistics teacher. Explain the Mann-Whitney U test: what it is, when to use it, what assumptions it has, and how it differs from a t-test. Use simple language and examples. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 1800,
        ],
        'enter_data' => [
            'name' => 'Help Enter Data',
            'description' => 'Guides users through entering data',
            'prompt' => 'You are a helpful assistant. Guide the user through entering their data for the Mann-Whitney U test calculator. Explain what each field means and provide examples of correct data format. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.6,
            'max_tokens' => 1500,
        ],
    ],
    'context' => [
        'test_type' => 'non-parametric',
        'purpose' => 'compare two independent samples',
        'alternative' => 'independent samples t-test (if data is normal)',
        'assumptions' => [
            'Independent observations',
            'Ordinal or continuous data',
            'Data can be ranked',
        ],
        'output' => [
            'U statistic' => 'Test statistic value',
            'p-value' => 'Probability of observing the data if null hypothesis is true',
            'effect_size' => 'Measure of the magnitude of the difference',
        ],
    ],
];