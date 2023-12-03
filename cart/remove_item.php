<?php
session_start();

if (isset($_POST['book_id'])) {
     $bookId = $_POST['book_id'];

     // Remove the item from the cart
     unset($_SESSION['cart'][$bookId]);

     // Calculate the total items in the cart
     $cartCount = array_sum($_SESSION['cart']);

     // Return the updated cart count (you can return additional information if needed)
     echo $cartCount;
}
