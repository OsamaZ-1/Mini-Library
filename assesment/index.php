<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Manager</title>
    <link rel="stylesheet" href="styles\index.css">
</head>
<body>
    <!-- Book Library -->
    <h1>📚 Mini Book Library</h1>
    <!-- NLP Filtering Form -->
    <form method="GET" class="form-container" style="margin-bottom: 20px;">
        <input type="text" name="q" placeholder="e.g. borrowed books by Tolkien" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width: 70%;">
        <button type="submit">Search</button>
    </form>
    <a href="add.php" class="button add">➕ Add New Book</a>
    <div class="card-container">

        <?php

            $q = strtolower($_GET['q'] ?? '');
            $where = [];
            $params = [];
            $types = '';

            // Borrowed status
            if (str_contains($q, 'borrowed')) {
                $where[] = 'borrowed = ?';
                $params[] = 1;
                $types .= 'i';
            } elseif (str_contains($q, 'available') || str_contains($q, 'not borrowed')) {
                $where[] = 'borrowed = ?';
                $params[] = 0;
                $types .= 'i';
            }

            // Author (e.g. "by Tolkien")
            if (preg_match('/by ([\w\s]+)/', $q, $matches)) {
                $author = trim($matches[1]);
                $where[] = 'author LIKE ?';
                $params[] = "%$author%";
                $types .= 's';
            }

            // Title (e.g. "titled Dune")
            if (preg_match('/titled ([\w\s]+)/', $q, $matches)) {
                $title = trim($matches[1]);
                $where[] = 'title LIKE ?';
                $params[] = "%$title%";
                $types .= 's';
            }

            // Year: exact (e.g. "books from 1997")
            if (preg_match('/(from|in|year) (\d{4})/', $q, $matches)) {
                $where[] = 'year = ?';
                $params[] = (int)$matches[2];
                $types .= 'i';
            }

            // Year: after (e.g. "after 2005")
            if (preg_match('/after (\d{4})/', $q, $matches)) {
                $where[] = 'year > ?';
                $params[] = (int)$matches[1];
                $types .= 'i';
            }

            // Year: before (e.g. "before 1990")
            if (preg_match('/before (\d{4})/', $q, $matches)) {
                $where[] = 'year < ?';
                $params[] = (int)$matches[1];
                $types .= 'i';
            }

            // Year: between (e.g. "between 1990 and 2000")
            if (preg_match('/between (\d{4}) and (\d{4})/', $q, $matches)) {
                $where[] = 'year BETWEEN ? AND ?';
                $params[] = (int)$matches[1];
                $params[] = (int)$matches[2];
                $types .= 'ii';
            }

            // Fallback fuzzy search
            if (empty($author) && empty($title) && empty($params) && !empty($q)) {
                $where[] = '(title LIKE ? OR author LIKE ?)';
                $params[] = "%$q%";
                $params[] = "%$q%";
                $types .= 'ss';
            }

            $sql = "SELECT * FROM books";
            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $stmt = $conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while($book = $result->fetch_assoc()):
        ?>

        <div class="book-card <?= $book['borrowed'] ? 'borrowed' : '' ?>">
            <?php if ($book['image_path']): ?>
                <img src="<?= $book['image_path'] ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <?php else: ?>
                <div class="placeholder-img">No Image</div>
            <?php endif; ?>
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Year:</strong> <?= $book['year'] ?></p>
            <div class="borrowed-status">
                <label>
                    <input type="checkbox" class="borrow-toggle" data-id="<?= $book['id'] ?>" <?= $book['borrowed'] ? 'checked' : '' ?>>
                    Borrowed
                </label>
            </div>
            <div class="card-actions">
                <a href="edit.php?id=<?= $book['id'] ?>" class="button edit">Edit</a>
                <a href="delete.php?id=<?= $book['id'] ?>" class="button delete" onclick="return confirm('Delete this book?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>
<script>
    document.querySelectorAll('.borrow-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const bookId = this.dataset.id;
            const borrowed = this.checked ? 1 : 0;

            // Find the parent card
            const card = this.closest('.book-card');

            // Toggle the borrowed class
            if (borrowed) {
                card.classList.add('borrowed');
            } else {
                card.classList.remove('borrowed');
            }

            // Send AJAX request
            fetch('toggle_borrowed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${bookId}&borrowed=${borrowed}`
            });
        });
    });
</script>
</html>
