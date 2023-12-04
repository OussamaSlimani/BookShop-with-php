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

// Check if category_id is provided in the URL
if (!isset($_GET['category_id'])) {
     // Redirect to the category list page if category_id is not provided
     header('Location: categories.php');
     exit();
}

$category_id = $_GET['category_id'];

// Fetch the category details from the database
$stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
     // Redirect to the category list page if the category doesn't exist
     header('Location: categories.php');
     exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     // Handle form submission
     $name = $_POST['name'];
     $description = $_POST['description'];

     // Validate input (you can add more validation as needed)

     // Update the category in the database
     $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE category_id = ?");
     $stmt->execute([$name, $description, $category_id]);

     // Redirect to the category list page
     header('Location: categories.php');
     exit();
}
?>

<head>
     <meta charset="utf-8" />
     <title>Categories</title>
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
                              <i class="bi bi-file-earmark-spreadsheet me-2"></i>Books
                         </a>
                    </li>
                    <!-- End -->
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2">
                         <a href="categories.php"> <i class="bi bi-card-list me-2"></i>Categories</a>
                    </li>
                    <!-- End -->
               </ul>
               <ul class="menu_items">
                    <div class="menu_title">
                         <h4>Users</h4>
                    </div>
                    <li class="item me-2 p-2 m-2">
                         <a href="../user/users.php"> <i class="bi bi-people-fill me-2"></i>Users lists </a>
                    </li>
               </ul>

               <ul class="menu_items">
                    <div class="menu_title">
                         <h4>Sliders</h4>
                    </div>
                    <!-- Start -->
                    <li class="item me-2 p-2 m-2">
                         <a href="../slider/sliders.php">
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
                         <a href="../index.php"> <i class="bi bi-arrow-left me-2"></i>Home </a>
                    </li>
                    <!-- End -->
               </ul>
          </div>
     </nav>
     
     <div class="container-admin">
          <div class="container" id="admin_component">
               <h2>Edit Category</h2>

               <!-- Category Form -->
               <form method="post" action="edit_category.php?category_id=<?= $category_id ?>">
                    <div class="mb-3">
                         <label for="name" class="form-label">Category Name</label>
                         <input type="text" class="form-control" id="name" name="name" value="<?= $category['name'] ?>" required>
                    </div>
                    <div class="mb-3">
                         <label for="description" class="form-label">Category Description</label>
                         <textarea class="form-control" id="description" name="description" rows="3" required><?= $category['description'] ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Category</button>
               </form>
          </div>
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