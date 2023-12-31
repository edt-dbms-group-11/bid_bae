-- Insert into User table with hashed passwords using SHA fn 
INSERT INTO User (username, password, email, display_name, opt_in_email, balance, locked_balance) VALUES
    ('student_1', SHA('123'), 'ucaba75@ucl.ac.uk', 'Student 1', TRUE, 2560.00, 0.00);

-- Insert into Category table
INSERT INTO Category (name, description) VALUES
    ('Electronics', 'Electronics and gadgets for tech enthusiasts'),
    ('Fashion', 'Trendy fashion items and accessories'),
    ('Home & Garden', 'Furniture and decor for your home'),
    ('Books & Literature', 'A wide selection of books for all interests'),
    ('Sports & Outdoors', 'Sports equipment and outdoor gear');

-- Insert into Item table
INSERT INTO Item (name, description, image_url, category_id, user_id, is_available) VALUES
    ('iPhone 13 Pro', 'Latest iPhone model with advanced features', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDACgcHiMeGSgjISMtKygwPGRBPDc3PHtYXUlkkYCZlo+AjIqgtObDoKrarYqMyP/L2u71////m8H////6/+b9//j/2wBDASstLTw1PHZBQXb4pYyl+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj/wAARCAAgACADASIAAhEBAxEB/8QAFwAAAwEAAAAAAAAAAAAAAAAAAgMEAP/EACgQAAIBBAAEBgMBAAAAAAAAAAECEQADEiExQWGBIzJRccHwBKHh0f/EABYBAQEBAAAAAAAAAAAAAAAAAAABA//EABcRAQEBAQAAAAAAAAAAAAAAAAABESH/2gAMAwEAAhEDEQA/AIsQxO9gbkcKK3bzfUMR0ms4KriylRGgeP3/ACKotXMHNwnTTPQa9un9pvGmkPbCuA8LlziI+7oCgRtkTGiBxqq9czcXAdLEdRv36/yp1zZMFGQAAIA4ffmKu1L0V649wAsxIXQBP770CsyMGUwRVDWvBYF/K5APJgBIPf5pbfjlXxzQ7G59edWWYFszOxZjJNHZuPaBKsQG0QD3ntWWwS+OaDZ3PpzpoteCoD+ZwCeSgiSe3xS2YP/Z', 1, 1, true),
    ('Designer Handbag', 'Luxury handbag from a top designer brand', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDACgcHiMeGSgjISMtKygwPGRBPDc3PHtYXUlkkYCZlo+AjIqgtObDoKrarYqMyP/L2u71////m8H////6/+b9//j/2wBDASstLTw1PHZBQXb4pYyl+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj/wAARCAAgACADASIAAhEBAxEB/8QAFwAAAwEAAAAAAAAAAAAAAAAAAgMEAP/EACgQAAIBBAAEBgMBAAAAAAAAAAECEQADEiExQWGBIzJRccHwBKHh0f/EABYBAQEBAAAAAAAAAAAAAAAAAAABA//EABcRAQEBAQAAAAAAAAAAAAAAAAABESH/2gAMAwEAAhEDEQA/AIsQxO9gbkcKK3bzfUMR0ms4KriylRGgeP3/ACKotXMHNwnTTPQa9un9pvGmkPbCuA8LlziI+7oCgRtkTGiBxqq9czcXAdLEdRv36/yp1zZMFGQAAIA4ffmKu1L0V649wAsxIXQBP770CsyMGUwRVDWvBYF/K5APJgBIPf5pbfjlXxzQ7G59edWWYFszOxZjJNHZuPaBKsQG0QD3ntWWwS+OaDZ3PpzpoteCoD+ZwCeSgiSe3xS2YP/Z', 2, 1, true),
    ('Antique Oak Dining Table', 'Vintage dining table with intricate carvings', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDACgcHiMeGSgjISMtKygwPGRBPDc3PHtYXUlkkYCZlo+AjIqgtObDoKrarYqMyP/L2u71////m8H////6/+b9//j/2wBDASstLTw1PHZBQXb4pYyl+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj4+Pj/wAARCAAgACADASIAAhEBAxEB/8QAFwAAAwEAAAAAAAAAAAAAAAAAAgMEAP/EACgQAAIBBAAEBgMBAAAAAAAAAAECEQADEiExQWGBIzJRccHwBKHh0f/EABYBAQEBAAAAAAAAAAAAAAAAAAABA//EABcRAQEBAQAAAAAAAAAAAAAAAAABESH/2gAMAwEAAhEDEQA/AIsQxO9gbkcKK3bzfUMR0ms4KriylRGgeP3/ACKotXMHNwnTTPQa9un9pvGmkPbCuA8LlziI+7oCgRtkTGiBxqq9czcXAdLEdRv36/yp1zZMFGQAAIA4ffmKu1L0V649wAsxIXQBP770CsyMGUwRVDWvBYF/K5APJgBIPf5pbfjlXxzQ7G59edWWYFszOxZjJNHZuPaBKsQG0QD3ntWWwS+OaDZ3PpzpoteCoD+ZwCeSgiSe3xS2YP/Z', 3, 1, true);


-- NOT FOR PRODUCTION; DEV AND EXPLANATORY PURPOSE ONLY
-- Insert into Auction table
-- INSERT INTO Auction (reserved_price, start_price, end_price, current_price, start_time, end_time, status, seller_id, description, title) VALUES
--     (500.00, 100.00, 450.00, 100.00, '2023-10-25 08:00:00', '2023-10-28 18:00:00', 'IN_PROGRESS', 1, 'Description for auction 1', 'Auction 1'),
--     (700.00, 200.00, 600.00, 200.00, '2023-10-26 10:00:00', '2023-10-29 20:00:00', 'IN_PROGRESS', 2, 'Description for auction 2', 'Auction 2'),
--     (800.00, 300.00, 700.00, 300.00, '2023-10-27 12:00:00', '2023-10-30 22:00:00', 'IN_PROGRESS', 3, 'Description for auction 3', 'Auction 3'),
--     (900.00, 400.00, 800.00, 400.00, '2023-10-28 14:00:00', '2023-10-31 00:00:00', 'IN_PROGRESS', 4, 'Description for auction 4', 'Auction 4'),
--     (1000.00, 500.00, 900.00, 500.00, '2023-10-29 16:00:00', '2023-11-01 02:00:00', 'IN_PROGRESS', 5, 'Description for auction 5', 'Auction 5');

-- Insert into Bid table
-- INSERT INTO Bid (user_id, auction_id, bid_price) VALUES
--     (2, 1, 120.00),
--     (3, 1, 150.00),
--     (1, 2, 220.00),
--     (3, 2, 280.00),
--     (4, 2, 320.00),
--     (1, 3, 330.00),
--     (2, 3, 370.00),
--     (4, 3, 420.00),
--     (5, 3, 480.00),
--     (1, 4, 450.00);


-- -- Insert into Watchlist table
-- INSERT INTO Watchlist (user_id, auction_id) VALUES
--     (1, 1),
--     (1, 2),
--     (2, 1),
--     (3, 3),
--     (4, 2),
--     (5, 3);

-- -- Insert into Auction_Product table
-- INSERT INTO Auction_Product (item_id, auction_id) VALUES
--     (1, 1),
--     (2, 2),
--     (3, 3),
--     (4, 4),
--     (5, 5);