
<?php
session_start();

require __DIR__ . '/../includes/connection.php';

$page_title = "Member List";

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
         FROM members
         WHERE name ILIKE :keyword"
    );

    $countStmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);
} else {
    $countStmt = $pdo->query(
        "SELECT COUNT(*) FROM members"
    );
}

$totalMembers = (int) $countStmt->fetchColumn();

$totalPages = max(1, (int) ceil($totalMembers / $limit));

$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Fetch member records
if ($keyword !== '') {
    $stmt = $pdo->prepare(
        "SELECT *
         FROM members
         WHERE name ILIKE :keyword
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
         FROM members
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section>

    <h2>Member List</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </p>
    <?php endif; ?>

    <!-- Server-side search -->
    <form method="get" action="list.php"
        class="search-box" id="server-search-form">

        <label for="search-input">
            Search Member Name
        </label>

        <input
            type="text"
            id="search-input"
            name="keyword"
            placeholder="Type member name..."
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
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($members)): ?>

                    <tr>
                        <td colspan="5">
                            No member data found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($members as $member): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($member['member_id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($member['name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($member['address'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($member['phone'] ?? ''); ?>
                            </td>

                            <td>

                                <a href="edit.php?id=<?php echo $member['id']; ?>">
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
                                        value="<?php echo $member['id']; ?>"
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

            (<?php echo $totalMembers; ?> members)
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
