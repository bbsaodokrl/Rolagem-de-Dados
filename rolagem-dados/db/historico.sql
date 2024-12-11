CREATE TABLE historico (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id TEXT,
    comando TEXT,
    resultado TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
