CREATE TABLE internal_links (
  id int(11) NOT NULL AUTO_INCREMENT,
  from_type varchar(50) NOT NULL,
  from_id int(11) NOT NULL,
  to_type varchar(50) NOT NULL,
  to_id int(11) NOT NULL,
  anchor_text varchar(255) NOT NULL,
  nofollow tinyint(1) NOT NULL DEFAULT 0,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (id),
  KEY idx_from (from_type,from_id),
  KEY idx_to (to_type,to_id)
);

CREATE TABLE media (
  id int(11) NOT NULL AUTO_INCREMENT,
  filename varchar(255) NOT NULL,
  path varchar(255) NOT NULL,
  mime_type varchar(100) NOT NULL,
  size int(11) NOT NULL,
  created_at datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE migrations (
  id int(11) NOT NULL AUTO_INCREMENT,
  plugin varchar(100) NOT NULL,
  version varchar(50) NOT NULL,
  executed_at datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_plugin_version (plugin,version)
);

CREATE TABLE page_media (
  page_id int(11) NOT NULL,
  media_id int(11) NOT NULL,
  PRIMARY KEY (page_id,media_id)
);

CREATE TABLE page_publications (
  id int(11) NOT NULL AUTO_INCREMENT,
  page_id int(11) NOT NULL,
  version_id int(11) NOT NULL,
  publish_at datetime NOT NULL,
  executed_at datetime DEFAULT NULL,
  created_by int(11) DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY publish_at (publish_at),
  KEY executed_at (executed_at)
);

CREATE TABLE page_versions (
  id int(11) NOT NULL AUTO_INCREMENT,
  page_id int(11) NOT NULL,
  title varchar(255) NOT NULL,
  content mediumtext NOT NULL,
  created_at datetime NOT NULL DEFAULT current_timestamp(),
  created_by int(11) DEFAULT NULL,
  is_autosave tinyint(1) NOT NULL DEFAULT 0,
  template varchar(100) NOT NULL DEFAULT 'default',
  PRIMARY KEY (id)
);

CREATE TABLE pages (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  author_id INT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  published_version_id INT DEFAULT NULL,
  is_homepage TINYINT(1) NOT NULL DEFAULT 0,
  template VARCHAR(100) NOT NULL DEFAULT 'default',
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_pages_slug (slug)
);

CREATE TABLE seo_metadata (
  id int(11) NOT NULL AUTO_INCREMENT,
  subject_type varchar(50) NOT NULL,
  subject_id int(11) NOT NULL,
  title varchar(255) NOT NULL,
  description text NOT NULL,
  no_index tinyint(1) NOT NULL DEFAULT 0,
  canonical varchar(255) DEFAULT NULL,
  open_graph longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_subject (subject_type, subject_id)
  );

CREATE TABLE user_tokens (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  type enum('activation','password_reset','two_factor') NOT NULL,
  token_hash char(64) NOT NULL,
  expires_at datetime NOT NULL,
  used_at datetime DEFAULT NULL,
  created_at datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY uniq_user_tokens_hash (token_hash),
  KEY idx_user_tokens_user_type (user_id,type),
  KEY idx_user_tokens_expires (expires_at)
  );

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(191) NOT NULL,
  password_hash VARCHAR(255) DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  email_verified_at DATETIME DEFAULT NULL,
  roles LONGTEXT NOT NULL,
  two_factor_secret VARCHAR(64) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
);



