
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/connection.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$year = $_POST['year'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stock = $_POST['stock'] ?? '';
$category = trim($_POST['category'] ?? '');

$errors = [];

if (!$id || $id < 1) {
    $errors[] = "Invalid book ID.";
}

if ($title === '') {
    $errors[] = "Title is required.";
}

if ($author === '') {
    $errors[] = "Author is required.";
}

if (!is_numeric($year) || $year < 1900 || $year > 2026) {
    $errors[] = "Year must be between 1900-2026.";
}

if (!is_numeric($stock) || $stock < 0 || (int)$stock != $stock) {
    $errors[] = "Stock must be a non-negative integer.";
}

if ($isbn !== '' && !preg_match('/^[0-9-]+$/', $isbn)) {
    $errors[] = "ISBN can contain only digits and hyphens.";
}

$allowedCategories = ['fiction', 'non-fiction', 'reference'];

if (!in_array($category, $allowedCategories, true)) {
    $errors[] = "Invalid book category.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => implode(' ', $errors)
    ];

    header('Location: edit.php?id=' . (int)$id);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE books
         SET title = :title,
             author = :author,
             year = :year,
             isbn = :isbn,
             stock = :stock,
             category = :category
         WHERE id = :id"
    );

    $stmt->execute([
        'title' => $title,
        'author' => $author,
        'year' => (int)$year,
        'isbn' => $isbn,
        'stock' => (int)$stock,
        'category' => $category,
        'id' => $id
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Book updated successfully.'
    ];

} catch (PDOException $e) {
    error_log($e->getMessage());

    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Unable to update book.'
    ];
}

header('Location: list.php');
exit;
