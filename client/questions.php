<?php
include(__DIR__ . '/../common/auth.php');
include(__DIR__ . '/../common/db.php');
?>

<div class="container">
    <div class="row">
        <div class="col-8">
            <h1 class="heading">Questions</h1>

            <?php
            // Initialize query parts
            $query = "";
            $params = [];
            $types = "";

            // Build query based on GET parameters
            if (isset($_GET["c-id"])) {
                $cid = intval($_GET["c-id"]);
                $query = "SELECT * FROM questions WHERE category_id = ?";
                $params[] = $cid;
                $types .= "i";
            } else if (isset($_GET["u-id"])) {
                $uid = intval($_GET["u-id"]);
                $query = "SELECT * FROM questions WHERE user_id = ?";
                $params[] = $uid;
                $types .= "i";
            } else if (isset($_GET["latest"])) {
                $query = "SELECT * FROM questions ORDER BY id DESC";
            } else if (isset($_GET["search"])) {
                $search = "%" . $_GET["search"] . "%";
                $query = "SELECT * FROM questions WHERE title LIKE ?";
                $params[] = $search;
                $types .= "s";
            } else {
                $query = "SELECT * FROM questions";
            }

            // Execute query
            if (!empty($params)) {
                $stmt = $conn->prepare($query);
                if ($stmt === false) {
                    die("Prepare failed: " . htmlspecialchars($conn->error));
                }
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query($query);
            }

            // Display questions
            if ($result && $result->num_rows > 0) {
                foreach ($result as $row) {
                    $title = htmlspecialchars($row['title']);
                    $id = (int)$row['id'];

                    echo "<div class='row question-list'>";
                    echo "<h4 class='my-question'><a href='?q-id=$id'>$title</a>";

                    // Show delete link only if viewing own questions
                    if (isset($uid) && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $uid) {
                        echo " <a href='./server/requests.php?delete=$id' onclick=\"return confirm('Are you sure you want to delete this question?');\">Delete</a>";
                    }

                    echo "</h4></div>";
                }
            } else {
                echo "<p>No questions found.</p>";
            }
            ?>
        </div>

        <div class="col-4">
            <?php include('categorylist.php'); ?>
        </div>
    </div>
</div>