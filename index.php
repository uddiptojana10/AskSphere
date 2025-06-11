<?php
include(__DIR__ . '/common/auth.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AskSphere</title>
    <?php include(__DIR__ . '/client/commonFiles.php'); ?>
</head>
<body class="asksphere-body">
    <?php include(__DIR__ . '/client/header.php'); ?>

    <?php
    // Handle routing based on query parameters
    if (isset($_GET['signup']) && $_GET['signup'] === 'true' && !isset($_SESSION['user_id'])) {
        include(__DIR__ . '/client/signup.php');
    } elseif (isset($_GET['login']) && $_GET['login'] === 'true' && !isset($_SESSION['user_id'])) {
        include(__DIR__ . '/client/login.php');
    } elseif (isset($_GET['ask']) && $_GET['ask'] === 'true' && isset($_SESSION['user_id'])) {
        include(__DIR__ . '/client/ask.php');
    } elseif (isset($_GET['q-id'])) {
        $qid = $_GET['q-id'];
        include(__DIR__ . '/client/question-details.php');
    } elseif (isset($_GET['c-id'])) {
        $cid = $_GET['c-id'];
        include(__DIR__ . '/client/questions.php');
    } elseif (isset($_GET['u-id'])) {
        $uid = $_GET['u-id'];
        include(__DIR__ . '/client/questions.php');
    } elseif (isset($_GET['latest'])) {
        include(__DIR__ . '/client/questions.php');
    } elseif (isset($_GET['search'])) {
        $search = $_GET['search'];
        include(__DIR__ . '/client/questions.php');
    } else {
        include(__DIR__ . '/client/home.php');
    }
    ?>
</body>
</html>