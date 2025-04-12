CREATE TABLE waiting_list (
  id VARCHAR(36) PRIMARY KEY,
  userId VARCHAR(36) NOT NULL,
  productId VARCHAR(36) NOT NULL,
  requestDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('WAITING', 'NOTIFIED', 'EXPIRED', 'CANCELLED') DEFAULT 'WAITING',
  notificationDate TIMESTAMP NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(255),
  preferredContact ENUM('EMAIL', 'PHONE', 'BOTH') DEFAULT 'PHONE',
  notificationAttempts INT DEFAULT 0,
  lastNotificationAttempt TIMESTAMP NULL,
  notes TEXT,
  FOREIGN KEY (userId) REFERENCES users(id),
  FOREIGN KEY (productId) REFERENCES product(id)
);