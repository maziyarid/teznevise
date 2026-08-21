/**
 * TezNevise AI Chat React Component
 * 
 * This file provides the complete AI chat interface that integrates with your WordPress theme
 */

// WordPress dependencies
const { useState, useRef, useEffect } = wp.element;
const { __ } = wp.i18n;
const { addQueryArgs } = wp.url;

// Main Chat Component
function TezNeviseAIChat({ toolId, agentId, collaborationMode, thinkingEnabled }) {
    const [messages, setMessages] = useState([
        {
            id: 'initial',
            role: 'assistant',
            content: (tezneviseAiConfig?.settings?.persian_initial_message || 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری'),
            agentName: 'Assistants',
            model: 'gpt-4',
            timestamp: new Date(),
        }
    ]);
    
    const [inputValue, setInputValue] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [selectedAgent, setSelectedAgent] = useState(null);
    const [showSettings, setShowSettings] = useState(false);
    const [usageStats, setUsageStats] = useState({
        today: 0,
        thisWeek: 0,
        thisMonth: 0,
        total: 0,
        credits: 0
    });
    
    const messagesEndRef = useRef(null);
    
    // Initialize with agent from props or default
    useEffect(() => {
        const defaultAgent = (tezneviseAiConfig?.agents || []).find(a => a.id === agentId) || 
                           (tezneviseAiConfig?.agents || [])[0] || 
                           { id: 'general', name: 'Assistants', model: 'gpt-4', color: '#3b82f6' };
        setSelectedAgent(defaultAgent);
        
        // Load usage stats
        loadUsageStats();
    }, []);
    
    // Scroll to bottom
    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);
    
    // Load usage statistics
    const loadUsageStats = async () => {
        try {
            const response = await wp.apiFetch({
                path: addQueryArgs('/teznevise-ai/v1/usage', {}),
                method: 'GET'
            });
            setUsageStats(response);
        } catch (error) {
            console.error('Failed to load usage stats:', error);
        }
    };
    
    // Check if user has enough credits
    const hasEnoughCredits = () => {
        const freeLimit = parseInt(tezneviseAiConfig?.settings?.free_tier_limit || 10);
        const signedLimit = parseInt(tezneviseAiConfig?.settings?.signed_in_limit || 100);
        
        if (tezneviseAiConfig?.isLoggedIn) {
            return usageStats.today < signedLimit;
        }
        return usageStats.today < freeLimit;
    };
    
    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!inputValue.trim() || isLoading) return;
        
        if (!hasEnoughCredits()) {
            const freeLimit = tezneviseAiConfig?.settings?.free_tier_limit || 10;
            const signedLimit = tezneviseAiConfig?.settings?.signed_in_limit || 100;
            const errorMessage = {
                id: `error-${Date.now()}`,
                role: 'assistant',
                content: tezneviseAiConfig?.isLoggedIn 
                    ? `You have used ${usageStats.today} of ${signedLimit} messages today. Please purchase more credits.`
                    : `Free tier limit reached (${freeLimit} messages). Please sign in or purchase credits.`,
                agentName: 'System',
                timestamp: new Date(),
            };
            setMessages(prev => [...prev, errorMessage]);
            return;
        }
        
        const userMessage = {
            id: `user-${Date.now()}`,
            role: 'user',
            content: inputValue.trim(),
            agentName: 'You',
            timestamp: new Date(),
        };
        
        setMessages(prev => [...prev, userMessage]);
        setInputValue('');
        setIsLoading(true);
        
        try {
            const response = await wp.apiFetch({
                path: addQueryArgs('/teznevise-ai/v1/chat', {}),
                method: 'POST',
                data: {
                    message: userMessage.content,
                    session_id: `session-${toolId || 'general'}-${Date.now()}`,
                    tool_id: toolId || '',
                    agent_id: selectedAgent?.id || agentId || 'general',
                    model: selectedAgent?.model || 'gpt-4',
                    collaboration_mode: collaborationMode || 'single',
                    thinking_enabled: thinkingEnabled !== 'false',
                },
                headers: {
                    'X-WP-Nonce': tezneviseAiConfig?.nonce || ''
                }
            });
            
            if (response?.success) {
                const assistantMessage = {
                    id: `assistant-${Date.now()}`,
                    role: 'assistant',
                    content: response.content,
                    agentName: response.agent_name || selectedAgent?.name || 'Assistants',
                    model: response.model || selectedAgent?.model || 'gpt-4',
                    thinkingProcess: response.thinking_process,
                    timestamp: new Date(),
                };
                setMessages(prev => [...prev, assistantMessage]);
                
                // Update usage stats
                if (response.usage) {
                    setUsageStats(prev => ({
                        ...prev,
                        ...response.usage
                    }));
                }
            } else {
                const errorMessage = {
                    id: `error-${Date.now()}`,
                    role: 'assistant',
                    content: response?.message || 'Sorry, I encountered an error. Please try again.',
                    agentName: 'System',
                    timestamp: new Date(),
                };
                setMessages(prev => [...prev, errorMessage]);
            }
        } catch (error) {
            const errorMessage = {
                id: `error-${Date.now()}`,
                role: 'assistant',
                content: 'Sorry, I encountered an error. Please try again.',
                agentName: 'System',
                timestamp: new Date(),
            };
            setMessages(prev => [...prev, errorMessage]);
        } finally {
            setIsLoading(false);
        }
    };
    
    // Format time for display
    const formatTime = (date) => {
        return date.toLocaleTimeString('fa-IR', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
    };
    
    // Clear chat
    const clearChat = () => {
        setMessages([
            {
                id: 'initial',
                role: 'assistant',
                content: tezneviseAiConfig?.settings?.persian_initial_message || 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
                agentName: 'Assistants',
                model: 'gpt-4',
                timestamp: new Date(),
            }
        ]);
    };
    
    // Change agent
    const handleAgentChange = (agentId) => {
        const agent = (tezneviseAiConfig?.agents || []).find(a => a.id === agentId);
        if (agent) {
            setSelectedAgent(agent);
        }
    };
    
    // Get remaining messages
    const getRemainingMessages = () => {
        const freeLimit = parseInt(tezneviseAiConfig?.settings?.free_tier_limit || 10);
        const signedLimit = parseInt(tezneviseAiConfig?.settings?.signed_in_limit || 100);
        const limit = tezneviseAiConfig?.isLoggedIn ? signedLimit : freeLimit;
        return Math.max(0, limit - usageStats.today);
    };
    
    // Get message count
    const getMessageCount = () => {
        return messages.filter(m => m.role !== 'system').length;
    };
    
    return (
        <div className="teznevise-ai-chat-container">
            <div className="ai-chat-card">
                {/* Header */}
                <div className="ai-chat-header">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                        <div style={{
                            width: '40px',
                            height: '40px',
                            background: selectedAgent?.color || '#3b82f6',
                            borderRadius: '50%',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'white',
                            fontWeight: 'bold',
                            flexShrink: 0
                        }}>
                            {selectedAgent?.name?.charAt(0) || 'A'}
                        </div>
                        <div>
                            <div style={{ fontWeight: '600', fontSize: '14px' }}>
                                {selectedAgent?.name || 'Assistants'}
                            </div>
                            <div style={{ fontSize: '12px', color: '#6b7280' }}>
                                {selectedAgent?.description || 'AI Assistant'}
                            </div>
                        </div>
                    </div>
                    
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <select
                            value={selectedAgent?.id || agentId || 'general'}
                            onChange={(e) => handleAgentChange(e.target.value)}
                            disabled={isLoading}
                            style={{ padding: '8px', borderRadius: '6px', border: '1px solid #d1d5db', fontSize: '14px' }}
                        >
                            {(tezneviseAiConfig?.agents || []).map(agent => (
                                <option key={agent.id} value={agent.id}>
                                    {agent.name}
                                </option>
                            ))}
                        </select>
                        
                        <button
                            onClick={() => setShowSettings(!showSettings)}
                            style={{ 
                                padding: '8px',
                                background: 'none',
                                border: 'none',
                                cursor: 'pointer',
                                borderRadius: '6px',
                                fontSize: '16px'
                            }}
                            title="Settings"
                        >
                            ⚙️
                        </button>
                    </div>
                </div>
                
                {/* Settings Panel */}
                {showSettings && (
                    <div style={{
                        padding: '16px 20px',
                        borderBottom: '1px solid #e5e7eb',
                        background: '#f9fafb'
                    }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
                            <span style={{ fontWeight: '600' }}>Settings</span>
                            <button
                                onClick={() => setShowSettings(false)}
                                style={{ background: 'none', border: 'none', cursor: 'pointer', fontSize: '18px' }}
                            >
                                ×
                            </button>
                        </div>
                        
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '12px', marginBottom: '12px' }}>
                            <div>
                                <label style={{ display: 'block', fontSize: '12px', marginBottom: '4px', fontWeight: '500' }}>
                                    Collaboration Mode
                                </label>
                                <select
                                    value={collaborationMode || 'single'}
                                    disabled
                                    style={{ width: '100%', padding: '6px', borderRadius: '4px', border: '1px solid #d1d5db' }}
                                >
                                    <option value="single">Single Agent</option>
                                    <option value="collaborative">Collaborative</option>
                                    <option value="separate">Separate with Reflections</option>
                                </select>
                            </div>
                            
                            <div>
                                <label style={{ display: 'block', fontSize: '12px', marginBottom: '4px', fontWeight: '500' }}>
                                    Thinking Process
                                </label>
                                <select
                                    value={thinkingEnabled !== 'false' ? 'enabled' : 'disabled'}
                                    disabled
                                    style={{ width: '100%', padding: '6px', borderRadius: '4px', border: '1px solid #d1d5db' }}
                                >
                                    <option value="enabled">Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style={{ display: 'flex', gap: '8px' }}>
                            <button
                                onClick={() => setShowSettings(false)}
                                style={{ 
                                    padding: '8px 16px',
                                    background: 'white',
                                    border: '1px solid #d1d5db',
                                    borderRadius: '6px',
                                    cursor: 'pointer',
                                    fontSize: '14px'
                                }}
                            >
                                Close
                            </button>
                        </div>
                    </div>
                )}
                
                {/* Usage Stats */}
                <div style={{
                    padding: '0 20px 12px 20px',
                    display: 'flex',
                    gap: '16px',
                    fontSize: '12px',
                    color: '#6b7280',
                    flexWrap: 'wrap'
                }}>
                    <span>
                        {tezneviseAiConfig?.isLoggedIn ? 'Premium' : 'Free'}: {usageStats.today}/{tezneviseAiConfig?.isLoggedIn ? (tezneviseAiConfig?.settings?.signed_in_limit || 100) : (tezneviseAiConfig?.settings?.free_tier_limit || 10)} today
                    </span>
                    <span>
                        💳 {usageStats.credits} credits
                    </span>
                    <span>
                        🤖 Model: {selectedAgent?.model || 'gpt-4'}
                    </span>
                </div>
                
                {/* Messages Area */}
                <div className="ai-chat-messages">
                    {messages.map((message) => (
                        <div 
                            key={message.id}
                            className={`ai-message ${message.role}`}
                            style={{
                                marginLeft: message.role === 'user' ? 'auto' : '0',
                                marginRight: message.role === 'user' ? '0' : 'auto',
                                background: message.role === 'user' ? '#3b82f6' : '#f3f4f6',
                                color: message.role === 'user' ? 'white' : '#1f2937',
                                padding: '12px 16px',
                                borderRadius: '12px',
                                maxWidth: '80%',
                                borderBottomLeftRadius: message.role === 'user' ? '12px' : '4px',
                                borderBottomRightRadius: message.role === 'user' ? '4px' : '12px',
                            }}
                        >
                            {/* Thinking Process */}
                            {message.thinkingProcess && message.role !== 'user' && (
                                <div className="ai-thinking-process" style={{
                                    background: '#e5e7eb',
                                    padding: '8px 12px',
                                    borderRadius: '8px',
                                    fontSize: '12px',
                                    color: '#4b5563',
                                    marginBottom: '8px',
                                    whiteSpace: 'pre-wrap',
                                    fontFamily: 'monospace'
                                }}>
                                    {message.thinkingProcess}
                                </div>
                            )}
                            
                            {/* Message Content */}
                            <div style={{ whiteSpace: 'pre-wrap', lineHeight: '1.5' }}>
                                {message.content}
                            </div>
                            
                            {/* Message Meta */}
                            <div style={{
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                marginTop: '8px',
                                fontSize: '11px',
                                opacity: 0.7
                            }}>
                                <span>{formatTime(message.timestamp)}</span>
                                
                                {message.role !== 'user' && message.agentName && (
                                    <span className="ai-message-agent-name">
                                        - {message.agentName}
                                        {message.model && (
                                            <span style={{ marginLeft: '8px', padding: '2px 6px', background: 'rgba(0,0,0,0.1)', borderRadius: '4px', fontSize: '10px' }}>
                                                {message.model}
                                            </span>
                                        )}
                                    </span>
                                )}
                                
                                {message.role === 'user' && (
                                    <span>You</span>
                                )}
                            </div>
                        </div>
                    ))}
                    
                    {isLoading && (
                        <div className="ai-message assistant" style={{
                            background: '#f3f4f6',
                            color: '#1f2937',
                            padding: '12px 16px',
                            borderRadius: '12px',
                            maxWidth: '80%',
                            marginRight: 'auto',
                            borderBottomLeftRadius: '4px'
                        }}>
                            <div style={{ display: 'flex', gap: '4px' }}>
                                <div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both' }}></div>
                                <div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both 0.2s' }}></div>
                                <div style={{ width: '8px', height: '8px', background: '#3b82f6', borderRadius: '50%', animation: 'bounce 1.4s infinite ease-in-out both 0.4s' }}></div>
                            </div>
                        </div>
                    )}
                    
                    <div ref={messagesEndRef} />
                </div>
                
                {/* Input Area */}
                <form onSubmit={handleSubmit} className="ai-chat-input">
                    <input
                        type="text"
                        value={inputValue}
                        onChange={(e) => setInputValue(e.target.value)}
                        placeholder={tezneviseAiConfig?.isLoggedIn 
                            ? `Message ${selectedAgent?.name || 'AI'}...` 
                            : 'Sign in for unlimited messages'
                        }
                        disabled={isLoading || !hasEnoughCredits()}
                        style={{ flex: 1 }}
                    />
                    <button
                        type="submit"
                        disabled={!inputValue.trim() || isLoading || !hasEnoughCredits()}
                        style={{
                            padding: '10px 16px',
                            background: '#3b82f6',
                            color: 'white',
                            border: 'none',
                            borderRadius: '8px',
                            cursor: 'pointer',
                            fontSize: '14px',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '4px'
                        }}
                    >
                        {isLoading ? '...' : '▶'}
                    </button>
                </form>
                
                {/* Footer */}
                <div style={{
                    padding: '12px 20px',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    fontSize: '12px',
                    color: '#6b7280',
                    borderTop: '1px solid #e5e7eb'
                }}>
                    <span>
                        {getMessageCount()} messages
                    </span>
                    <button
                        onClick={clearChat}
                        style={{ 
                            background: 'none',
                            border: 'none',
                            color: '#6b7280',
                            cursor: 'pointer',
                            fontSize: '12px'
                        }}
                    >
                        Clear chat
                    </button>
                </div>
            </div>
        </div>
    );
}

// Initialize the chat when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Find all chat containers
    const chatContainers = document.querySelectorAll('.teznevise-ai-chat-container');
    
    chatContainers.forEach(container => {
        const toolId = container.dataset.toolId || '';
        const agentId = container.dataset.agentId || 'general';
        const collaborationMode = container.dataset.collaborationMode || 'single';
        const thinkingEnabled = container.dataset.thinkingEnabled !== 'false';
        
        // Create a React root and render the chat
        if (typeof wp !== 'undefined' && wp.element && wp.element.createRoot) {
            const root = wp.element.createRoot(container);
            root.render(
                wp.element.createElement(TezNeviseAIChat, {
                    toolId,
                    agentId,
                    collaborationMode,
                    thinkingEnabled
                })
            );
        }
    });
});

// Add animation for loading dots
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
    .teznevise-ai-chat-container {
        max-width: 800px;
        margin: 20px auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .teznevise-ai-chat-container .ai-chat-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background: white;
    }
    
    .teznevise-ai-chat-container .ai-chat-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .teznevise-ai-chat-container .ai-chat-messages {
        height: 400px;
        overflow-y: auto;
        padding: 16px 20px;
    }
    
    .teznevise-ai-chat-container .ai-chat-input {
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
    }
    
    .teznevise-ai-chat-container .ai-chat-input input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .teznevise-ai-chat-container .ai-chat-input input:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }
    
    .teznevise-ai-chat-container .ai-chat-input button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    
    /* RTL Support for Persian */
    [dir="rtl"] .teznevise-ai-chat-container .ai-message.user {
        margin-right: auto;
        margin-left: 0;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 12px;
    }
    
    [dir="rtl"] .teznevise-ai-chat-container .ai-message.assistant {
        margin-left: auto;
        margin-right: 0;
        border-bottom-right-radius: 4px;
        border-bottom-left-radius: 12px;
    }
`;
document.head.appendChild(style);
