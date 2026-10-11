<?php

session_start();

require __DIR__ . '/../includes/connection.php';


$name = trim($_POST['name'] ?? '');

$memberId = trim($_POST['member_id'] ?? '');

$address = trim($_POST['address'] ?? '');

$phone = trim($_POST['phone'] ?? '');


$errors = [];


/* Name validation */

if ($name === '') {

    $errors[] = "Name is required.";

}


/* Member ID validation */

if ($memberId === '') {

    $errors[] = "Member ID is required.";

}


/* Address validation */

if ($address === '') {

    $errors[] = "Address is required.";

}


/* Phone validation */

if ($phone === '') {

    $errors[] = "Phone is required.";

} elseif (
    !preg_match('/^[0-9+\-\s]+$/', $phone)
) {

    $errors[] =
        "Phone can contain only numbers, +, - and spaces.";

}

if (!empty($errors)) {

    $_SESSION['flash'] = [

        'type' => 'error',

        'message' =>
            implode(' ', $errors)

    ];

    header('Location: add.php');

    exit;

}

$stmt = $pdo->prepare(

    "INSERT INTO members
    (name, member_id, address, phone)

    VALUES
    (:name, :member_id, :address, :phone)

    RETURNING id"

);



try {
    $stmt->execute([
        'name' => $name,
        'member_id' => $memberId,
        'address' => $address,
        'phone' => $phone
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Member added successfully.'
    ];

} catch (PDOException $e) {
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Member ID already in use. Please use a different ID.'
        ];
    } else {
        error_log($e->getMessage());

        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Unable to save member. Please try again.'
        ];
    }
}

/* Redirect */

header('Location: list.php');

exit;