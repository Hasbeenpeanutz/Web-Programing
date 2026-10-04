<?php

session_start();

$name = trim($_POST['name'] ?? '');
$memberId = trim($_POST['member_id'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$errors = [];


// Name validation
if ($name === '') {
    $errors[] = "Name is required.";
}


// Member ID validation
if ($memberId === '') {
    $errors[] = "Member ID is required.";
}


// Address validation
if ($address === '') {
    $errors[] = "Address is required.";
}


// Phone validation
if ($phone === '') {

    $errors[] = "Phone is required.";

} elseif (!preg_match('/^[0-9+\-\s]+$/', $phone)) {

    $errors[] =
        "Phone can contain only numbers, +, - and spaces.";

}


// Jika terdapat error
if (!empty($errors)) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => implode(' ', $errors)
    ];

    header('Location: add.php');

    exit;
}


// Pastikan session members tersedia
if (!isset($_SESSION['members'])) {
    $_SESSION['members'] = [];
}


// Simpan data member
$_SESSION['members'][] = [

    'name' => $name,

    'member_id' => $memberId,

    'address' => $address,

    'phone' => $phone

];


// Flash message berhasil
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Member added successfully.'
];


// Redirect ke Member List
header('Location: list.php');

exit;