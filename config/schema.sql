-- Simple Board Database Schema
-- Run this file to initialize the database

CREATE DATABASE IF NOT EXISTS simple_board
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE simple_board;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(20)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    nickname    VARCHAR(20)  NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NOT NULL,
    content     TEXT         NOT NULL,
    view_count  INT UNSIGNED NOT NULL DEFAULT 0,
    is_deleted  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id     INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    content     TEXT         NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id),
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
