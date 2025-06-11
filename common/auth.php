<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser from caching pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// List of pages that don't require login
$public_pages = [
    'index.php',
    'home.php',
    'login.php',
    'signup.php',
    'question-details.php', // Allow guests to view questions
    'answers.php',          // Allow guests to view answers
];

// Current script filename
$current_page = basename($_SERVER['PHP_SELF']);

// Redirect only if user not logged in and page is NOT public
if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: /AskSphere/?login=true");
    exit();
}
?>