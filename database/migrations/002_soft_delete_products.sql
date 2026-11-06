USE inventory_saas_db;

ALTER TABLE products
    ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL
    AFTER updated_at;

CREATE INDEX index_products_deleted_at
    ON products (deleted_at);