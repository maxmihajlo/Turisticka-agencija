<?php
session_start();
include_once('konekcija.php');

if(isset($_GET['obrisi'])){
    $id = $_GET['obrisi'];
    mysqli_query($db, "DELETE FROM klijent WHERE ID_klijent=".$id);
    header("Location: modul_klijenti.php");
}

if(isset($_POST['unos'])){
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $email = $_POST['email'];

    if($ime!="" && $prezime!="" && $email!=""){
        $ime = mysqli_real_escape_string($db,$ime);
        $prezime = mysqli_real_escape_string($db,$prezime);
        $email = mysqli_real_escape_string($db,$email);

        mysqli_query($db, "INSERT INTO klijent (ime, prezime, email) VALUES ('$ime','$prezime','$email')");
        header("Location: modul_klijenti.php");
    }
}

$edit = 0;
$ime_e = "";
$prezime_e = "";
$email_e = "";

if(isset($_GET['izmeni'])){
    $edit = $_GET['izmeni'];
    $rez = mysqli_query($db, "SELECT ime, prezime, email FROM klijent WHERE ID_klijent=".$edit);

    if($rez && mysqli_num_rows($rez)==1){
        $k = mysqli_fetch_array($rez);
        $ime_e = $k['ime'];
        $prezime_e = $k['prezime'];
        $email_e = $k['email'];
    }else{
        $edit = 0;
    }
}

if(isset($_POST['izmena'])){
    $id = $_POST['id'];
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $email = $_POST['email'];

    if($id!="" && $ime!="" && $prezime!="" && $email!=""){
        $ime = mysqli_real_escape_string($db,$ime);
        $prezime = mysqli_real_escape_string($db,$prezime);
        $email = mysqli_real_escape_string($db,$email);

        mysqli_query($db, "UPDATE klijent SET ime='$ime', prezime='$prezime', email='$email' WHERE ID_klijent=".$id);
        header("Location: modul_klijenti.php");
    }
}

$lista = mysqli_query($db, "SELECT ID_klijent, ime, prezime, email FROM klijent ORDER BY ID_klijent DESC");
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Modul - Klijenti</title>
<style>
body{font-family:Arial;margin:0;background:#e6eef5;}
header{background:#1f3a5f;color:#fff;padding:20px;text-align:center;}
.container{max-width:900px;margin:25px auto;padding:0 10px;}
.card{background:#fff;padding:18px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.15);margin-bottom:18px;}
h2{margin:0 0 10px;color:#1f3a5f;}
label{font-weight:bold;display:block;margin-top:8px;}
input{width:100%;padding:10px;margin-top:6px;box-sizing:border-box;}
button{margin-top:12px;padding:10px 14px;background:#1f7a8c;color:#fff;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#145a66;}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,.15);border-radius:8px;overflow:hidden;}
th{background:#254a73;color:#fff;}
th,td{padding:10px;border-bottom:1px solid #ddd;}
a{font-weight:bold;text-decoration:none;}
.edit{color:#1f7a8c;margin-right:10px;}
.delete{color:#c0392b;}
.back{display:inline-block;margin-bottom:10px;font-weight:bold;color:#1f3a5f;text-decoration:none;}
</style>
</head>
<body>

<header>
  <h1>Turistička agencija</h1>
  <p>Modul: Klijenti</p>
</header>

<div class="container">
  <a class="back" href="pocetna.php">← Nazad na meni</a>

  <div class="card">
    <?php if($edit==0){ ?>
      <h2>Unos klijenta</h2>
      <form method="post">
        <label>Ime</label>
        <input type="text" name="ime" required>

        <label>Prezime</label>
        <input type="text" name="prezime" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <button type="submit" name="unos">Sačuvaj</button>
      </form>
    <?php } else { ?>
      <h2>Izmena klijenta</h2>
      <form method="post">
        <input type="hidden" name="id" value="<?php echo $edit; ?>">

        <label>Ime</label>
        <input type="text" name="ime" value="<?php echo $ime_e; ?>" required>

        <label>Prezime</label>
        <input type="text" name="prezime" value="<?php echo $prezime_e; ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $email_e; ?>" required>

        <button type="submit" name="izmena">Sačuvaj izmenu</button>
      </form>
    <?php } ?>
  </div>

  <h2>Lista klijenata</h2>
  <table>
    <tr>
      <th>Ime</th>
      <th>Prezime</th>
      <th>Email</th>
      <th>Akcije</th>
    </tr>

    <?php
    if($lista && mysqli_num_rows($lista)>0){
        while($r=mysqli_fetch_array($lista)){
            echo "<tr>
                    <td>".$r['ime']."</td>
                    <td>".$r['prezime']."</td>
                    <td>".$r['email']."</td>
                    <td>
                      <a class='edit' href='modul_klijenti.php?izmeni=".$r['ID_klijent']."'>Izmeni</a>
                      <a class='delete' href='modul_klijenti.php?obrisi=".$r['ID_klijent']."' onclick=\"return confirm('Da li ste sigurni?');\">Obriši</a>
                    </td>
                  </tr>";
        }
    }else{
        echo "<tr><td colspan='4'>Nema klijenata.</td></tr>";
    }
    ?>
  </table>
</div>

</body>
</html>
