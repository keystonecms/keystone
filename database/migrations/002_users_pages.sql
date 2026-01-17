ALTER TABLE users
ADD CONSTRAINT chk_users_status
CHECK (status IN ('pending','active','disabled'));

ALTER TABLE pages
ADD CONSTRAINT chk_pages_status
CHECK (status IN ('draft','published'));