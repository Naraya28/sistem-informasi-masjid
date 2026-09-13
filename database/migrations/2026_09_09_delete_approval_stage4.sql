CREATE TABLE delete_requests (
    id INT NOT NULL AUTO_INCREMENT,
    entity_type ENUM('keuangan', 'laporan') NOT NULL,
    record_id INT NOT NULL,
    requested_by INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    reviewed_by INT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_delete_requests_target_status (entity_type, record_id, status),
    KEY idx_delete_requests_requested_by (requested_by),
    KEY idx_delete_requests_status (status),
    KEY idx_delete_requests_created_at (created_at),
    CONSTRAINT fk_delete_requests_requested_by FOREIGN KEY (requested_by) REFERENCES users (id) ON UPDATE CASCADE,
    CONSTRAINT fk_delete_requests_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES users (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
