CREATE TABLE IF NOT EXISTS post_comments (
  id TEXT PRIMARY KEY,
  post_slug TEXT NOT NULL,
  name TEXT NOT NULL,
  phone TEXT NOT NULL,
  body TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'held',
  ai_reason TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS post_comments_slug_idx ON post_comments (post_slug, created_at DESC);
CREATE INDEX IF NOT EXISTS post_comments_status_idx ON post_comments (status);

CREATE TABLE IF NOT EXISTS ai_runs (
  id TEXT PRIMARY KEY,
  user_id TEXT,
  kind TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS ai_runs_user_idx ON ai_runs (user_id, created_at DESC);
