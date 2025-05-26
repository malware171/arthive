SET foreign_key_checks = 0;

DROP TABLE IF EXISTS artists;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    encrypted_password VARCHAR(255) NOT NULL,
    type ENUM('artist', 'client'),
    avatar_url VARCHAR(255)
);

CREATE TABLE artists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bio TEXT,
  portfolio_url VARCHAR(255),
  ai_detection_count INT,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR (20),
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

);

CREATE TABLE artworks {
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR (45),
  price DECIMAL (10, 2)
  date DATE,
  description TEXT()

}

SET foreign_key_checks = 1;