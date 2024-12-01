<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            // Check if user has a profile
            $profile_sql = "SELECT * FROM user_profiles WHERE user_id = ?";
            $profile_stmt = $conn->prepare($profile_sql);
            $profile_stmt->bind_param("i", $user['id']);
            $profile_stmt->execute();
            $profile_result = $profile_stmt->get_result();

            if ($profile_result->num_rows == 0) {
                // User doesn't have a profile, redirect to profile creation
                header("Location: prof.php");
            } else {
                // Check if user has a trip plan
                $trip_sql = "SELECT * FROM trip_plans WHERE user_id = ?";
                $trip_stmt = $conn->prepare($trip_sql);
                $trip_stmt->bind_param("i", $user['id']);
                $trip_stmt->execute();
                $trip_result = $trip_stmt->get_result();

                if ($trip_result->num_rows == 0) {
                    // User doesn't have a trip plan, redirect to trip planning
                    header("Location: trip.php");
                } else {
                    // User has both profile and trip plan, redirect to main page
                    header("Location: demo7.php");
                }
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-form {
            background-color: #faa8a8;
            padding: 25px;
            border: 2px solid #000;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 300px;
            color: #0b0101;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #f7f8f9;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #eff2f4;
            color: #0e0101;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #050000;
            color: #faf8f8;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: #333;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            color: #f52424;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #f52424;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if(isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="reg.php">Register here</a></p>
        </div>
    </div>
</body>
</html>