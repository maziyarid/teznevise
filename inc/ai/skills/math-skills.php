<?php
/**
 * Math Expert Skills
 */

return [
    'math' => [
        'name' => 'Math Expert',
        'description' => 'Specialized in mathematical calculations and explanations',
        'model' => 'gpt-4',
        'color' => '#10b981',
        'icon' => 'sparkles',
        'thinking_enabled' => true,
        'skills' => [
            'solve_equations' => [
                'name' => 'Solve Mathematical Equations',
                'description' => 'Solves equations step by step',
                'system_prompt' => 'You are an expert mathematician. When solving equations: 1) Show all steps clearly, 2) Explain each step, 3) Provide the final answer, 4) Offer to explain any step in more detail if needed. For WordPress integration, use LaTeX format for equations when appropriate. Always respond in the same language as the user.',
                'user_prompt_template' => 'Please solve this equation: {equation}',
                'temperature' => 0.2,
                'max_tokens' => 2000,
            ],
            'explain_formulas' => [
                'name' => 'Explain Mathematical Formulas',
                'description' => 'Explains mathematical formulas and their components',
                'system_prompt' => 'You are a math teacher. When explaining formulas: 1) Describe what the formula calculates, 2) Explain each variable or component, 3) Provide examples of how to use it, 4) Discuss when it is appropriate to use. Use LaTeX format for mathematical notation. Always respond in the same language as the user.',
                'user_prompt_template' => 'Can you explain this formula: {formula}?',
                'temperature' => 0.3,
                'max_tokens' => 1800,
            ],
            'calculate_values' => [
                'name' => 'Calculate Numerical Values',
                'description' => 'Performs numerical calculations',
                'system_prompt' => 'You are a calculator. When performing calculations: 1) Show the calculation step by step, 2) Provide the final result, 3) Explain what the result means in context if possible. Always respond in the same language as the user.',
                'user_prompt_template' => 'Please calculate: {calculation}',
                'temperature' => 0.2,
                'max_tokens' => 1500,
            ],
            'proof_theorems' => [
                'name' => 'Proof Theorems',
                'description' => 'Provides proofs for mathematical theorems',
                'system_prompt' => 'You are a mathematician. When providing proofs: 1) State the theorem clearly, 2) Provide a step-by-step proof, 3) Justify each step, 4) Discuss the significance of the theorem, 5) Mention any assumptions or limitations. Use LaTeX format for mathematical notation. Always respond in the same language as the user.',
                'user_prompt_template' => 'Can you prove {theorem}?',
                'temperature' => 0.3,
                'max_tokens' => 2500,
            ],
        ],
    ],
];