ALTER TABLE users
ADD CONSTRAINT chk_users_status
CHECK (status IN ('pending','active','disabled'));

ALTER TABLE pages
ADD CONSTRAINT chk_pages_status
CHECK (status IN ('draft','published'));

ALTER TABLE menu_items
ADD CONSTRAINT fk_menu_items_menu
FOREIGN KEY (menu_id) REFERENCES menus(id)
ON DELETE CASCADE;

ALTER TABLE menu_items
ADD CONSTRAINT fk_menu_items_parent
FOREIGN KEY (parent_id) REFERENCES menu_items(id)
ON DELETE CASCADE;
