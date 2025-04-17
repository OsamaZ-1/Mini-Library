# 📚 Book Management System (Vanilla PHP)

A simple, clean book catalog system built with **Vanilla PHP** and **MySQL**, supporting basic CRUD operations, image uploads, a borrowed tracker, and smart filtering using natural language-like queries.

---

## ✨ Features

- 📖 Add, edit, and delete books
- 🖼 Upload and display cover images
- ✅ Toggle borrowed status live (AJAX-powered)
- 🔍 Smart search bar with basic NLP parsing:
  - `borrowed books by Tolkien`
  - `available books by Jane Austen`
  - `titled Dune`
- 🎴 Books displayed as responsive **cards** instead of tables
- 🧼 Clean and user-friendly UI

---

## Clone or Download the Project

```bash
git clone https://github.com/your-username/book-management-php.git
```

## Import the DB

Import the books.sql file to your server.

## 🗂 Or Set up the Database table from Scratch

CREATE TABLE books (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
author VARCHAR(255) NOT NULL,
year INT,
image_path VARCHAR(255),
borrowed TINYINT(1) DEFAULT 0
);

## Run the Application

On your server open the index.php page, e.g. localhost/your_folder/index.php

## Image Error

Make sure the uploads folder is writable.
