// app/javascript/profile.js
document.addEventListener("DOMContentLoaded", function() {
  const imageInput = document.getElementById("imageUpload");
  if (imageInput) {
    const preview = document.getElementById("imagePreview");
    const placeholder = document.getElementById("placeholder");
    imageInput.addEventListener("change", function(event) {
      const file = event.target.files[0];
      if (file && file.type.match("image.*")) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove("hidden");
          if (placeholder) placeholder.classList.add("hidden");
        };
        reader.readAsDataURL(file);
      }
    });
  }
});
//# sourceMappingURL=/assets/profile.js.map
