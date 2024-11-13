
function exit (){
    const parent=document.querySelector(".img_manager");
    parent.parentNode.removeChild(parent);   /* this clears the class and its children classes */
     document.getElementById("script2").style.opacity=1;

    }



function img_manager(){
    var input = document.getElementById('choose');
    var file = input.files[0];
    var profile = document.getElementById("script2");
    var img=document.getElementById("choose");
    if(file){
   

     profile.style.opacity=0.3;                                 /* now that we are in the top css class we will dim everything except for what is outside this class */
                                                                
     
                                                                
                                                                
   const parent=document.getElementById("script");        /* we are going to the script id that is our target location */
   const img_manager=document.createElement("div");       /* creating a div element in a variable */
   img_manager.classList.add("img_manager");             /* giving this div a name through its variable */
   parent.appendChild(img_manager);                     /* making the class that have #scrit id a parent to the img_manager class */





     /* add an image container class */
     const container = document.createElement('div');   /* first creates the div for the class */
     const confirmation =document.createElement('div');
     container.classList.add('container');
     document.querySelector(".img_manager").appendChild(container);
     confirmation.classList.add('confirmation');
     document.querySelector(".img_manager").appendChild(confirmation);



     

     const reader = new FileReader();

     reader.onload=function(e){

      console.log(e.target.result);

     const pointer=document.createElement('img');
     pointer.src = e.target.result;
     document.querySelector(".container").appendChild(pointer); /* the querySelector lets us select any element in the css file , so use it to select class */
     const form = document.createElement('form');
     form.method='post';
     form.action='submit_img.php';
     form.id='img';
     form.enctype = 'multipart/form-data'; 

     const hiddenInput = document.createElement('input');
     hiddenInput.type = 'hidden';
      hiddenInput.name = 'img'; // This should match the name used in $_POST["img"]
     hiddenInput.value = e.target.result;

     const send = document.createElement('button');
     send.type='submit';
     send.textContent='confirm';
     send.id="cf";
     send.name="submit";

     const cancel= document.createElement('button');
     cancel.textContent='cancel';
     cancel.onclick=exit;
     
     document.querySelector(".img_manager").appendChild(form);
     form.appendChild(container);
     form.appendChild(hiddenInput);
     form.appendChild(confirmation);
     document.querySelector(".confirmation ").appendChild(send);
     document.querySelector(".confirmation").appendChild(cancel);
        }

     reader.readAsDataURL(file);



    }


    
}

