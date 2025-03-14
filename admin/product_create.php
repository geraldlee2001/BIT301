<?php
include "../php/databaseConnection.php";
include "../php/tokenDecoding.php";


$merchantSql = "SELECT * FROM merchants";
$merchantResult = $conn->query($merchantSql);

require_once '../php/databaseConnection.php';
include '../component/organizer_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $eventName = $_POST['event_name'];
    $description = $_POST['description'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];

    // **1️⃣ 检查是否已有相同日期和时间的活动**
    $checkQuery = "SELECT * FROM events WHERE event_date = ? AND event_time = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $eventDate, $eventTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0)
    {
        echo "<script>alert('该日期和时间已有活动，请选择其他时间！');</script>";
    }
    else
    {
        // **2️⃣ 处理文件上传**
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir))
        {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = ''; // **存储图片路径**
        if (!empty($_FILES['event_file']['name']))
        {
            $fileExt = strtolower(pathinfo($_FILES['event_file']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'png', 'jpeg'];

            if (in_array($fileExt, $allowedExts) && $_FILES['event_file']['size'] <= 5 * 1024 * 1024)
            {
                $fileName = uniqid('event_') . '.' . $fileExt;
                $filePath = $uploadDir . $fileName;
                move_uploaded_file($_FILES['event_file']['tmp_name'], $filePath);
            }
            else
            {
                echo "<script>alert('文件格式错误或大小超出限制！');</script>";
            }
        }

        // **3️⃣ 插入数据库**
        $insertQuery = "INSERT INTO events (event_name, description, event_date, event_time, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssss", $eventName, $description, $eventDate, $eventTime, $filePath);

        if ($stmt->execute())
        {
            echo "<script>alert('活动创建成功！'); window.location.href='products.php';</script>";
        }
        else
        {
            echo "<script>alert('创建活动失败: " . $conn->error . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Create Event</h2>

        <!-- 📌 显示日历 -->
        <div id="calendar"></div>

        <form action="product_create.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Event Name</label>
                <input type="text" name="event_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date</label>
                <input type="date" name="event_date" id="event_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Time</label>
                <input type="time" name="event_time" id="event_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload File (JPG, PNG, PDF, Max 5MB)</label>
                <input type="file" name="event_file" class="form-control" accept=".jpg, .png, .pdf">
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
    <?php include '../component/organizer_footer.php'; ?>

    <script>
        $(document).ready(function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: 'admin/php/fetch_events.php' // 📌 获取已预订的事件
            });
            calendar.render();

            // 📌 当选择日期和时间时，检查是否冲突
            $("#event_date, #event_time").change(function () {
                var selectedDate = $("#event_date").val();
                var selectedTime = $("#event_time").val();

                $.ajax({
                    url: "admin/php/check_availability.php",
                    type: "POST",
                    data: { event_date: selectedDate, event_time: selectedTime },
                    success: function (response) {
                        if (response === "unavailable") {
                            alert("该日期和时间已被预订，请选择其他时间！");
                            $("#event_date").val("");
                            $("#event_time").val("");
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>