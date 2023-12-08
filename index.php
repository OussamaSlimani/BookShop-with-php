<?php
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


// categories
$selectCategoriesSql = "SELECT * FROM categories";
$selectCategoriesStmt = $pdo->query($selectCategoriesSql);
$categories = $selectCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// sliders
$selectSliderSql = "SELECT * FROM sliders";
$selectSliderStmt = $pdo->query($selectSliderSql);
$sliders = $selectSliderStmt->fetchAll(PDO::FETCH_ASSOC);


// Query to retrieve the newest 10 books from the 'books' table
$query = "SELECT * FROM books ORDER BY Publisher_date DESC LIMIT 10";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch the data as an associative array
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to retrieve books with a minimum 40% discount
$queryDiscountedBooks = "SELECT * FROM books WHERE promo >= 40 ORDER BY promo DESC";
$stmtDiscountedBooks = $pdo->prepare($queryDiscountedBooks);
$stmtDiscountedBooks->execute();

// Fetch the data as an associative array for Minimum 40% Discount
$discountedBooks = $stmtDiscountedBooks->fetchAll(PDO::FETCH_ASSOC);

// Query to retrieve books in Literary Genres (Biography, Literature, Historical)
$queryLiteraryGenres = "SELECT * FROM books WHERE category_id IN (1, 2, 3) LIMIT 3";
$stmtLiteraryGenres = $pdo->prepare($queryLiteraryGenres);
$stmtLiteraryGenres->execute();

// Fetch the data as an associative array for Literary Genres
$literaryGenres = $stmtLiteraryGenres->fetchAll(PDO::FETCH_ASSOC);

// Query to retrieve books in Fiction Genres (Fantasy, Thriller, Romance)
$queryFictionGenres = "SELECT * FROM books WHERE category_id IN (4, 5, 6) LIMIT 3";
$stmtFictionGenres = $pdo->prepare($queryFictionGenres);
$stmtFictionGenres->execute();

// Fetch the data as an associative array for Fiction Genres
$fictionGenres = $stmtFictionGenres->fetchAll(PDO::FETCH_ASSOC);

// Close the connection
$pdo = null;
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
            <a class="nav-link" href="cart/shopping_cart.php">
              <i class="bi bi-cart"></i>Shopping cart
              <span class="cart-notification">0</span>
            </a>
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


  <!-- Carousel Start -->
  <div class="container-fluid p-0 mb-5 mt-5 pt-4">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <?php
        foreach ($sliders as $key => $slider) {
          $isActive = ($key == 0) ? 'active' : '';
        ?>
          <div class="carousel-item <?= $isActive ?>">
            <img class="w-100" src="<?= str_replace('../', '', $slider['image_path']) ?>" alt="<?= $slider['alt'] ?>" />
            <div class="carousel-caption">
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-lg-7 pt-5"></div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>

      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
  <!-- Carousel End -->


  <!-- Facts Start -->
  <div id="number" class="container-xxl">
    <div class="container py-2">
      <div class="text-center mx-auto mb-2 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <div class="d-inline-block rounded-pill bg-light text-primary py-1 px-3 mb-3">
          ByteReads
        </div>
        <h1 class="display-6">Quantifying Success.</h1>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="container-xxl pt-4">
          <div class="container">
            <div class="row g-4">
              <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                  <i class="fa fa-certificate fa-4x text-primary mb-4"></i>
                  <h5 class="mb-3">Years Old</h5>
                  <div class="d-flex justify-content-center align-items-center">
                    <h1 class="display-6 mb-0">+</h1>
                    <h1 class="display-5 mb-0">13</h1>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                  <i class="fa fa-users fa-4x text-primary mb-4"></i>
                  <h5 class="mb-3">Happy clients</h5>
                  <div class="d-flex justify-content-center align-items-center">
                    <h1 class="display-6 mb-0">+</h1>
                    <h1 class="display-5 mb-0">6500</h1>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                  <i class="fa fa-book fa-4x text-primary mb-4"></i>
                  <h5 class="mb-3">Titles</h5>
                  <div class="d-flex justify-content-center align-items-center">
                    <h1 class="display-6 mb-0">+</h1>
                    <h1 class="display-5 mb-0">10,000</h1>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                  <i class="fa fa-flag fa-4x text-primary mb-4"></i>
                  <h5 class="mb-3">Countrie Reach</h5>
                  <div class="d-flex justify-content-center align-items-center">
                    <h1 class="display-6 mb-0">+</h1>
                    <h3 class="display-5 mb-0">30</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Facts End -->


  <!-- =================== New Releases Discount =================== -->
  <div class="container-xxl pt-5 mt-5">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 mb-5 text-border">New Releases Discount</h1>
      </div>
      <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
        <?php foreach ($books as $book) : ?>
          <div class="d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="product">
              <a href='details.php?book_id=<?= $book['book_id'] ?>'>
                <div class="product-img">
                  <img src="<?php echo str_replace('../', '', $book['image_path']); ?>" class="img-fluid" alt="" />
                </div>
                <div class="product-info">
                  <h4><?php echo $book['title']; ?></h4>
                  <p>
                    <span style="text-decoration: line-through; color: red"><?php echo $book['price']; ?> TND</span>
                    <span style="color: green"><?php echo round($book['price'] - ($book['price'] / $book['promo']), 2); ?> TND</span>
                  </p>
                </div>
              </a>
              <button class="btn btn-primary mb-2" onclick="addToCart(<?php echo $book['book_id']; ?>)">
                Add to cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- =================== New Releases Discount End =================== -->


  <!-- =================== Minimum 40% Discount Start =================== -->
  <div class="container-xxl pt-5 mt-5">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 mb-5 text-border">Minimum 40% Discount</h1>
      </div>
      <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
        <?php foreach ($discountedBooks as $discountedBook) : ?>
          <div class="d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="product">
              <a href='details.php?book_id=<?= $discountedBook['book_id'] ?>'>
                <div class="product-img">
                  <img src="<?php echo str_replace('../', '', $discountedBook['image_path']); ?>" class="img-fluid" alt="" />
                </div>
                <div class="product-info">
                  <h4><?php echo $discountedBook['title']; ?></h4>
                  <p>
                    <span style="text-decoration: line-through; color: red"><?php echo $discountedBook['price']; ?> TND</span>
                    <span style="color: green"><?php echo round($discountedBook['price'] - ($discountedBook['price'] / $discountedBook['promo']), 2); ?> TND</span>
                  </p>
                </div>
              </a>
              <button class="btn btn-primary mb-2" onclick="addToCart(<?php echo $discountedBook['book_id']; ?>)">
                Add to cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- =================== Minimum 40% Discount End =================== -->


  <!-- =================== Literary Genres Discount =================== -->
  <div class="container-xxl pt-5 mt-5">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 mb-5 text-border">Literary Genres Discount</h1>
      </div>
      <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
        <?php foreach ($literaryGenres as $literaryGenre) : ?>
          <div class="d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="product">
              <a href='details.php?book_id=<?= $literaryGenre['book_id'] ?>'>
                <div class="product-img">
                  <img src="<?php echo str_replace('../', '', $literaryGenre['image_path']); ?>" class="img-fluid" alt="" />
                </div>
                <div class="product-info">
                  <h4><?php echo $literaryGenre['title']; ?></h4>
                  <p>
                    <span style="text-decoration: line-through; color: red"><?php echo $literaryGenre['price']; ?> TND</span>
                    <span style="color: green"><?php echo round($literaryGenre['price'] - ($literaryGenre['price'] / $literaryGenre['promo']), 2); ?> TND</span>
                  </p>
                </div>
              </a>
              <button class="btn btn-primary mb-2" onclick="addToCart(<?php echo $literaryGenre['book_id']; ?>)">
                Add to cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- =================== Literary Genres Discount End =================== -->



  <!-- =================== Fiction Genres Discount =================== -->
  <div class="container-xxl pt-5 mt-5">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 mb-5 text-border">Fiction Genres Discount</h1>
      </div>
      <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
        <?php foreach ($fictionGenres as $fictionGenre) : ?>
          <div class="d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="product">
              <a href='details.php?book_id=<?= $fictionGenre['book_id'] ?>'>
                <div class="product-img">
                  <img src="<?php echo str_replace('../', '', $fictionGenre['image_path']); ?>" class="img-fluid" alt="" />
                </div>
                <div class="product-info">
                  <h4><?php echo $fictionGenre['title']; ?></h4>
                  <p>
                    <span style="text-decoration: line-through; color: red"><?php echo $fictionGenre['price']; ?> TND</span>
                    <span style="color: green"><?php echo round($fictionGenre['price'] - ($fictionGenre['price'] / $fictionGenre['promo']), 2); ?> TND</span>
                  </p>
                </div>
              </a>
              <button class="btn btn-primary mb-2" onclick="addToCart(<?php echo $fictionGenre['book_id']; ?>)">
                Add to cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- =================== Fiction Genres Discount End =================== -->

  <!-- =================== Sponsors Start =================== -->
  <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container py-5">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 text-border">Meet Our Sponsors</h1>
      </div>
      <div class="owl-carousel sponsor-carousel wow fadeInUp" data-wow-delay="0.6s">
        <div class="testimonial-item my-4">
          <div class="d-flex align-items-center">
            <img class="img-fluid rounded" src="img/clients/client-1.png" />
          </div>
        </div>
        <div class="testimonial-item my-4">
          <div class="d-flex align-items-center">
            <img class="img-fluid rounded" src="img/clients/client-2.png" />
          </div>
        </div>
        <div class="testimonial-item my-4">
          <div class="d-flex align-items-center">
            <img class="img-fluid rounded" src="img/clients/client-3.png" />
          </div>
        </div>
        <div class="testimonial-item my-4">
          <div class="d-flex align-items-center">
            <img class="img-fluid rounded" src="img/clients/client-4.png" />
          </div>
        </div>
        <div class="testimonial-item my-4">
          <div class="d-flex align-items-center">
            <img class="img-fluid rounded" src="img/clients/client-5.png" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== footer ====== -->
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
  <script src="lib/wow/wow.min.js"></script>
  <script src="lib/easing/easing.min.js"></script>
  <script src="lib/waypoints/waypoints.min.js"></script>
  <script src="lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="lib/parallax/parallax.min.js"></script>

  <!-- Template Javascript -->
  <script src="js/main.js"></script>

  <script>
    function addToCart(bookId) {
      // Send AJAX request to add the item to the cart
      $.ajax({
        type: "POST",
        url: "cart/add_to_cart.php", // Create this file to handle the server-side logic
        data: {
          book_id: bookId
        },
        success: function(response) {
          // Update the cart icon notification
          updateCartNotification(response);
        }
      });
    }

    function updateCartNotification(count) {
      // Update the notification bubble on the cart icon
      // You can use your preferred method to update the notification (e.g., jQuery)
      $(".cart-notification").text(count);
    }
  </script>


</body>

</html>