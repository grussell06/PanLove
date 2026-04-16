<?php
session_start();
?>
<?php
require_once "dbFuncs.php";
$pdo = connectDB();

//$sql = "SELECT id, title, description, event_date FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 10";
//$stmt = $pdo->prepare($sql);
//$stmt->execute();
//$upcomingEvents = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <link rel="stylesheet" href="../css/web.css">
    <link rel="stylesheet" href="../css/post.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Homepage</title>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Emilys+Candy&display=swap');
    </style>
  </head>
  <body>
    <ul class = "navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="./events.php">Create Event</a></li>
      <?php endif; ?>
        <li><a href="./announcements.php">Announcements</a></li>
        <li><a href="./myChapter.php">My Chapter</a></li>
        <?php
        if (isset($_SESSION['user_id'])) {
            // User is logged in
            echo '<li><a href="./profile.php">Profile</a></li>';
        } else {
            // User is not logged in
            echo '<li><a href="./login.php">Login</a></li>';
        }
        ?>
    </ul>
  <div class="content">
    <h1>Welcome to Panlove</h1>
    <p>This is where your main page data goes.</p>
  

</div>

<!-- Button that triggers the pop-up -->
<div class="container text-center my-4">
    <button type="button" id="postButton" class="btn btn-lg w-50" data-bs-toggle="modal" data-bs-target="#postModal">
        What's on your mind?
    </button>
</div>

<!-- Pop-up window -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="postModalLabel">Create Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="createPost.php" method="POST" enctype="multipart/form-data">
        <textarea name="content" class="form-control mb-3" rows="4" placeholder="What's on your mind?" required></textarea>
    
        <!-- Hidden input -->
        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">

        <!-- "Add Photo" button -->
          <div class="mb-3">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('imageInput').click()">
              Add Photo
            </button>
          </div>

        <!--Preview container with 'X' button -->
        <div id="imagePreviewContainer" class="mb-3" style="display: none; text-align: center; position: relative; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
          <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 250px; border-radius: 5px;">
        
          <!--The X button to delete/remove the photo -->
          <button type="button" id="removePhoto" class="btn-close" aria-label="Close" 
                style="position: absolute; top: 10px; right: 10px; background-color: white; border: 1px solid #ccc;"></button>
        </div>

      <div class="d-grid">
        <button type="submit" name="submitPost" class="btn btn-primary">Post</button>
      </div>
      </form>

      </div>
    </div>
  </div>
</div>


<div id="feed" class="container">
    <?php
    try {
        $pdo = connectDB(); 
        $query = "SELECT users.fname, users.lname, posts.post_id, posts.content, posts.imageName, posts.timestamp, 
                  (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count
                  FROM posts 
                  JOIN users ON posts.user_id = users.user_id 
                  ORDER BY posts.timestamp DESC";        
        $stmt = $pdo->query($query);

        // Display posts
        while ($row = $stmt->fetch()) {
            echo '<div class="post-card">';
              echo '<strong>' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</strong>';
              echo '<small style="display: block; color: #777;">' . $row['timestamp'] . '</small>';
              echo '<p style="margin-top: 10px;">' . htmlspecialchars($row['content']) . '</p>';
            //Display image if uploaded
              if (!empty($row['imageName'])) {
                echo '<div class="post-image">';
                  echo '<img src="uploads/' . htmlspecialchars($row['imageName']) .'"alt = "Post photo">';
                echo '</div>';
              }
              echo '<div class="post-footer" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">';
                echo '  <button class="btn btn-light btn-sm like-btn" data-post-id="' . $row['post_id'] . '">';
                echo '     👍 Like (<span class="like-count">' . $row['like_count'] . '</span>)';
                echo '  </button>';
              echo '</div>';
            
$p_id = $row['post_id'];

echo '<div class="post-footer mt-2">';
    //Toggle link
    echo '<a class="text-decoration-none small text-muted" data-bs-toggle="collapse" href="#collapseComments' . $p_id . '" role="button">
            View Comments
          </a>';

    //Hidden container so comments aren't automatically displayed
    echo '<div class="collapse mt-2" id="collapseComments' . $p_id . '">';
        echo '<div class="p-2 mt-2 bg-light rounded">';
            
        echo '<div id="comment-list-' . $p_id . '">';
            // --- DISPLAY COMMENTS ---
            $commStmt = $pdo->prepare("SELECT users.fname, comments.comment_text 
                                       FROM comments 
                                       JOIN users ON comments.user_id = users.user_id 
                                       WHERE comments.post_id = ? 
                                       ORDER BY comments.timestamp ASC");
            $commStmt->execute([$p_id]);
            
            while ($comment = $commStmt->fetch()) {
                echo '<div class="mb-2">';
                echo '  <strong>' . htmlspecialchars($comment['fname']) . ':</strong> ' . htmlspecialchars($comment['comment_text']);
                echo '</div>';
            }
          echo '</div>';
            // --- COMMENT FORM ---
          echo '<form class="comment-form d-flex mt-2" data-post-id="' . $p_id . '">';
            echo '<input type="hidden" name="post_id" value="' . $p_id . '">';
            echo '<input type="text" name="comment_text" class="form-control form-control-sm me-2" placeholder="Write a comment..." required>';
            echo '<button type="submit" class="btn btn-primary btn-sm">Reply</button>';
          echo '</form>';

                echo '</div>'; // End card-body
              echo '</div>'; // End collapse
            echo '</div>'; // End footer
            echo '</div>';

        }
    } catch (PDOException $e) {
        echo "Error loading feed: " . $e->getMessage();
    }
    ?>
</div>




<div class="footer" style="text-align: center;">
    <p>&copy; 2026 PanLove. All rights reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const imageInput = document.getElementById('imageInput');
  const previewContainer = document.getElementById('imagePreviewContainer');
  const previewImage = document.getElementById('imagePreview');

    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            // Read the file and set it as the image source
            previewImage.src = URL.createObjectURL(file);
            // Show the preview container
            previewContainer.style.display = 'block';
        } else {
            // Hide if no file is selected
            previewContainer.style.display = 'none';
        }
    }
    document.getElementById('removePhoto').onclick = () => {
      imageInput.value = ""; // Clears the file from the input
    previewContainer.style.display = 'none'; // Hides the preview
    };

    document.querySelectorAll('.like-btn').forEach(button => {
    button.onclick = function() {
        const postId = this.getAttribute('data-post-id');
        const countSpan = this.querySelector('.like-count');

        // Fetch is a modern way to do AJAX
        fetch(`likePost.php?post_id=${postId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the number inside the span instantly
                    countSpan.innerText = data.newCount;
                }
            })
            .catch(error => console.error('Error:', error));
    };
});

document.querySelectorAll('.comment-form').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault(); // Stop the page from refreshing

        const formData = new FormData(this);
        const postId = this.getAttribute('data-post-id');
        const commentList = document.getElementById(`comment-list-${postId}`);
        const inputField = this.querySelector('input[name="comment_text"]');

        fetch('addComment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Create the new comment HTML element
                const newComment = document.createElement('div');
                newComment.className = 'mb-2';
                newComment.innerHTML = `<strong>${data.fname}:</strong> ${data.comment_text}`;
                
                // Add it to the list and clear the input
                commentList.appendChild(newComment);
                inputField.value = ''; 
            }
        })
        .catch(error => console.error('Error:', error));
    };
});
  </script>
  </body>
</html>