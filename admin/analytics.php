<?php
include '../php/tokenDecoding.php';
include "../php/databaseConnection.php";

$userRole = $decoded->role ?? 'MERCHANT';
$merchantId = $decoded->merchantId ?? null;

// Query events if user is an organizer
$query = $merchantId ? "SELECT * FROM product WHERE merchantId = '$merchantId'" : "SELECT * FROM product";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Analytics Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="sb-nav-fixed">
  <?php include "./component/header.php" ?>
  <div id="layoutSidenav">
    <?php include "./component/sidebar.php" ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h1>Analytics Dashboard</h1>

          <label for="period">Select Time Period:</label>
          <select id="period" onchange="loadChartData()">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>

          <?php if ($userRole === 'MERCHANT') : ?>
            <label for="eventId">Select Event:</label>
            <select id="eventId" onchange="loadChartData()">
              <?php while ($item = $data->fetch_assoc()) : ?>
                <option value="<?= $item['ID'] ?>"><?= $item['name'] ?></option>
              <?php endwhile; ?>
            </select>
          <?php endif; ?>

          <canvas id="salesChart"></canvas>
        </div>
      </main>
    </div>
  </div>

  <script>
    let chart;

    function loadChartData() {
      const period = document.getElementById('period').value;
      const eventId = document.getElementById('eventId') ? document.getElementById('eventId').value : null;
      const role = '<?= strtolower($userRole) ?>';

      fetch(`/admin/php/getAnalytics.php?period=${period}&eventId=${eventId}&role=${role}`)
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            alert("No data available for the selected period. Try changing the date range or selecting a different event.");
            if (chart) chart.destroy();
            return;
          }

          const labels = data.map(item => item.period);
          let datasets = [];
          if (role === 'merchant') {
            // Event Organizer View
            datasets = [{
                label: 'Tickets Sold',
                data: data.map(item => parseInt(item.total_tickets)),
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
              },
              {
                label: 'Revenue (RM /100)',
                data: data.map(item => parseFloat(item.revenue / 100)),
                backgroundColor: 'rgba(255, 159, 64, 0.6)'
              }
            ];
            // Seat occupancy excluded for merchant
          } else {
            datasets = [{
                label: 'Total Events Hosted',
                data: data.map(item => parseInt(item.total_events)),
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
              },
              {
                label: 'Total Seats Booked',
                data: data.map(item => parseInt(item.total_booked_seats)),
                backgroundColor: 'rgba(255, 99, 132, 0.6)'
              }
            ];

          }

          if (chart) chart.destroy();

          const ctx = document.getElementById('salesChart').getContext('2d');
          chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        })
        .catch(err => console.error("Error fetching data:", err));
    }

    window.onload = loadChartData;
  </script>
</body>

</html>