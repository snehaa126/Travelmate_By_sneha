<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user already has a profile
$check_sql = "SELECT * FROM user_profiles WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // User already has a profile, redirect to trip planning
    header("Location: trip.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gender = $_POST['gender'];
    $languages = implode(",", $_POST['languages']);
    $looking_for = implode(",", $_POST['looking_for']);
    $education = $_POST['education'];
    $relationship_status = $_POST['Relationship-status'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile-image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile-image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile-image"]["size"] > 500000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile-image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO user_profiles (user_id, gender, profile_image, languages, looking_for, education, relationship_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $user_id, $gender, $target_file, $languages, $looking_for, $education, $relationship_status);

            if ($stmt->execute()) {
                $success = "Profile created successfully.";
                header("Location: trip.php");
                exit();
            } else {
                $error = "Error creating profile: " . $stmt->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Profile</title>
    <link rel="stylesheet" href="pro.css">
</head>
<body>
    <div class="profile-form">
        <h2>Create Your Profile</h2>
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <?php if ($success) { echo "<p class='success'>$success</p>"; } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="profile-image">Profile Image:</label>
                <input type="file" id="profile-image" name="profile-image" accept="image/*" required>
            </div>
            <div class="form-group">
    <label>Languages:</label>
    <div class="checkbox-group">
        <label><input type="checkbox" name="languages[]" value="marathi"> Marathi</label>
        <label><input type="checkbox" name="languages[]" value="english"> English</label>
        <label><input type="checkbox" name="languages[]" value="french"> French</label>
        <label><input type="checkbox" name="languages[]" value="spanish"> Spanish</label>
    </div>
</div>
            <div class="form-group">
                <label for="looking-for">Looking For:</label>
                <select id="looking-for" name="looking_for[]" multiple required>
                    <option value="friends">Friends</option>
                    <option value="adventure">Adventure</option>
                    <option value="soulmate">Soulmate</option>
                    <option value="travel_buddy">Travel Buddy</option>
                </select>
            </div>
            <div class="form-group">
                <label for="education">Education:</label>
                <select id="education" name="education" required>
                    <option value="high_school">High School</option>
                    <option value="undergraduate">Undergraduate</option>
                    <option value="post-graduation">Post Graduation</option>
                    <option value="phd">Ph.D.</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Relationship-status">Relationship status:</label>
                <select id="Relationship-status" name="Relationship-status" required>
                    <option value="Single">Single</option>
                    <option value="In a Relationship">In a Relationship</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <button type="submit">Create Profile</button>
        </form>
    </div>
</body>
</html>
