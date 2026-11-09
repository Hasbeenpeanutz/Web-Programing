
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/connection.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$name = trim($_POST['name'] ?? '');
$memberId = trim($_POST['member_id'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$errors = [];

if (!$id || $id < 1) {
    $errors[] = "Invalid member ID.";
}

if ($name === '') {
    $errors[] = "Name is required.";
}

if ($memberId === '') {
    $errors[] = "Member ID is required.";
}

if ($address === '') {
    $errors[] = "Address is required.";
}

if ($phone === '') {
    $errors[] = "Phone is required.";
} elseif (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
    $errors[] = "Phone can contain only numbers, +, - and spaces.";
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
        "UPDATE members
         SET name = :name,
             member_id = :member_id,
             address = :address,
             phone = :phone
         WHERE id = :id"
    );

    $stmt->execute([
        'name' => $name,
        'member_id' => $memberId,
        'address' => $address,
        'phone' => $phone,
        'id' => $id
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Member updated successfully.'
    ];

} catch (PDOException $e) {
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Member ID already in use.'
        ];
    } else {
        error_log($e->getMessage());

        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Unable to update member.'
        ];
    }
}

header('Location: list.php');
exit;
