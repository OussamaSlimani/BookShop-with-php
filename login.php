<?php
session_start();
$error_message = "";

if (isset($_COOKIE['remember_email']) && $_COOKIE['remember_password']) {
  $rememberedEmail = $_COOKIE['remember_email'];
  $rememberedPassword = $_COOKIE['remember_password'];
} else {
  $rememberedEmail = "";
  $rememberedPassword = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $remember = isset($_POST['remember']) && ($_POST['remember'] == 1) ? true : false;

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_message = "Invalid email address";
  } else {
    $DB_NAME = 'bookshop';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_HOST = 'localhost';

    try {
      $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {

      if ($remember == true) {
        setcookie('remember_email', $row['email'], time() + 30 * 24 * 60 * 60);
        setcookie('remember_password', $password, time() + 30 * 24 * 60 * 60);
      } else {
        setcookie('remember_email', '', time() - 3600, '/');
        setcookie('remember_password', '', time() - 3600, '/');
      }

      if ($row['is_admin'] == 1) {
        header("Location: book/books.php");
        exit();
      } else {
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        exit();
      }
    } else {
      $error_message = "Invalid login credentials";
    }
  }
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
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">
                      Login to Your Account
                    </h5>
                    <p class="text-center small">
                      Enter your email & password to login
                    </p>
                  </div>

                  <form class="row g-3 needs-validation" method="post">
                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Your Email</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="email" class="form-control" id="yourEmail" required value="<?php echo $rememberedEmail; ?>" />
                        <div class="invalid-feedback">
                          Please enter a valid email.
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required value="<?php echo $rememberedPassword; ?>" />
                      <div class="invalid-feedback">
                        Please enter your password.
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" />
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">
                        Login
                      </button>
                    </div>
                    <div class="col-12">
                      <?php if (!empty($error_message)) : ?>
                        <div class="alert alert-danger" role="alert">
                          <?php echo htmlspecialchars($error_message); ?>
                        </div>
                      <?php endif; ?>
                      <p class="small mb-0">
                        Don't have an account?
                        <a href="register.php">Create an account</a>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>

</html>