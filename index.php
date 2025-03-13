<!-- @format -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>EVENT X - Your Gateway to Live Events</title>
  <!-- Favicon-->
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body id="page-top">
  <?php include "./component/header.php" ?>


  <!-- Masthead-->
  <header class="masthead">
    <div class="container">
      <div class="masthead-subheading">Welcome To EventX!</div>
      <div class="masthead-heading text-uppercase">Your Gateway to Live Events</div>
      <a class="btn btn-primary btn-xl text-uppercase" href="#services">Explore Events</a>
    </div>
  </header>

  <!-- Services Section-->
  <section class="page-section" id="services">
    <div class="container">
      <div class="text-center">
        <h2 class="section-heading text-uppercase">Our Services</h2>
        <h3 class="section-subheading text-muted">Everything you need to attend and enjoy your favorite events.</h3>
      </div>
      <div class="row text-center">
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-ticket-alt fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="my-3">Buy Tickets</h4>
          <p class="text-muted">Secure your spot at concerts, sports, festivals, and more.</p>
        </div>
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-calendar-alt fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="my-3">Event Calendar</h4>
          <p class="text-muted">Check upcoming events, timings, and locations near you.</p>
        </div>
        <div class="col-md-4">
          <span class="fa-stack fa-4x">
            <i class="fas fa-circle fa-stack-2x text-primary"></i>
            <i class="fas fa-star fa-stack-1x fa-inverse"></i>
          </span>
          <h4 class="my-3">Top Rated Shows</h4>
          <p class="text-muted">Discover trending events based on user ratings and reviews.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Portfolio Grid Section (Now: Featured Events) -->
  <section class="page-section bg-light" id="portfolio">
    <div class="container">
      <div class="text-center">
        <h2 class="section-heading text-uppercase">Featured Events</h2>
        <h3 class="section-subheading text-muted">Don’t miss out on these top picks!</h3>
      </div>
      <div class="row">
        <!-- Example Event Card -->
        <div class="col-lg-4 col-sm-6 mb-4">
          <div class="portfolio-item">
            <a class="portfolio-link" data-bs-toggle="modal" href="#eventModal1">
              <div class="portfolio-hover">
                <div class="portfolio-hover-content"><i class="fas fa-plus fa-3x"></i></div>
              </div>
              <img class="img-fluid" src="assets/img/events/concert.jpg" alt="..." />
            </a>
            <div class="portfolio-caption">
              <div class="portfolio-caption-heading">Rock Fest 2025</div>
              <div class="portfolio-caption-subheading text-muted">Live Concert</div>
            </div>
          </div>
        </div>

        <!-- Repeat for more events... -->
      </div>
    </div>
  </section>

  <!-- Event Modal 1 -->
  <div class="portfolio-modal modal fade" id="eventModal1" tabindex="-1" aria-labelledby="eventModal1Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="close-modal" data-bs-dismiss="modal"><i class="fas fa-times"></i></div>
        <div class="modal-body">
          <!-- Event Details -->
          <h2 class="text-uppercase">Rock Fest 2025</h2>
          <p class="item-intro text-muted">Join thousands of fans for an unforgettable music experience.</p>
          <img class="img-fluid d-block mx-auto" src="assets/img/events/concert.jpg" alt="..." />
          <p>Experience electrifying performances by top rock bands from around the world. This year's Rock Fest features 3 stages, food trucks, VIP lounges, and more!</p>
          <ul class="list-inline">
            <li><strong>Date:</strong> July 18–20, 2025</li>
            <li><strong>Location:</strong> Bukit Jalil Stadium, Kuala Lumpur</li>
            <li><strong>Category:</strong> Music Festival</li>
          </ul>
          <button class="btn btn-primary btn-xl text-uppercase" data-bs-dismiss="modal" type="button">
            <i class="fas fa-xmark me-1"></i>
            Close Event
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">
      <div class="text-center">
        <h2 class="section-heading text-uppercase">Get In Touch</h2>
        <h3 class="section-subheading text-muted">Have questions about events or ticketing?</h3>
      </div>
      <form id="contactForm" data-sb-form-api-token="API_TOKEN">
        <div class="row align-items-stretch mb-5">
          <div class="col-md-6">
            <!-- Name input-->
            <div class="form-group">
              <input class="form-control" id="name" type="text" placeholder="Your Name *" required />
            </div>
            <!-- Email input-->
            <div class="form-group">
              <input class="form-control" id="email" type="email" placeholder="Your Email *" required />
            </div>
            <!-- Phone input-->
            <div class="form-group mb-md-0">
              <input class="form-control" id="phone" type="tel" placeholder="Your Phone *" required />
            </div>
          </div>
          <div class="col-md-6">
            <!-- Message input-->
            <div class="form-group form-group-textarea mb-md-0">
              <textarea class="form-control" id="message" placeholder="Your Message *" required></textarea>
            </div>
          </div>
        </div>
        <!-- Submit Button-->
        <div class="text-center">
          <button class="btn btn-primary btn-xl text-uppercase" id="submitButton" type="submit">Send Message</button>
        </div>
      </form>
    </div>
  </section>

  <?php include "./component/footer.php"; ?>


  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
  <!-- * *                               SB Forms JS                               * *-->
  <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
</body>

</html>