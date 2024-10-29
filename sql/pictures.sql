CREATE TABLE IF NOT EXISTS pictures (
                                        ID INT PRIMARY KEY AUTO_INCREMENT,
                                        path VARCHAR(255) NOT NULL,
    creator INT NOT NULL,
    FOREIGN KEY (creator) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
    );