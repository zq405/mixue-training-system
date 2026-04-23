<?php
// migrate_cloud.php - 云数据库迁移脚本
require_once 'config.php';

echo "Starting cloud database migration...\n";

$sqls = [
    "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS training (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        content TEXT NOT NULL,
        category ENUM('front', 'back') NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS quiz (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        A VARCHAR(255) NOT NULL,
        B VARCHAR(255) NOT NULL,
        C VARCHAR(255),
        D VARCHAR(255),
        answer CHAR(1) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        training_id INT NOT NULL,
        status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        score INT DEFAULT 0,
        retake_count INT DEFAULT 0,
        UNIQUE KEY unique_progress (username, training_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS quiz_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        score INT NOT NULL,
        total INT NOT NULL,
        percentage INT NOT NULL,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS overall_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        front_completed INT DEFAULT 0,
        back_completed INT DEFAULT 0,
        quiz_score INT DEFAULT 0,
        quiz_passed BOOLEAN DEFAULT FALSE,
        certificate_issued BOOLEAN DEFAULT FALSE,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user (username)
    )"
];

foreach ($sqls as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✓ Table created successfully\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

// 插入默认用户
$conn->query("INSERT IGNORE INTO users (username, password) VALUES 
    ('admin', '123456'),
    ('staff1', '123456'),
    ('staff2', '123456')");

echo "Migration completed!\n";
?>