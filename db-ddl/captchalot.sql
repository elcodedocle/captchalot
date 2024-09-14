CREATE TABLE IF NOT EXISTS captchalot
(
    nonce       VARCHAR(255) NOT NULL PRIMARY KEY,
    value       VARCHAR(255) NOT NULL,
    session_id  VARCHAR(255) NULL,
    ip          VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);
CREATE INDEX captchalot_created_at_index
    ON captchalot (created_at);
