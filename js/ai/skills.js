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
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = tezneviseSkills;
}