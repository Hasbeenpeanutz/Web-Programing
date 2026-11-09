
<?php
$page_title = "Edit Member";
require __DIR__ . '/../includes/connection.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Invalid member ID.'
    ];
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM members WHERE id = :id"
);
$stmt->execute(['id' => $id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Member not found.'
    ];
    header('Location: list.php');
    exit;
}

include __DIR__ . '/../includes/header.php';

?>

<section>
    <h2>Edit Member</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </p>
    <?php endif; ?>

    <form id="add-form" method="post" action="process_edit.php">

        <input type="hidden" name="id"
            value="<?php echo $member['id']; ?>">

        <p>
            <label for="name">Name</label>
            <input type="text" id="name" name="name"
                value="<?php echo htmlspecialchars($member['name']); ?>"
                required>
        </p>

        <p>
            <label for="member_id">Member ID</label>
            <input type="text" id="member_id" name="member_id"
                value="<?php echo htmlspecialchars($member['member_id']); ?>"
                required>
        </p>

        <p>
            <label for="address">Address</label>
            <input type="text" id="address" name="address"
                value="<?php echo htmlspecialchars($member['address'] ?? ''); ?>">
        </p>

        <p>
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone"
                value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
        </p>

        <p>
            <button type="submit">Update Member</button>
            <a href="list.php">Cancel</a>
        </p>

    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
