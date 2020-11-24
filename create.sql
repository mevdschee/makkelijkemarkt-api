CREATE DATABASE `makkelijkemarkt` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'makkelijkemarkt'@'localhost' IDENTIFIED BY 'makkelijkemarkt';
GRANT ALL PRIVILEGES ON `makkelijkemarkt`.* TO 'makkelijkemarkt'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
