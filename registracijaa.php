<?php
session_start();
include_once("konekcija.php");

if(!isset($_POST['ime_zaposleni'])){
    header("Location: registracijaa.html");
}

$ime = $_POST['ime_zaposleni'];
$prezime = $_POST['prezime_zaposleni'];
$pol = $_POST['pol_zaposleni'];
$email = $_POST['email_zaposleni'];
$lozinka = $_POST['lozinka_zaposleni'];

$ime = mysqli_real_escape_string($db, $ime);
$prezime = mysqli_real_escape_string($db, $prezime);
$pol = mysqli_real_escape_string($db, $pol);
$email = mysqli_real_escape_string($db, $email);
$lozinka = mysqli_real_escape_string($db, $lozinka);

mysqli_query($db, "INSERT INTO zaposleni (ime, prezime, email, pol, lozinka)
                   VALUES ('$ime', '$prezime', '$email', '$pol', '$lozinka')");

header("Location: prijava.html");
?>
