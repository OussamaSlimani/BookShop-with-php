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

// Fetch data with pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;

$offset = ($page - 1) * $limit;
$query = "SELECT b.*, c.name AS category FROM books b
          LEFT JOIN categories c ON b.category_id = c.category_id
          LIMIT {$limit} OFFSET {$offset}";

$stmt = $pdo->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination - Get total rows count
$totalRows = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();


if (isset($_POST['bookName'])) {
     $bookName = $_POST['bookName'];

     // Modify your SQL query to include the filter
     $query = "SELECT b.*, c.name AS category FROM books b
            LEFT JOIN categories c ON b.category_id = c.category_id
            WHERE b.title LIKE :bookName
            LIMIT {$limit} OFFSET {$offset}";

     $stmt = $pdo->prepare($query);
     $stmt->bindValue(':bookName', '%' . $bookName . '%', PDO::PARAM_STR);
     $stmt->execute();

     $filteredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

     // Output the filtered data as HTML
     foreach ($filteredProducts as $product) {
          echo "<tr>
            <td class='text-center'>{$product['book_id']}</td>
            <td>{$product['title']}</td>
            <td>{$product['author']}</td>
            <td>{$product['price']}</td>
            <td>{$product['promo']}</td>
            <td>{$product['quantity']}</td>
            <td>{$product['category']}</td>
            <td class='td-actions text-right'>
              <a href='edit_book.php?book_id={$product['book_id']}'><i class='fa fa-pen me-2'></i></a>
              <a href='delete_book.php?book_id={$product['book_id']}'><i class='fa fa-trash me-2'></i></a>
            </td>
          </tr>";
     }
}
