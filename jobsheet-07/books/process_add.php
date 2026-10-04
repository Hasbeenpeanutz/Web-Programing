<?php

session_start();

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$year = $_POST['year'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stock = $_POST['stock'] ?? '';
$category = trim($_POST['category'] ?? '');

$errors = [];


// Title validation
if ($title === '') {
    $errors[] = "Title is required.";
}


// Author validation
if ($author === '') {
    $errors[] = "Author is required.";
}


// Year validation
if (
    !is_numeric($year) ||
    $year < 1900 ||
    $year > 2026
) {
    $errors[] = "Year must be between 1900-2026.";
}


// Stock validation
if (
    !is_numeric($stock) ||
    $stock < 0
) {
    $errors[] = "Stock cannot be negative.";
}


// ISBN validation
// ISBN boleh kosong.
// Jika diisi, hanya boleh mengandung angka dan tanda "-".
if (
    $isbn !== '' &&
    !preg_match('/^[0-9-]+$/', $isbn)
) {
    $errors[] =
        "ISBN can contain only digits and hyphens.";
}


// Jika ada error
if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => implode(' ', $errors)
    ];

    header('Location: add.php');

    exit;
}


// Pastikan session books tersedia
if (!isset($_SESSION['books'])) {
    $_SESSION['books'] = [];
}


// Simpan data buku
$_SESSION['books'][] = [

    'title' => $title,

    'author' => $author,

    'year' => (int) $year,

    'isbn' => $isbn,

    'stock' => (int) $stock,

    'category' => $category

];


// Flash message berhasil
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Book added successfully.'
];


// Redirect ke Book List
header('Location: list.php');

exit;