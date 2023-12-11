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

$selectSliderSql = "SELECT * FROM sliders";
$selectSliderStmt = $pdo->query($selectSliderSql);
$sliders = $selectSliderStmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through each slider and generate a form
foreach ($sliders as $slider) {
     // Handle form submission for each slider
     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_slider_' . $slider['slider_id']])) {
          $updatedAlt = $_POST['alt'];
          $updatedImagePath = '';

          // Handle image upload
          if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === 0) {
               $targetDir = "../img/uploads/";

               if (!is_dir($targetDir)) {
                    die("Error: The directory $targetDir does not exist.");
               }

               $targetFile = $targetDir . basename($_FILES['image_path']['name']);

               if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile)) {
                    echo "File has been uploaded successfully.";
               } else {
                    echo "Error uploading file.";
               }

               $updatedImagePath = $targetFile;
          }


          $updateAllSql = "UPDATE sliders SET 
            alt = :alt,
            image_path = :image_path
            WHERE slider_id = :slider_id";

          $updateAllStmt = $pdo->prepare($updateAllSql);

          $updateAllStmt->bindParam(':alt', $updatedAlt);
          $updateAllStmt->bindParam(':image_path', $updatedImagePath);
          $updateAllStmt->bindParam(':slider_id', $slider['slider_id']);

          $updateAllStmt->execute();

          header("Location: sliders.php");
          exit;
     }
}
?>

<!DOCTYPE html>
<html lang="en">

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
                         <a href="./sliders.php">
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
          <?php foreach ($sliders as $slider) : ?>
               <!-- Edit slider Form Start -->
               <form action="" method="post" enctype="multipart/form-data">
                    <div class="header">
                         <h3 class="title">Edit slider <?= $slider['slider_id'] ?></h3>
                    </div>
                    <div class="body">
                         <!-- Title Field -->
                         <div class="form-group">
                              <label>Alt</label>
                              <input type="text" name="alt" class="form-control" value="<?= htmlspecialchars($slider['alt']) ?>" required />
                         </div>

                         <!-- slider image Field -->
                         <div class="form-group">
                              <label>slider Cover</label>
                              <input type="file" name="image_path" class="form-control" accept="image/*" />
                         </div>

                         <div>
                              <!-- Reset and Submit Buttons -->
                              <input type="reset" class="btn btn-default" value="Cancel" />
                              <input type="submit" class="btn btn-success" value="Update slider" name="edit_slider_<?= $slider['slider_id'] ?>" />
                         </div>
                    </div>
               </form>
          <?php endforeach; ?>
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