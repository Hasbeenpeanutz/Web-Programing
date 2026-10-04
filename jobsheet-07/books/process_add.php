<?php

session_start();


$title = trim($_POST['title'] ?? '');

$author = trim($_POST['author'] ?? '');

$year = $_POST['year'] ?? '';

$isbn = trim($_POST['isbn'] ?? '');

$stock = $_POST['stock'] ?? '';

$category = trim($_POST['category'] ?? '');


$errors = [];


if ($title === '') {

    $errors[] = "Title is required.";

}


if ($author === '') {

    $errors[] = "Author is required.";

}


if (
    !is_numeric($year) ||
    $year < 1900 ||
    $year > 2026
) {

    $errors[] =
        "Year must be between 1900-2026.";

}


if (
    !is_numeric($stock) ||
    $stock < 0
) {

    $errors[] =
        "Stock cannot be negative.";

}


/*
 * Jika terdapat error,
 * kembali ke halaman Add Book.
 */

if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => implode(' ', $errors)
    ];

    header('Location: add.php');

    exit;
}


/*
 * Jika session books belum ada,
 * buat array kosong terlebih dahulu.
 */

if (!isset($_SESSION['books'])) {

    $_SESSION['books'] = [];

}


/*
 * Tambahkan data buku baru
 * ke dalam session.
 */

$_SESSION['books'][] = [

    'title' => $title,

    'author' => $author,

    'year' => (int) $year,

    'isbn' => $isbn,

    'stock' => (int) $stock,

    'category' => $category

];


/*
 * Flash message berhasil.
 */

$_SESSION['flash'] = [

    'type' => 'success',

    'message' =>
        'Book added successfully.'

];


/*
 * Redirect ke Book List.
 */

header('Location: list.php');

exit;