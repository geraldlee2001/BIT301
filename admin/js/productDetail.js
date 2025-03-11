/** @format */

// Get the image upload input field.
const imageInput = document.getElementById("image");

// Get the image preview element.
const imagePreview = document.getElementById("imagePreview");

// Add an event listener to the image upload input field.
imageInput.addEventListener("change", function (event) {
  // Get the uploaded image file.
  const imageFile = event.target.files[0];

  // Create a new FileReader object.
  const reader = new FileReader();

  // Load the uploaded image file.
  reader.onload = function (event) {
    // Set the image preview element's src attribute to the loaded image data.
    imagePreview.src = event.target.result;
  };

  // Read the uploaded image file as a data URL.
  reader.readAsDataURL(imageFile);
});
