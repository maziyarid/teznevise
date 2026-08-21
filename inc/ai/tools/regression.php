<?php
/**
 * Regression Analysis AI Configuration
 */

return [
    'id' => 'regression',
    'name' => 'Regression Analysis',
    'description' => 'Models relationships between dependent and independent variables',
    'default_agent' => 'math',
    'collaboration_mode' => 'separate',
    'thinking_enabled' => true,
    'initial_message' => 'اگه نتونستی با ابزار Regression کار کنی میتونی از من کمک بگیری',
    'free_tier_limit' => 10,
    'signed_in_limit' => 100,
    'cost_per_message' => 0.02,
    'recommended_agents' => ['math', 'stats', 'general'],
    'skills' => [
        'interpret_regression' => [
            'name' => 'Interpret Regression Results',
            'description' => 'Explains regression coefficients and model fit',
            'prompt' => 'You are a statistics and math expert specializing in regression analysis. When a user provides regression results, explain: 1) What each coefficient means (the change in the dependent variable for a one-unit change in the predictor), 2) What the R-squared value indicates about model fit, 3) How to interpret the p-values for each predictor, 4) Practical implications of the model. Always respond in Persian if the user writes in Persian, otherwise use English.',
            'temperature' => 0.4,
            'max_tokens' => 2500,
        ],
        'choose_model' => [
            'name' => 'Choose Regression Model',
            'description' => 'Helps user choose the right regression model',
            'prompt' => 'You are a statistical consultant. Help the user choose the appropriate regression model based on their data: 1) Simple linear regression for one predictor, 2) Multiple linear regression for multiple predictors, 3) Logistic regression for binary outcomes, 4) Other models as appropriate. Ask clarifying questions about their data and research question. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 2000,
        ],
        'check_assumptions' => [
            'name' => 'Check Regression Assumptions',
            'description' => 'Guides user through checking regression assumptions',
            'prompt' => 'You are a statistics expert. Guide the user through checking the assumptions of linear regression: 1) Linearity, 2) Independence of errors, 3) Homoscedasticity (constant variance), 4) Normality of residuals. Explain how to check each assumption and what to do if assumptions are violated. Respond in Persian if the user writes in Persian.',
            'temperature' => 0.5,
            'max_tokens' => 2200,
        ],
    ],
    'context' => [
        'test_type' => 'predictive',
        'purpose' => 'model and predict relationships between variables',
        'types' => [
            'simple_linear' => 'One independent variable',
            'multiple_linear' => 'Multiple independent variables',
            'logistic' => 'Binary dependent variable',
            'polynomial' => 'Non-linear relationships',
        ],
        'assumptions' => [
            'Linear relationship between predictors and outcome',
            'Independent errors',
            'Homoscedasticity (constant variance of errors)',
            'Normally distributed residuals',
            'No or little multicollinearity',
        ],
        'output' => [
            'coefficients' => 'Estimated effect of each predictor',
            'intercept' => 'Expected value when all predictors are zero',
            'r_squared' => 'Proportion of variance explained by the model',
            'adjusted_r_squared' => 'R-squared adjusted for number of predictors',
            'p_values' => 'Statistical significance of each predictor',
            'confidence_intervals' => 'Range of values likely to contain true coefficients',
        ],
    ],
];