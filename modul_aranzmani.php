<?php
session_start();
include_once('konekcija.php');

$poruka = "";
//*brisanje aranzmana/
if(isset($_GET['obrisi'])){
    $id = $_GET['obrisi'];

    $provera = mysqli_query($db, "SELECT ID_rezervacija FROM rezervacija WHERE ID_aranzmana=".$id);

    if($provera && mysqli_num_rows($provera)>0){
        header("Location: modul_aranzmani.php?msg=ne_moze");
    }else{
        mysqli_query($db, "DELETE FROM aranzman WHERE ID_aranzmana=".$id);
        header("Location: modul_aranzmani.php?msg=obrisan");
    }
}
//*unos aranzmana/
if(isset($_POST['unos'])){
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $polazak = $_POST['datum_polaska'];
    $povratak = $_POST['datum_povratka'];
    $cena = $_POST['cena_po_osobi'];
    $cena = str_replace(",", ".", $cena);  

    if($naziv!="" && $opis!="" && $polazak!="" && $povratak!="" && $cena!=""){
        $naziv = mysqli_real_escape_string($db,$naziv);
        $opis = mysqli_real_escape_string($db,$opis);
        $polazak = mysqli_real_escape_string($db,$polazak);
        $povratak = mysqli_real_escape_string($db,$povratak);
        $cena = mysqli_real_escape_string($db,$cena);

        mysqli_query($db, "INSERT INTO aranzman (naziv, opis, datum_polaska, datum_povratka, cena_po_osobi)
                           VALUES ('$naziv','$opis','$polazak','$povratak','$cena')");
        header("Location: modul_aranzmani.php?msg=sacuvan");
    }else{
        header("Location: modul_aranzmani.php?msg=greska");
    }
}
//*izmena aranzmana/
$edit = 0;
$naziv_e = "";
$opis_e = "";
$polazak_e = "";
$povratak_e = "";
$cena_e = "";

if(isset($_GET['izmeni'])){
    $edit = $_GET['izmeni'];

    $rez = mysqli_query($db, "SELECT * FROM aranzman WHERE ID_aranzmana=".$edit);

    if($rez && mysqli_num_rows($rez)==1){
        $a = mysqli_fetch_array($rez);
        $naziv_e = $a['naziv'];
        $opis_e = $a['opis'];
        $polazak_e = $a['datum_polaska'];
        $povratak_e = $a['datum_povratka'];
        $cena_e = $a['cena_po_osobi'];
    }else{
        $edit = 0;
    }
}

if(isset($_POST['izmena'])){
    $id = $_POST['id'];
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $polazak = $_POST['datum_polaska'];
    $povratak = $_POST['datum_povratka'];

    $cena = $_POST['cena_po_osobi'];
    $cena = str_replace(",", ".", $cena);

    if($id!="" && $naziv!="" && $opis!="" && $polazak!="" && $povratak!="" && $cena!=""){

        $naziv = mysqli_real_escape_string($db,$naziv);
        $opis = mysqli_real_escape_string($db,$opis);
        $polazak = mysqli_real_escape_string($db,$polazak);
        $povratak = mysqli_real_escape_string($db,$povratak);
        $cena = mysqli_real_escape_string($db,$cena);

        mysqli_query($db, "UPDATE aranzman SET
                           naziv='$naziv',
                           opis='$opis',
                           datum_polaska='$polazak',
                           datum_povratka='$povratak',
                           cena_po_osobi='$cena'
                           WHERE ID_aranzmana=".$id);

        header("Location: modul_aranzmani.php?msg=izmenjen");
    }else{
        header("Location: modul_aranzmani.php?msg=greska");
    }
}

if(isset($_GET['msg'])){
    if($_GET['msg']=="sacuvan") $poruka="Aranžman je sačuvan.";
    if($_GET['msg']=="izmenjen") $poruka="Aranžman je izmenjen.";
    if($_GET['msg']=="obrisan") $poruka="Aranžman je obrisan.";
    if($_GET['msg']=="ne_moze") $poruka="Ne možete obrisati aranžman jer postoje rezervacije za njega.";
    if($_GET['msg']=="greska") $poruka="Popunite sva polja ispravno.";
}

$lista = mysqli_query($db, "SELECT * FROM aranzman ORDER BY datum_polaska DESC");
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Modul - Aranžmani</title>
<style>
body{font-family:Arial;margin:0;background:#e6eef5;}
header{background:#1f3a5f;color:#fff;padding:20px;text-align:center;}
.container{max-width:900px;margin:25px auto;padding:0 10px;}
.card{background:#fff;padding:18px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.15);margin-bottom:18px;}
h2{margin:0 0 10px;color:#1f3a5f;}
label{font-weight:bold;display:block;margin-top:8px;}
input,textarea{width:100%;padding:10px;margin-top:6px;box-sizing:border-box;}
textarea{resize:vertical;}
button{margin-top:12px;padding:10px 14px;background:#1f7a8c;color:#fff;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#145a66;}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,.15);border-radius:8px;overflow:hidden;}
th{background:#254a73;color:#fff;}
th,td{padding:10px;border-bottom:1px solid #ddd;vertical-align:top;}
a{font-weight:bold;text-decoration:none;}
.edit{color:#1f7a8c;margin-right:10px;}
.delete{color:#c0392b;}
.back{display:inline-block;margin-bottom:10px;font-weight:bold;color:#1f3a5f;text-decoration:none;}
.small{font-size:12px;color:#555;}
.msg{background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;}
</style>
</head>
<body>

<header>
  <h1>Turistička agencija</h1>
  <p>Modul: Aranžmani</p>
</header>

<div class="container">
  <a class="back" href="pocetna.php">← Nazad na meni</a>

  <?php if($poruka!="") echo "<div class='msg'>".$poruka."</div>"; ?>

  <div class="card">
    <?php if($edit==0){ ?>
      <h2>Unos aranžmana</h2>
      <form method="post">
        <label>Naziv</label>
        <input type="text" name="naziv" required>

        <label>Opis</label>
        <textarea name="opis" rows="4" required></textarea>

        <label>Datum polaska</label>
        <input type="date" name="datum_polaska" required>

        <label>Datum povratka</label>
        <input type="date" name="datum_povratka" required>

        <label>Cena po osobi</label>
        <input type="text" name="cena_po_osobi" placeholder="npr. 499.99 ili 499,99" required>

        <button type="submit" name="unos">Sačuvaj</button>
      </form>
    <?php }else{ ?>
      <h2>Izmena aranžmana</h2>
      <form method="post">
        <input type="hidden" name="id" value="<?php echo $edit; ?>">

        <label>Naziv</label>
        <input type="text" name="naziv" value="<?php echo $naziv_e; ?>" required>

        <label>Opis</label>
        <textarea name="opis" rows="4" required><?php echo $opis_e; ?></textarea>

        <label>Datum polaska</label>
        <input type="date" name="datum_polaska" value="<?php echo $polazak_e; ?>" required>

        <label>Datum povratka</label>
        <input type="date" name="datum_povratka" value="<?php echo $povratak_e; ?>" required>

        <label>Cena po osobi</label>
        <input type="text" name="cena_po_osobi" value="<?php echo $cena_e; ?>" required>

        <button type="submit" name="izmena">Sačuvaj izmenu</button>
      </form>
    <?php } ?>
  </div>

  <h2>Lista aranžmana</h2>
  <table>
    <tr>
      <th>Naziv</th>
      <th>Datumi</th>
      <th>Cena</th>
      <th>Opis</th>
      <th>Akcije</th>
    </tr>

    <?php
    if($lista && mysqli_num_rows($lista)>0){
        while($r=mysqli_fetch_array($lista)){
            echo "<tr>
                    <td><b>".$r['naziv']."</b></td>
                    <td>
                      <span class='small'>Polazak:</span> ".$r['datum_polaska']."<br>
                      <span class='small'>Povratak:</span> ".$r['datum_povratka']."
                    </td>
                    <td>".$r['cena_po_osobi']."</td>
                    <td>".$r['opis']."</td>
                    <td>
                      <a class='edit' href='modul_aranzmani.php?izmeni=".$r['ID_aranzmana']."'>Izmeni</a>
                      <a class='delete' href='modul_aranzmani.php?obrisi=".$r['ID_aranzmana']."' onclick=\"return confirm('Da li ste sigurni?');\">Obriši</a>
                    </td>
                  </tr>";
        }
    }else{
        echo "<tr><td colspan='5'>Nema aranžmana.</td></tr>";
    }
    ?>
  </table>
</div>

</body>
</html>
