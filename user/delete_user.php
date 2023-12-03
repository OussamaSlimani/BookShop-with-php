<?php
// Database connection parameters
$DB_NAME = 'bookshop';
$DB_USER = 'root';
$DB_PASS = '';
$DB_HOST = 'localhost';

try {
     $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME}", $DB_USER, $DB_PASS);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Error: " . $e->getMessage());
}

// Check if user_id parameter exists in the URL
if (isset($_GET['user_id'])) {
     $user_id = $_GET['user_id'];

     // Prepare and execute the SQL query to delete the user
     $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
     $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

     try {
          $stmt->execute();
          header("Location: users.php"); // Redirect to the users list page after successful deletion
          exit();
     } catch (PDOException $e) {
          die("Error: " . $e->getMessage());
     }
} else {
     // If user_id parameter is not provided, redirect to the users list page
     header("Location: users.php");
     exit();
}
