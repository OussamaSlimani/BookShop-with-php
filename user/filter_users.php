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
$limit = 10; // Number of rows per page

$offset = ($page - 1) * $limit;
$query = "SELECT * FROM users LIMIT {$limit} OFFSET {$offset}";

$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination - Get total rows count
$totalRows = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();


if (isset($_POST['userName'])) {
     $userName = $_POST['userName'];

     // Modify your SQL query to include the filter
     $query = "SELECT * FROM users
            WHERE full_name LIKE :userName
            LIMIT {$limit} OFFSET {$offset}";

     $stmt = $pdo->prepare($query);
     $stmt->bindValue(':userName', '%' . $userName . '%', PDO::PARAM_STR);
     $stmt->execute();

     $filteredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

     // Output the filtered data as HTML
     foreach ($filteredUsers as $user) {
          echo "<tr>
            <td class='text-center'>{$user['user_id']}</td>
            <td>{$user['full_name']}</td>
            <td>{$user['email']}</td>
            <td class='td-actions text-right'>
              <a href='delete_user.php?user_id={$user['user_id']}'><i class='fa fa-trash me-2'></i></a>
            </td>
          </tr>";
     }
}
