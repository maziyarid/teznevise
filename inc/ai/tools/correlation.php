<?php
/**
 * Correlation Analysis AI Configuration
 */

return [
    'id' => 'correlation',
    'name' => 'Correlation Analysis',
    'description' => 'Measures the strength and direction of relationships between variables',
    'default_agent' => 'stats',
    'collaboration_mode' => 'collaborative',
    'thinking_enabled' => true,
    'initial_message' => 'اگه نتونستی با ابزار Correlation کار کنی میتونی از من کمک بگیری',
    'free_tier_limit' => 10,
    'signed_in_limit' => 100,
    'cost_per_message' => 0.01,
    'recommended_agents' => ['stats', 'math', 'general'],
    'skills' => [
        'interpret_correlation' => [
            'name' => 'Interpret Correlation Coefficient',
            'description' => 'Explains what the correlation coefficient means',
            'prompt' => 'You are a statistics expert specializing in correlation analysis. When a user provides a correlation coefficient (r), explain: 1) What the sign (+/-) means, 2) What the magnitude (0 to 1) means, 3) How to interpret the strength of the relationship, 4) The difference between correlation and causation. Always respond in Persian if the user writes in Persian, otherwise use English.',
            'temperature' => 0.4,
            'max_tokens' => 1800,
        ],
        'choose_test' => [
            'name' => 'Choose Correlation Test',
            'description' => 'Helps user choose between Pearson and Spearman correlation',
            'prompt' => 'You are a statistics consultant. Help the user choose between Pearson correlation (for linear relationships with normally distributed data) and Spearman correlation (for monotonic relationships or non-normal data). Explain the differences and when to use each. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 1600,
        ],
        'explain_significance' => [
            'name' => 'Explain Statistical Significance',
            'description' => 'Explains p-value in correlation analysis',
            'prompt' => 'You are a statistics teacher. Explain what the p-value means in the context of correlation analysis. Explain that it tests whether the observed correlation is significantly different from zero. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 1500,
        ],
    ],
    'context' => [
        'test_type' => 'descriptive',
        'purpose' => 'measure relationship strength and direction',
        'types' => [
            'pearson' => 'For linear relationships with normally distributed data',
            'spearman' => 'For monotonic relationships or non-normal data',
        ],
        'output' => [
            'correlation_coefficient' => 'r - ranges from -1 to 1, indicates strength and direction',
            'p-value' => 'Tests if correlation is significantly different from zero',
            'confidence_interval' => 'Range likely to contain the true correlation',
        ],
        'interpretation' => [
            '0.00-0.19' => 'Very weak',
            '0.20-0.39' => 'Weak',
            '0.40-0.59' => 'Moderate',
            '0.60-0.79' => 'Strong',
            '0.80-1.00' => 'Very strong',
        ],
    ],
];