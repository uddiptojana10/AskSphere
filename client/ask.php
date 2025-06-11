<?php
include(__DIR__ . '/../common/auth.php');

// Restrict access to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: /AskSphere/?login=true");
    exit;
}
?>

<div class="container">
  <h1 class="heading">Ask A Question</h1>

  <form action="./server/requests.php" method="post">
    <div class="col-6 offset-sm-3 margin-bottom-15">
      <label for="title" class="form-label">Title</label>
      <input type="text" name="title" class="form-control" id="title" placeholder="Enter question" required>
    </div>

    <div class="col-6 offset-sm-3 margin-bottom-15">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" id="description" placeholder="Enter details" required></textarea>
    </div>

    <div class="col-6 offset-sm-3 margin-bottom-15">
      <label for="category" class="form-label">Category</label>
      <?php include("category.php"); ?>
    </div>

    <div class="col-6 offset-sm-3 margin-bottom-15">
      <button type="submit" name="ask" class="btn btn-primary">Ask Question</button>
    </div>
  </form>
</div>