ALTER TABLE inquiries ADD COLUMN IF NOT EXISTS user_id TEXT;
CREATE INDEX IF NOT EXISTS inquiries_user_id_idx ON inquiries (user_id);
CREATE INDEX IF NOT EXISTS inquiries_created_at_idx ON inquiries (created_at DESC);

CREATE TABLE IF NOT EXISTS app_profiles (
  user_id TEXT PRIMARY KEY,
  role TEXT NOT NULL DEFAULT 'user',
  display_name TEXT,
  phone TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS tickets (
  id TEXT PRIMARY KEY,
  user_id TEXT NOT NULL,
  subject TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'open',
  priority TEXT NOT NULL DEFAULT 'normal',
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS tickets_user_id_idx ON tickets (user_id);
CREATE INDEX IF NOT EXISTS tickets_status_idx ON tickets (status);

CREATE TABLE IF NOT EXISTS ticket_messages (
  id TEXT PRIMARY KEY,
  ticket_id TEXT NOT NULL,
  user_id TEXT NOT NULL,
  body TEXT NOT NULL,
  is_staff INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS ticket_messages_ticket_idx ON ticket_messages (ticket_id);

CREATE TABLE IF NOT EXISTS page_views (
  id TEXT PRIMARY KEY,
  path TEXT NOT NULL,
  referrer TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS page_views_path_idx ON page_views (path);
CREATE INDEX IF NOT EXISTS page_views_created_at_idx ON page_views (created_at DESC);

CREATE TABLE IF NOT EXISTS click_events (
  id TEXT PRIMARY KEY,
  path TEXT NOT NULL,
  label TEXT NOT NULL,
  href TEXT,
  x INTEGER,
  y INTEGER,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS click_events_path_idx ON click_events (path);
CREATE INDEX IF NOT EXISTS click_events_label_idx ON click_events (label);

CREATE TABLE IF NOT EXISTS page_fields (
  slug TEXT PRIMARY KEY,
  eyebrow TEXT,
  title TEXT,
  lead TEXT,
  features TEXT,
  body TEXT,
  cta_text TEXT,
  cta_url TEXT,
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS site_settings (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL DEFAULT ''
);

INSERT INTO site_settings (key, value) VALUES
  ('ga_id', ''),
  ('clarity_id', ''),
  ('analytics_note', 'first-party')
ON CONFLICT (key) DO NOTHING;
