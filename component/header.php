 <!-- Navigation-->
 <?php require_once './vendor/autoload.php';
  ?>
 <nav class="navbar navbar-expand-lg navbar-dark fixed-top " id="mainNav">
   <div class="container">
     <a class="navbar-brand" href="../">
       <img src="images/Logo Creator (Community) (1).png" alt="..." width="50" style="margin-right: 10px;" />Event X</a>
     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
       Menu
       <i class="fas fa-bars ms-1"></i>
     </button>
     <div class="collapse navbar-collapse" id="navbarResponsive">
       <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
         <?php
          switch ($_SERVER['REQUEST_URI']) {
            case "/login.php":
            case "/signup.php":
            case "/profile_create.php":
              echo "<div/>";
              break;
            case "/index.php":
            case "/":
              echo ' <li class="nav-item">
          <a class="nav-link" href="/event.php">Event</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contact">Contact</a>
        </li>';
              break;
            case "/product.php":
            default:
              break;
          }
          ?>

         <li class="nav-item">
           <div class="flex">
             <?php require_once './vendor/autoload.php';
              @!!include './component/profileButton.php';
              ?>
             <div class="modal" id="myModal">
               <div class="modal-content">
                 <button class="btn" id="profile-button">Profile</button>
                 <button class="btn" id="history-button">Purchased</button>
                 <button class="btn" id="logout-button">Logout</button>
               </div>
             </div>
           </div>
         </li>

         <!-- <li class="nav-item">
           <ul class="navbar-nav ">
             <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                 <?php
                  @!!include './component/profileButton.php';
                  ?>
               </a>
               <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                 <li> <button class="btn" id="profile-button">Profile</button></li>
                 <li> <button class="btn" id="settings-button">Settings</button></li>
                 <li>
                   <hr class="dropdown-divider" />
                 </li>
                 <li> <button class="btn" id="logout-button">Logout</button></li>
               </ul>
             </li>
           </ul>
         </li> -->
       </ul>

     </div>
   </div>
 </nav>

 <script src="js/scripts.js"></script>