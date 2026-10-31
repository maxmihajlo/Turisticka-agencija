<?php
session_start();
include_once("konekcija.php");

if(isset($_POST['email_zaposleni']) && isset($_POST['lozinka_zaposleni'])){

    $email = mysqli_real_escape_string($db, $_POST['email_zaposleni']);
    $lozinka = mysqli_real_escape_string($db, $_POST['lozinka_zaposleni']);

    $rezultat = mysqli_query($db, "SELECT ID_zaposleni
                                   FROM zaposleni
                                   WHERE email='$email' AND lozinka='$lozinka'
                                   LIMIT 1");

    $z = mysqli_fetch_array($rezultat);

    if($z){
        $_SESSION['id_zaposleni'] = $z['ID_zaposleni'];
        header("Location: pocetna.php");  
    }else{
        header("Location: prijava.html");  
    }
}else{
    header("Location: prijava.html");   
}
?>
