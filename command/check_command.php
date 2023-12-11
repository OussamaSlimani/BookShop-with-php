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

if (isset($_GET['command_id'])) {
     $commandId = $_GET['command_id'];

     // Update the value of 'valid' to 'Yes'
     $updateQuery = "UPDATE commands SET valid = 1 WHERE command_id = :command_id";
     $stmt = $pdo->prepare($updateQuery);
     $stmt->bindParam(':command_id', $commandId, PDO::PARAM_INT);

     try {
          $stmt->execute();
          header("Location: commands.php");
          exit();
     } catch (PDOException $e) {
          echo "Error updating command: " . $e->getMessage();
     }
} else {
     echo "Invalid command ID.";
}
