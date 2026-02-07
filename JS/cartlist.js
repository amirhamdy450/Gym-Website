 /* DOMContentLoaded inusres that all html including the cart image before execution */
document.addEventListener('DOMContentLoaded', function() {   /* when we declare the .js in the html header we are just assuming that the html is loaded  but some elments may take thier time load like external images */
    const open = document.getElementById('cart_btn');
    const cartlist = document.getElementById('cartlist');
    
    
      open.addEventListener('click', openList);
  
      function openList() {                    /* the purpose of this function is to show or hide cartlist sidebar when the cart is clicked*/
        cartlist.classList.toggle('open'); /* toggle adds the open class if it is not declared and removes it if it exists */

      }  /* when open class is not declared by default i made the sidebar class left 110% on the x-axis 2d-plane which hides it completly */
/* the open class resets the transform over the x-axis and therefore makes the sidebar visible and we could havve */
/* i could have used also display attribute to achive hiding and showing elements but it will not have a transform effect when the sidebar is shown*/ 
});