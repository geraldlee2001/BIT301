<?php
// Database configuration
$servername = "127.0.0.1"; // 数据库服务器地址
$username = "root"; // 数据库用户名
$password = ""; // 数据库密码
$dbname = "superconcert";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
