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