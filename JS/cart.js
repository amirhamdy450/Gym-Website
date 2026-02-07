

function initializeCounter(){

   var count=parseInt(sessionStorage.getItem("qty"));
       // If qty is not present in session storage or it's NaN, retrieve it from the server-side PHP session variable
       if (isNaN(count)) {
         $.ajax({
             url: './Cart_Session.php', // Replace with the appropriate URL to fetch the quantity from PHP session
             method: 'POST',
             dataType: 'json',
             data: {'action': 'init'},
             success: function(response) {
                 count = response.totalQuantity; // Assuming the totalQuantity is the quantity you want to retrieve
                 sessionStorage.setItem("qty", count);
                 updateCartCounter(count);
             },
             error: function(jqXHR, textStatus, errorThrown) {
                 console.error('Failed to fetch quantity from server:', errorThrown);
             }
         });
     } else {
         updateCartCounter(count);
     }


    updateCartCounter(count);
   

   }
   
   window.onload = initializeCounter;
   

 function updateCartCounter(count){
   if(count>0){
   
   const cart = document.getElementById("cart");
   let qtySpan = cart.querySelector(".qty");
   
   if (!qtySpan) {
       qtySpan = document.createElement("span");
       qtySpan.className = "qty";
       cart.appendChild(qtySpan);
   }

   qtySpan.innerHTML = count;
   }
}






   function AddToCart(id,price){
      const quantity = 1;
      
      $.ajax({                                /* we will use Ajax to pass values from JS to PHP session storage */
         url: './Cart_Session.php',            /* js sessionstorage is not shared among php so we cannot use it as a shared place but we can pass values using Ajax with a Post request 
         and from that post request we will retrieve values and set it in session*/
         method: 'POST',                                 /* define the request header */
         datatype:"json",
         /* we get the data from the parameters above and put them in this keyed array  and then pass them in post request to the above php page*/
         data: {"action":"add","itemId": id, "price": price, "quantity": quantity},    /* action is the only variable that we set and don't retrieve from the parameters */  
         success: function(response) {
            try {
               var JSONresponse = JSON.parse(response);
               // Handle jsonResponse as an object
           } catch (e) {
               console.error('Error parsing JSON:', e);
           }
            
            sessionStorage.setItem("qty",JSONresponse['totalQuantity']);  /* set the js local storage to the totalquantity we got from php by checking array session */
            const cart=document.getElementById("cart");
            // Check if the qty span already exists
            let qtySpan = cart.querySelector(".qty");
   
            if (!qtySpan) {
               // If it doesn't exists, create it
               qtySpan = document.createElement("span");
               qtySpan.className = "qty";
               cart.appendChild(qtySpan);
             }
   
            qtySpan.innerHTML=parseInt(sessionStorage.getItem("qty")) ;
            
            /* change the quantity inside the html sidebar */
            const qty=document.getElementById("qty-" + id);
            qty.innerText=JSONresponse['current_qty'];
            initializeCounter(); /* update the counter */
   
            alert('The item was added to your cart!');
         },
         error: function(jqXHR, textStatus, errorThrown) {
            alert('Failed !');
         }
     });
     
   
   
   
   
   }
   
   
   
   
   
  
   function RemoveFromCart(id){
      
      
      $.ajax({                                /* we will use Ajax to pass values from JS to PHP session storage */
         url: './Cart_Session.php',            /* js sessionstorage is not shared among php so we cannot use it as a shared place but we can pass values using Ajax with a Post request 
         and from that post request we will retrieve values and set it in session*/
         method: 'POST',                                 /* define the request header */
         /* we get the data from the parameters above and we only need an id since we are sure that the item exists*/
         data: {"action":"remove","itemId": id},    /* action is the only variable that we set and don't retrieve from the parameters */  
         success: function(response) {
            try {
               var JSONresponse = JSON.parse(response);
               // Handle jsonResponse as an object
           } catch (e) {
               console.error('Error parsing JSON:', e);
           }
   
            if(JSONresponse["flag"]==1){
               console.log("flagged");
               const product=document.getElementById("item-" + id);
               product.parentNode.removeChild(product);
   
            }
   
   
   
   
   
            
            sessionStorage.setItem("qty",JSONresponse["totalQuantity"]);  /* set the js session storage to the totalquantity we got from php by checking array session */
            const cart=document.getElementById("cart");
            // Check if the qty span already exists
            let qtySpan = cart.querySelector(".qty");
   
            if (!qtySpan) {
               // If it doesn't exists, create it
               qtySpan = document.createElement("span");
               qtySpan.className = "qty";
               cart.appendChild(qtySpan);
             }
   
            qtySpan.innerHTML=parseInt(sessionStorage.getItem("qty")) ;
               if(JSONresponse["flag"]!=0){
              /* change the quantity inside the html sidebar */
               const qty=document.getElementById("qty-" + id);
               qty.innerText=JSONresponse['current_qty'];
               }
   
   
               initializeCounter(); /* update the counter */

            alert('The item was removed from your cart!');
         },
         error: function(jqXHR, textStatus, errorThrown) {
            alert('Failed !');
         }
     });
     
   
   
   
   
   }