<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
    } else {
        // Insert message into database
        $sql = "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Thank you! Your message has been sent successfully.';
        } else {
            $response['message'] = 'Sorry, there was an error sending your message. Please try again.';
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// If not POST request, redirect to home
header("Location: ../index.php");
exit();
