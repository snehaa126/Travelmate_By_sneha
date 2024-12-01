<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile information
$sql = "SELECT u.email, up.* FROM users u 
        LEFT JOIN user_profiles up ON u.id = up.user_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if the user has completed their profile
$profile_completed = !empty($user['gender']) && !empty($user['languages']) && !empty($user['looking_for']);

// Fetch user's trip plans
$sql = "SELECT * FROM trip_plans WHERE user_id = ? ORDER BY start_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$trip_plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all user profiles including the current user only if the profile is completed
$all_users = [];
if ($profile_completed) {
    $sql = "SELECT u.id, u.email, up.*, tp.city as trip_city, tp.start_date, tp.end_date, tp.budget 
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            LEFT JOIN trip_plans tp ON u.id = tp.user_id
            WHERE u.id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $all_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Function to get username from email
function getUsername($email) {
    $parts = explode('@', $email);
    return $parts[0];
}

// Fetch unread message count
$sql = "SELECT COUNT(*) as unread_count
        FROM messages
        WHERE receiver_id = ? AND is_read = FALSE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$unread_count = $result->fetch_assoc()['unread_count'];

// Retrieve error message from session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Clear the error message after retrieving it

// Global colors
$primary_color = "#faa8a8";
$secondary_color = "#f4f4f4";
$text_color = "#35424a";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Mate - Find Your Travel Partner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: <?php echo $secondary_color; ?>;
            color: <?php echo $text_color; ?>;
        }
        header {
            background-color: <?php echo $primary_color; ?>;
            color: <?php echo $text_color; ?>;
            padding: 10px 5%;
            text-align: center;
            position: relative;
        }
        main {
            display: flex;
            padding: 2rem;
            gap: 2rem;
        }
        .user-section {
            width: 30%;
        }
        .other-profiles {
            width: 70%;
        }
        .user-profile, .trip-plans, .other-profile-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .trip-plan {
            background-color: <?php echo $secondary_color; ?>;
            border-radius: 4px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .other-profile-card {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .other-profile-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
        }
        .profile-actions {
            margin-left: auto;
        }
        .profile-actions a {
            font-size: 1.2rem;
            color: <?php echo $text_color; ?>;
            margin-left: 0.5rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .unread-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: <?php echo $primary_color; ?>;
            color: <?php echo $text_color; ?>;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #e99898;
        }
        .error-message {
            background-color: #ffcccb;
            color: #d8000c;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            background-color: black;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .logout-button:hover {
            background-color: #2c3e50;
        }
        .logout-icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="animate__animated animate__bounceInDown">Travel Mate</h1>
        <?php if ($profile_completed): ?>
        <div class="search-bar animate__animated animate__fadeInUp">
            <input type="text" id="citySearch" placeholder="Search by city">
            <button onclick="searchProfiles()">Search</button>
        </div>
        <?php endif; ?>
        <a href="logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt logout-icon"></i> Logout
        </a>
    </header>

    <?php if (!empty($error_message)): ?>
        <div class="error-message animate__animated animate__fadeIn">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <main>
        <section class="user-section animate__animated animate__fadeInLeft">
            <div class="user-profile">
                <h2>My Profile</h2>
                <p>Username: <?php echo htmlspecialchars(getUsername($user['email'])); ?></p>
                <p>Gender: <?php echo htmlspecialchars($user['gender'] ?? 'Not specified'); ?></p>
                <p>Languages: <?php echo htmlspecialchars($user['languages'] ?? 'Not specified'); ?></p>
                <p>Looking for: <?php echo htmlspecialchars($user['looking_for'] ?? 'Not specified'); ?></p>
                <p>Education: <?php echo htmlspecialchars($user['education'] ?? 'Not specified'); ?></p>
                <p>Relationship status: <?php echo htmlspecialchars($user['relationship_status'] ?? 'Not specified'); ?></p>
                <?php if (!$profile_completed): ?>
                    <p><strong>Please complete your profile to see other users.</strong></p>
                    <a href="edit_profile.php" class="btn">Complete Profile</a>
                <?php endif; ?>
            </div>

            <div class="trip-plans">
                <h2>My Trip Plans</h2>
                <?php if (!empty($trip_plans)): ?>
                    <?php foreach ($trip_plans as $plan): ?>
                        <div class="trip-plan">
                            <h3><?php echo htmlspecialchars($plan['city']); ?></h3>
                            <p>Start Date: <?php echo htmlspecialchars($plan['start_date']); ?></p>
                            <p>End Date: <?php echo htmlspecialchars($plan['end_date']); ?></p>
                            <p>Budget: $<?php echo htmlspecialchars($plan['budget']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No trip plans added yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($profile_completed): ?>
        <section class="other-profiles animate__animated animate__fadeInRight">
            <h2>Find Travel Partners</h2>
            <?php foreach ($all_users as $other_user): ?>
                <div class="other-profile-card" onclick="showUserProfile(<?php echo $other_user['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($other_user['profile_image'] ?? 'default-avatar.png'); ?>" alt="Profile Picture">
                    <div data-trip-city="<?php echo htmlspecialchars($other_user['trip_city'] ?? ''); ?>">
                        <h3><?php echo htmlspecialchars(getUsername($other_user['email'])); ?></h3>
                        <p>
                            <?php echo htmlspecialchars($other_user['gender'] ?? 'Gender not specified'); ?>, 
                            <?php echo htmlspecialchars($other_user['languages'] ?? 'Languages not specified'); ?>
                        </p>
                    </div>
                    <div class="profile-actions">
                        <a href="#" onclick="event.stopPropagation(); startChat(<?php echo $other_user['id']; ?>)">
                            <i class="fas fa-comment"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="unread-count"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="#" onclick="event.stopPropagation();"><i class="fab fa-facebook"></i></a>
                        <a href="#" onclick="event.stopPropagation();"><i class="fab fa-instagram"></i></a>
                        <a href="#" onclick="event.stopPropagation();"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <?php else: ?>
        <section class="other-profiles animate__animated animate__fadeInRight">
            <h2>Complete Your Profile</h2>
            <p>To see other travel partners and start connecting, please complete your profile first.</p>
            <a href="edit_profile.php" class="btn">Complete Profile</a>
        </section>
        <?php endif; ?>
    </main>

    <div id="userProfileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="userProfileDetails"></div>
        </div>
    </div>

    <script>
        function searchProfiles() {
            const searchTerm = document.getElementById('citySearch').value.toLowerCase().trim();
            const profileCards = document.querySelectorAll('.other-profile-card');
            
            profileCards.forEach(card => {
                const userDetails = card.querySelector('div');
                const tripCity = userDetails.dataset.tripCity ? userDetails.dataset.tripCity.toLowerCase() : '';
                
                if (tripCity.includes(searchTerm) || searchTerm === '') {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Add event listener for real-time search
        document.getElementById('citySearch').addEventListener('input', searchProfiles);

        function showUserProfile(userId) {
            const user = <?php echo json_encode($all_users); ?>.find(u => u.id == userId);
            const modal = document.getElementById('userProfileModal');
            const profileDetails = document.getElementById('userProfileDetails');
            
            profileDetails.innerHTML = `
                <h2>${getUsername(user.email)}'s Profile</h2>
                <p>Gender: ${user.gender || 'Not specified'}</p>
                <p>Languages: ${user.languages || 'Not specified'}</p>
                <p>Looking for: ${user.looking_for || 'Not specified'}</p>
                <p>Education: ${user.education || 'Not specified'}</p>
                <p>Relationship status: ${user.relationship_status || 'Not specified'}</p>
                <h3>Trip Plan</h3>
                <p>City: ${user.trip_city || 'Not specified'}</p>
                <p>Start Date: ${user.start_date || 'Not specified'}</p>
                <p>End Date: ${user.end_date || 'Not specified'}</p>
                <p>Budget: $${user.budget || 'Not specified'}</p>
                <div class="profile-actions">
                    <a href="#" onclick="startChat(${user.id})"><i class="fas fa-comment"></i> Chat</a>
                    <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                    <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                    <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                </div>
            `;
            
            modal.style.display = "block";
        }

        // Close the modal when clicking on <span> (x)
        document.querySelector('.close').onclick = function() {
            document.getElementById('userProfileModal').style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('userProfileModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function startChat(userId) {
            window.location.href = `chat.php?user_id=${userId}`;
        }

        function getUsername(email) {
            return email.split('@')[0];
        }
    </script>
</body>
</html>