
<?php
session_start();

require __DIR__ . '/../includes/connection.php';

$page_title = "Book List";

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Search keyword
$keyword = trim($_GET['keyword'] ?? '');

// Pagination
$limit = 5;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

if (!$page || $page < 1) {
    $page = 1;
}

// Count total records
if ($keyword !== '') {
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM books
         WHERE title ILIKE :keyword"
    );

    $countStmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);
} else {
    $countStmt = $pdo->query(
        "SELECT COUNT(*) FROM books"
    );
}

$totalBooks = (int) $countStmt->fetchColumn();

$totalPages = max(1, (int) ceil($totalBooks / $limit));

$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Fetch book records
if ($keyword !== '') {
    $stmt = $pdo->prepare(
        "SELECT *
         FROM books
         WHERE title ILIKE :keyword
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue(
        ':keyword',
        '%' . $keyword . '%',
        PDO::PARAM_STR
    );
} else {
    $stmt = $pdo->prepare(
        "SELECT *
         FROM books
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section>

    <h2>Book List</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </p>
    <?php endif; ?>

    <!-- Server-side search -->
    <form method="get" action="list.php"
        class="search-box" id="server-search-form">

        <label for="search-input">
            Search Book Title
        </label>

        <input
            type="text"
            id="search-input"
            name="keyword"
            placeholder="Type book title..."
            value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>"
        >

        <button type="submit">Search</button>

        <?php if ($keyword !== ''): ?>
            <a href="list.php">Clear Search</a>
        <?php endif; ?>

    </form>

    <div class="table-responsive">

        <table>

            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Stock</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($books)): ?>

                    <tr>
                        <td colspan="6">
                            No book data found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($books as $book): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($book['title']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </td>

                            <td>
                                <?php echo $book['year']; ?>
                            </td>

                            <td>
                                <?php echo $book['stock']; ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($book['date_added'])
                                    ? htmlspecialchars($book['date_added'])
                                    : '-';
                                ?>
                            </td>

                            <td>

                                <a href="edit.php?id=<?php echo $book['id']; ?>">
                                    <button type="button">Edit</button>
                                </a>

                                <form
                                    method="post"
                                    action="delete.php"
                                    class="form-delete"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo $book['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn-delete"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <!-- Pagination -->
    <div class="pagination">

        <p>
            Page <?php echo $page; ?>
            of <?php echo $totalPages; ?>

            (<?php echo $totalBooks; ?> books)
        </p>

        <?php if ($page > 1): ?>
            <a href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page - 1; ?>">
                Previous
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <a
                href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>"
                class="<?php echo $page === $i ? 'active' : ''; ?>"
            >
                <?php echo $i; ?>
            </a>

        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page + 1; ?>">
                Next
            </a>
        <?php endif; ?>

    </div>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
