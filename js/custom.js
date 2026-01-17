const dropArea = document.getElementById("dropArea");
const fileInput = document.getElementById("fileInput");
const previewImage = document.getElementById("previewImage");

if (dropArea && fileInput) {
    // Open file picker on click
    dropArea.addEventListener("click", () => fileInput.click());
}

// Handle file selection
if (fileInput) {
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            showPreview(fileInput.files[0]);
        }
    });
}

// Preview function
function showPreview(file) {
    if (!previewImage) {
        return;
    }
    const reader = new FileReader();
    reader.onload = () => {
        previewImage.src = reader.result;
        previewImage.classList.remove("d-none");
    };
    reader.readAsDataURL(file);
}
