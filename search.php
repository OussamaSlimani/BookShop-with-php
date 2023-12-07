<?php
// Include database connection code here
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>ChariTeam - Free Nonprofit Website Template</title>
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
  <link href="lib/animate/animate.min.css" rel="stylesheet" />
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet" />
</head>

<body>
  <!-- Spinner Start -->
  <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
  </div>
  <!-- Spinner End -->

  <!-- ====================== Navbar Start ===================== -->
  <?php
  session_start();

  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
  } else {
    $user_id = "you should be logged in";
  }

  if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();

    header('Location: index.php');
    exit();
  }
  if (isset($_POST['login'])) {
    session_unset();
    session_destroy();

    header('Location: login.php');
    exit();
  }
  // categories
    $selectCategoriesSql = "SELECT * FROM categories";
    $selectCategoriesStmt = $pdo->query($selectCategoriesSql);
    $categories = $selectCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);
  $query = isset($_GET['query']) ? $_GET['query'] : '';


  // Your SQL query to retrieve books based on the search query
    $searchQuery = "SELECT * FROM books WHERE title LIKE :query OR author LIKE :query";
    $stmtSearch = $pdo->prepare($searchQuery);
    $stmtSearch->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmtSearch->execute();
    $searchedBooks = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <div class="container-fluid fixed-top px-0 wow fadeIn bg-light" data-wow-delay="0.1s">
    <nav class="navbar navbar-expand-lg navbar-dark py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
      <a href="./index.php" class="navbar-brand ms-lg-0">
        <h1 class="fw-bold text-primary m-0">ByteReads</h1>
      </a>
      <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto p-4 p-lg-0">
          <li class="nav-item">
            <a href="./index.php" class="nav-link active">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a href="./categorie/categories.php" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Category</a>
            <div class="dropdown-menu m-0">
              <?php
              foreach ($categories as $category) {
                echo "<a href='category_list.php?category_id={$category['category_id']}' class='dropdown-item'>{$category['name']}</a>";
              }
              ?>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./cart/shopping_cart.php">
              <i class="bi bi-cart"></i>Shopping cart</a>
          </li>

          <li class="nav-item d-flex align-items-center">
            <form method="POST" action="">
              <?php if ($user_id != "you should be logged in") : ?>
                <button type="submit" name="logout" class="btn btn-primary nav-link px-2 py-2">Logout</button>
              <?php else : ?>
                <button type="submit" name="login" class="btn btn-primary nav-link px-2 py-2">Login</button>
              <?php endif; ?>
            </form>
          </li>

          <li class="nav-item d-flex align-items-center">
            <div class="input-group">
              <form method="GET" action="search.php" class="form-inline my-2 my-lg-0">
                <div class="d-flex">
                    <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </div>
              </form>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
  <!-- ====================== Navbar End ===================== -->
  <!-- Page Header Start -->
  <div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center">
      <h1 class="display-4 text-white animated slideInDown mb-4">
        Search Results for "<?php echo htmlspecialchars($query); ?>"
      </h1>
    </div>
  </div>
  <!-- Page Header End -->

  <!-- Display Searched Books -->
  <div class="container" data-aos="fade-up">
    <div class="row p-2 m-2">
      <?php foreach ($searchedBooks as $book) : ?>
        <div class="col-lg-3 col-md-6">
          <div class="product">
            <div class="product-img">
              <img src="<?php echo str_replace('../', '', $book['image_path']); ?>" class="img-fluid" alt="" />
            </div>
            <div class="product-info">
              <h4><?php echo $book['title']; ?></h4>
              <p>
                <span style="text-decoration: line-through; color: red"><?php echo $book['price']; ?> TND</span>
                <span style="color: green"><?php echo round($book['price'] - ($book['price'] / $book['promo']), 2); ?> TND</span>
              </p>
              <a class="btn btn-primary py-2 px-2 mb-2" href="">
                Add to cart
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

 <!-- Footer Start -->
 <div class="container-fluid bg-dark text-white-50 footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-lg-3 col-md-6">
          <h1 class="fw-bold text-primary mb-4">ByteReads</h1>
          <p>
            Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat
            ipsum et lorem et sit, sed stet lorem sit clita
          </p>
          <div class="d-flex pt-2">
            <a class="btn btn-square me-1" href=""><i class="fab fa-twitter"></i></a>
            <a class="btn btn-square me-1" href=""><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-square me-1" href=""><i class="fab fa-youtube"></i></a>
            <a class="btn btn-square me-0" href=""><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Address</h5>
          <p>
            <i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA
          </p>
          <p><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
          <p><i class="fa fa-envelope me-3"></i>info@example.com</p>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Quick Links</h5>
          <a class="btn btn-link" href="">New Releases</a>
          <a class="btn btn-link" href="">40% Discount</a>
          <a class="btn btn-link" href="">Literary Genres</a>
          <a class="btn btn-link" href="">Fiction Genres</a>
        </div>
        <div class="col-lg-3 col-md-6">
          <h5 class="text-light mb-4">Newsletter</h5>
          <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
          <div class="position-relative mx-auto" style="max-width: 400px">
            <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email" />
            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">
              SignUp
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid copyright">
      <div class="container">
        <div class="row">
          <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            &copy; 2023, All Right Reserved.
          </div>
          <div class="col-md-6 text-center text-md-end">
            Designed By Oussama Slimani & Iheb Charfeddine
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer End -->

  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./lib/wow/wow.min.js"></script>
  <script src="lib/easing/easing.min.js"></script>
  <script src="lib/waypoints/waypoints.min.js"></script>
  <script src="lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="lib/parallax/parallax.min.js"></script>

  <!-- Template Javascript -->
  <script src="js/main.js"></script>
</body>

</html>
