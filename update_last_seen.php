<?php

session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Include the database connection file
    include '../db.conn.php';

    // Get the logged-in user's user_id from SESSION
    $id = $_SESSION['user_id'];

    $sql = "UPDATE users
            SET last_seen = NOW()
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
} else {
    header("Location: ../../index.php");
    exit;
}