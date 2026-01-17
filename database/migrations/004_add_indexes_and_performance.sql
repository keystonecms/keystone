CREATE INDEX idx_internal_links_from
ON internal_links (from_type, from_id);

CREATE INDEX idx_internal_links_to
ON internal_links (to_type, to_id);

CREATE INDEX idx_page_publications_publish_at
ON page_publications (publish_at);

CREATE INDEX idx_user_tokens_expires
ON user_tokens (expires_at);