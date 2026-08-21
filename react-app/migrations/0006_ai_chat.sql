ALTER TABLE ai_agents ADD COLUMN IF NOT EXISTS api_base TEXT NOT NULL DEFAULT '';
ALTER TABLE ai_agents ADD COLUMN IF NOT EXISTS api_key_setting TEXT NOT NULL DEFAULT '';

CREATE TABLE IF NOT EXISTS ai_threads (
  id TEXT PRIMARY KEY,
  user_id TEXT,
  tool TEXT NOT NULL DEFAULT '',
  title TEXT NOT NULL DEFAULT '',
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS ai_threads_user_idx ON ai_threads (user_id, updated_at DESC);

CREATE TABLE IF NOT EXISTS ai_thread_messages (
  id TEXT PRIMARY KEY,
  thread_id TEXT NOT NULL,
  role TEXT NOT NULL,
  agent_name TEXT NOT NULL DEFAULT '',
  thinking TEXT NOT NULL DEFAULT '',
  content TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS ai_thread_msg_idx ON ai_thread_messages (thread_id, created_at);

INSERT INTO site_settings (key, value) VALUES
  ('ai_guest_limit', '3'),
  ('ai_signed_in_limit', '20'),
  ('ai_cost_per_message', '5'),
  ('xai_api_key', ''),
  ('openai_api_key', ''),
  ('openai_api_base', 'https://api.openai.com/v1')
ON CONFLICT (key) DO NOTHING;
