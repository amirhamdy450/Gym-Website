<?php
session_start(); // Start the PHP Session


$action=$_POST['action'];
$removeclass=false;

// Create cart in session if it doesn't exist
if (!isset($_SESSION["cart"])) {
  $_SESSION["cart"] = [];
}


if($action=="add"){
  $itemId = $_POST['itemId'];
  $price = $_POST['price'];
  $quantity = $_POST['quantity'];

  // Check if the item is in the cart and increase the quantity in that case
  if (array_key_exists($itemId, $_SESSION["cart"])) {
      $_SESSION['cart'][$itemId]['quantity']++;

  } else {
      // Structure the product data
      $item = ["id" => $itemId, "price" => $price,  "quantity" => $quantity ];        /* this is a keyed array */
      // Item is not in the cart, so add item to cart
      $_SESSION["cart"][$itemId] = $item;     /* now we will go the cart and this keyed array to the itemID unlike the default static array that is indexed according to the size , we are now making a custom index with product id */
  }


}  

else if($action=="remove"){
  $itemId=$_POST['itemId'];



    // Check if the item is in the cart and increase the quantity in that case
    if (array_key_exists($itemId, $_SESSION["cart"])) {
      $_SESSION['cart'][$itemId]['quantity']--;

        if($_SESSION['cart'][$itemId]['quantity'] <= 0) {    /* now if this reduction made the quantity of this product in cart zero then we will delete the array associated with this product id in our session array */
          unset($_SESSION["cart"][$itemId]);
          $removeclass=true;
        }

  }





}







$totalQuantity = 0;          /* intiliazing total quantity */
foreach ($_SESSION['cart'] as $item) {        /* looping through the cart array in session */
    $totalQuantity += $item['quantity'];     /* addding all quanitites available in the array to get the total quantity */
}


$itemId=$_POST['itemId'];

$responseArray=array(

  "totalQuantity"=>(int)$totalQuantity,
  "current_qty"=>(int) $_SESSION['cart'][$itemId]['quantity']  ,
  "flag"=>(int)$removeclass,

);

echo json_encode($responseArray);      /* echo is the official way to return the request back to ajax now with the totalquantity variable */





/* echo 'Product added to cart!';
 */?>