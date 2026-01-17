
ALTER TABLE user_tokens
ADD CONSTRAINT fk_user_tokens_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

ALTER TABLE page_versions
ADD CONSTRAINT fk_page_versions_page
FOREIGN KEY (page_id) REFERENCES pages(id)
ON DELETE CASCADE;

ALTER TABLE users
ADD CONSTRAINT chk_users_roles_json
CHECK (json_valid(roles));

ALTER TABLE seo_metadata
ADD CONSTRAINT chk_seo_open_graph_json
CHECK (json_valid(open_graph));