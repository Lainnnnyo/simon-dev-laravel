-- Byte & Brew: existing Wasmer MySQL database only. Export a backup first.
-- Select simon_pos in Database Explorer before running this script.
-- No table drops, record deletions, seeds, or public/default passwords.

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='users' AND column_name='is_staff')=0, 'ALTER TABLE `users` ADD `is_staff` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE final_stmt FROM @ddl; EXECUTE final_stmt; DEALLOCATE PREPARE final_stmt;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='users' AND column_name='is_admin')=0, 'ALTER TABLE `users` ADD `is_admin` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE final_stmt FROM @ddl; EXECUTE final_stmt; DEALLOCATE PREPARE final_stmt;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='users' AND column_name='deleted_at')=0, 'ALTER TABLE `users` ADD `deleted_at` TIMESTAMP NULL', 'SELECT 1');
PREPARE final_stmt FROM @ddl; EXECUTE final_stmt; DEALLOCATE PREPARE final_stmt;

SET @ddl = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='customers' AND column_name='deleted_at')=0, 'ALTER TABLE `customers` ADD `deleted_at` TIMESTAMP NULL', 'SELECT 1');
PREPARE final_stmt FROM @ddl; EXECUTE final_stmt; DEALLOCATE PREPARE final_stmt;

ALTER TABLE customers MODIFY phone VARCHAR(30) NULL;
CREATE TABLE IF NOT EXISTS products (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL,
 price DECIMAL(10,2) NOT NULL, stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
 image VARCHAR(255) NULL, is_archived TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS sales (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id BIGINT UNSIGNED NOT NULL, customer_id BIGINT UNSIGNED NULL, sold_by BIGINT UNSIGNED NOT NULL,
 quantity INT UNSIGNED NOT NULL, unit_price DECIMAL(10,2) NOT NULL, total_price DECIMAL(14,2) NOT NULL,
 product_name VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NULL, staff_name VARCHAR(255) NOT NULL,
 request_key CHAR(36) NOT NULL UNIQUE, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 CONSTRAINT sales_product_id_foreign FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT sales_customer_id_foreign FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
 CONSTRAINT sales_sold_by_foreign FOREIGN KEY(sold_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS password_reset_tokens (email VARCHAR(255) PRIMARY KEY,token VARCHAR(255) NOT NULL,created_at TIMESTAMP NULL);
CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(255) PRIMARY KEY,user_id BIGINT UNSIGNED NULL,ip_address VARCHAR(45) NULL,user_agent TEXT NULL,payload LONGTEXT NOT NULL,last_activity INT NOT NULL,INDEX sessions_user_id_index(user_id),INDEX sessions_last_activity_index(last_activity));
CREATE TABLE IF NOT EXISTS cache (`key` VARCHAR(255) PRIMARY KEY,value MEDIUMTEXT NOT NULL,expiration INT NOT NULL);
CREATE TABLE IF NOT EXISTS cache_locks (`key` VARCHAR(255) PRIMARY KEY,owner VARCHAR(255) NOT NULL,expiration INT NOT NULL);
