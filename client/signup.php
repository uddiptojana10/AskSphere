<?php
include(__DIR__ . '/../common/auth.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === 'signup.php' && (!isset($_GET['signup']) || $_GET['signup'] !== 'true')) {
    header("Location: {$base_url}?signup=true");
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
    <h1 class="heading mb-4 text-neon animate-glow">Signup</h1>

    <?php if (isset($_GET['error'])): ?>
        <p class="text-danger"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo $base_url; ?>server/requests.php" class="asksphere-form">
      <div class="mb-4">
        <label for="username" class="form-label asksphere-label">User Name</label>
        <input type="text" name="username" class="form-control asksphere-input" id="username" placeholder="Enter user name" required>
      </div>

      <div class="mb-4">
        <label for="email" class="form-label asksphere-label">User Email</label>
        <input type="email" name="email" class="form-control asksphere-input" id="email" placeholder="Enter user email" required>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label asksphere-label">User Password</label>
        <input type="password" name="password" class="form-control asksphere-input" id="password" placeholder="Enter user password" required>
      </div>

      <div class="mb-5">
        <label for="address" class="form-label asksphere-label">User Address</label>
        <input type="text" name="address" class="form-control asksphere-input" id="address" placeholder="Enter user address">
      </div>

      <div class="text-center">
        <button type="submit" name="signup" class="btn asksphere-btn-primary px-5 py-2 animate-pulse">Signup</button>
      </div>
    </form>
  </div>
</div>