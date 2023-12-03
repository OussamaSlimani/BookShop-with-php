<?php
session_start();

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

function getBookDetails($bookId)
{
  // Include your database connection logic here
  global $pdo;

  // Implement the query to fetch book details based on book_id
  $query = "SELECT * FROM books WHERE book_id = :book_id";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
  $stmt->execute();

  // Fetch the book details as an associative array
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to calculate the total price for a specific item
function calculateItemTotal($bookDetails, $quantity)
{
  return $bookDetails['price'] * $quantity;
}

// Check if the cart is empty
$cartIsEmpty = empty($_SESSION['cart']);
$cartItems = $cartIsEmpty ? [] : $_SESSION['cart'];


// total price variable
$totalPrice = 0;
foreach ($cartItems as $bookId => $quantity) {
  $bookDetails = getBookDetails($bookId);
  $itemTotal = calculateItemTotal($bookDetails, $quantity);
  $totalPrice += $itemTotal;
}

?>

<!-- Add JavaScript to handle quantity update and item removal -->
<script>
  function updateQuantity(bookId, change) {
    // Send AJAX request to update the quantity in the session
    $.ajax({
      type: "POST",
      url: "update_quantity.php",
      data: {
        book_id: bookId,
        change: change
      },
      success: function(response) {
        // Reload the page or update the specific elements as needed
        location.reload();
      }
    });
  }

  function removeItem(bookId) {
    // Send AJAX request to remove the item from the session
    $.ajax({
      type: "POST",
      url: "remove_item.php",
      data: {
        book_id: bookId
      },
      success: function(response) {
        // Reload the page or update the specific elements as needed
        location.reload();
      }
    });
  }
</script>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Shopping cart</title>
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

  <!--  -->
  <div class="container-fluid fixed-top px-0 wow fadeIn bg-light" data-wow-delay="0.1s">
    <nav class="navbar navbar-expand-lg navbar-dark py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
      <a href="index.html" class="navbar-brand ms-lg-0">
        <h1 class="fw-bold text-primary m-0">ByteReads</h1>
      </a>
      <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto p-4 p-lg-0">
          <li class="nav-item">
            <a href="index.html" class="nav-link active">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Category</a>
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
              <input type="text" name="username" class="form-control" id="yourUsername" required />
              <span class="input-group-text" id="inputGroupPrepend">
                <i class="fa fa-search"></i></span>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
  <!-- ====================== Navbar End ===================== -->

  <!-- Shopping Card Start -->
  <section class="container-fluid p-0 mb-5 mt-5 pt-4">
    <div class="container py-5">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col">
          <div class="card p-3 mb-3" style="border-radius: 10px">
            <div class="card-body p-4">
              <hr />

              <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                  <p class="mb-1 fw-bold">Shopping cart</p>
                </div>
              </div>

              <?php if ($cartIsEmpty) : ?>
                <p>Your shopping cart is empty.</p>
              <?php else : ?>
                <!-- Loop through cart items and display them -->
                <?php foreach ($cartItems as $bookId => $quantity) : ?>
                  <?php
                  // Fetch book details based on book_id
                  $bookDetails = getBookDetails($bookId);
                  ?>
                  <div class="card mb-3">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <div class="d-flex flex-row align-items-center">
                          <div>
                            <img src="<?php echo $bookDetails['image_path']; ?>" class="img-fluid rounded-3" alt="Shopping item" style="width: 70px" />
                          </div>
                          <div class="ms-3">
                            <h4><?php echo $bookDetails['title']; ?></h4>
                            <!-- Display additional book details as needed -->
                            <p>Author: <?php echo $bookDetails['author']; ?></p>
                            <p>Price: $<?php echo $bookDetails['price']; ?></p>
                          </div>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                          <div class="d-flex flex-row">
                            <!-- Add quantity handling -->
                            <button class="btn btn-link px-2" onclick="updateQuantity(<?php echo $bookId; ?>, -1)">-</button>
                            <input id="quantity_<?php echo $bookId; ?>" min="1" name="quantity" value="<?php echo $quantity; ?>" type="number" class="form-control form-control-sm" style="width: 50px" />
                            <button class="btn btn-link px-2" onclick="updateQuantity(<?php echo $bookId; ?>, 1)">+</button>
                          </div>
                          <div>
                            <h4 class="mb-0 px-4">$<?php echo calculateItemTotal($bookDetails, $quantity); ?></h4>
                          </div>
                          <!-- Add remove item functionality -->
                          <a href="#" onclick="removeItem(<?php echo $bookId; ?>)"><i class="fas fa-trash-alt"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
                <!-- End loop -->
              <?php endif; ?>

            </div>

            <hr class="my-4" />

            <div class="d-flex justify-content-between mb-4">
              <h4 class="mb-2">Total</h4>
              <h4 class="mb-2">$<?php echo number_format($totalPrice, 2); ?></h4>

              <!-- Checkout form -->
              <form method="POST" action="process_checkout.php">
                <input type="hidden" name="totalPrice" value="<?php echo $totalPrice; ?>">
                <button type="submit" class="btn btn-primary">
                  <div class="d-flex justify-content-between">
                    <span>Checkout <i class="fas fa-long-arrow-alt-right ms-2"></i></span>
                  </div>
                </button>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Shopping Card End -->


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
  <script src="../lib/wow/wow.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="../lib/parallax/parallax.min.js"></script>

  <!-- Template Javascript -->
  <script src="../js/main.js"></script>
</body>

</html>