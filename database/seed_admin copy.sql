INSERT INTO users (email, password_hash, active, roles)
VALUES (
    'admin@keystone.local',
    '$argon2id$v=19$m=65536,t=4,p=1$examplehash',
    1,
    JSON_ARRAY('admin')
);
