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
$query = "SELECT c.*, u.full_name AS client_name, b.title AS book_title
          FROM commands c
          LEFT JOIN users u ON c.user_id = u.user_id
          LEFT JOIN books b ON c.book_id = b.book_id
          LIMIT {$limit} OFFSET {$offset}";

$stmt = $pdo->query($query);
$commands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination - Get total rows count
$totalRows = $pdo->query("SELECT COUNT(*) FROM commands")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="utf-8" />
     <title>Manage Books</title>
     <meta content="width=device-width, initial-scale=1.0" name="viewport" />
     <meta content="" name="keywords" />
     <meta content="" name="description" />

     <!-- Favicon -->
     <link href="img/favicon.ico" rel="icon" />

     <!-- Google Web Fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com" />
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Saira:wght@500;600;700&display=swap" rel="stylesheet" />

     <!-- Icon Font Stylesheet -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

     <!-- Libraries Stylesheet -->
     <link href="../lib/animate/animate.min.css" rel="stylesheet" />
     <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

     <!-- Customized Bootstrap Stylesheet -->
     <link href="../css/bootstrap.min.css" rel="stylesheet" />

     <!-- Template Stylesheet -->
     <link href="../css/style.css" rel="stylesheet" />
</head>

<body>

     <!-- navbar -->
     <nav class="navbar">
          <div class="logo_item">
               <i class="bi bi-list" id="sidebarOpen"></i>
          </div>
          <a href="index.html" class="navbar-brand ms-lg-0">
               <h3 class="fw-bold text-primary m-0">ByteReads Dahsboard</h3>
          </a>
     </nav>

     <!-- sidebar -->
     <nav class="sidebar">
          <div class="menu_content">
               <ul class="menu_items">
                    <div class="menu_title">
                         <h4 class="mt-4">Dahsboard</h4>
                    </div>
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2 active">
                         <a href="../book/books.php">
                              <i class="bi bi-file-earmark-spreadsheet me-2"></i>Product
                         </a>
                    </li>
                    <!-- End -->
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2">
                         <a href="#"> <i class="bi bi-card-list me-2"></i>Category</a>
                    </li>
                    <!-- End -->
               </ul>
               <ul class="menu_items">
                    <div class="menu_title">
                         <h4>Users</h4>
                    </div>
                    <li class="item me-2 p-2 m-2">
                         <a href="#"> <i class="bi bi-people-fill me-2"></i>Users lists </a>
                    </li>
               </ul>

               <ul class="menu_items">
                    <div class="menu_title">
                         <h4>Sliders</h4>
                    </div>
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2">
                         <a href="#">
                              <i class="bi bi-card-image me-2"></i>Choose pictures
                         </a>
                    </li>
                    <!-- End -->
               </ul>
               <ul class="menu_items">
                    <div class="menu_title">
                         <h4>Commands</h4>
                    </div>
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2">
                         <a href="#">
                              <i class="bi bi-bag-fill me-2"></i>Clients commands
                         </a>
                    </li>
                    <!-- End -->
               </ul>
               <ul class="menu_items">
                    <div class="menu_title mt-4"></div>
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2 fw-bold">
                         <a href="#"> <i class="bi bi-arrow-left me-2"></i>Home </a>
                    </li>
                    <!-- End -->
               </ul>
          </div>
     </nav>

     <div class="container-admin">
          <!-- Manage Products Start -->
          <div class="table-responsive">
               <table class="table">
                    <thead>
                         <tr>
                              <th class="text-center">#</th>
                              <th>Book Title</th>
                              <th>Client Full Name</th>
                              <th>Total Price</th>
                              <th>Valid</th>
                              <th class="text-right">Actions</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php foreach ($commands as $command) : ?>
                              <?php
                              // Check if the command is not valid
                              $rowClass = $command['valid'] ? '' : 'bg-danger text-white';
                              ?>
                              <tr class="<?= $rowClass ?>">
                                   <td class="text-center"><?= $command['command_id'] ?></td>
                                   <td><?= $command['book_title'] ?></td>
                                   <td><?= $command['client_name'] ?></td>
                                   <td><?= $command['total_price'] ?></td>
                                   <td><?= $command['valid'] ? 'Yes' : 'No' ?></td>
                                   <!-- Action buttons -->
                                   <td class="td-actions text-right">
                                        <a href='check_command.php?command_id=<?= $command['command_id'] ?>'><i class='fa fa-check me-2'></i></a>
                                   </td>
                              </tr>
                         <?php endforeach; ?>
                    </tbody>
               </table>
          </div>
     </div>




     <!-- Pagination Section -->
     <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-end">
               <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
               </li>
               <?php for ($i = 1; $i <= ceil($totalRows / $limit); $i++) : ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                         <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
               <?php endfor; ?>
               <li class="page-item <?= $page >= ceil($totalRows / $limit) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
               </li>
          </ul>
     </nav>
     </div>
     <!-- Admin Dashboard End -->
     </div>
     <!-- JavaScript -->
     <script>
          const sidebar = document.querySelector(".sidebar");
          sidebarOpen.addEventListener("click", () =>
               sidebar.classList.toggle("close")
          );

          if (window.innerWidth < 768) {
               sidebar.classList.add("close");
          } else if (window.innerWidth >= 768) {
               sidebar.classList.remove("close");
          }
     </script>
</body>

</html>