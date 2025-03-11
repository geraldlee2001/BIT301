/** @format */

// @ts-nocheck
/**
 * !
 * Start Bootstrap - Agency v7.0.12 (https://startbootstrap.com/theme/agency)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-agency/blob/master/LICENSE)
 *
 * @format
 */

//
// Scripts
//
// JavaScript to show/hide the modal
var modal = document.getElementById("myModal");
var cartBtn = document.getElementById("cartBtn");
var showModalButton = document.getElementById("profile");

showModalButton.addEventListener("click", function () {
  modal.style.display = "block";
});

// Close the modal when clicking outside the content
modal.addEventListener("click", function (e) {
  if (e.target === modal) {
    modal.style.display = "none";
  }
});

// Close the modal when the "Logout" button is clicked
var logoutButton = document.getElementById("logout-button");
logoutButton.addEventListener("click", function () {
  document.cookie.split(";").forEach(function (c) {
    document.cookie = c
      .replace(/^ +/, "")
      .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
  });
  modal.style.display = "none";
  window.location.pathname === "/"
    ? location.reload()
    : (window.location.href = "/");
});

// Implement actions for "Profile" and "Settings" buttons
var profileButton = document.getElementById("profile-button");
var historyButton = document.getElementById("history-button");

profileButton.addEventListener("click", function () {
  // Implement the profile action here
  window.location.href = "/profile.php";
});

historyButton.addEventListener("click", function () {
  // Implement the settings action here
  window.location.href = "/purchase_history.php";
});

window.addEventListener("DOMContentLoaded", (event) => {
  // Navbar shrink function
  var navbarShrink = function () {
    const navbarCollapsible = document.body.querySelector("#mainNav");
    if (!navbarCollapsible) {
      return;
    }
    if (window.scrollY === 0) {
      navbarCollapsible.classList.remove("navbar-shrink");
    } else {
      navbarCollapsible.classList.add("navbar-shrink");
    }
  };

  // Shrink the navbar
  navbarShrink();

  // Shrink the navbar when page is scrolled
  document.addEventListener("scroll", navbarShrink);

  //  Activate Bootstrap scrollspy on the main nav element
  const mainNav = document.body.querySelector("#mainNav");
  if (mainNav) {
    new bootstrap.ScrollSpy(document.body, {
      target: "#mainNav",
      rootMargin: "0px 0px -40%",
    });
  }

  // Collapse responsive navbar when toggler is visible
  const navbarToggler = document.body.querySelector(".navbar-toggler");
  const responsiveNavItems = [].slice.call(
    document.querySelectorAll("#navbarResponsive .nav-link")
  );
  responsiveNavItems.map(function (responsiveNavItem) {
    responsiveNavItem.addEventListener("click", () => {
      if (window.getComputedStyle(navbarToggler).display !== "none") {
        navbarToggler.click();
      }
    });
  });
});

cartBtn.addEventListener("click", function () {
  window.location.href = "/cart.php";
});


