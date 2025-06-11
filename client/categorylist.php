<?php
include(__DIR__ . '/../common/auth.php');
include(__DIR__ . '/../common/db.php');
?>

<div>
    <h1 class="heading">Categories</h1>

    <?php
    $query = "SELECT * FROM category";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        foreach ($result as $row) {
            $name = ucfirst(htmlspecialchars($row['name']));
            $id = htmlspecialchars($row['id']);
            echo "<div class='row question-list'>
                    <h4><a href='?c-id=$id'>$name</a></h4>
                  </div>";
        }
    } else {
        echo "<p>No categories available.</p>";
    }
    ?>
</div>