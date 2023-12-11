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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
     // Validate and sanitize the form data
     $title = htmlspecialchars($_POST['title']);
     $category_id = intval($_POST['category_id']);
     $author = htmlspecialchars($_POST['author']);
     $language = htmlspecialchars($_POST['language']);
     $summary = htmlspecialchars($_POST['summary']);
     $publisher_date = $_POST['publisher_date'];
     $image_path = ''; // Handle image upload separately
     $no_of_pages = intval($_POST['no_of_pages']);
     $price = floatval($_POST['price']);
     $promo = intval($_POST['promo']);
     $quantity = intval($_POST['quantity']);

     // Handle image upload
     if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === 0) {
          $targetDir = "../img/uploads/";
          $targetFile = $targetDir . basename($_FILES['image_path']['name']);
          move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile);
          $image_path = $targetFile;
     }

     // Insert the new product into the database
     $insertBookSql = "INSERT INTO books (title, category_id, author, language, summary, publisher_date, image_path, no_of_pages, price, promo, quantity) 
                 VALUES (:title, :category_id, :author, :language, :summary, :publisher_date, :image_path, :no_of_pages, :price, :promo, :quantity)";

     $insertBookStmt = $pdo->prepare($insertBookSql);

     $insertBookStmt->bindParam(':title', $title);
     $insertBookStmt->bindParam(':category_id', $category_id);
     $insertBookStmt->bindParam(':author', $author);
     $insertBookStmt->bindParam(':language', $language);
     $insertBookStmt->bindParam(':summary', $summary);
     $insertBookStmt->bindParam(':publisher_date', $publisher_date);
     $insertBookStmt->bindParam(':image_path', $image_path);
     $insertBookStmt->bindParam(':no_of_pages', $no_of_pages);
     $insertBookStmt->bindParam(':price', $price);
     $insertBookStmt->bindParam(':promo', $promo);
     $insertBookStmt->bindParam(':quantity', $quantity);

     try {
          $insertBookStmt->execute();
          echo "Book added successfully!";
          // Redirect to the product list page if book_id is not provided in the URL
          header("Location: books.php");
          exit();
     } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
     }
}

// Fetch categories from the database using prepared statement
$categorySql = "SELECT * FROM categories";
$categoryStmt = $pdo->prepare($categorySql);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="utf-8" />
     <title>Add book</title>
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
                         <a href="../categorie/categories.php"> <i class="bi bi-card-list me-2"></i>Categories</a>
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
                         <a href="../command/commands.php">
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
          <!-- Manage Products Start -->
          <form action="" method="post" enctype="multipart/form-data">
               <div class="header">
                    <h3 class="title">Add New Book</h3>
               </div>
               <div class="body">
                    <div class="form-group">
                         <label>Title</label>
                         <input type="text" name="title" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Category</label>
                         <select class="form-control" name="category_id" required>
                              <?php
                              foreach ($categories as $category) {
                                   echo "<option value='" . $category['category_id'] . "'>" . $category['name'] . "</option>";
                              }
                              ?>
                         </select>
                    </div>
                    <div class="form-group">
                         <label>Author</label>
                         <input type="text" name="author" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Language</label>
                         <input type="text" name="language" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Summary</label>
                         <input type="text" name="summary" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Publisher_date</label>
                         <input type="date" name="publisher_date" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Book Cover</label>
                         <input type="file" name="image_path" class="form-control" />
                    </div>
                    <div class="form-group">
                         <label>No. of Pages</label>
                         <input type="text" name="no_of_pages" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Price</label>
                         <input type="text" name="price" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Promo</label>
                         <input type="text" name="promo" class="form-control" required />
                    </div>
                    <div class="form-group">
                         <label>Quantity</label>
                         <input type="text" name="quantity" class="form-control" required />
                    </div>
               </div>
               <div>
                    <input type="reset" class="btn btn-default" value="Cancel" />
                    <input type="submit" class="btn btn-success" value="Add Book" name="add_book" />
               </div>
          </form>
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