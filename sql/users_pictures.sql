CREATE TABLE IF NOT EXISTS users_pictures(
    user_id INT NOT NULL,
    picture_id INT NOT NULL,
    PRIMARY KEY (user_id, picture_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (picture_id) REFERENCES pictures(id) ON DELETE CASCADE
);
