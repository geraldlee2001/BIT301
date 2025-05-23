<?php
include "../php/tokenDecoding.php";
$username = $decoded->username;
$role = $decoded->role;
switch ($role) {
  case "ADMIN":
    echo ' <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Management</div>
            <a class="nav-link" href="./index.php">
              <div class="sb-nav-link-icon">
                <i class="fas fa-tachometer-alt"></i>
              </div>
              Customer
            </a>
            <a class="nav-link" href="./organizers.php">
              <div class="sb-nav-link-icon">
                <i class="fas fa-tachometer-alt"></i>
              </div>
              Organizer
            </a>

            <a class="nav-link" href="./analytics.php">
            <div class="sb-nav-link-icon">
              <i class="fas fa-tachometer-alt"></i>
            </div>
            Analytics
          </a>
          </div>
        </div>
        <div class="sb-sidenav-footer">
          <div class="small">Logged in as:</div>
          ' . $username . '
        </div>
      </nav>
    </div>';
    break;
  case "MERCHANT":
    echo ' <div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
      <div class="sb-sidenav-menu">
        <div class="nav">
          <div class="sb-sidenav-menu-heading">Management</div>

          <a class="nav-link" href="./products.php">
          <div class="sb-nav-link-icon">
            <i class="fas fa-box"></i>
          </div>
          Events
          </a>

          <a class="nav-link" href="./promo_codes.php">
          <div class="sb-nav-link-icon">
            <i class="fas fa-tag"></i>
          </div>
          Promo Codes
          </a>
           <a class="nav-link" href="./analytics.php">
            <div class="sb-nav-link-icon">
              <i class="fas fa-tachometer-alt"></i>
            </div>
            Analytics
          </a>
        </div>
      </div>
      <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        ' . $username . '
      </div>
    </nav>
  </div>';
    break;
} ?>

<link href="css/styles.css" rel="stylesheet" />