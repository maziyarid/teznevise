ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS university TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS field TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS degree TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS city TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS bio TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS referral_code TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS referred_by TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS profile_complete INTEGER NOT NULL DEFAULT 0;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS default_agent_id TEXT;
ALTER TABLE app_profiles ADD COLUMN IF NOT EXISTS bonus_granted INTEGER NOT NULL DEFAULT 0;

CREATE UNIQUE INDEX IF NOT EXISTS app_profiles_referral_idx ON app_profiles (referral_code);

CREATE TABLE IF NOT EXISTS wallets (
  user_id TEXT PRIMARY KEY,
  balance INTEGER NOT NULL DEFAULT 0,
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS wallet_ledger (
  id TEXT PRIMARY KEY,
  user_id TEXT NOT NULL,
  amount INTEGER NOT NULL,
  kind TEXT NOT NULL,
  reason TEXT NOT NULL,
  ref TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS wallet_ledger_user_idx ON wallet_ledger (user_id, created_at DESC);

CREATE TABLE IF NOT EXISTS coin_packs (
  id TEXT PRIMARY KEY,
  title TEXT NOT NULL,
  coins INTEGER NOT NULL,
  irr_price INTEGER NOT NULL,
  active INTEGER NOT NULL DEFAULT 1,
  sort INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS payments (
  id TEXT PRIMARY KEY,
  user_id TEXT NOT NULL,
  pack_id TEXT,
  gateway TEXT NOT NULL,
  amount_irr INTEGER NOT NULL,
  coins INTEGER NOT NULL,
  status TEXT NOT NULL DEFAULT 'pending',
  authority TEXT,
  ref_id TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  paid_at TIMESTAMPTZ
);
CREATE INDEX IF NOT EXISTS payments_user_idx ON payments (user_id, created_at DESC);
CREATE INDEX IF NOT EXISTS payments_auth_idx ON payments (authority);

CREATE TABLE IF NOT EXISTS share_rewards (
  id TEXT PRIMARY KEY,
  user_id TEXT NOT NULL,
  slug TEXT NOT NULL,
  network TEXT NOT NULL,
  coins INTEGER NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  UNIQUE (user_id, slug, network)
);

CREATE TABLE IF NOT EXISTS comment_rewards (
  user_id TEXT NOT NULL,
  post_slug TEXT NOT NULL,
  coins INTEGER NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  PRIMARY KEY (user_id, post_slug)
);

CREATE TABLE IF NOT EXISTS projects (
  id TEXT PRIMARY KEY,
  user_id TEXT NOT NULL,
  title TEXT NOT NULL,
  service TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'intake',
  progress INTEGER NOT NULL DEFAULT 5,
  due_at TEXT,
  notes TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS projects_user_idx ON projects (user_id, updated_at DESC);

CREATE TABLE IF NOT EXISTS project_events (
  id TEXT PRIMARY KEY,
  project_id TEXT NOT NULL,
  status TEXT NOT NULL,
  note TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS project_events_idx ON project_events (project_id, created_at ASC);

CREATE TABLE IF NOT EXISTS secure_vault (
  id TEXT PRIMARY KEY,
  ticket_id TEXT NOT NULL,
  user_id TEXT NOT NULL,
  filename TEXT NOT NULL,
  mime TEXT NOT NULL,
  byte_size INTEGER NOT NULL,
  payload TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS vault_ticket_idx ON secure_vault (ticket_id);

CREATE TABLE IF NOT EXISTS ai_agents (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  provider TEXT NOT NULL,
  model TEXT NOT NULL DEFAULT '',
  system_prompt TEXT NOT NULL DEFAULT '',
  active INTEGER NOT NULL DEFAULT 1,
  sort INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tool_skills (
  id TEXT PRIMARY KEY,
  tool_slug TEXT NOT NULL,
  title TEXT NOT NULL,
  body_md TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS tool_skills_slug_idx ON tool_skills (tool_slug);

CREATE TABLE IF NOT EXISTS search_log (
  id TEXT PRIMARY KEY,
  query TEXT NOT NULL,
  user_id TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS search_log_q_idx ON search_log (query);

CREATE TABLE IF NOT EXISTS bottom_nav_items (
  id TEXT PRIMARY KEY,
  label TEXT NOT NULL,
  href TEXT NOT NULL,
  icon TEXT NOT NULL DEFAULT 'home',
  sort INTEGER NOT NULL DEFAULT 0,
  active INTEGER NOT NULL DEFAULT 1
);

ALTER TABLE post_comments ADD COLUMN IF NOT EXISTS user_id TEXT;

INSERT INTO site_settings (key, value) VALUES
  ('tezcoin_irr', '1000'),
  ('profile_bonus', '1000'),
  ('comment_reward', '25'),
  ('share_reward', '40'),
  ('referral_bonus', '200'),
  ('zarinpal_merchant', ''),
  ('aqaye_pin', ''),
  ('gateway_mode', 'sandbox'),
  ('openrouter_key', ''),
  ('youcom_key', ''),
  ('tavily_key', ''),
  ('enamad_code', ''),
  ('samandehi_code', '')
ON CONFLICT (key) DO NOTHING;

INSERT INTO coin_packs (id, title, coins, irr_price, active, sort) VALUES
  ('starter', 'بسته آغاز', 500, 450000, 1, 1),
  ('plus', 'بسته پژوهش', 1500, 1200000, 1, 2),
  ('pro', 'بسته دفاع', 4000, 2800000, 1, 3)
ON CONFLICT (id) DO NOTHING;

INSERT INTO ai_agents (id, name, provider, model, system_prompt, active, sort) VALUES
  ('method', 'دستیار روش تحقیق', 'xai', 'grok-4.5', 'You are a Persian research-methods tutor for Teznevise. Answer in clear Persian. Never invent citations.', 1, 1),
  ('stats', 'آمارگر تزنویسه', 'openrouter', 'openai/gpt-4o-mini', 'You are a statistics tutor. Explain tests, assumptions and interpretation in Persian. Never invent p-values.', 1, 2),
  ('sources', 'کاشف منابع', 'tavily', '', 'Search academic-relevant web sources, then summarize in Persian with URLs.', 1, 3),
  ('you', 'پژوهشگر You', 'you', '', 'Use You.com results to answer research questions in Persian with source links.', 1, 4)
ON CONFLICT (id) DO NOTHING;

INSERT INTO bottom_nav_items (id, label, href, icon, sort, active) VALUES
  ('home', 'خانه', '/', 'home', 1, 1),
  ('tools', 'ابزارها', '/tools', 'tools', 2, 1),
  ('blog', 'بلاگ', '/blog', 'blog', 3, 1),
  ('phone', 'تماس', 'tel:+989302822091', 'phone', 4, 1)
ON CONFLICT (id) DO NOTHING;

