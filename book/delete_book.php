<?php
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

if (isset($_GET['book_id'])) {
     $bookId = $_GET['book_id'];

     // Prepare SQL statement to delete a book
     $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = :book_id");
     $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);

     try {
          $stmt->execute();

          // Redirect back to the product list page after deletion
          header("Location: books.php");
          exit();
     } catch (PDOException $e) {
          die("Error: " . $e->getMessage());
     }
} else {
     header("Location: books.php");
     exit();
}
