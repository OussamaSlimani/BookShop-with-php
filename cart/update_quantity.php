<?php
session_start();

if (isset($_POST['book_id']) && isset($_POST['change'])) {
     $bookId = $_POST['book_id'];
     $change = $_POST['change'];

     // Ensure the quantity is at least 1
     $_SESSION['cart'][$bookId] = max(1, $_SESSION['cart'][$bookId] + $change);

     // Calculate the total items in the cart
     $cartCount = array_sum($_SESSION['cart']);

     echo $cartCount;
}
