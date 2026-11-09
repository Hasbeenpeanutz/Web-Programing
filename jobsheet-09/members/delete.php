
<?php
session_start();
require __DIR__ . '/../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Invalid member ID.'
    ];

    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare(
    "DELETE FROM members WHERE id = :id"
);

$stmt->execute(['id' => $id]);

$_SESSION['flash'] = [
    'type' => 'success',
    'message' => $stmt->rowCount() > 0
        ? 'Member deleted successfully.'
        : 'Member not found.'
];

header('Location: list.php');
exit;
