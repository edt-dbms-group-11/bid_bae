-- Insert into User table with hashed passwords using SHA2 fn
INSERT INTO User (username, password, email, display_name, opt_in_email, balance) VALUES
    ('user1', SHA2('password1', 256), 'user1@example.com', 'User One', TRUE, 2560.00),
    ('user2', SHA2('password2', 256), 'user2@example.com', 'User Two', FALSE, 50.00),
    ('user3', SHA2('password3', 256), 'user3@example.com', 'User Three', TRUE, 75.00),
    ('user4', SHA2('password4', 256), 'user4@example.com', 'User Four', FALSE, 200.00),
    ('user5', SHA2('password5', 256), 'user5@example.com', 'User Five', TRUE, 150.00);

-- Insert into Auction table
INSERT INTO Auction (reserved_price, start_price, end_price, current_price, start_time, end_time, status, seller_id, description, title) VALUES
    (500.00, 100.00, 450.00, 100.00, '2023-10-25 08:00:00', '2023-10-28 18:00:00', 'IN_PROGRESS', 1, 'Description for auction 1', 'Auction 1'),
    (700.00, 200.00, 600.00, 200.00, '2023-10-26 10:00:00', '2023-10-29 20:00:00', 'IN_PROGRESS', 2, 'Description for auction 2', 'Auction 2'),
    (800.00, 300.00, 700.00, 300.00, '2023-10-27 12:00:00', '2023-10-30 22:00:00', 'IN_PROGRESS', 3, 'Description for auction 3', 'Auction 3'),
    (900.00, 400.00, 800.00, 400.00, '2023-10-28 14:00:00', '2023-10-31 00:00:00', 'IN_PROGRESS', 4, 'Description for auction 4', 'Auction 4'),
    (1000.00, 500.00, 900.00, 500.00, '2023-10-29 16:00:00', '2023-11-01 02:00:00', 'IN_PROGRESS', 5, 'Description for auction 5', 'Auction 5');

-- Insert into Bid table
INSERT INTO Bid (user_id, auction_id, bid_price) VALUES
    (2, 1, 120.00),
    (3, 1, 150.00),
    (1, 2, 220.00),
    (3, 2, 280.00),
    (4, 2, 320.00),
    (1, 3, 330.00),
    (2, 3, 370.00),
    (4, 3, 420.00),
    (5, 3, 480.00),
    (1, 4, 450.00);

-- Insert into Category table
INSERT INTO Category (name, description) VALUES
    ('Electronics', 'Electronics and gadgets for tech enthusiasts'),
    ('Fashion', 'Trendy fashion items and accessories'),
    ('Home & Garden', 'Furniture and decor for your home'),
    ('Books & Literature', 'A wide selection of books for all interests'),
    ('Sports & Outdoors', 'Sports equipment and outdoor gear');

-- Insert into Item table
INSERT INTO Item (name, description, image_url, category_id) VALUES
    ('iPhone 13 Pro', 'Latest iPhone model with advanced features', 'https://example.com/iphone13.jpg', 1),
    ('Designer Handbag', 'Luxury handbag from a top designer brand', 'https://example.com/designer-handbag.jpg', 2),
    ('Antique Oak Dining Table', 'Vintage dining table with intricate carvings', 'https://example.com/dining-table.jpg', 3),
    ('The Great Gatsby', 'F. Scott Fitzgerald''s classic novel', 'https://example.com/great-gatsby.jpg', 4),
    ('Mountain Bike', 'High-performance mountain bike for outdoor adventures', 'https://example.com/mountain-bike.jpg', 5),
    ('Smart TV', 'Large-screen smart TV with 4K resolution', 'https://example.com/smart-tv.jpg', 1),
    ('Leather Jacket', 'Stylish leather jacket for the fashion-savvy', 'https://example.com/leather-jacket.jpg', 2),
    ('Vintage Record Player', 'Retro record player for vinyl enthusiasts', 'https://example.com/record-player.jpg', 3);

-- Insert into Watchlist table
INSERT INTO Watchlist (user_id, auction_id) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (3, 3),
    (4, 2),
    (5, 3);

-- Insert into Auction_Product table
INSERT INTO Auction_Product (item_id, auction_id) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5);

