-- Create user table
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(100) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `opt_in_email` tinyint(1) NOT NULL,
  `balance` DECIMAL(10,2),
  PRIMARY KEY (`id`),
  UNIQUE KEY `Email` (`email`),
  UNIQUE KEY `Username` (`username`)
);

-- Seed user table
INSERT INTO `user` (`id`, `password`, `username`, `email`, `opt_in_email`, `balance`) VALUES
(1, '$2y$04$ZE7GUosk8008cjozV4SHnuspfzMoC1SMv9XUQNKn58l8ptPzlbM.K', 'Paul', 'paul@yahoo.com', 0, 2000),
(2, '$2y$04$ZE7GUosk8008cjozV4SHnuspfzMoC1SMv9XUQNKn58l8ptPzlbM.K', 'Ans', 'ans@yahoo.com', 0, 1000),
(3, '$2y$04$ZE7GUosk8008cjozV4SHnuspfzMoC1SMv9XUQNKn58l8ptPzlbM.K', 'Jash', 'jash@yahoo.com', 1, 9000),
(4, '$2y$04$ZE7GUosk8008cjozV4SHnuspfzMoC1SMv9XUQNKn58l8ptPzlbM.K', 'Gaz', 'gaz@yahoo.com', 1, 100);

-- Create Item and Category table

DROP TABLE IF EXISTS category;
CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
);

DROP TABLE IF EXISTS item;
CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE
);

INSERT INTO category (name, description) VALUES
('Apparel', 'Women and men tops, bottom, outerwears or shoes'),
('Food', 'Exquisite and unique foods'),
('Antiques', 'Curated hidden finds'),
('Electronics', 'Particular devices consisting analog / digital circuits');

INSERT INTO item (name, description, image, category_id) VALUES
('Apple Watch', 'Gen 8, nike band limited edition with Arsenal print', 'https://i.ibb.co/P66tyDc/apple.jpg', 4),
('Wool Coat', 'In navy, cashmere', 'https://i.ibb.co/vLZVxLy/coat.webp', 1),
('Chinese Antiques', 'From 1860 ish, bid fast, good condition', 'https://i.ibb.co/ZBg8jCh/guci.jpg', 3);
