<?php
include "../php/databaseConnection.php";
include "../php/tokenDecoding.php";
require_once '../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

if (!isset($_GET['id'])) {
  header('Location: products.php');
  exit;
}

$eventId = $_GET['id'];

// Fetch event details
$eventSql = "SELECT * FROM product WHERE id = ?";
$stmt = $conn->prepare($eventSql);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
  header('Location: products.php');
  exit;
}

// Handle ticket type creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'create_ticket_type') {
    try {
      $ticketTypeId = Uuid::uuid4();
      $name = $_POST['name'];
      $price = $_POST['price'];
      $maxQuantity = $_POST['maxQuantity'];
      $restrictions = $_POST['restrictions'];

      $sql = "INSERT INTO ticket_types (id, eventId, name, price, maxQuantity, restrictions) 
                  VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssdis", $ticketTypeId, $eventId, $name, $price, $maxQuantity, $restrictions);

      if ($stmt->execute()) {
        // If seat assignments were provided
        if (isset($_POST['selected_seats']) && !empty($_POST['selected_seats'])) {
          $selectedSeats = explode(',', $_POST['selected_seats']);
          foreach ($selectedSeats as $seat) {
            list($row, $number) = explode('-', $seat);
            $seatId = Uuid::uuid4();

            $seatSql = "INSERT INTO seats (id, eventId, seatRow, seatNumber, ticketTypeId) 
                                  VALUES (?, ?, ?, ?, ?)";
            $seatStmt = $conn->prepare($seatSql);
            $seatStmt->bind_param("sssss", $seatId, $eventId, $row, $number, $ticketTypeId);
            $seatStmt->execute();
          }
        }
        header('Location: ticket_type_management.php?id=' . $eventId . '&success=1');
        exit;
      } else {
        header('Location: ticket_type_management.php?id=' . $eventId . '&error=failed_to_create');
        exit;
      }
    } catch (Exception $e) {
      header('Location: ticket_type_management.php?id=' . $eventId . '&error=' . urlencode($e->getMessage()));
      exit;
    }
  }
}

// Fetch existing ticket types
$ticketTypesSql = "SELECT * FROM ticket_types WHERE eventId = ?";
$stmt = $conn->prepare($ticketTypesSql);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$ticketTypes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Manage Ticket Types - <?php echo htmlspecialchars($event['name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <style>
    .seat-map {
      display: grid;
      gap: 5px;
      padding: 20px;
    }

    .seat {
      width: 45px;
      height: 45px;
      border: 1px solid #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
    }

    .seat:hover {
      transform: scale(1.1);
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .seat.selected {
      background-color: #007bff;
      color: white;
    }

    .seat.assigned {
      background-color: #28a745;
      color: white;
    }

    .seat.assigned::after {
      content: attr(data-ticket-type);
      position: absolute;
      bottom: -20px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 10px;
      white-space: nowrap;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 2px 4px;
      border-radius: 3px;
      display: none;
      z-index: 1000;
    }

    .seat.assigned:hover::after {
      display: block;
      z-index: 1000;
    }

    #selectedSeatsInfo {
      margin-top: 20px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
    }

    .zoom-controls {
      margin: 10px 0;
    }

    .zoom-controls button {
      margin: 0 5px;
    }
  </style>
</head>

<body class="sb-nav-fixed">
  <?php include './component/header.php'; ?>
  <div id="layoutSidenav">
    <?php include './component/sidebar.php'; ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h2>Manage Ticket Types - <?php echo htmlspecialchars($event['name']); ?></h2>

          <!-- Ticket Type Form -->
          <div>
            <h3>Add Ticket Type</h3>
            <form id="ticketTypeForm" method="POST" action="ticket_type_management.php?id=<?php echo $eventId; ?>">
              <input type="hidden" name="action" value="create_ticket_type">
              <div class="mb-3">
                <label class="form-label">Ticket Type</label>
                <select name="name" class="form-control" id="ticketTypeSelect" required>
                  <option value="">Select Ticket Type</option>
                  <option value="General Admission">General Admission</option>
                  <option value="VIP">VIP</option>
                  <option value="Senior Citizen">Senior Citizen</option>
                  <option value="Child">Child</option>
                  <option value="Others">Others</option>
                </select>
              </div>
              <div class="mb-3" id="customTicketTypeDiv" style="display: none;">
                <label class="form-label">Custom Ticket Type Name</label>
                <input type="text" id="customTicketType" name="name" class="form-control" maxlength="50" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
                <small class="form-text text-muted">
                  Suggested prices:<br>
                  VIP: RM 200<br>
                  General Admission: RM 100<br>
                  Senior Citizen: RM 80<br>
                  Child: RM 60
                </small>
              </div>
              <div class="mb-3">
                <label class="form-label">Max Quantity <span id="totalRemainingSeats" class="text-muted">(0 seats available)</span></label>
                <input type="number" name="maxQuantity" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Restrictions</label>
                <textarea name="restrictions" class="form-control"></textarea>
              </div>
              <div class="mt-3">
                <h3>Seat Map</h3>
                <div class="seat-map" id="seatMap">
                  <!-- Seats will be generated here via JavaScript -->
                </div>
              </div>
              <input type="hidden" name="selected_seats" id="selectedSeatsInput" value="">
              <button type="submit" class="btn btn-primary">Create Ticket Type</button>
            </form>
          </div>



          <!-- Existing Ticket Types -->
          <div class="mt-5">
            <h3>Existing Ticket Types</h3>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Max Quantity</th>
                    <th>Restrictions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($ticketTypes as $type): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($type['name']); ?></td>
                      <td><?php echo htmlspecialchars($type['price']); ?></td>
                      <td><?php echo htmlspecialchars($type['maxQuantity']); ?></td>
                      <td><?php echo htmlspecialchars($type['restrictions']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
      <?php include './component/footer.php'; ?>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js"></script>
  <script>
    // Seat map configuration
    const seatRanges = {
      'A': [
        [1, 8],
        [15, 33],
        [36, 43]
      ],
      'B': [
        [1, 10],
        [15, 34],
        [36, 44]
      ],
      'C': [
        [1, 11],
        [15, 33],
        [36, 46]
      ],
      'D': [
        [1, 12],
        [15, 34],
        [36, 47]
      ],
      'E': [
        [1, 12],
        [15, 31],
        [36, 47]
      ],
      'F': [
        [1, 12],
        [15, 32],
        [36, 47]
      ],
      'G': [
        [1, 12],
        [15, 31],
        [36, 47]
      ],
      'H': [
        [1, 11],
        [15, 32],
        [36, 46]
      ],
      'J': [
        [1, 10],
        [15, 32],
        [36, 45]
      ],
      'K': [
        [1, 8],
        [15, 29],
        [36, 43]
      ],
      'L': [
        [1, 5],
        [15, 30],
        [36, 40]
      ],
      'AA': [
        [1, 13],
        [15, 36],
        [37, 50]
      ],
      'BB': [
        [1, 13],
        [15, 36],
        [37, 50]
      ],
      'CC': [
        [1, 13],
        [15, 36],
        [37, 50]
      ],
      'DD': [
        [1, 13],
        [15, 36],
        [37, 49]
      ],
      'EE': [
        [1, 12],
        [15, 35],
        [37, 48]
      ]
    };

    let availableSeatsPerRow = {};

    function generateSeatMap() {
      const seatMap = document.getElementById('seatMap');

      // Add controls container
      const controlsContainer = document.createElement('div');
      controlsContainer.className = 'controls-container mb-3';
      controlsContainer.style.display = 'flex';
      controlsContainer.style.gap = '10px';
      controlsContainer.style.alignItems = 'center';
      controlsContainer.style.flexWrap = 'wrap';

      // Add zoom controls
      const zoomControls = document.createElement('div');
      zoomControls.className = 'zoom-controls';
      zoomControls.innerHTML = `
        <button class="btn btn-sm btn-secondary" onclick="adjustZoom(1.1)">Zoom In</button>
        <button class="btn btn-sm btn-secondary" onclick="adjustZoom(0.9)">Zoom Out</button>
      `;
      controlsContainer.appendChild(zoomControls);

      // Add row selection controls
      const rowControls = document.createElement('div');
      rowControls.className = 'row-controls';
      rowControls.innerHTML = `
        <select class="form-select form-select-sm" id="rowSelector" style="width: 100px; display: inline-block; margin-right: 10px;">
          <option value="">Select Row</option>
        </select>
        <button type="button" class="btn btn-sm btn-primary" onclick="selectEntireRow()">Select Row</button>
        <button type="button" class="btn btn-sm btn-success ms-2" onclick="selectAllAvailableSeats()">Select All Available</button>
      `;
      controlsContainer.appendChild(rowControls);

      seatMap.parentElement.insertBefore(controlsContainer, seatMap);

      // Add selected seats info container
      const selectedSeatsInfo = document.createElement('div');
      selectedSeatsInfo.id = 'selectedSeatsInfo';
      seatMap.parentElement.insertBefore(selectedSeatsInfo, seatMap.nextSibling);

      // Fetch both booked and assigned seats
      Promise.all([
          fetch(`../php/getBookedSeats.php?id=<?php echo $eventId; ?>`).then(r => r.json()),
          fetch(`../php/getAssignedSeats.php?id=<?php echo $eventId; ?>`).then(r => r.json())
        ])
        .then(([bookedData, assignedData]) => {
          const bookedSeats = new Set(bookedData.bookedSeats);
          const assignedSeats = assignedData.assignedSeats;

          // Reset available seats count
          availableSeatsPerRow = {};

          Object.entries(seatRanges).forEach(([row, ranges]) => {
            availableSeatsPerRow[row] = 0;
            ranges.forEach(([start, end]) => {
              for (let i = start; i <= end; i++) {
                const seat = document.createElement('div');
                const seatLabel = `${row}-${i}`;
                seat.className = 'seat';
                seat.dataset.seat = seatLabel;
                seat.textContent = `${row}${i}`;

                if (bookedSeats.has(seatLabel)) {
                  seat.classList.add('assigned');
                  seat.dataset.ticketType = 'Booked';
                } else if (assignedSeats[seatLabel]) {
                  seat.classList.add('assigned');
                  seat.style.backgroundColor = getTicketTypeColor(assignedSeats[seatLabel]);
                  seat.dataset.ticketType = assignedSeats[seatLabel];
                } else {
                  seat.onclick = () => toggleSeat(seat);
                  availableSeatsPerRow[row]++;
                }

                seatMap.appendChild(seat);
              }
            });
            const breakDiv = document.createElement('div');
            breakDiv.style.flexBasis = '100%';
            breakDiv.style.height = '10px';
            seatMap.appendChild(breakDiv);
          });

          seatMap.style.display = 'flex';
          seatMap.style.flexWrap = 'wrap';
          seatMap.style.gap = '5px';
          seatMap.style.transform = 'scale(1)';
          seatMap.style.transformOrigin = 'center top';

          // Update row selector with only rows that have available seats
          updateRowSelector();
        });
    }

    let selectedSeatsCount = 0;
    const maxQuantityInput = document.querySelector('input[name="maxQuantity"]');

    // Add event listener for max quantity input validation
    maxQuantityInput.addEventListener('input', function() {
      const totalAvailableSeats = Object.values(availableSeatsPerRow).reduce((sum, count) => sum + count, 0);
      const inputValue = parseInt(this.value) || 0;

      if (inputValue > totalAvailableSeats) {
        this.value = totalAvailableSeats;
        alert(`Maximum available seats is ${totalAvailableSeats}`);
      }

      // Update selected seats info when max quantity changes
      updateSelectedSeatsInfo();
    });

    function toggleSeat(seat) {
      const maxQuantity = parseInt(maxQuantityInput.value) || 0;
      const isSelected = seat.classList.contains('selected');

      if (!isSelected && selectedSeatsCount >= maxQuantity) {
        alert('Maximum number of seats reached!');
        return;
      }

      seat.classList.toggle('selected');
      selectedSeatsCount = isSelected ? selectedSeatsCount - 1 : selectedSeatsCount + 1;
      updateSelectedSeatsInfo();
      updateRowSelector();
      updateRowSelector();
    }

    // Update the updateSelectedSeatsInfo function
    function updateSelectedSeatsInfo(additionalMessage = '') {
      const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'))
        .map(seat => seat.dataset.seat);
      const infoDiv = document.getElementById('selectedSeatsInfo');
      const maxQuantity = parseInt(maxQuantityInput.value) || 0;
      const remainingSeats = maxQuantity - selectedSeats.length;

      // Calculate total available seats
      const totalAvailableSeats = Object.values(availableSeatsPerRow).reduce((sum, count) => sum + count, 0);

      // Update the total remaining seats display
      document.getElementById('totalRemainingSeats').textContent = `(${totalAvailableSeats} seats available)`;

      // Update max quantity input with total available seats
      maxQuantityInput.setAttribute('max', totalAvailableSeats);
      maxQuantityInput.setAttribute('placeholder', `Max ${totalAvailableSeats} seats available`);

      if (selectedSeats.length > 0) {
        infoDiv.innerHTML = `
          <div class="alert alert-info">
            <strong>Selected Seats:</strong> ${selectedSeats.join(', ')}<br>
            <strong>Progress:</strong> ${selectedSeats.length}/${maxQuantity} seats selected<br>
            <strong>Remaining:</strong> ${remainingSeats} seats to select
            ${additionalMessage ? `<br><span style="color: #666; font-style: italic">${additionalMessage}</span>` : ''}
          </div>
        `;
      } else {
        infoDiv.innerHTML = `
          <div class="alert alert-warning">
            ${maxQuantity ? `<em>No seats selected. You need to select ${maxQuantity} seats.</em>` : `<em>Please set the maximum quantity (up to ${totalAvailableSeats} seats available)</em>`}
          </div>
        `;
      }
    }

    function adjustZoom(factor) {
      const seatMap = document.getElementById('seatMap');
      const currentScale = parseFloat(seatMap.style.transform.replace('scale(', '').replace(')', '') || 1);
      const newScale = currentScale * factor;

      if (newScale >= 0.5 && newScale <= 2) {
        seatMap.style.transform = `scale(${newScale})`;
      }
    }

    function updateRowSelector() {
      const rowSelector = document.getElementById('rowSelector');
      rowSelector.innerHTML = '<option value="">Select Row</option>';

      Object.entries(availableSeatsPerRow).forEach(([row, count]) => {
        if (count > 0) {
          rowSelector.innerHTML += `<option value="${row}">${row} (${count} available)</option>`;
        }
      });
    }

    function selectAllAvailableSeats() {
      const maxQuantity = parseInt(maxQuantityInput.value) || 0;
      if (!maxQuantity) {
        alert('Please set the maximum quantity first');
        return;
      }

      let seatsToSelect = maxQuantity - selectedSeatsCount;
      if (seatsToSelect <= 0) return;

      const availableSeats = Array.from(document.querySelectorAll('.seat'))
        .filter(seat => !seat.classList.contains('assigned') && !seat.classList.contains('selected'));

      for (let i = 0; i < Math.min(seatsToSelect, availableSeats.length); i++) {
        availableSeats[i].classList.add('selected');
        selectedSeatsCount++;
      }

      updateSelectedSeatsInfo();
      updateRowSelector();
    }

    function selectEntireRow() {
      const selectedRow = document.getElementById('rowSelector').value;
      if (!selectedRow) return;

      const maxQuantity = parseInt(maxQuantityInput.value) || 0;
      if (!maxQuantity) {
        alert('Please set the maximum quantity first');
        return;
      }

      const seats = Array.from(document.querySelectorAll('.seat'));
      let remainingToSelect = maxQuantity - selectedSeatsCount;

      // Get only seats from the selected row
      const rowSeats = seats.filter(seat =>
        seat.dataset.seat.startsWith(selectedRow + '-') &&
        !seat.classList.contains('assigned') &&
        !seat.classList.contains('selected')
      );

      // Select seats in current row up to remaining quantity
      const seatsToSelect = rowSeats.slice(0, remainingToSelect);
      seatsToSelect.forEach(seat => {
        seat.classList.add('selected');
        selectedSeatsCount++;
        remainingToSelect--;
      });

      if (seatsToSelect.length > 0) {
        if (remainingToSelect > 0) {
          alert(`Selected ${seatsToSelect.length} seats in row ${selectedRow}\nYou still need to select ${remainingToSelect} more seats to reach the maximum quantity.`);
        } else {
          alert(`Selected ${seatsToSelect.length} seats in row ${selectedRow}\nAll seats selected!`);
        }
      } else {
        alert(`No available seats in row ${selectedRow}`);
      }

      updateSelectedSeatsInfo();
      updateRowSelector();
    }

    function getTicketTypeColor(ticketType) {
      const colors = {
        'VIP': '#FFD700',
        'General Admission': '#90EE90',
        'Senior Citizen': '#87CEEB',
        'Child': '#FFA07A'
      };
      return colors[ticketType] || '#28a745';
    }

    // Update hidden input with selected seats before form submission
    document.getElementById('ticketTypeForm').addEventListener('submit', function() {
      const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'))
        .map(seat => seat.dataset.seat);
      document.getElementById('selectedSeatsInput').value = selectedSeats.join(',');
    });

    // Handle ticket type selection
    document.getElementById('ticketTypeSelect').addEventListener('change', function() {
      const customDiv = document.getElementById('customTicketTypeDiv');
      const customInput = document.getElementById('customTicketType');

      if (this.value === 'Others') {
        customDiv.style.display = 'block';
        customInput.required = true;
      } else {
        customDiv.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
      }
    });

    // Handle form submission
    document.getElementById('ticketTypeForm').addEventListener('submit', function(e) {
      const ticketTypeSelect = document.getElementById('ticketTypeSelect');
      const customInput = document.getElementById('customTicketType');

      if (ticketTypeSelect.value === 'Others' && !customInput.value.trim()) {
        e.preventDefault();
        alert('Please enter a custom ticket type name');
        return;
      }

      if (ticketTypeSelect.value === 'Others') {
        ticketTypeSelect.value = customInput.value;
      }
    });

    document.getElementById('ticketTypeSelect').addEventListener('change', function() {
      const customTicketTypeDiv = document.getElementById('customTicketTypeDiv');
      const customTicketType = document.getElementById('customTicketType');

      if (this.value === 'Others') {
        customTicketTypeDiv.style.display = 'block';
        customTicketType.required = true;
      } else {
        customTicketTypeDiv.style.display = 'none';
        customTicketType.required = false;
      }
    });

    document.getElementById('ticketTypeForm').addEventListener('submit', function(e) {
      const ticketTypeSelect = document.getElementById('ticketTypeSelect');
      const customTicketType = document.getElementById('customTicketType');

      if (ticketTypeSelect.value === 'Others') {
        if (!customTicketType.value.trim()) {
          e.preventDefault();
          alert('Please enter a custom ticket type name');
          return;
        }
        ticketTypeSelect.value = customTicketType.value;
      }
    });

    // Initialize seat map
    generateSeatMap();
  </script>
</body>

</html>