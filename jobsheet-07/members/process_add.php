<?php

session_start();


$name = trim($_POST['name'] ?? '');

$memberId = trim($_POST['member_id'] ?? '');

$address = trim($_POST['address'] ?? '');

$phone = trim($_POST['phone'] ?? '');


$errors = [];


if ($name === '') {

    $errors[] = "Name is required.";
}


if ($memberId === '') {

    $errors[] = "Member ID is required.";
}


/*
 * Jika terdapat error,
 * kembali ke halaman Add Member.
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
 * Jika session members belum ada,
 * buat array kosong.
 */

if (!isset($_SESSION['members'])) {

    $_SESSION['members'] = [];
}


/*
 * Tambahkan member baru.
 */

$_SESSION['members'][] = [

    'name' => $name,

    'member_id' => $memberId,

    'address' => $address,

    'phone' => $phone

];


/*
 * Flash message berhasil.
 */

$_SESSION['flash'] = [

    'type' => 'success',

    'message' =>
    'Member added successfully.'

];


/*
 * Redirect ke Member List.
 */

header('Location: list.php');

exit;
