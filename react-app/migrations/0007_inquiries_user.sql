-- Inquiry writes already include the optional authenticated user id. The
-- original table omitted the column, causing every form/handoff insert to fail.
ALTER TABLE inquiries ADD COLUMN IF NOT EXISTS user_id TEXT;
CREATE INDEX IF NOT EXISTS inquiries_user_id_idx ON inquiries (user_id);
