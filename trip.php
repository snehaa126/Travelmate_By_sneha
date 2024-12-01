<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user already has a trip plan
$check_sql = "SELECT * FROM trip_plans WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // User already has a trip plan, redirect to main page
    header("Location: demo7.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $trip_city = !empty($_POST['trip-city']) ? trim($_POST['trip-city']) : null;
    $start_date = !empty($_POST['trip-date-start']) ? trim($_POST['trip-date-start']) : null;
    $end_date = !empty($_POST['trip-date-end']) ? trim($_POST['trip-date-end']) : null;
    $budget = !empty($_POST['trip-budget']) ? intval($_POST['trip-budget']) : null;

    // Check if all fields are filled
    if ($trip_city && $start_date && $end_date && $budget) {
        $sql = "INSERT INTO trip_plans (user_id, city, start_date, end_date, budget) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $user_id, $trip_city, $start_date, $end_date, $budget);

        if ($stmt->execute()) {
            $success = "Trip plan added successfully.";
            // Redirect to demo7.php after successful insertion
            header("Location: demo7.php");
            exit();
        } else {
            $error = "Error adding trip plan: " . $stmt->error;
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Plan Form</title>
    <link rel="stylesheet" href="trip.css">
</head>
<body>
    <div class="profile-form">
        <h2>Trip Plan</h2>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <?php if ($success) { echo "<p class='success'>$success</p>"; } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h3>My Trip Plan</h3>
            <div class="trip-plan-item">
                <label for="trip-city">I AM GOING TO:</label>
                <input type="text" id="trip-city" name="trip-city" required>
            </div>
            <div class="trip-plan-item">
                <label for="trip-date-start">Start Date:</label>
                <input type="date" id="trip-date-start" name="trip-date-start" required>
            </div>
            <div class="trip-plan-item">
                <label for="trip-date-end">End Date:</label>
                <input type="date" id="trip-date-end" name="trip-date-end" required>
            </div>
            <div class="trip-plan-item">
                <label for="trip-budget">Budget:</label>
                <input type="number" id="trip-budget" name="trip-budget" required>
            </div>
            <button type="submit">Add Trip Plan</button>
        </form>
    </div>
</body>
</html>