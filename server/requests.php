<?php
session_start();
include(__DIR__ . '/../common/db.php');

// Define the base URL dynamically
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

// Helper function to redirect with error
function redirectWithError($location, $error) {
    // Check if the location already contains a query string
    $separator = (strpos($location, '?') !== false) ? '&' : '?';
    $redirect_url = $location . $separator . "error=" . urlencode($error);
    error_log("Redirecting to $redirect_url with error: $error");
    header("Location: $redirect_url");
    exit;
}

// Signup
if (isset($_POST['signup'])) {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, address) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        redirectWithError($base_url . "?signup=true", "Database error");
    }
    $stmt->bind_param('ssss', $username, $email, $password, $address);
    $result = $stmt->execute();

    if ($result) {
        $user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user'] = ['username' => $username, 'email' => $email, 'user_id' => $user_id];
        error_log("Signup successful, user_id: $user_id");
        header("Location: $base_url");
        exit;
    } else {
        error_log("Signup failed: " . $stmt->error);
        redirectWithError($base_url . "?signup=true", "Failed to register user");
    }
    $stmt->close();
}

// Login
if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        redirectWithError($base_url . "?login=true", "Database error");
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $user_id = $row['id'];
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user'] = ['username' => $username, 'email' => $email, 'user_id' => $user_id];
            error_log("Login successful, user_id: $user_id");
            header("Location: $base_url");
            exit;
        } else {
            error_log("Login failed: Incorrect password for email $email");
            redirectWithError($base_url . "?login=true", "Incorrect password");
        }
    } else {
        error_log("Login failed: User not found for email $email");
        redirectWithError($base_url . "?login=true", "User not found");
    }
    $stmt->close();
}

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    error_log("User logged out");
    header("Location: $base_url");
    exit;
}

// Ask a question
if (isset($_POST["ask"])) {
    if (!isset($_SESSION['user_id'])) {
        redirectWithError($base_url . "?login=true", "Please log in to ask a question");
    }

    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $category_id = intval($_POST['category']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO questions (title, description, category_id, user_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        redirectWithError($base_url . "?ask=true", "Database error");
    }
    $stmt->bind_param('ssii', $title, $description, $category_id, $user_id);
    $result = $stmt->execute();

    if ($result) {
        error_log("Question added by user_id: $user_id");
        header("Location: $base_url");
        exit;
    } else {
        error_log("Question add failed: " . $stmt->error);
        redirectWithError($base_url . "?ask=true", "Failed to add question");
    }
    $stmt->close();
}

// Submit an answer
if (isset($_POST["answer"])) {
    if (!isset($_SESSION['user_id'])) {
        redirectWithError($base_url . "?login=true", "Please log in to submit an answer");
    }

    $answer = filter_var($_POST['answer'], FILTER_SANITIZE_STRING);
    $question_id = intval($_POST['question_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO answers (answer, question_id, user_id) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        redirectWithError($base_url . "?q-id=$question_id", "Database error");
    }
    $stmt->bind_param('sii', $answer, $question_id, $user_id);
    $result = $stmt->execute();

    if ($result) {
        error_log("Answer added by user_id: $user_id for question_id: $question_id");
        header("Location: $base_url?q-id=$question_id");
        exit;
    } else {
        error_log("Answer add failed: " . $stmt->error);
        redirectWithError($base_url . "?q-id=$question_id", "Failed to submit answer");
    }
    $stmt->close();
}

// Delete a question
if (isset($_GET["delete"])) {
    if (!isset($_SESSION['user_id'])) {
        redirectWithError($base_url . "?login=true", "Please log in to delete a question");
    }

    $qid = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        redirectWithError($base_url, "Database error");
    }
    $stmt->bind_param('ii', $qid, $_SESSION['user_id']);
    $result = $stmt->execute();

    if ($result) {
        error_log("Question deleted, id: $qid by user_id: " . $_SESSION['user_id']);
        header("Location: $base_url");
        exit;
    } else {
        error_log("Question delete failed: " . $stmt->error);
        redirectWithError($base_url, "Failed to delete question");
    }
    $stmt->close();
}

// Handle voting action
if (isset($_POST['action']) && $_POST['action'] === 'vote') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to vote.']);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $answer_id = isset($_POST['answer_id']) ? intval($_POST['answer_id']) : 0;
    $vote_type = isset($_POST['vote_type']) && in_array($_POST['vote_type'], ['upvote', 'downvote']) ? $_POST['vote_type'] : null;

    if (!$answer_id || !$vote_type) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    $check_query = "SELECT * FROM answer_votes WHERE answer_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param('ii', $answer_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already voted on this answer.']);
        exit;
    }

    $insert_query = "INSERT INTO answer_votes (answer_id, user_id, vote_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('iis', $answer_id, $user_id, $vote_type);
    $success = $stmt->execute();

    if ($success) {
        $upvote_query = "SELECT COUNT(*) as count FROM answer_votes WHERE answer_id = ? AND vote_type = 'upvote'";
        $downvote_query = "SELECT COUNT(*) as count FROM answer_votes WHERE answer_id = ? AND vote_type = 'downvote'";

        $stmt = $conn->prepare($upvote_query);
        $stmt->bind_param('i', $answer_id);
        $stmt->execute();
        $upvotes = $stmt->get_result()->fetch_assoc()['count'];

        $stmt = $conn->prepare($downvote_query);
        $stmt->bind_param('i', $answer_id);
        $stmt->execute();
        $downvotes = $stmt->get_result()->fetch_assoc()['count'];

        $net_votes = $upvotes - $downvotes;
        echo json_encode(['success' => true, 'net_votes' => $net_votes]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record vote.']);
    }
    $stmt->close();
    exit;
}
?>  