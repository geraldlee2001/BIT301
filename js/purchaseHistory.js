/** @format */

// @ts-nocheck
/** @format */

const onOpenModal = (cartItemId, productId) => {
  const modal = document.getElementById("reviewModal");
  const overlay = document.getElementById("overlay");
  if (modal == null) return;
  document.getElementById("productId").value = productId;
  document.getElementById("cartItemId").value = cartItemId;
  openModal(overlay, modal);
  overlay?.addEventListener("click", () => {
    closeModal(overlay, modal);
  });
};

const onCloseModal = () => {
  const modal = document.getElementById("reviewModal");
  const overlay = document.getElementById("overlay");
  if (modal == null) return;
  closeModal(overlay, modal);
};

function openModal(overlay, modal) {
  modal.classList.add("active");
  modal.style.display = "block";
  overlay?.classList.add("active");
}

function closeModal(overlay, modal) {
  modal.style.display = "none";
  modal.classList.remove("active");
  overlay?.classList.remove("active");
}

jQuery(document).ready(function ($) {
  $(".rating .star")
    .hover(function () {
      $(this).addClass("to_rate");
      $(this)
        .parent()
        .find(".star:lt(" + $(this).index() + ")")
        .addClass("to_rate");
      $(this)
        .parent()
        .find(".star:gt(" + $(this).index() + ")")
        .addClass("no_to_rate");
    })
    .mouseout(function () {
      $(this).parent().find(".star").removeClass("to_rate");
      $(this)
        .parent()
        .find(".star:gt(" + $(this).index() + ")")
        .removeClass("no_to_rate");
    })
    .click(function () {
      $(this).removeClass("to_rate").addClass("rated");
      $(this)
        .parent()
        .find(".star:lt(" + $(this).index() + ")")
        .removeClass("to_rate")
        .addClass("rated");
      $(this)
        .parent()
        .find(".star:gt(" + $(this).index() + ")")
        .removeClass("no_to_rate")
        .removeClass("rated");

      // Get the rating
      var rating = $(this).parent().find(".star.rated").length;
      const ratingInput = document.getElementById("rating");
      // @ts-ignore
      ratingInput.value = rating;
      // TODO: Save your rate here
    });
});
