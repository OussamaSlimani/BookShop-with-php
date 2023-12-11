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

$selectCategoriesSql = "SELECT * FROM categories";
$selectCategoriesStmt = $pdo->query($selectCategoriesSql);
$categories = $selectCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['book_id'])) {
     $book_id = $_GET['book_id'];

     $selectBookSql = "SELECT * FROM books WHERE book_id = :book_id";
     $selectBookStmt = $pdo->prepare($selectBookSql);
     $selectBookStmt->bindParam(':book_id', $book_id);
     $selectBookStmt->execute();
     $bookDetails = $selectBookStmt->fetch(PDO::FETCH_ASSOC);

     if (!$bookDetails) {
          exit;
     }
} else {
     exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_book'])) {
     $book_id = $_POST['book_id'];
     $updatedTitle = $_POST['title'];
     $updatedImagePath = '';
     $updatedSummary = $_POST['summary'];
     $updatedAuthor = $_POST['author'];
     $updatedPublisherDate = $_POST['publisher_date'];
     $updatedLanguage = $_POST['language'];
     $updatedNoOfPages = $_POST['no_of_Pages'];
     $updatedPrice = $_POST['price'];
     $updatedPromo = $_POST['promo'];
     $updatedQuantity = $_POST['quantity'];
     $updatedCategoryId = $_POST['category_id'];

     // Handle image upload
     if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === 0) {
          $targetDir = "../img/uploads/";
          $targetFile = $targetDir . basename($_FILES['image_path']['name']);
          move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile);
          $updatedImagePath = $targetFile;
     }

     $updateAllSql = "UPDATE books SET 
        title = :title,
        image_path = :image_path,
        summary = :summary,
        author = :author,
        publisher_date = :publisher_date,
        language = :language,
        no_of_Pages = :no_of_pages,
        price = :price,
        promo = :promo,
        quantity = :quantity,
        category_id = :category_id
        WHERE book_id = :book_id";

     $updateAllStmt = $pdo->prepare($updateAllSql);

     $updateAllStmt->bindParam(':title', $updatedTitle);
     $updateAllStmt->bindParam(':image_path', $updatedImagePath);
     $updateAllStmt->bindParam(':summary', $updatedSummary);
     $updateAllStmt->bindParam(':author', $updatedAuthor);
     $updateAllStmt->bindParam(':publisher_date', $updatedPublisherDate);
     $updateAllStmt->bindParam(':language', $updatedLanguage);
     $updateAllStmt->bindParam(':no_of_pages', $updatedNoOfPages);
     $updateAllStmt->bindParam(':price', $updatedPrice);
     $updateAllStmt->bindParam(':promo', $updatedPromo);
     $updateAllStmt->bindParam(':quantity', $updatedQuantity);
     $updateAllStmt->bindParam(':category_id', $updatedCategoryId);
     $updateAllStmt->bindParam(':book_id', $book_id);

     $updateAllStmt->execute();

     header("Location: books.php");
     exit;
}
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
                    <li class="admin-active item me-2 p-2 m-2 active">
                         <a href="./books.php">
                              <i class="bi bi-file-earmark-spreadsheet me-2"></i>Books
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

          <!-- Edit Book Form Start -->
          <!-- Edit Book Form Start -->
          <form action="" method="post" enctype="multipart/form-data">
               <div class="header">
                    <h3 class="title">Edit Book</h3>
               </div>
               <div class="body">
                    <!-- Title Field -->
                    <div class="form-group">
                         <label>Title</label>
                         <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($bookDetails['title']) ?>" required />
                    </div>

                    <!-- Category Field -->
                    <div class="form-group">
                         <label>Category</label>
                         <select class="form-control" name="category_id" required>
                              <?php
                              foreach ($categories as $category) {
                                   $selected = ($category['category_id'] == $bookDetails['category_id']) ? 'selected' : '';
                                   echo "<option value='{$category['category_id']}' $selected>{$category['name']}</option>";
                              }
                              ?>
                         </select>
                    </div>

                    <!-- Author Field -->
                    <div class="form-group">
                         <label>Author</label>
                         <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($bookDetails['author']) ?>" required />
                    </div>

                    <!-- Summary Field -->
                    <div class="form-group">
                         <label>Summary</label>
                         <input type="text" name="summary" class="form-control" value="<?= htmlspecialchars($bookDetails['summary']) ?>" required />
                    </div>

                    <!-- Publisher Date Field -->
                    <div class="form-group">
                         <label>Publisher Date</label>
                         <input type="date" name="publisher_date" class="form-control" value="<?= htmlspecialchars($bookDetails['publisher_date']) ?>" required />
                    </div>

                    <!-- Book Cover Field -->
                    <div class="form-group">
                         <label>Book Cover</label>
                         <input type="file" name="image_path" class="form-control" accept="image/*" />
                    </div>

                    <!-- Language Field -->
                    <div class="form-group">
                         <label>Language</label>
                         <input type="text" name="language" class="form-control" value="<?= htmlspecialchars($bookDetails['language']) ?>" required />
                    </div>

                    <!-- Number of Pages Field -->
                    <div class="form-group">
                         <label>Number of Pages</label>
                         <input type="text" name="no_of_Pages" class="form-control" value="<?= htmlspecialchars($bookDetails['no_of_Pages']) ?>" required />
                    </div>

                    <!-- Price Field -->
                    <div class="form-group">
                         <label>Price</label>
                         <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($bookDetails['price']) ?>" required />
                    </div>

                    <!-- Promo Field -->
                    <div class="form-group">
                         <label>Promo</label>
                         <input type="number" name="promo" class="form-control" value="<?= htmlspecialchars($bookDetails['promo']) ?>" required />
                    </div>

                    <!-- Quantity Field -->
                    <div class="form-group">
                         <label>Quantity</label>
                         <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($bookDetails['quantity']) ?>" required />
                    </div>

                    <!-- Hidden Input -->
                    <input type="hidden" name="book_id" value="<?= htmlspecialchars($bookDetails['book_id']) ?>" />

               </div>
               <div>
                    <!-- Reset and Submit Buttons -->
                    <input type="submit" class="btn btn-success" value="Update Book" name="edit_book" />
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