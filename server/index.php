<?php
// Detect if running locally (XAMPP) or on InfinityFree
$is_local = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

// Set the redirect path based on the environment
$redirect_path = $is_local ? '/asksphere/' : '/';

// Redirect to the homepage
header("Location: $redirect_path");
exit;
?>