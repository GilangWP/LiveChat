<?php
session_start();

// Check if username and password are submitted
if (isset($_POST['username']) && isset($_POST['password'])) {

    // Include the database connection file
    include '../db.conn.php';

    // Get data from POST request and store them in variables
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Simple form validation
    if (empty($username)) {
        $em = "Username is required"; // Error message
        header("Location: ../../index.php?error=$em"); // Redirect to 'index.php' and pass error message
    } elseif (empty($password)) {
        $em = "Password is required"; // Error message
        header("Location: ../../index.php?error=$em"); // Redirect to 'index.php' and pass error message
    } else {
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        // Check if the username exists
        if ($stmt->rowCount() === 1) {
            // Fetch user data
            $user = $stmt->fetch();

            // Check if the provided password matches the stored encrypted password
            if (password_verify($password, $user['password'])) {
                // Successful login
                // Create the SESSION
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['user_id'] = $user['user_id'];

                // Redirect to 'home.php'
                header("Location: ../../home.php");
            } else {
                $em = "Incorrect Username or password"; // Error message
                header("Location: ../../index.php?error=$em"); // Redirect to 'index.php' and pass error message
            }
        } else {
            $em = "Incorrect Username or password"; // Error message
            header("Location: ../../index.php?error=$em"); // Redirect to 'index.php' and pass error message
        }
    }
    
} else {
    header("Location: ../../index.php");
    exit;
}
?>