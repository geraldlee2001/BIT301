<?php
require_once '../php/databaseConnection.php';
include '../component/organizer_header.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid event ID!'); window.location.href='browse_events.php';</script>";
    exit;
}

$eventId = $_GET['id'];

$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<script>alert('Event not found!'); window.location.href='browse_events.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'];
    $description = $_POST['description'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];

    // **1️⃣ 检查是否有时间冲突**
    $checkQuery = "SELECT * FROM events WHERE event_date = ? AND event_time = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ssi", $eventDate, $eventTime, $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('该日期和时间已有其他活动，请选择其他时间！');</script>";
    } else {
        // **2️⃣ 处理文件上传**
        $filePath = $event['image']; 
        if (!empty($_FILES['event_file']['name'])) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExt = strtolower(pathinfo($_FILES['event_file']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'png', 'jpeg'];
            
            if (in_array($fileExt, $allowedExts) && $_FILES['event_file']['size'] <= 5 * 1024 * 1024) {
                $fileName = uniqid('event_') . '.' . $fileExt;
                $filePath = $uploadDir . $fileName;
                move_uploaded_file($_FILES['event_file']['tmp_name'], $filePath);
            } else {
                echo "<script>alert('文件格式错误或大小超出限制！');</script>";
            }
        }
        
        // **3️⃣ 更新数据库**
        $updateQuery = "UPDATE events SET event_name=?, description=?, event_date=?, event_time=?, image=? WHERE id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssssi", $eventName, $description, $eventDate, $eventTime, $filePath, $eventId);
        
        if ($stmt->execute()) {
            echo "<script>alert('活动更新成功！'); window.location.href='browse_events.php';</script>";
        } else {
            echo "<script>alert('更新失败: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Event</h2>
        <form action="edit_events.php?id=<?php echo $eventId; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Event Name</label>
                <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date</label>
                <input type="date" name="event_date" class="form-control" value="<?php echo $event['event_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Time</label>
                <input type="time" name="event_time" class="form-control" value="<?php echo $event['event_time']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="<?php echo $event['image']; ?>" alt="Event Image" width="200">
            </div>
            <div class="mb-3">
                <label class="form-label">Upload New Image (JPG, PNG, Max 5MB)</label>
                <input type="file" name="event_file" class="form-control" accept=".jpg, .png">
            </div>
            <button type="submit" class="btn btn-success">Update Event</button>
            <a href="browse_events.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php include '../component/organizer_footer.php'; ?>
</body>
</html>
