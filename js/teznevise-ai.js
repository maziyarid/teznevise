/**
 * TezNevise AI - Main entry point
 */

const { useState, useEffect, useRef, useCallback } = wp.element;
const { __ } = wp.i18n;
const { addQueryArgs } = wp.url;

const TezNeviseAIConfig = window.tezneviseAiConfig || {};

const TezNeviseAIUtils = {
    formatDate: (date) => new Date(date).toLocaleDateString('fa-IR'),
    formatTime: (date) => new Date(date).toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit', hour12: false }),
    generateId: () => 'id-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9),
    countTokens: (text) => Math.ceil(text.length / 4),
    getAgentColor: (agentId) => {
        const agents = TezNeviseAIConfig.agents || [];
        const agent = agents.find(a => a.id === agentId);
        return agent ? agent.color : '#3b82f6';
    },
    getAgentName: (agentId) => {
        const agents = TezNeviseAIConfig.agents || [];
        const agent = agents.find(a => a.id === agentId);
        return agent ? agent.name : 'Assistant';
    },
};

const TezNeviseAIAPI = {
    async sendMessage(data) {
        try {
            const response = await wp.apiFetch({
                path: addQueryArgs('/teznevise-ai/v1/chat', {}),
                method: 'POST',
                data: data,
                headers: { 'X-WP-Nonce': TezNeviseAIConfig.nonce || '', 'Content-Type': 'application/json' },
            });
            return response;
        } catch (error) { console.error('AI API Error:', error); throw error; }
    },
    async getSkills(agentId) {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/skills', { agent_id: agentId }), method: 'GET' });
            return response || [];
        } catch (error) { console.error('Failed to load skills:', error); return []; }
    },
    async getUsage(toolId) {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/usage', { tool_id: toolId }), method: 'GET' });
            return response || {};
        } catch (error) { console.error('Failed to load usage:', error); return {}; }
    },
    async startSession(data) {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/session/start', {}), method: 'POST', data: data, headers: { 'X-WP-Nonce': TezNeviseAIConfig.nonce || '' } });
            return response;
        } catch (error) { console.error('Failed to start session:', error); throw error; }
    },
    async getAgents() {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/agents', {}), method: 'GET' });
            return response || [];
        } catch (error) { console.error('Failed to load agents:', error); return []; }
    },
};

window.TezNeviseAIConfig = TezNeviseAIConfig;
window.TezNeviseAIUtils = TezNeviseAIUtils;
window.TezNeviseAIAPI = TezNeviseAIAPI;
