<?php
include(__DIR__ . '/../common/auth.php');
include(__DIR__ . '/../common/db.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

// Ensure question ID is available
if (!isset($_GET['q-id']) || !is_numeric($_GET['q-id'])) {
    echo "<p>Invalid question ID.</p>";
    exit;
}
$qid = (int)$_GET['q-id'];
?>

<div class="container asksphere-container">
    <h1 class="heading text-neon animate-glow">Question</h1>
    <div class="row">
        <div class="col-8">
            <?php
            // Fetch question
            $query = "SELECT * FROM questions WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $qid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $cid = (int)$row['category_id'];

                // Output question details safely
                echo "<h4 class='margin-bottom-15 question-title'>Question: " . htmlspecialchars($row['title']) . "</h4>";
                echo "<p class='margin-bottom-15'>" . nl2br(htmlspecialchars($row['description'])) . "</p>";

                // Close the statement before including answers.php
                $stmt->close();

                // Include answers
                include("answers.php");
            ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="<?php echo $base_url; ?>server/requests.php" method="post" class="asksphere-form mt-4">
                        <input type="hidden" name="question_id" value="<?php echo $qid; ?>">
                        <div class="mb-3">
                            <label for="answer" class="form-label asksphere-label">Your Answer</label>
                            <textarea name="answer" class="form-control asksphere-input margin-bottom-15" id="answer" placeholder="Your answer..." required autocomplete="off"></textarea>
                        </div>
                        <div class="text-center">
                            <button class="btn asksphere-btn-primary px-5 py-2 animate-pulse" type="submit" name="submit_answer">Write your answer</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="mt-4">Please <a href="<?php echo $base_url; ?>?login=true">log in</a> to write your answer.</p>
                <?php endif; ?>
            <?php
            } else {
                echo "<p>Sorry, this question does not exist.</p>";
                $stmt->close();
            }
            ?>
        </div>

        <div class="col-4">
            <?php
            if (isset($cid) && $cid > 0) {
                // Get category name
                $categoryQuery = "SELECT name FROM category WHERE id = ?";
                $stmtCat = $conn->prepare($categoryQuery);
                $stmtCat->bind_param("i", $cid);
                $stmtCat->execute();
                $categoryResult = $stmtCat->get_result();

                if ($categoryResult && $categoryResult->num_rows > 0) {
                    $categoryRow = $categoryResult->fetch_assoc();
                    echo "<h1 class='heading text-neon animate-glow'>" . htmlspecialchars(ucfirst($categoryRow['name'])) . "</h1>";

                    // Close $stmtCat before proceeding
                    $stmtCat->close();

                    // Related questions except current question
                    $relatedQuery = "SELECT id, title FROM questions WHERE category_id = ? AND id != ?";
                    $stmtRel = $conn->prepare($relatedQuery);
                    $stmtRel->bind_param("ii", $cid, $qid);
                    $stmtRel->execute();
                    $relatedResult = $stmtRel->get_result();

                    while ($row = $relatedResult->fetch_assoc()) {
                        $id = (int)$row['id'];
                        $title = htmlspecialchars($row['title']);
                        echo "<div class='question-list'>
                            <h4><a href='?q-id=$id'>$title</a></h4>
                        </div>";
                    }
                    $stmtRel->close();
                } else {
                    echo "<p>Category not found.</p>";
                    $stmtCat->close();
                }
            }
            ?>
        </div>
    </div>
</div>