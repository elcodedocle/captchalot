CREATE TABLE IF NOT EXISTS captchalot.captchalot
(
    nonce       VARCHAR(255) NOT NULL PRIMARY KEY,
    value       VARCHAR(255) NOT NULL,
    session_id  VARCHAR(255) NULL,
    ip          VARCHAR(255) NULL
);
