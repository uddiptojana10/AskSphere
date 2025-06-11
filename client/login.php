<?php
include(__DIR__ . '/../common/auth.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === 'login.php' && (!isset($_GET['login']) || $_GET['login'] !== 'true')) {
    header("Location: {$base_url}?login=true");
    exit;
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: $base_url");
    exit;
}
?>

<div class="container asksphere-container d-flex justify-content-center align-items-center min-vh-100">
  <div class="asksphere-form-card">
    <h1 class="heading mb-4 text-neon animate-glow">Login</h1>

    <?php if (isset($_GET['error'])): ?>
        <p class="text-danger"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form action="<?php echo $base_url; ?>server/requests.php" method="post" class="asksphere-form">
      <div class="mb-4">
        <label for="email" class="form-label asksphere-label">User Email</label>
        <input type="email" name="email" class="form-control asksphere-input" id="email" placeholder="Enter your email" required>
      </div>

      <div class="mb-5">
        <label for="password" class="form-label asksphere-label">User Password</label>
        <input type="password" name="password" class="form-control asksphere-input" id="password" placeholder="Enter your password" required autocomplete="off">
      </div>

      <div class="text-center">
        <button type="submit" name="login" class="btn asksphere-btn-primary px-5 py-2 animate-pulse">Login</button>
      </div>
    </form>
  </div>
</div>