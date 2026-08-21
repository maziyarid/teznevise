/**
 * TezNevise AI Agents - Agent definitions
 */

const tezneviseAgents = {
    general: {
        id: 'general',
        name: 'Assistants',
        description: 'General purpose assistant for all tools',
        model: 'gpt-4',
        color: '#3b82f6',
        icon: 'brain',
        thinking_enabled: true,
        api_endpoint: 'https://api.openai.com/v1/chat/completions',
    },
    math: {
        id: 'math',
        name: 'Math Expert',
        description: 'Specialized in mathematical calculations and explanations',
        model: 'gpt-4',
        color: '#10b981',
        icon: 'sparkles',
        thinking_enabled: true,
        api_endpoint: 'https://api.openai.com/v1/chat/completions',
    },
    stats: {
        id: 'stats',
        name: 'Statistics Helper',
        description: 'Expert in statistical analysis and interpretation',
        model: 'gpt-4',
        color: '#8b5cf6',
        icon: 'brain',
        thinking_enabled: true,
        api_endpoint: 'https://api.openai.com/v1/chat/completions',
    },
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = tezneviseAgents;
}