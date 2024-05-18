<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded without errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $target_dir = "upload/"; // Change this to the desired directory for uploaded files
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is allowed (you can modify this to allow specific file types)
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "mp3", "mp4", "txt", "docx", "pptx", "xlsx", "apk", "rar", "zip");
        if (!in_array($file_type, $allowed_types)) {
            $response = array(
                "success" => false,
                "message" => "Sorry, only JPG, JPEG, PNG, GIF, PDF, MP3, MP4, TXT, DOCX, PPTX, XLSX, APK, RAR, and ZIP files are allowed."
            );
        } else {
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $filename = $_FILES["file"]["name"];
                $filesize = $_FILES["file"]["size"];
                $filetype = $_FILES["file"]["type"];
                $from_id = $_POST['from_id'];
                $to_id = $_POST['to_id'];

                // Database connection
                $db_host = "localhost";
                $db_user = "root";
                $db_pass = "";
                $db_name = "chat_app_db";

                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                if ($conn->connect_error) {
                    $response = array(
                        "success" => false,
                        "message" => "Connection failed: " . $conn->connect_error
                    );
                } else {
                    // Check if the file already exists in the database
                    $check_query = "SELECT * FROM files WHERE filename = '$filename'";
                    $check_result = $conn->query($check_query);

                    // Insert the file information into the database
                    $sql = "INSERT INTO `files` (`from_id`, `to_id`, `filename`, `filesize`, `filetype`, `upload_date`) VALUES ($from_id, $to_id, '$filename', $filesize, '$filetype', NOW())";

                    if ($conn->query($sql) === TRUE) {
                        $response = array(
                            "success" => true,
                            "message" => "The file " . basename($_FILES["file"]["name"]) . " has been uploaded and the information has been stored in the database."
                        );
                    } else {
                        $response = array(
                            "success" => false,
                            "message" => "Sorry, there was an error uploading your file and storing information in the database: " . $conn->error
                        );
                    }

                    $conn->close();
                }
            } else {
                $response = array(
                    "success" => false,
                    "message" => "Sorry, there was an error uploading your file."
                );
            }
        }
    } else {
        $response = array(
            "success" => false,
            "message" => "No file was uploaded."
        );
    }
} else {
    $response = array(
        "success" => false,
        "message" => "Invalid request method."
    );
}

echo json_encode($response);