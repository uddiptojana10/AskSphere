<?php
include(__DIR__ . '/../common/auth.php');
include(__DIR__ . '/../common/db.php');
?>

<select class="form-control" name="category" id="category" required>
    <option value="">Select A Category</option>
    <?php 
    $query = "SELECT * FROM category";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        foreach ($result as $row) {
            $name = htmlspecialchars($row['name']);
            $id = htmlspecialchars($row['id']);
            echo "<option value=\"$id\">$name</option>";
        }
    } else {
        echo "<option disabled>No categories available</option>";
    }
    ?>
</select>
