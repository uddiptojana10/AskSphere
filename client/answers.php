<?php
// Session already started in question-details.php, no need to start again
include(__DIR__ . '/../common/auth.php');
include(__DIR__ . '/../common/db.php');

// Ensure question ID is available (already validated in question-details.php)
$qid = isset($qid) ? intval($qid) : 0;
if ($qid <= 0) {
    echo "<p>Invalid question ID.</p>";
    exit;
}

// Get user ID from session
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
?>

<div class="container">
    <div class="offset-sm-1">
        <h5 class="heading text-neon mb-4">Answers:</h5>

        <?php 
        $query = "SELECT a.id, a.answer, a.user_id as answer_user_id,
                  (SELECT COUNT(*) FROM answer_votes WHERE answer_id = a.id AND vote_type = 'upvote') as upvotes,
                  (SELECT COUNT(*) FROM answer_votes WHERE answer_id = a.id AND vote_type = 'downvote') as downvotes
                  FROM answers a WHERE a.question_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $qid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $answer_id = intval($row['id']);
                $answer = htmlspecialchars($row['answer']);
                $answer_user_id = intval($row['answer_user_id']);
                $upvotes = intval($row['upvotes']);
                $downvotes = intval($row['downvotes']);
                $net_votes = $upvotes - $downvotes;

                // Check if the user has already voted on this answer
                $user_vote = null;
                $has_voted = false;
                if ($user_id) {
                    $vote_query = "SELECT vote_type FROM answer_votes WHERE answer_id = ? AND user_id = ?";
                    $vote_stmt = $conn->prepare($vote_query);
                    $vote_stmt->bind_param('ii', $answer_id, $user_id);
                    $vote_stmt->execute();
                    $vote_result = $vote_stmt->get_result();
                    if ($vote_result && $vote_result->num_rows > 0) {
                        $vote_row = $vote_result->fetch_assoc();
                        $user_vote = $vote_row['vote_type'];
                        $has_voted = true;
                    }
                    $vote_stmt->close();
                }

                // Check if this is the user's own answer
                $is_own_answer = $user_id && $user_id === $answer_user_id;

                // Determine button states
                $upvote_disabled = $user_id ? ($has_voted || $is_own_answer ? 'disabled' : '') : 'disabled';
                $downvote_disabled = $user_id ? ($has_voted || $is_own_answer ? 'disabled' : '') : 'disabled';
                $upvote_class = $user_vote === 'upvote' ? 'voted' : '';
                $downvote_class = $user_vote === 'downvote' ? 'voted' : '';
                $login_prompt = $user_id ? '' : '<p class="text-muted small">Please log in to vote.</p>';
                $own_answer_prompt = $is_own_answer ? '<p class="text-muted small">You cannot vote on your own answer.</p>' : '';
                ?>

                <div class="row answer-wrapper mb-3">
                    <p class="answer-text"><?php echo $answer; ?></p>
                    <div class="vote-section d-flex align-items-center ms-3">
                        <button class="vote-btn vote-up me-2 <?php echo $upvote_class; ?>" 
                                data-answer-id="<?php echo $answer_id; ?>" 
                                data-vote-type="upvote" 
                                <?php echo $upvote_disabled; ?>>
                            ▲
                        </button>
                        <span class="vote-count me-2"><?php echo $net_votes; ?></span>
                        <button class="vote-btn vote-down <?php echo $downvote_class; ?>" 
                                data-answer-id="<?php echo $answer_id; ?>" 
                                data-vote-type="downvote" 
                                <?php echo $downvote_disabled; ?>>
                            ▼
                        </button>
                    </div>
                </div>
                <?php 
                if (!$user_id) echo $login_prompt;
                if ($is_own_answer) echo $own_answer_prompt;
                ?>
            <?php
            }
        } else {
            echo "<p>No answers found for this question.</p>";
        }
        $stmt->close();
        ?>
    </div>
</div>

<script>
// JavaScript to handle voting via AJAX
document.querySelectorAll('.vote-btn').forEach(button => {
    button.addEventListener('click', function() {
        const answerId = this.getAttribute('data-answer-id');
        const voteType = this.getAttribute('data-vote-type');

        fetch('/AskSphere/server/requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=vote&answer_id=${answerId}&vote_type=${voteType}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update vote count
                const voteCount = this.parentElement.querySelector('.vote-count');
                voteCount.textContent = data.net_votes;

                // Disable both buttons and mark the voted one
                const siblingButton = this.parentElement.querySelector(voteType === 'upvote' ? '.vote-down' : '.vote-up');
                this.classList.add('voted');
                this.setAttribute('disabled', true);
                siblingButton.setAttribute('disabled', true);
            } else {
                alert(data.message || 'Error processing vote.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while voting.');
        });
    });
});
</script>