document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggleCreate");
    const createForm = document.getElementById("createForm");

    if (toggleButton && createForm) {
        toggleButton.addEventListener("click", function () {
            createForm.classList.toggle("hidden");

            if (createForm.classList.contains("hidden")) {
                toggleButton.textContent = "Create Announcement";
            } else {
                toggleButton.textContent = "Hide Form";
            }
        });
    }
});