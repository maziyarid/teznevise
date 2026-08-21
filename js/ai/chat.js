<?php
/**
 * NOTE: This is a JavaScript file, not PHP. The .php extension was used in the canvas
 * but it should be .js for the actual file.
 */

/**
 * TezNevise AI Chat Component - Per Tool Version
 * 
 * This component loads tool-specific configuration and skills
 */

const { useState, useRef, useEffect } = wp.element;
const { __ } = wp.i18n;
const { addQueryArgs } = wp.url;

function TezNeviseAIChat({ toolId, agentId, collaborationMode, thinkingEnabled, toolConfig }) {
    const [messages, setMessages] = useState([
        {
            id: 'initial',
            role: 'assistant',
            content: toolConfig?.initial_message || tezneviseAiConfig?.settings?.persian_initial_message || 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
            agentName: toolConfig?.default_agent_name || 'Assistants',
            model: 'gpt-4',
            timestamp: new Date(),
            skillUsed: null,
        }
    ]);
    
    const [inputValue, setInputValue] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [selectedAgent, setSelectedAgent] = useState(null);
    const [selectedSkill, setSelectedSkill] = useState(null);
    const [showSettings, setShowSettings] = useState(false);
    const [showSkills, setShowSkills] = useState(false);
    const [usageStats, setUsageStats] = useState({ today: 0, thisWeek: 0, thisMonth: 0, total: 0, credits: 0 });
    const [availableAgents, setAvailableAgents] = useState([]);
    const [availableSkills, setAvailableSkills] = useState([]);
    const messagesEndRef = useRef(null);
    
    useEffect(() => {
        const toolAgents = toolConfig?.recommended_agents || [];
        const allAgents = tezneviseAiConfig?.agents || [];
        const filteredAgents = allAgents.filter(agent => toolAgents.includes(agent.id) || agent.id === agentId);
        setAvailableAgents(filteredAgents);
        const defaultAgent = filteredAgents.find(a => a.id === agentId) || filteredAgents[0] || { id: 'general', name: 'Assistants', model: 'gpt-4', color: '#3b82f6' };
        setSelectedAgent(defaultAgent);
        loadUsageStats();
        if (defaultAgent.id) loadSkills(defaultAgent.id);
    }, [toolId, agentId, toolConfig]);
    
    const loadSkills = async (agentId) => {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/skills', { agent_id: agentId }), method: 'GET' });
            setAvailableSkills(response || []);
        } catch (error) { console.error('Failed to load skills:', error); }
    };
    
    const loadUsageStats = async () => {
        try {
            const response = await wp.apiFetch({ path: addQueryArgs('/teznevise-ai/v1/usage', { tool_id: toolId }), method: 'GET' });
            setUsageStats(response);
        } catch (error) { console.error('Failed to load usage stats:', error); }
    };
    
    useEffect(() => { messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' }); }, [messages]);
    
    const hasEnoughCredits = () => {
        const freeLimit = toolConfig?.free_tier_limit || tezneviseAiConfig?.settings?.free_tier_limit || 10;
        const signedLimit = toolConfig?.signed_in_limit || tezneviseAiConfig?.settings?.signed_in_limit || 100;
        return tezneviseAiConfig?.isLoggedIn ? usageStats.today < signedLimit : usageStats.today < freeLimit;
    };
    
    const getRemainingMessages = () => {
        const freeLimit = toolConfig?.free_tier_limit || 10;
        const signedLimit = toolConfig?.signed_in_limit || 100;
        const limit = tezneviseAiConfig?.isLoggedIn ? signedLimit : freeLimit;
        return Math.max(0, limit - usageStats.today);
    };
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!inputValue.trim() || isLoading) return;
        if (!hasEnoughCredits()) {
            const freeLimit = toolConfig?.free_tier_limit || 10;
            const signedLimit = toolConfig?.signed_in_limit || 100;
            setMessages(prev => [...prev, {
                id: 'error-' + Date.now(),
                role: 'assistant',
                content: tezneviseAiConfig?.isLoggedIn
                    ? 'You have used ' + usageStats.today + ' of ' + signedLimit + ' messages today for this tool. Please purchase more credits.'
                    : 'Free tier limit reached (' + freeLimit + ' messages). Please sign in or purchase credits.',
                agentName: 'System',
                timestamp: new Date(),
            }]);
            return;
        }
        
        const userMessage = { id: 'user-' + Date.now(), role: 'user', content: inputValue.trim(), agentName: 'You', timestamp: new Date(), skillUsed: selectedSkill?.skill_id };
        setMessages(prev => [...prev, userMessage]);
        setInputValue('');
        setIsLoading(true);
        
        try {
            const response = await wp.apiFetch({
                path: addQueryArgs('/teznevise-ai/v1/chat', {}),
                method: 'POST',
                data: {
                    message: userMessage.content,
                    session_id: 'session-' + toolId + '-' + Date.now(),
                    tool_id: toolId,
                    agent_id: selectedAgent?.id || agentId || 'general',
                    model: selectedAgent?.model || 'gpt-4',
                    collaboration_mode: collaborationMode || toolConfig?.collaboration_mode || 'single',
                    thinking_enabled: thinkingEnabled !== 'false' && toolConfig?.thinking_enabled !== false,
                    skill_id: selectedSkill?.skill_id,
                },
                headers: { 'X-WP-Nonce': tezneviseAiConfig?.nonce || '' }
            });
            
            if (response?.success) {
                setMessages(prev => [...prev, {
                    id: 'assistant-' + Date.now(),
                    role: 'assistant',
                    content: response.content,
                    agentName: response.agent_name || selectedAgent?.name || 'Assistants',
                    model: response.model || selectedAgent?.model || 'gpt-4',
                    thinkingProcess: response.thinking_process,
                    timestamp: new Date(),
                    skillUsed: response.skill_used,
                }]);
                if (response.usage) setUsageStats(prev => ({ ...prev, ...response.usage }));
                setSelectedSkill(null);
            } else {
                setMessages(prev => [...prev, {
                    id: 'error-' + Date.now(),
                    role: 'assistant',
                    content: response?.message || 'Sorry, I encountered an error. Please try again.',
                    agentName: 'System',
                    timestamp: new Date(),
                }]);
            }
        } catch (error) {
            setMessages(prev => [...prev, {
                id: 'error-' + Date.now(),
                role: 'assistant',
                content: 'Sorry, I encountered an error. Please try again.',
                agentName: 'System',
                timestamp: new Date(),
            }]);
        } finally { setIsLoading(false); }
    };
    
    const handleAgentChange = (agentId) => {
        const agent = availableAgents.find(a => a.id === agentId);
        if (agent) { setSelectedAgent(agent); loadSkills(agent.id); setSelectedSkill(null); }
    };
    const handleSkillSelect = (skill) => { setSelectedSkill(skill); setShowSkills(false); };
    const clearChat = () => { setMessages([{ id: 'initial', role: 'assistant', content: toolConfig?.initial_message || tezneviseAiConfig?.settings?.persian_initial_message || 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری', agentName: toolConfig?.default_agent_name || 'Assistants', model: 'gpt-4', timestamp: new Date() }]); };
    const formatTime = (date) => date.toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit', hour12: false });
    const getMessageCount = () => messages.filter(m => m.role !== 'system').length;
    
    return (
        <div className="teznevise-ai-chat-container">
            <div className="ai-chat-card">
                <div className="ai-chat-header">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                        <div style={{ width: '40px', height: '40px', background: selectedAgent?.color || '#3b82f6', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'white', fontWeight: 'bold', flexShrink: 0 }}>{selectedAgent?.name?.charAt(0) || 'A'}</div>
                        <div><div style={{ fontWeight: '600', fontSize: '14px' }}>{selectedAgent?.name || 'Assistants'}</div><div style={{ fontSize: '12px', color: '#6b7280' }}>{toolConfig?.name || 'AI Assistant'}</div></div>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <select value={selectedAgent?.id || agentId || 'general'} onChange={(e) => handleAgentChange(e.target.value)} disabled={isLoading} style={{ padding: '8px', borderRadius: '6px', border: '1px solid #d1d5db', fontSize: '14px' }}>{availableAgents.map(agent => <option key={agent.id} value={agent.id}>{agent.name}</option>)}</select>
                        <button onClick={() => setShowSkills(!showSkills)} style={{ padding: '8px', background: 'none', border: 'none', cursor: 'pointer', borderRadius: '6px', fontSize: '16px' }} title="Skills">â¡</button>
                        <button onClick={() => setShowSettings(!showSettings)} style={{ padding: '8px', background: 'none', border: 'none', cursor: 'pointer', borderRadius: '6px', fontSize: '16px' }} title="Settings">âï¸</button>
                    </div>
                </div>
                {showSkills && availableSkills.length > 0 && <div style={{ padding: '16px 20px', borderBottom: '1px solid #e5e7eb', background: '#f9fafb', maxHeight: '200px', overflowY: 'auto' }}><div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}><span style={{ fontWeight: '600' }}>Select a Skill</span><button onClick={() => setShowSkills(false)} style={{ background: 'none', border: 'none', cursor: 'pointer', fontSize: '18px' }}>Ã</button></div><div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(250px, 1fr))', gap: '8px' }}>{availableSkills.map(skill => <button key={skill.skill_id} onClick={() => handleSkillSelect(skill)} style={{ padding: '8px 12px', border: selectedSkill?.skill_id === skill.skill_id ? '2px solid #3b82f6' : '1px solid #d1d5db', borderRadius: '6px', background: 'white', cursor: 'pointer', textAlign: 'left', fontSize: '14px' }}><div style={{ fontWeight: '500' }}>{skill.name}</div><div style={{ fontSize: '12px', color: '#6b7280' }}>{skill.description}</div></button>)}</div></div>}
                {showSettings && <div style={{ padding: '16px 20px', borderBottom: '1px solid #e5e7eb', background: '#f9fafb' }}><div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}><span style={{ fontWeight: '600' }}>Settings</span><button onClick={() => setShowSettings(false)} style={{ background: 'none', border: 'none', cursor: 'pointer', fontSize: '18px' }}>Ã</button></div><div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '12px', marginBottom: '12px' }}><div><label style={{ display: 'block', fontSize: '12px', marginBottom: '4px', fontWeight: '500' }}>Collaboration Mode</label><select value={collaborationMode || toolConfig?.collaboration_mode || 'single'} disabled style={{ width: '100%', padding: '6px', borderRadius: '4px', border: '1px solid #d1d5db' }}><option value="single">Single Agent</option><option value="collaborative">Collaborative</option><option value="separate">Separate with Reflections</option></select></div><div><label style={{ display: 'block', fontSize: '12px', marginBottom: '4px', fontWeight: '500' }}>Thinking Process</label><select value={(thinkingEnabled !== 'false' && toolConfig?.thinking_enabled !== false) ? 'enabled' : 'disabled'} disabled style={{ width: '100%', padding: '6px', borderRadius: '4px', border: '1px solid #d1d5db' }}><option value="enabled">Enabled</option><option value="disabled">Disabled</option></select></div></div><div style={{ display: 'flex', gap: '8px' }}><button onClick={() => setShowSettings(false)} style={{ padding: '8px 16px', background: 'white', border: '1px solid #d1d5db', borderRadius: '6px', cursor: 'pointer', fontSize: '14px' }}>Close</button></div></div>}
                {selectedSkill && <div style={{ padding: '8px 20px', background: '#e0f2fe', borderBottom: '1px solid #bae6fd', fontSize: '12px', color: '#0369a1' }}><strong>Skill:</strong> {selectedSkill.name} - {selectedSkill.description}<button onClick={() => setSelectedSkill(null)} style={{ marginLeft: '8px', background: 'none', border: 'none', cursor: 'pointer', color: '#0369a1' }}>Ã</button></div>}
                <div style={{ padding: '0 20px 12px 20px', display: 'flex', gap: '16px', fontSize: '12px', color: '#6b7280', flexWrap: 'wrap' }}><span>{tezneviseAiConfig?.isLoggedIn ? 'Premium' : 'Free'}: {usageStats.today}/{toolConfig?.signed_in_limit || tezneviseAiConfig?.settings?.signed_in_limit || 100} today</span><span>ð³ {usageStats.credits} credits</span><span>ð¤ Model: {selectedAgent?.model || 'gpt-4'}</span>{selectedSkill && <span>â¡ Skill: {selectedSkill.name}</span>}</div>
                <div className="ai-chat-messages">
                    {messages.map((message) => {
                        const isUser = message.role === 'user';
                        return <div key={message.id} className={"ai-message " + message.role} style={{ marginLeft: isUser ? 'auto' : '0', marginRight: isUser ? '0' : 'auto', background: isUser ? '#3b82f6' : '#f3f4f6', color: isUser ? 'white' : '#1f2937', padding: '12px 16px', borderRadius: '12px', maxWidth: '80%', borderBottomLeftRadius: isUser ? '12px' : '4px', borderBottomRightRadius: isUser ? '4px' : '12px' }}>
                            {message.thinkingProcess && message.role !== 'user' && <div className="ai-thinking-process" style={{ background: '#e5e7eb', padding: '8px 12px', borderRadius: '8px', fontSize: '12px', color: '#4b5563', marginBottom: '8px', whiteSpace: 'pre-wrap', fontFamily: 'monospace', direction: 'ltr', textAlign: 'left' }}><strong>Thinking Process:</strong>
{message.thinkingProcess}</div>}
                            {message.skillUsed && message.role !== 'user' && <div style={{ background: 'rgba(59, 130, 246, 0.1)', padding: '4px 8px', borderRadius: '4px', fontSize: '11px', color: '#1d4ed8', marginBottom: '8px', display: 'inline-block' }}>Skill: {message.skillUsed}</div>}
                            <div style={{ whiteSpace: 'pre-wrap', lineHeight: '1.5' }}>{message.content}</div>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '8px', fontSize: '11px', opacity: 0.7 }}><span>{formatTime(message.timestamp)}</span>{message.role !== 'user' && message.agentName && <span className="ai-message-agent-name">- {message.agentName}{message.model && <span style={{ marginLeft: '8px', padding: '2px 6px', background: 'rgba(0,0,0,0.1)', borderRadius: '4px', fontSize: '10px' }}>{message.model}</span>}</span>}{message.role === 'user' && <span>You</span>}</div>
                        </div>;
                    })}
                    {isLoading && <div className="ai-message assistant" style={{ background: '#f3f4f6', color: '#1f2937', padding: '12px 16px', borderRadius: '12px', maxWidth: '80%', marginRight: 'auto', borderBottomLeftRadius: '4px' }}><div style={{ display: 'flex', gap: '4px' }}><div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both' }}></div><div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both 0.2s' }}></div><div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both 0.4s' }}></div></div></div>}
                    <div ref={messagesEndRef} />
                </div>
                <form onSubmit={handleSubmit} className="ai-chat-input">
                    <input type="text" value={inputValue} onChange={(e) => setInputValue(e.target.value)} placeholder={tezneviseAiConfig?.isLoggedIn ? 'Message ' + (selectedAgent?.name || 'AI') + '...' : 'Sign in for unlimited messages'} disabled={isLoading || !hasEnoughCredits()} style={{ flex: 1 }} />
                    <button type="submit" disabled={!inputValue.trim() || isLoading || !hasEnoughCredits()} style={{ padding: '10px 16px', background: '#3b82f6', color: 'white', border: 'none', borderRadius: '8px', cursor: 'pointer', fontSize: '14px' }}>{isLoading ? '...' : (selectedSkill ? 'â¡' : 'â¶')}</button>
                </form>
                <div style={{ padding: '12px 20px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '12px', color: '#6b7280', borderTop: '1px solid #e5e7eb' }}>
                    <span>{getMessageCount()} messages | {getRemainingMessages()} remaining</span>
                    <button onClick={clearChat} style={{ background: 'none', border: 'none', color: '#6b7280', cursor: 'pointer', fontSize: '12px' }}>Clear chat</button>
                </div>
            </div>
        </div>
    );
}

document.addEventListener('DOMContentLoaded', function() {
    const chatContainers = document.querySelectorAll('.teznevise-ai-chat-wrapper');
    chatContainers.forEach(container => {
        const toolId = container.dataset.toolId || '';
        const agentId = container.dataset.agentId || 'general';
        const collaborationMode = container.dataset.collaborationMode || 'single';
        const thinkingEnabled = container.dataset.thinkingEnabled !== 'false';
        const toolConfig = container.dataset.toolConfig ? JSON.parse(container.dataset.toolConfig) : null;
        if (typeof wp !== 'undefined' && wp.element && wp.element.createRoot) {
            const root = wp.element.createRoot(container);
            root.render(wp.element.createElement(TezNeviseAIChat, { toolId, agentId, collaborationMode, thinkingEnabled, toolConfig }));
        }
    });
});
