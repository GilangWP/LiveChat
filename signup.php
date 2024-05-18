<?php
// Check if username, password, and name are submitted
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['name'])) {

    // Include the database connection file
    include '../db.conn.php';

    // Get data from POST request and store them in variables
    $name = $_POST['name'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Make URL data format
    $data = 'name=' . $name . '&username=' . $username;

    // Simple form validation
    if (empty($name)) {
        $em = "Name is required"; // Error message
        header("Location: ../../signup.php?error=$em"); // Redirect to 'signup.php' and pass error message
        exit;
    } elseif (empty($username)) {
        $em = "Username is required"; // Error message
        header("Location: ../../signup.php?error=$em&$data"); // Redirect to 'signup.php' and pass error message and data
        exit;
    } elseif (empty($password)) {
        $em = "Password is required"; // Error message
        header("Location: ../../signup.php?error=$em&$data"); // Redirect to 'signup.php' and pass error message and data
        exit;
    } else {
        // Check the database if the username is taken
        $sql = "SELECT username FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $em = "The username ($username) is taken"; // Error message
            header("Location: ../../signup.php?error=$em&$data"); // Redirect to 'signup.php' and pass error message and data
            exit;
        } else {
            // Profile Picture Uploading
            if (isset($_FILES['pp'])) {
                // Get data and store them in variables
                $img_name = $_FILES['pp']['name'];
                $tmp_name = $_FILES['pp']['tmp_name'];
                $error = $_FILES['pp']['error'];

                // If there is no error occurred while uploading
                if ($error === 0) {
                    // Get image extension and store it in variable
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    
                    // Convert the image extension into lowercase and store it in variable
                    $img_ex_lc = strtolower($img_ex);

                    // Creating an array that stores allowed image extensions to upload
                    $allowed_exs = array("jpg", "jpeg", "png");

                    // Check if the image extension is present in the $allowed_exs array
                    if (in_array($img_ex_lc, $allowed_exs)) {
                        // Renaming the image with the user's username
                        $new_img_name = $username . '.' . $img_ex_lc;

                        // Creating the upload path on the root directory
                        $img_upload_path = '../../uploads/' . $new_img_name;

                        // Move uploaded image to the ./upload folder
                        move_uploaded_file($tmp_name, $img_upload_path);
                    } else {
                        $em = "You can't upload files of this type"; // Error message
                        header("Location: ../../signup.php?error=$em&$data"); // Redirect to 'signup.php' and pass error message and data
                        exit;
                    }
                }
            }
 
            // Password hashing
            $password = password_hash($password, PASSWORD_DEFAULT);

            // If the user uploaded a profile picture
            if (isset($new_img_name)) {
                // Inserting data into the database
                $sql = "INSERT INTO users (name, username, password, p_p) VALUES (?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $username, $password, $new_img_name]);
            } else {
                // Inserting data into the database
                $sql = "INSERT INTO users (name, username, password) VALUES (?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $username, $password]);
            }

            $sm = "Account created successfully"; // Success message
            header("Location: ../../index.php?success=$sm"); // Redirect to 'index.php' and pass success message
            exit;
        }
    }
} else {
    header("Location: ../../signup.php");
    exit;
}
?>