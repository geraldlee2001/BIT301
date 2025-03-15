<?php
include '../php/tokenDecoding.php';
include "../php/databaseConnection.php";

// Fetch products for the dropdown (filtered by merchant if applicable)
$query = isset($decoded->merchantId)
  ? "SELECT * FROM product WHERE merchantId = '{$decoded->merchantId}'"
  : "SELECT * FROM product";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Analytics Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <?php include "./component/header.php" ?>
  <div id="layoutSidenav">
    <?php include "./component/sidebar.php" ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h1 class="mt-4">Analytics Dashboard</h1>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="period" class="form-label">Select Time Period:</label>
              <select id="period" class="form-select" onchange="loadChartData()">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>

            <div class="col-md-8">
              <label for="eventId" class="form-label">Select Event:</label>
              <select id="eventId" class="form-select" onchange="loadChartData()">
                <?php while ($item = $data->fetch_assoc()) {
                  echo '<option value="' . $item["ID"] . '">' . htmlspecialchars($item['name']) . '</option>';
                } ?>
              </select>
            </div>
          </div>

          <canvas id="salesChart" height="120"></canvas>
        </div>
      </main>
    </div>
  </div>

  <script>
    let chart;

    function loadChartData() {
      const period = document.getElementById('period').value;
      const eventId = document.getElementById('eventId').value;

      fetch(`/admin/php/getAnalytics.php?period=${period}&eventId=${eventId}`)
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            alert("No data available for the selected event and period.");
            if (chart) chart.destroy();
            return;
          }

          const labels = data.map(item => item.period);
          const seatOccupancy = data.map(item => parseInt(item.seat_occupancy));
          const revenue = data.map(item => parseFloat(item.revenue)); // In RM

          if (chart) chart.destroy();

          const ctx = document.getElementById('salesChart').getContext('2d');
          chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [{
                  label: 'Seats Booked',
                  data: seatOccupancy,
                  backgroundColor: 'rgba(75, 192, 192, 0.6)',
                  borderColor: 'rgba(75, 192, 192, 1)',
                  borderWidth: 1
                },
                {
                  label: 'Revenue (RM)',
                  data: revenue,
                  backgroundColor: 'rgba(255, 159, 64, 0.6)',
                  borderColor: 'rgba(255, 159, 64, 1)',
                  borderWidth: 1
                }
              ]
            },
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: 'Event Performance Analytics'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return `${context.dataset.label}: ${context.raw}`;
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Values'
                  }
                },
                x: {
                  title: {
                    display: true,
                    text: 'Period'
                  }
                }
              }
            }
          });
        })
        .catch(err => {
          console.error("Error fetching analytics data:", err);
          alert("Failed to load analytics. Please try again.");
        });
    }

    window.onload = loadChartData;
  </script>
</body>

</html>