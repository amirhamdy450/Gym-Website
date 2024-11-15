<?php
include ('img_handler.php');

// Connect to the database
$con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( ");
$db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database :(");
$id = $_GET['id'];

// Retrieve the current values of the record from the database
$sql = "SELECT * FROM instructor WHERE Ins_ID=$id";
$result = mysqli_query($con, $sql);
$instructor = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
  switch ($_POST['action']) {
    
    case 'add':
      $name = $_POST['name'];
      $city = $_POST['city'];
      $nationality= $_POST['nationality'];
      $email = $_POST['email'];
      $bday = $_POST['birthdate'];
      $gender = $_POST['gender'];
      $salary = $_POST['salary'];
      $sql = "INSERT INTO instructor(Ins_Name,City,Email,BirthDate,Gender,Salary)  VALUES ('$name', '$city', '$email', '$bday', '$gender', '$salary');";
      $result = mysqli_query($con, $sql);
      $recent_id=mysqli_insert_id($con);    /* this functions helps us retrieve the id that was just created by the lates because it is auto incremented in the database and thhere is no way knowing it without it */
      img_handler($recent_id);

      if ($result) {
        echo "Instructor added successfully!";
      } else {
        echo "Error adding instructor: " . mysqli_error($con);
      }
      
      break;

    case 'edit':
      // Retrieve the new values from the form
      $name = $_POST['name'];
      $email = $_POST['email'];
      $city = $_POST['city'];
      $nationality= $_POST['nationality'];
      $bday = $_POST['bday'];
      $gender = $_POST['gender'];
      $salary = $_POST['salary'];

      // Update the record in the database
      $sql = "UPDATE instructor SET Ins_Name='$name', Email='$email', City ='$city',Nationality='$nationality' , BirthDate='$bday' , Gender='$gender',Salary='$salary' WHERE Ins_ID=$id";
      $result = mysqli_query($con, $sql);
      img_handler();
      if ($result) {
        echo "Supplement added successfully!";
      } else {
        echo "Error adding supplement: " . mysqli_error($con);
      }

      // Redirect back to the instructors page
      header("Location: Admin.php");
      exit(); }
    }

 if ($_GET['action']=="delete"){
      // Display an alert message to confirm the deletion
      
        $sql = "DELETE FROM instructor WHERE Ins_ID=$id";
        $result = mysqli_query($con, $sql);
        
        if ($result){
        // Redirect back to the instructors page
        header('Location: Admin.php');}
        else{
          echo '<script>alert("Error deleting record.' . mysqli_error($con) . '");</script>';

        }
        exit();
       
      
  }


mysqli_close($con);


?>
<!DOCTYPE html>
<html>
<head>
  <title>Instructor</title>
  <link rel="stylesheet" href="Panel.css">
  
</head>
<body>
  <h1>Admin Dashboard</h1>
  <?php if ($_GET['action'] == 'edit') { ?>

    <div class="container">
      <div class="window">
        <h2>Edit Instructor</h2>
        <form method="post" enctype="multipart/form-data"> 
          <input type="hidden" name="id" value="<?php echo $instructor['Ins_ID']; ?>">
          <input type="hidden" name="action" value="edit">
          <label for="name">Name:</label>
          <input type="text" name="name" value="<?php echo $instructor['Ins_Name']; ?>">
          <label for="email">Email:</label>
          <input type="email" name="email" value="<?php echo $instructor['Email']; ?>">
          <label for="city">City:</label>
          <input type="text" name="city" value="<?php echo $instructor['City']; ?>">
          <label for="nationality">Nationality:</label>

        
          <select name="nationality">

          <?php 
          $current_nation=$instructor['Nationality'];
          $nations = array('Afghan','Albanian','Algerian','American','Andorran','Angolan','Antiguans','Argentinean','Armenian','Australian','Austrian','Azerbaijani','Bahamian','Bahraini','Bangladeshi','Barbadian','Barbudans','Batswana','Belarusian','Belgian','Belizean','Beninese','Bhutanese','Bolivian','Bosnian','Brazilian','British','Bruneian','Bulgarian','Burkinabe','Burmese','Burundian','Cambodian','Cameroonian','Canadian','Cape Verdean','Central African','Chadian','Chilean','Chinese','Colombian','Comoran','Congolese','Costa Rican','Croatian','Cuban','Cypriot','Czech','Danish','Djibouti','Dominican','Dutch','East Timorese','Ecuadorean','Egyptian','Emirian','Equatorial Guinean','Eritrean','Estonian','Ethiopian','Fijian','Filipino','Finnish','French','Gabonese','Gambian','Georgian','German','Ghanaian','Greek','Grenadian','Guatemalan','Guinea-Bissauan','Guinean','Guyanese','Haitian','Herzegovinian','Honduran','Hungarian','I-Kiribati','Icelander','Indian','Indonesian','Iranian','Iraqi','Irish','Israeli','Italian','Ivorian','Jamaican','Japanese','Jordanian','Kazakhstani','Kenyan','Kittian and Nevisian','Kuwaiti','Kyrgyz','Laotian','Latvian','Lebanese','Liberian','Libyan','Liechtensteiner','Lithuanian','Luxembourger','Macedonian','Malagasy','Malawian','Malaysian','Maldivan','Malian','Maltese','Marshallese','Mauritanian','Mauritian','Mexican','Micronesian','Moldovan','Monacan','Mongolian','Moroccan','Mosotho','Motswana','Mozambican','Namibian','Nauruan','Nepali','New Zealander','Nicaraguan','Nigerian','Nigerien','North Korean','Northern Irish','Norwegian','Omani','Pakistani','Palauan','Panamanian','Papua New Guinean','Paraguayan','Peruvian','Polish','Portuguese','Qatari','Romanian','Russian','Rwandan','Saint Lucian','Salvadoran','Samoan','San Marinese','Sao Tomean','Saudi','Scottish','Senegalese','Serbian','Seychellois','Sierra Leonean','Singaporean','Slovakian','Slovenian','Solomon Islander','Somali','South African','South Korean','Spanish','Sri Lankan','Sudanese','Surinamer','Swazi','Swedish','Swiss','Syrian','Taiwanese','Tajik','Tanzanian','Thai','Togolese','Tongan','Trinidadian/Tobagonian','Tunisian','Turkish','Tuvaluan','Ugandan','Ukrainian','Uruguayan','Uzbekistani','Venezuelan','Vietnamese','Welsh','Yemenite','Zambian','Zimbabwean');
          foreach($nations as $nation){
            if($nation == $current_nation){
                echo '<option selected="selected">' . $nation . '</option>';
            }else{
                echo '<option>' . $nation . '</option>';
            }
        }
        ?>
          </select>
          


          <label for="bday">Birth Date:</label>
          <input type="date" name="bday" value="<?php echo $instructor['BirthDate']; ?>">
          <label for="gender">Gender:</label>

          <select name="gender" required>
          <option <?php if($instructor["Gender"]=="M") { ?>  selected="selected"   <?php } ?> value="M">Male</option>
          <option <?php if($instructor["Gender"]=="F") { ?>  selected="selected"   <?php } ?>  value="F">Female</option>
        </select>
          <label for="Salary">Salary:</label>
          <input type="number" name="salary" value="<?php echo $instructor['Salary']; ?>">
          <label for="profile-picture-img">Instructor Image:</label>

          <?php if (!isset($instructor['Ins_img'])){ ?>
            <div class="unset"><h1>Not Set !</h1>
            <?php } else{ ?>

             <div class="unset">    <?php      echo '<img src="data:image/jpeg;base64,' . $instructor["Ins_img"] . '" id="profile-picture-img" alt="Profile picture" >'; ?>


              <?php } ?> 
              <input type="file"  id="choose" name="file_img"  accept=".jpg, .jpeg, .png, .gif" >   </div>




          <button type="submit" name="submit">Save Changes</button>
        </form>
      </div>
    </div>
    <?php } ?>

    <?php if ($_GET['action'] == 'add') { ?>
      <div class="container">
    <div class="window">
      <h2>Add Instructor</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="city">City:</label>
        <input type="text" name="city" required>

        <label for="city">Nationality:</label>
        <select name="nationality"  id="select_nation">
        <option value="">-- select one --</option>
        <option value="afghan">Afghan</option>
        <option value="albanian">Albanian</option>
        <option value="algerian">Algerian</option>
        <option value="american">American</option>
        <option value="andorran">Andorran</option>
        <option value="angolan">Angolan</option>
        <option value="antiguans">Antiguans</option>
        <option value="argentinean">Argentinean</option>
        <option value="armenian">Armenian</option>
        <option value="australian">Australian</option>
        <option value="austrian">Austrian</option>
        <option value="azerbaijani">Azerbaijani</option>
        <option value="bahamian">Bahamian</option>
        <option value="bahraini">Bahraini</option>
        <option value="bangladeshi">Bangladeshi</option>
        <option value="barbadian">Barbadian</option>
        <option value="barbudans">Barbudans</option>
        <option value="batswana">Batswana</option>
        <option value="belarusian">Belarusian</option>
        <option value="belgian">Belgian</option>
        <option value="belizean">Belizean</option>
        <option value="beninese">Beninese</option>
        <option value="bhutanese">Bhutanese</option>
        <option value="bolivian">Bolivian</option>
        <option value="bosnian">Bosnian</option>
        <option value="brazilian">Brazilian</option>
        <option value="british">British</option>
        <option value="bruneian">Bruneian</option>
        <option value="bulgarian">Bulgarian</option>
        <option value="burkinabe">Burkinabe</option>
        <option value="burmese">Burmese</option>
        <option value="burundian">Burundian</option>
        <option value="cambodian">Cambodian</option>
        <option value="cameroonian">Cameroonian</option>
        <option value="canadian">Canadian</option>
        <option value="cape verdean">Cape Verdean</option>
        <option value="central african">Central African</option>
        <option value="chadian">Chadian</option>
        <option value="chilean">Chilean</option>
        <option value="chinese">Chinese</option>
        <option value="colombian">Colombian</option>
        <option value="comoran">Comoran</option>
        <option value="congolese">Congolese</option>
        <option value="costa rican">Costa Rican</option>
        <option value="croatian">Croatian</option>
        <option value="cuban">Cuban</option>
        <option value="cypriot">Cypriot</option>
        <option value="czech">Czech</option>
        <option value="danish">Danish</option>
        <option value="djibouti">Djibouti</option>
        <option value="dominican">Dominican</option>
        <option value="dutch">Dutch</option>
        <option value="east timorese">East Timorese</option>
        <option value="ecuadorean">Ecuadorean</option>
        <option value="egyptian">Egyptian</option>
        <option value="emirian">Emirian</option>
        <option value="equatorial guinean">Equatorial Guinean</option>
        <option value="eritrean">Eritrean</option>
        <option value="estonian">Estonian</option>
        <option value="ethiopian">Ethiopian</option>
        <option value="fijian">Fijian</option>
        <option value="filipino">Filipino</option>
        <option value="finnish">Finnish</option>
        <option value="french">French</option>
        <option value="gabonese">Gabonese</option>
        <option value="gambian">Gambian</option>
        <option value="georgian">Georgian</option>
        <option value="german">German</option>
        <option value="ghanaian">Ghanaian</option>
        <option value="greek">Greek</option>
        <option value="grenadian">Grenadian</option>
        <option value="guatemalan">Guatemalan</option>
        <option value="guinea-bissauan">Guinea-Bissauan</option>
        <option value="guinean">Guinean</option>
        <option value="guyanese">Guyanese</option>
        <option value="haitian">Haitian</option>
        <option value="herzegovinian">Herzegovinian</option>
        <option value="honduran">Honduran</option>
        <option value="hungarian">Hungarian</option>
        <option value="icelander">Icelander</option>
        <option value="indian">Indian</option>
        <option value="indonesian">Indonesian</option>
        <option value="iranian">Iranian</option>
        <option value="iraqi">Iraqi</option>
        <option value="irish">Irish</option>
        <option value="israeli">Israeli</option>
        <option value="italian">Italian</option>
        <option value="ivorian">Ivorian</option>
        <option value="jamaican">Jamaican</option>
        <option value="japanese">Japanese</option>
        <option value="jordanian">Jordanian</option>
        <option value="kazakhstani">Kazakhstani</option>
        <option value="kenyan">Kenyan</option>
        <option value="kittian and nevisian">Kittian and Nevisian</option>
        <option value="kuwaiti">Kuwaiti</option>
        <option value="kyrgyz">Kyrgyz</option>
        <option value="laotian">Laotian</option>
        <option value="latvian">Latvian</option>
        <option value="lebanese">Lebanese</option>
        <option value="liberian">Liberian</option>
        <option value="libyan">Libyan</option>
        <option value="liechtensteiner">Liechtensteiner</option>
        <option value="lithuanian">Lithuanian</option>
        <option value="luxembourger">Luxembourger</option>
        <option value="macedonian">Macedonian</option>
        <option value="malagasy">Malagasy</option>
        <option value="malawian">Malawian</option>
        <option value="malaysian">Malaysian</option>
        <option value="maldivan">Maldivan</option>
        <option value="malian">Malian</option>
        <option value="maltese">Maltese</option>
        <option value="marshallese">Marshallese</option>
        <option value="mauritanian">Mauritanian</option>
        <option value="mauritian">Mauritian</option>
        <option value="mexican">Mexican</option>
        <option value="micronesian">Micronesian</option>
        <option value="moldovan">Moldovan</option>
        <option value="monacan">Monacan</option>
        <option value="mongolian">Mongolian</option>
        <option value="moroccan">Moroccan</option>
        <option value="mosotho">Mosotho</option>
        <option value="motswana">Motswana</option>
        <option value="mozambican">Mozambican</option>
        <option value="namibian">Namibian</option>
        <option value="nauruan">Nauruan</option>
        <option value="nepalese">Nepalese</option>
        <option value="new zealander">New Zealander</option>
        <option value="ni-vanuatu">Ni-Vanuatu</option>
        <option value="nicaraguan">Nicaraguan</option>
        <option value="nigerien">Nigerien</option>
        <option value="north korean">North Korean</option>
        <option value="northern irish">Northern Irish</option>
        <option value="norwegian">Norwegian</option>
        <option value="omani">Omani</option>
        <option value="pakistani">Pakistani</option>
        <option value="palauan">Palauan</option>
        <option value="panamanian">Panamanian</option>
        <option value="papua new guinean">Papua New Guinean</option>
        <option value="paraguayan">Paraguayan</option>
        <option value="peruvian">Peruvian</option>
        <option value="polish">Polish</option>
        <option value="portuguese">Portuguese</option>
        <option value="qatari">Qatari</option>
        <option value="romanian">Romanian</option>
        <option value="russian">Russian</option>
        <option value="rwandan">Rwandan</option>
        <option value="saint lucian">Saint Lucian</option>
        <option value="salvadoran">Salvadoran</option>
        <option value="samoan">Samoan</option>
        <option value="san marinese">San Marinese</option>
        <option value="sao tomean">Sao Tomean</option>
        <option value="saudi">Saudi</option>
        <option value="scottish">Scottish</option>
        <option value="senegalese">Senegalese</option>
        <option value="serbian">Serbian</option>
        <option value="seychellois">Seychellois</option>
        <option value="sierra leonean">Sierra Leonean</option>
        <option value="singaporean">Singaporean</option>
        <option value="slovakian">Slovakian</option>
        <option value="slovenian">Slovenian</option>
        <option value="solomon islander">Solomon Islander</option>
        <option value="somali">Somali</option>
        <option value="south african">South African</option>
        <option value="south korean">South Korean</option>
        <option value="spanish">Spanish</option>
        <option value="sri lankan">Sri Lankan</option>
        <option value="sudanese">Sudanese</option>
        <option value="surinamer">Surinamer</option>
        <option value="swazi">Swazi</option>
        <option value="swedish">Swedish</option>
        <option value="swiss">Swiss</option>
        <option value="syrian">Syrian</option>
        <option value="taiwanese">Taiwanese</option>
        <option value="tajik">Tajik</option>
        <option value="tanzanian">Tanzanian</option>
        <option value="thai">Thai</option>
        <option value="togolese">Togolese</option>
        <option value="tongan">Tongan</option>
        <option value="trinidadian or tobagonian">Trinidadian or Tobagonian</option>
        <option value="tunisian">Tunisian</option>
        <option value="turkish">Turkish</option>
        <option value="tuvaluan">Tuvaluan</option>
        <option value="ugandan">Ugandan</option>
        <option value="ukrainian">Ukrainian</option>
        <option value="uruguayan">Uruguayan</option>
        <option value="uzbekistani">Uzbekistani</option>
        <option value="venezuelan">Venezuelan</option>
        <option value="vietnamese">Vietnamese</option>
        <option value="welsh">Welsh</option>
        <option value="yemenite">Yemenite</option>
        <option value="zambian">Zambian</option>
        <option value="zimbabwean">Zimbabwean</option>
        </select>
         






        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="birthdate">Birthdate:</label>
        <input type="date" name="birthdate" required>
        <label for="gender">Gender:</label>
        <select name="gender" required>
          <option value="M">Male</option>
          <option value="F">Female</option>
        </select>
        <label for="salary">Salary:</label>
        <input type="number" name="salary" required>

        <label for="file_img">Instructor Image:</label>
        <input type="file"  id="choose" name="file_img"  accept=".jpg, .jpeg, .png, .gif" >   </div>
        
        <button type="submit" name="submit">Add Instructor</button>
      </form>
    </div>
  </div>
  <?php } ?>


</body>
</html>
