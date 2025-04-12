CREATE TABLE IF NOT EXISTS promo_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchantId INT NOT NULL,
  productId INT NOT NULL,
  code VARCHAR(50) NOT NULL,
  discount_amount DECIMAL(10, 2) NOT NULL,
  discount_type ENUM('percentage', 'fixed') NOT NULL,
  start_date DATE NOT NULL,
  expiry_date DATE NOT NULL,
  usage_limit INT NOT NULL,
  current_usage INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (merchantId) REFERENCES merchants(id),
  FOREIGN KEY (productId) REFERENCES product(id),
  UNIQUE (code)
)