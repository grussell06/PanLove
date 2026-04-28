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

document.getElementById('ajax-post-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop the page from refreshing

    const formData = new FormData(this); // Automatically grabs text and files
    const feed = document.getElementById('feed-container');

    fetch('createPost.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Expecting JSON back from PHP
    .then(data => {
        if (data.status === 'success') {
            // 1. Create a new post element
            const newPost = document.createElement('div');
            newPost.className = 'post-card';
            newPost.innerHTML = `
                <strong>${data.fname} ${data.lname}</strong>
                <p>${data.content}</p>
                ${data.imageName ? `<img src="../uploads/${data.imageName}">` : ''}
            `;

            // 2. Add it to the VERY TOP of the feed
            feed.insertBefore(newPost, feed.firstChild);

            // 3. Reset the form and close your modal
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('postModal')).hide();
        }
    })
    .catch(error => console.error('Error:', error));
});
