-- Table structure for tb_logs
CREATE TABLE tb_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    record_id INT NULL,
    log_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES tb_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index for better query performance
CREATE INDEX idx_logs_user_id ON tb_logs(user_id);
CREATE INDEX idx_logs_module ON tb_logs(module);
CREATE INDEX idx_logs_action ON tb_logs(action);
CREATE INDEX idx_logs_created_at ON tb_logs(created_at);
