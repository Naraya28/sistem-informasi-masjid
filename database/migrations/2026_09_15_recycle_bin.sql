-- Recycle Bin V1: soft-delete metadata plus a central lifecycle registry.
-- DDL implicitly commits in MariaDB. Back up and audit the live schema first.
USE sim_masjid_alfirdaus;

ALTER TABLE pengurus
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS deleted_by INT NULL,
    ADD COLUMN IF NOT EXISTS delete_request_id INT NULL,
    ADD INDEX IF NOT EXISTS idx_pengurus_deleted_at (deleted_at),
    ADD INDEX IF NOT EXISTS idx_pengurus_deleted_by (deleted_by),
    ADD INDEX IF NOT EXISTS idx_pengurus_delete_request (delete_request_id);

ALTER TABLE artikel
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS deleted_by INT NULL,
    ADD COLUMN IF NOT EXISTS delete_request_id INT NULL,
    ADD INDEX IF NOT EXISTS idx_artikel_deleted_at (deleted_at),
    ADD INDEX IF NOT EXISTS idx_artikel_deleted_by (deleted_by),
    ADD INDEX IF NOT EXISTS idx_artikel_delete_request (delete_request_id);

ALTER TABLE pesan
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS deleted_by INT NULL,
    ADD COLUMN IF NOT EXISTS delete_request_id INT NULL,
    ADD INDEX IF NOT EXISTS idx_pesan_deleted_at (deleted_at),
    ADD INDEX IF NOT EXISTS idx_pesan_deleted_by (deleted_by),
    ADD INDEX IF NOT EXISTS idx_pesan_delete_request (delete_request_id);

ALTER TABLE keuangan
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS deleted_by INT NULL,
    ADD COLUMN IF NOT EXISTS delete_request_id INT NULL,
    ADD INDEX IF NOT EXISTS idx_keuangan_deleted_at (deleted_at),
    ADD INDEX IF NOT EXISTS idx_keuangan_created_deleted (created_by, deleted_at),
    ADD INDEX IF NOT EXISTS idx_keuangan_deleted_by (deleted_by),
    ADD INDEX IF NOT EXISTS idx_keuangan_delete_request (delete_request_id);

ALTER TABLE laporan
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS deleted_by INT NULL,
    ADD COLUMN IF NOT EXISTS delete_request_id INT NULL,
    ADD INDEX IF NOT EXISTS idx_laporan_deleted_at (deleted_at),
    ADD INDEX IF NOT EXISTS idx_laporan_created_deleted (created_by, deleted_at),
    ADD INDEX IF NOT EXISTS idx_laporan_deleted_by (deleted_by),
    ADD INDEX IF NOT EXISTS idx_laporan_delete_request (delete_request_id);

DELIMITER //
CREATE PROCEDURE migration_20260915_add_recycle_fk(
    IN p_target_table VARCHAR(64),
    IN p_constraint_name VARCHAR(64),
    IN p_target_column VARCHAR(64),
    IN p_referenced_table VARCHAR(64),
    IN p_referenced_column VARCHAR(64)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
          AND TABLE_NAME = p_target_table
          AND CONSTRAINT_NAME = p_constraint_name
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ) THEN
        SET @recycle_fk_sql = CONCAT(
            'ALTER TABLE `', p_target_table, '` ADD CONSTRAINT `', p_constraint_name,
            '` FOREIGN KEY (`', p_target_column, '`) REFERENCES `', p_referenced_table,
            '` (`', p_referenced_column, '`) ON DELETE SET NULL ON UPDATE CASCADE'
        );
        PREPARE recycle_fk_statement FROM @recycle_fk_sql;
        EXECUTE recycle_fk_statement;
        DEALLOCATE PREPARE recycle_fk_statement;
    END IF;
END//
DELIMITER ;

CALL migration_20260915_add_recycle_fk('pengurus', 'fk_pengurus_deleted_by', 'deleted_by', 'users', 'id');
CALL migration_20260915_add_recycle_fk('pengurus', 'fk_pengurus_delete_request', 'delete_request_id', 'delete_requests', 'id');
CALL migration_20260915_add_recycle_fk('artikel', 'fk_artikel_deleted_by', 'deleted_by', 'users', 'id');
CALL migration_20260915_add_recycle_fk('artikel', 'fk_artikel_delete_request', 'delete_request_id', 'delete_requests', 'id');
CALL migration_20260915_add_recycle_fk('pesan', 'fk_pesan_deleted_by', 'deleted_by', 'users', 'id');
CALL migration_20260915_add_recycle_fk('pesan', 'fk_pesan_delete_request', 'delete_request_id', 'delete_requests', 'id');
CALL migration_20260915_add_recycle_fk('keuangan', 'fk_keuangan_deleted_by', 'deleted_by', 'users', 'id');
CALL migration_20260915_add_recycle_fk('keuangan', 'fk_keuangan_delete_request', 'delete_request_id', 'delete_requests', 'id');
CALL migration_20260915_add_recycle_fk('laporan', 'fk_laporan_deleted_by', 'deleted_by', 'users', 'id');
CALL migration_20260915_add_recycle_fk('laporan', 'fk_laporan_delete_request', 'delete_request_id', 'delete_requests', 'id');

DROP PROCEDURE migration_20260915_add_recycle_fk;

CREATE TABLE IF NOT EXISTS recycle_bin_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    entity_type VARCHAR(30) NOT NULL,
    record_id INT NOT NULL,
    display_label VARCHAR(255) DEFAULT NULL,
    deleted_by INT DEFAULT NULL,
    delete_request_id INT DEFAULT NULL,
    source_flow ENUM('admin_direct', 'approval') NOT NULL,
    status ENUM('trashed', 'restored', 'purged') NOT NULL DEFAULT 'trashed',
    deleted_at DATETIME NOT NULL,
    restored_by INT DEFAULT NULL,
    restored_at DATETIME DEFAULT NULL,
    purged_by INT DEFAULT NULL,
    purged_at DATETIME DEFAULT NULL,
    original_file_path VARCHAR(500) DEFAULT NULL,
    trash_file_path VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_recycle_entity_record (entity_type, record_id),
    KEY idx_recycle_status (status),
    KEY idx_recycle_deleted_at (deleted_at),
    KEY idx_recycle_deleted_by (deleted_by),
    KEY idx_recycle_delete_request (delete_request_id),
    KEY idx_recycle_status_deleted (status, deleted_at),
    KEY idx_recycle_restored_by (restored_by),
    KEY idx_recycle_purged_by (purged_by),
    CONSTRAINT fk_recycle_deleted_by FOREIGN KEY (deleted_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_recycle_delete_request FOREIGN KEY (delete_request_id) REFERENCES delete_requests (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_recycle_restored_by FOREIGN KEY (restored_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_recycle_purged_by FOREIGN KEY (purged_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
