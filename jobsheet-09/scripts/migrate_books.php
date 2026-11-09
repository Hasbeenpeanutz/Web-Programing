
<?php

require __DIR__ . '/../includes/connection.php';

// Find the old JSON file from jobsheet-06.
$jsonPaths = [
    __DIR__ . '/../../jobsheet-06/data/books.json',
    __DIR__ . '/../../jobsheet-06/data/book.json'
];

$jsonFile = null;

foreach ($jsonPaths as $path) {
    if (file_exists($path)) {
        $jsonFile = $path;
        break;
    }
}

if ($jsonFile === null) {
    exit(
        "JSON file not found. Check the path to your "
        . "jobsheet-06 data file."
    );
}

$json = file_get_contents($jsonFile);
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    exit("Invalid JSON: " . json_last_error_msg());
}

// Support either a list of books or an object containing "books".
if (isset($data['books']) && is_array($data['books'])) {
    $data = $data['books'];
} elseif (isset($data['book']) && is_array($data['book'])) {
    $data = $data['book'];
} elseif (isset($data['title'])) {
    $data = [$data];
}

if (!is_array($data)) {
    exit("Unexpected JSON structure. Please check the old data file.");
}

$check = $pdo->prepare(
    "SELECT 1 FROM books
     WHERE title = :title
       AND author = :author
       AND year = :year
     LIMIT 1"
);

$insert = $pdo->prepare(
    "INSERT INTO books
        (title, author, year, isbn, stock, category)
     VALUES
        (:title, :author, :year, :isbn, :stock, :category)"
);

$imported = 0;
$skipped = 0;

try {
    $pdo->beginTransaction();

    foreach ($data as $oldBook) {
        if (!is_array($oldBook)) {
            $skipped++;
            continue;
        }

        $title = trim((string) (
            $oldBook['title'] ?? ''
        ));

        $author = trim((string) (
            $oldBook['author'] ?? ''
        ));

        $year = $oldBook['year']
            ?? $oldBook['publication_year']
            ?? null;

        if (
            $title === '' ||
            $author === '' ||
            !is_numeric($year)
        ) {
            $skipped++;
            continue;
        }

        $year = (int) $year;

        $check->execute([
            'title' => $title,
            'author' => $author,
            'year' => $year
        ]);

        // Avoid importing the same title/author/year twice.
        if ($check->fetchColumn()) {
            $skipped++;
            continue;
        }

        $isbn = $oldBook['isbn'] ?? null;
        $stock = $oldBook['stock']
            ?? $oldBook['stok']
            ?? 0;
        $category = $oldBook['category']
            ?? $oldBook['kategori']
            ?? null;

        $insert->execute([
            'title' => $title,
            'author' => $author,
            'year' => $year,
            'isbn' => $isbn !== '' ? $isbn : null,
            'stock' => max(0, (int) $stock),
            'category' => $category !== '' ? $category : null
        ]);

        $imported++;
    }

    $pdo->commit();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());
    exit("Migration failed. Check the PHP error log.");
}

echo "Migration completed.\n";
echo "Imported: " . $imported . " book(s).\n";
echo "Skipped: " . $skipped . " record(s).\n";