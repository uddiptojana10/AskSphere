<?php
include(__DIR__ . '/../common/auth.php');
?>

<div class="container mt-5">
  <div class="jumbotron text-center">
    <h1 class="display-4">Welcome to AskSphere!</h1>
    <p class="lead">A place where you can ask questions and get answers from the community.</p>
    <hr class="my-4">
    <p>Browse topics, ask your doubts, or help others by sharing your knowledge.</p>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <a class="btn btn-primary btn-lg" href="?signup=true" role="button">Get Started</a>
      <a class="btn btn-outline-secondary btn-lg" href="?login=true" role="button">Login</a>
    <?php else: ?>
      <a class="btn btn-success btn-lg" href="?ask=true" role="button">Ask a Question</a>
    <?php endif; ?>
  </div>
</div>