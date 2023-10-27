-- Drop existing tables
DROP TABLE IF EXISTS Watchlist, Bid, Auction_Product, Item, Category, Auction, User;

-- Create user table
CREATE TABLE IF NOT EXISTS User (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(40) NOT NULL UNIQUE,
    display_name VARCHAR(30) NOT NULL,
    opt_in_email BOOLEAN DEFAULT FALSE,
    balance FLOAT(2) NOT NULL DEFAULT 0.00
);

-- Create auction table
CREATE TABLE IF NOT EXISTS Auction (
    id INT NOT NULL AUTO_INCREMENT,
    reserved_price FLOAT(2),
    start_price FLOAT(2) NOT NULL,
    end_price FLOAT(2),
    current_price FLOAT(2) NOT NULL,
    start_time DATETIME,
    end_time DATETIME,
    status ENUM('INIT', 'IN_PROGRESS', 'DONE', 'DISCARDED') NOT NULL,
    seller_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    title VARCHAR(40) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (seller_id) REFERENCES User(id) ON DELETE CASCADE
);

-- Create bid table
CREATE TABLE IF NOT EXISTS Bid (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    auction_id INT NOT NULL,
    bid_price FLOAT(2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES Auction(id) ON DELETE CASCADE
);

-- Create category table
CREATE TABLE IF NOT EXISTS Category (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    description VARCHAR(255),
    PRIMARY KEY (id)
);

-- Create item table
CREATE TABLE IF NOT EXISTS Item (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES Category(id) ON DELETE CASCADE
);

-- Create watchlist table
CREATE TABLE IF NOT EXISTS Watchlist (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    auction_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES Auction(id) ON DELETE CASCADE
);

-- Create multi-item auction MtoM table
CREATE TABLE IF NOT EXISTS Auction_Product (
    id INT NOT NULL AUTO_INCREMENT,
    item_id INT NOT NULL,
    auction_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (item_id) REFERENCES Item(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES Auction(id) ON DELETE CASCADE
);
