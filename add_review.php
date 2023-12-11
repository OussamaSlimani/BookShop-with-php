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

$selectCategoriesSql = "SELECT * FROM categories";
$selectCategoriesStmt = $pdo->query($selectCategoriesSql);
$categories = $selectCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

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

// Check if user_id and book_id are set in the URL
if (isset($_GET['user_id']) && isset($_GET['book_id'])) {
  $user_id = $_GET['user_id'];
  $book_id = $_GET['book_id'];
} else {
  exit;
}

// Select book details
$selectBookSql = "SELECT books.*, categories.name FROM books 
 JOIN categories ON books.category_id = categories.category_id 
 WHERE book_id = :book_id";

$selectBookStmt = $pdo->prepare($selectBookSql);
$selectBookStmt->bindParam(':book_id', $book_id);
$selectBookStmt->execute();
$bookDetails = $selectBookStmt->fetch(PDO::FETCH_ASSOC);

if (!$bookDetails) {
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve data from the form
  $rating = isset($_POST['rating']) ? $_POST['rating'] : null;
  $review_headline = isset($_POST['review_headline']) ? $_POST['review_headline'] : null;
  $review_text = isset($_POST['review_text']) ? $_POST['review_text'] : null;

  // Validate form data (you may add more validation as needed)
  if (empty($rating) || empty($review_text) || empty($review_headline)) {
    exit;
  }

  // Check if the user exists
  $checkUserSql = "SELECT COUNT(*) FROM users WHERE user_id = :user_id";
  $checkUserStmt = $pdo->prepare($checkUserSql);
  $checkUserStmt->bindParam(':user_id', $user_id);
  $checkUserStmt->execute();

  $userExists = $checkUserStmt->fetchColumn();

  if (!$userExists) {
    header('Location: 404.php');
    exit();
  }
  // Sanitize and validate user input
  $rating = intval($rating);
  $review_headline = htmlspecialchars($review_headline);
  $review_text = htmlspecialchars($review_text);

  // Insert the review into the database
  $insertReviewSql = "INSERT INTO reviews (review_headline, review_text, rating, user_id, book_id) 
                    VALUES (:review_headline, :review_text, :rating, :user_id, :book_id)";

  $insertReviewStmt = $pdo->prepare($insertReviewSql);
  $insertReviewStmt->bindParam(':review_headline', $review_headline);
  $insertReviewStmt->bindParam(':review_text', $review_text);

  $insertReviewStmt->bindParam(':rating', $rating);
  $insertReviewStmt->bindParam(':user_id', $user_id);
  $insertReviewStmt->bindParam(':book_id', $book_id);
  $insertReviewStmt->execute();

  try {
    header("Location: details.php?book_id={$book_id}");
    exit();
  } catch (PDOException $e) {
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>ByteReads</title>
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

  <!-- Books start -->
  <div class="container-xxl py-5" id="feature_box">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
          <div class="position-relative overflow-hidden ps-0 ps-lg-5 pt-5 h-100" style="min-height: 400px">
            <img class="position-absolute w-100 h-100" src="<?php echo str_replace('../', '', $bookDetails['image_path']); ?>" alt="<?php echo htmlspecialchars($bookDetails['title']); ?>" style="object-fit: cover" />
          </div>
        </div>
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
          <div class="border-start border-5 border-primary ps-4 mb-5">
            <h6 class="text-body text-uppercase mb-2"><?php echo htmlspecialchars($bookDetails['name']); ?></h6>
            <h1 class="display-6 mb-0"><?php echo htmlspecialchars($bookDetails['title']); ?></h1>
          </div>
          <p class="mb-5">
            <?php echo htmlspecialchars($bookDetails['summary']); ?>
          </p>

          <div class="row gx-4">
            <div class="col-sm-8 wow fadeIn" data-wow-delay="0.1s">
              <div class="d-flex align-items-center mb-3">
                <i class="fa fa-check text-primary flex-shrink-0 me-3"></i>
                <h5 class="mb-0">Author: <?php echo htmlspecialchars($bookDetails['author']); ?></h5>
              </div>

              <div class="d-flex align-items-center mb-3">
                <i class="fa fa-check text-primary flex-shrink-0 me-3"></i>
                <h5 class="mb-0">Publisher Date: <?php echo htmlspecialchars($bookDetails['publisher_date']); ?></h5>
              </div>

              <div class="d-flex align-items-center mb-3">
                <i class="fa fa-check text-primary flex-shrink-0 me-3"></i>
                <h5 class="mb-0">Language: <?php echo htmlspecialchars($bookDetails['language']); ?></h5>
              </div>

              <div class="d-flex align-items-center mb-3">
                <i class="fa fa-check text-primary flex-shrink-0 me-3"></i>
                <h5 class="mb-0">No of Pages: <?php echo htmlspecialchars($bookDetails['no_of_Pages']); ?></h5>
              </div>

              <div class="d-flex align-items-center mb-3">
                <i class="fa fa-check text-primary flex-shrink-0 me-3"></i>
                <h5 class="mb-0">
                  Price:
                  <span style="text-decoration: line-through; color: red"><?php echo htmlspecialchars($bookDetails['price']); ?>dt</span>
                  <span style="color: green"><?php echo htmlspecialchars($bookDetails['promo']); ?>dt</span>
                </h5>
              </div>

              <div class="d-flex align-items-center mb-3">
                <!-- Add to Cart Button -->
                <button class="btn btn-primary mb-2" onclick="addToCart(<?php echo $book['book_id']; ?>)">
                  Add to cart
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Books End -->


  <!-- review Start -->
  <div class="container-xxl py-6">
    <div class="container">
      <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px">
        <h1 class="display-6 mb-5 text-border">Write a Review</h1>
      </div>

      <div class="d-flex justify-content-center">
        <div class="add_review_form_div">
          <form method="POST" action="">
            <div class="mb-3">
              <label for="rating" class="form-label">Select a rating</label>
              <select class="form-select" id="rating" name="rating" required>
                <option value="">Select</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="headline" class="form-label">Write a headline for your review</label>
              <input type="text" class="form-control" id="headline" name="review_headline" required />
            </div>
            <div class="mb-3">
              <label for="review" class="form-label">Write your review</label>
              <textarea class="form-control" id="review" name="review_text" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
              Submit Review
            </button>
          </form>
        </div>
      </div>
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