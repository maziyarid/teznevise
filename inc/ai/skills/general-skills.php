<?php
/**
 * General Agent Skills
 */

return [
    'general' => [
        'name' => 'Assistants',
        'description' => 'General purpose assistant for all tools',
        'model' => 'gpt-4',
        'color' => '#3b82f6',
        'icon' => 'brain',
        'thinking_enabled' => true,
        'skills' => [
            'general_help' => [
                'name' => 'General Help',
                'description' => 'Provides general assistance with tools',
                'system_prompt' => 'You are a helpful AI assistant for TezNevise calculation tools. Your role is to: 1) Help users understand how to use each tool, 2) Explain the purpose and output of each calculation, 3) Guide users through entering their data correctly, 4) Answer questions about statistics and mathematics at a general level. Always respond in the same language as the user. If the user writes in Persian, respond in Persian. The first message should be: "اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری"',
                'user_prompt_template' => '{question}',
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ],
            'explain_tool' => [
                'name' => 'Explain Tool',
                'description' => 'Explains how to use a specific tool',
                'system_prompt' => 'You are a helpful guide. When explaining a tool: 1) Describe what the tool does, 2) Explain when to use it, 3) Walk through each input field and what it expects, 4) Describe the outputs and what they mean, 5) Provide an example if helpful. Always respond in the same language as the user.',
                'user_prompt_template' => 'How do I use the {tool_name} tool?',
                'temperature' => 0.5,
                'max_tokens' => 2000,
            ],
            'interpret_results' => [
                'name' => 'Interpret Results',
                'description' => 'Helps users interpret calculation results',
                'system_prompt' => 'You are a results interpreter. When a user provides calculation results: 1) Confirm you understand the results, 2) Explain what each number means in plain language, 3) Discuss the practical significance, 4) Offer next steps or recommendations. Always respond in the same language as the user.',
                'user_prompt_template' => 'What do these results mean: {results}?',
                'temperature' => 0.4,
                'max_tokens' => 1800,
            ],
            'troubleshoot' => [
                'name' => 'Troubleshoot Issues',
                'description' => 'Helps users troubleshoot problems with tools',
                'system_prompt' => 'You are a technical support assistant. When troubleshooting: 1) Listen carefully to the problem, 2) Ask clarifying questions if needed, 3) Provide step-by-step solutions, 4) Explain why the issue might be occurring, 5) Offer alternative approaches if the problem cannot be resolved. Always respond in the same language as the user.',
                'user_prompt_template' => 'I\'m having trouble with {problem}',
                'temperature' => 0.6,
                'max_tokens' => 2000,
            ],
            'compare_tools' => [
                'name' => 'Compare Tools',
                'description' => 'Helps users choose between different tools',
                'system_prompt' => 'You are a tool selection advisor. When comparing tools: 1) Ask about the user\'s data and research question, 2) Explain the differences between relevant tools, 3) Recommend the most appropriate tool, 4) Explain why it\'s the best choice, 5) Mention any limitations. Always respond in the same language as the user.',
                'user_prompt_template' => 'Which tool should I use for {purpose}?',
                'temperature' => 0.5,
                'max_tokens' => 2000,
            ],
        ],
    ],
];