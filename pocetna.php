<?php
session_start();
include_once("konekcija.php");

$id = $_SESSION['id_zaposleni'];

$rez = mysqli_query($db, "SELECT ime, prezime FROM zaposleni WHERE ID_zaposleni=".$id);
$z = mysqli_fetch_array($rez);

$ime = $z['ime'];
$prezime = $z['prezime'];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Početna - Turistička agencija</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e6eef5;
            color: #333;
        }
        header {
            background-color: #1f3a5f;
            color: #fff;
            padding: 22px 0;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            background-color: #254a73;
            padding: 12px 0;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 6px 14px;
            font-size: 18px;
            padding: 6px 10px;
            border-radius: 5px;
            transition: 0.2s;
        }
        nav a:hover {
            background-color: #1f7a8c;
        }
        .box {
            background: white;
            padding: 22px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
            width: 360px;
            margin: 50px auto 0 auto;
            text-align: center;
        }
        .box h2 {
            margin: 0 0 10px 0;
            color: #1f3a5f;
        }
        .logout {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 18px;
            font-size: 16px;
            color: #fff;
            background-color: #c0392b;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .logout:hover {
            background-color: #a93226;
        }
    </style>
</head>

<body>

<header>
    <h1>Turistička agencija</h1>
    <p>Interni sistem</p>
</header>

<nav>
    <a href="modul_klijenti.php">Klijenti</a>
    <a href="modul_rezervacije.php">Rezervacije</a>
    <a href="modul_aranzmani.php">Aranžmani</a>
    <a href="modul_partneri.php">Partneri</a>
    <a href="modul_finansije.php">Finansije</a>
    <a href="modul_racun.php">Račun</a>
</nav>

<div class="box">
    <h2>Dobrodošli, <?php echo $ime." ".$prezime; ?></h2>
    <p>Izaberite modul iz menija iznad.</p>

    <a class="logout" href="logout.php">Odjavi se</a>
</div>

</body>
</html>
