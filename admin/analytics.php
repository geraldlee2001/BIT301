<?php
include '../php/tokenDecoding.php';
include "../php/databaseConnection.php";
$query = @!!isset($decoded->merchantId) ? "SELECT *FROM product WHERE merchantId = '$decoded->merchantId'" : "SELECT *FROM product ";
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
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
          <label for="eventId">Select Event:</label>
          <select id="eventId" onchange="loadChartData()">
            <?php
            while ($item = $data->fetch_assoc()) {
              echo '<option value="' . $item["ID"]  . '">' . $item['name'] . '</option>';
            } ?>
          </select>



          <canvas id="salesChart"></canvas>
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
          const labels = data.map(item => item.period);
          const tickets = data.map(item => parseInt(item.total_tickets));
          const revenue = data.map(item => parseFloat(item.revenue / 1000));

          if (chart) chart.destroy();

          const ctx = document.getElementById('salesChart').getContext('2d');
          chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [{
                  label: 'Tickets Sold',
                  data: tickets,
                  backgroundColor: 'rgba(75, 192, 192, 0.6)'
                },
                {
                  label: 'Revenue (RM /K)',
                  data: revenue,
                  backgroundColor: 'rgba(255, 159, 64, 0.6)'
                }
              ]
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