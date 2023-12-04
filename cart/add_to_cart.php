<?php
session_start();

if (isset($_POST['book_id'])) {
     $bookId = $_POST['book_id'];

     // Add the item to the cart (you may need to fetch details from the database)
     $_SESSION['cart'][$bookId] = isset($_SESSION['cart'][$bookId]) ? $_SESSION['cart'][$bookId] + 1 : 1;

     // Calculate the total items in the cart
     $cartCount = array_sum($_SESSION['cart']);

     // Return the updated cart count
     echo $cartCount;
}
