<?php
session_start();

// Database connection parameters
$DB_NAME = 'bookshop';
$DB_USER = 'root';
$DB_PASS = '';
$DB_HOST = 'localhost';

// PDO connection
try {
     $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME}", $DB_USER, $DB_PASS);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Error: " . $e->getMessage());
}

function getBookDetails($bookId)
{
     global $pdo;

     // Implement the query to fetch book details based on book_id
     $query = "SELECT * FROM books WHERE book_id = :book_id";
     $stmt = $pdo->prepare($query);
     $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
     $stmt->execute();

     // Fetch the book details as an associative array
     return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if user_id is set
if (isset($_SESSION['user_id'])) {
     // Assuming you have the user_id from the session
     $user_id = $_SESSION['user_id'];

     if ($_SERVER["REQUEST_METHOD"] == "POST") {
          // Retrieve data from the URL or form
          $totalPrice = $_POST['totalPrice'];
          // Add more data retrieval if needed

          foreach ($_SESSION['cart'] as $bookId => $quantity) {
               // Check if the book_id exists in the books table
               $bookDetails = getBookDetails($bookId);

               if ($bookDetails) {
                    // Book exists, proceed with insertion
                    $query = "INSERT INTO commands (total_price, valid, user_id, book_id) VALUES (:total_price, :valid, :user_id, :book_id)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':total_price', $totalPrice, PDO::PARAM_STR);
                    $stmt->bindValue(':valid', false, PDO::PARAM_BOOL);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
                    $stmt->execute();
               }
          }

          unset($_SESSION['cart']);
          header('Location: success.html');
          exit();
     } else {
          // Handle invalid requests
          echo "Invalid request.";
     }
} else {
     header('Location: 404.html');
     exit();
}
