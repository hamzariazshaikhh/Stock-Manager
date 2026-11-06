USE inventory_saas_db;

ALTER TABLE users
    ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'owner'
    AFTER password_hash;

ALTER TABLE users
    ADD CONSTRAINT chk_users_role
    CHECK (role IN ('owner', 'admin', 'manager', 'viewer'));

CREATE INDEX index_users_role ON users (role);