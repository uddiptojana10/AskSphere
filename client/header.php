<?php
include(__DIR__ . '/../common/auth.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>

<nav class="navbar navbar-expand-lg sticky-top asksphere-navbar">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url; ?>">
      <img src="<?php echo $base_url; ?>public/logo.png" alt="AskSphere Logo" height="45" class="me-2" />
      <span class="fw-bold text-neon">AskSphere</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-semibold text-neon <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($_GET['login']) && !isset($_GET['signup']) && !isset($_GET['latest']) && !isset($_GET['ask']) && !isset($_GET['u-id'])) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>">Home</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-neon" href="<?php echo $base_url; ?>server/requests.php?logout=true">
              Logout (<?php echo htmlspecialchars(ucfirst($_SESSION['user']['username'])); ?>)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-neon <?php echo isset($_GET['ask']) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?ask=true">Ask A Question</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-neon <?php echo isset($_GET['u-id']) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?u-id=<?php echo urlencode($_SESSION['user_id']); ?>">My Questions</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-neon <?php echo isset($_GET['login']) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?login=true">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold text-neon <?php echo isset($_GET['signup']) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?signup=true">Sign Up</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link fw-semibold text-neon <?php echo isset($_GET['latest']) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>?latest=true">Latest Questions</a>
        </li>
      </ul>

      <form class="d-flex ms-lg-3" method="get" action="<?php echo $base_url; ?>">
        <input class="form-control rounded-0 py-2 asksphere-search-input" name="search" type="search" placeholder="Search questions" aria-label="Search" />
        <button class="btn rounded-0 px-4 asksphere-search-btn" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>