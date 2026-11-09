
<?php
$page_title = "Edit Book";
require __DIR__ . '/../includes/connection.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Invalid book ID.'
    ];
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM books WHERE id = :id"
);
$stmt->execute(['id' => $id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Book not found.'
    ];
    header('Location: list.php');
    exit;
}

include __DIR__ . '/../includes/header.php';

?>

<section>
    <h2>Edit Book</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </p>
    <?php endif; ?>

    <form id="add-form" method="post" action="process_edit.php">

        <input type="hidden" name="id"
            value="<?php echo $book['id']; ?>">

        <p>
            <label for="title">Title</label>
            <input type="text" id="title" name="title"
                value="<?php echo htmlspecialchars($book['title']); ?>"
                required>
        </p>

        <p>
            <label for="author">Author</label>
            <input type="text" id="author" name="author"
                value="<?php echo htmlspecialchars($book['author']); ?>"
                required>
        </p>

        <p>
            <label for="year">Publication Year</label>
            <input type="number" id="year" name="year"
                min="1900" max="2026"
                value="<?php echo $book['year']; ?>" required>
        </p>

        <p>
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn"
                value="<?php echo htmlspecialchars($book['isbn'] ?? ''); ?>">
        </p>

        <p>
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock"
                min="0" value="<?php echo $book['stock']; ?>" required>
        </p>

        <p>
            <label for="category">Category</label>
            <select id="category" name="category">
                <?php
                $categories = [
                    'fiction' => 'Fiction',
                    'non-fiction' => 'Non-Fiction',
                    'reference' => 'Reference'
                ];

                foreach ($categories as $value => $label):
                ?>
                    <option value="<?php echo $value; ?>"
                        <?php echo $book['category'] === $value ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <button type="submit">Update Book</button>
            <a href="list.php">Cancel</a>
        </p>

    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
