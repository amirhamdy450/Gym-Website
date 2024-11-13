<?php
session_start(); // Start the PHP Session


$action=$_POST['action'];
$removeclass=0;

// Create cart in session if it doesn't exist
if (!isset($_SESSION["cart"])) {
  $_SESSION["cart"] = [];
}


 if ($action=="init"){
  if(isset($_SESSION["totalQuantity"])){
  
  
  $totalQuantity = $_SESSION["totalQuantity"];          /* intiliazing total quantity */

  }else {
    $totalQuantity=null;
  }


$responseArray=array(

  "totalQuantity"=>(int)$totalQuantity,


);

echo json_encode($responseArray);
exit;
 
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
  
  $_SESSION['totalQuantity']++;



}  

if($action=="remove"){
  $itemId=$_POST['itemId'];

      // Check if the item exists in the cart
  if (array_key_exists($itemId, $_SESSION["cart"])) {
       // Only decrease quantity if it's greater than 0
        if($_SESSION['cart'][$itemId]['quantity'] > 0) {
          $_SESSION['cart'][$itemId]['quantity']--;
          $_SESSION['totalQuantity']--;

        
        }

        if($_SESSION['cart'][$itemId]['quantity'] <= 0) {
          unset($_SESSION["cart"][$itemId]);
          $removeclass=1;
      
      }
            

  }

}







$totalQuantity = $_SESSION["totalQuantity"];          /* intiliazing total quantity */


if (isset($_POST['itemId'])) {
  $itemId = $_POST['itemId'];
} else {
  $itemId = null;
}



$responseArray=array(

  "totalQuantity"=>(int)$totalQuantity,
  "current_qty"=>(int) $_SESSION['cart'][$itemId]['quantity']  ,
  "flag"=>(int)$removeclass,

);

echo json_encode($responseArray);      /* echo is the official way to return the request back to ajax now with the totalquantity variable */


exit;


/* echo 'Product added to cart!';
 */?>