/** @format */

// @ts-nocheck

const increaseQuantity = (button) => {
  const form = button.closest(".cart-item-form");
  const currentQuantitySpan = form.querySelector(".current-quantity");
  const currentQuantity = parseInt(currentQuantitySpan.value);
  const newQuantity = currentQuantity + 1;
  currentQuantitySpan.value = newQuantity;

  // Enable the submit button and trigger the form submission
  form.querySelector('input[type="submit"]').click();
};

const decreaseQuantity = (button) => {
  const form = button.closest(".cart-item-form");
  const currentQuantitySpan = form.querySelector(".current-quantity");
  const currentQuantity = parseInt(currentQuantitySpan.value);
  const newQuantity = currentQuantity - 1;
  currentQuantitySpan.value = newQuantity;

  // Enable the submit button and trigger the form submission
  form.querySelector('input[type="submit"]').click();
};

const deleteItem = (button) => {
  const form = button.closest(".cart-item-form");
  form.querySelector(".current-quantity").value = "0"; // Set quantity to 0
  form.querySelector('input[type="submit"]').click();
};
