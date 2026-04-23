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
    <link rel="stylesheet" href="https://jsdelivr.net">


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Homepage</title>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600;700&family=Original+Surfer&display=swap');
    </style>
  </head>
  <body>
    <ul class = "navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
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
    <h1 class = "atma-semibold">Welcome to PanLove!</h1>
  

</div>

<div class="container d-flex justify-content-center align-items-center my-4">
    <!-- Profile Picture Container -->
    <div class="profile-pic-preview me-3">
        <!-- Replace 'temp-profile.jpg' with your actual temp filename later -->
        <img src="../images/temp-profile.jpg" alt="Profile" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #f3b1af;">
    </div>

    <!-- The "What's on your mind?" Button -->
    <button type="button" id="postButton" data-bs-toggle="modal" data-bs-target="#postModal">
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
        <textarea name="content" id="userText" class="form-control mb-3" rows="4" placeholder="What's on your mind?" required></textarea>
    
        <!-- Hidden input -->
        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*">

        <!-- "Add Photo" button -->
          <div class="mb-3">
            <button type="button" id="addPhotoBtn" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('imageInput').click()">
              Add Photo
            </button>
          </div>

        <!--Preview container with 'X' button -->
        <div id="imagePreviewContainer" class="mb-3" style="display: none; text-align: center; position: relative; border: 1px solid #f49f51; padding: 5px; border-radius: 8px;">
          <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 250px; border-radius: 5px;">
        
          <!--The X button to delete/remove the photo -->
          <button type="button" id="removePhoto" class="btn-close" aria-label="Close" 
                style="position: absolute; top: 10px; right: 10px; background-color: white; border: 1px solid #eee;"></button>
        </div>

      <div class="d-grid">
        <button type="submit" name="submitPost" id="postBtn" class="btn btn-primary">Post</button>
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
        $query = "SELECT users.fname, users.lname, posts.user_id, posts.post_id, posts.content, posts.imageName, posts.timestamp, 
                  (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count
                  FROM posts 
                  JOIN users ON posts.user_id = users.user_id 
                  ORDER BY posts.timestamp DESC";        
        $stmt = $pdo->query($query);

        // Display posts
        while ($row = $stmt->fetch()) {
            echo '<div class="post-card">';
              echo '<strong>' . htmlspecialchars($row['fname'] . ' ' . $row['lname']) . '</strong>';
              $formattedDate = date("F j, Y, g:i a", strtotime($row['timestamp']));
              echo '<small style="display: block; color: #664171;">' . $formattedDate . '</small>';
              echo '<p style="margin-top: 10px;">' . htmlspecialchars($row['content']) . '</p>';
            //Display image if uploaded
              if (!empty($row['imageName'])) {
                echo '<div class="post-image">';
                  echo '<img src="../uploads' . htmlspecialchars($row['imageName']) .'"alt = "Post photo">';
                echo '</div>';
              }
                echo '<div class="post-footer" style="margin-top: 15px; border-top: 1px solid #ffffff; padding-top: 10px;">';
                echo '  <button class="btn btn-light btn-sm like-btn" id="likeBtn" data-post-id="' . $row['post_id'] . '">';
                echo '     ❤️ <span class="like-count">' . $row['like_count'] . '</span>';
                echo '  </button>';

                $p_id = $row['post_id'];
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
                echo '<a href="deletePost.php?post_id=' . $p_id . '"  
                         id="deletePostBtn"
                         onclick="return confirm(\'Are you sure you want to delete this post?\')">
                         🗑️
                      </a>';
            }
              echo '</div>';
            


echo '<div class="post-footer mt-2">';
    //Toggle link
    echo '<a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseComments' . $p_id . '" role="button">
            💬
          </a>';

    //Hidden container so comments aren't automatically displayed
    echo '<div class="collapse mt-2" id="collapseComments' . $p_id . '">';
        echo '<div class="p-2 mt-2 bg-light rounded">';
            
        echo '<div id="comment-list-' . $p_id . '">';
            // --- DISPLAY COMMENTS ---
            $commStmt = $pdo->prepare("SELECT users.fname, comments.comment_text, comments.user_id, comments.comment_id 
                                       FROM comments 
                                       JOIN users ON comments.user_id = users.user_id 
                                       WHERE comments.post_id = ? 
                                       ORDER BY comments.timestamp ASC");
            $commStmt->execute([$p_id]);
            
            while ($comment = $commStmt->fetch()) {
                echo '<div class="mb-2">';
                echo '  <strong>' . htmlspecialchars($comment['fname']) . ':</strong> ' . htmlspecialchars($comment['comment_text']);
                
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
                  echo '  <a href="deleteComment.php?comment_id=' . $comment['comment_id'] . '"  
                   text-decoration: none;"
                   onclick="return confirm(\'Delete comment?\')">
                   <i class="bi bi-trash"></i> 🗑️
                </a>';
    }
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
<script src="../js/home.js"></script>

  </body>
</html>