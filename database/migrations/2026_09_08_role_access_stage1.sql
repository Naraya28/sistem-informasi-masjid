-- Stage 1: role support and creator references only. No account/data creation.
-- Apply once, after inspecting the current schema and checking orphan creators.
-- Expected: admin/pengurus enum; no keuangan.created_by; laporan.created_by INT NULL.
-- DDL implicitly commits in MariaDB/MySQL; do not treat this as rollbackable DML.
USE sim_masjid_alfirdaus;

ALTER TABLE users
    MODIFY COLUMN role ENUM('admin','bendahara','pengurus') NOT NULL;

-- Historical transactions may keep NULL; never infer their creator.
ALTER TABLE keuangan
    ADD COLUMN created_by INT(11) NULL,
    ADD INDEX idx_keuangan_created_by (created_by),
    ADD CONSTRAINT fk_keuangan_created_by FOREIGN KEY (created_by)
        REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;

-- Reuse the existing column; preflight must find zero orphan creators.
ALTER TABLE laporan
    ADD INDEX idx_laporan_created_by (created_by),
    ADD CONSTRAINT fk_laporan_created_by FOREIGN KEY (created_by)
        REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT;
