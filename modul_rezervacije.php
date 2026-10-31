<?php
session_start();
include_once('konekcija.php');

$id_zaposleni = $_SESSION['id_zaposleni'];
$poruka = "";

if(isset($_GET['msg'])){
    if($_GET['msg']=="sacuvano") $poruka="Rezervacija je sačuvana i kreiran je račun.";
    if($_GET['msg']=="obrisano") $poruka="Rezervacija i račun su obrisani.";
    if($_GET['msg']=="placen")  $poruka="Račun je plaćen.";
    if($_GET['msg']=="greska")  $poruka="Popunite sva polja ispravno.";
    if($_GET['msg']=="db")      $poruka="Greška u bazi (proveri veze/ključeve).";
    if($_GET['msg']=="nema")    $poruka="Greška: izabrani aranžman ne postoji.";
    if($_GET['msg']=="izmenjeno") $poruka="Rezervacija je izmenjena.";
}


if(isset($_GET['obrisi'])){
    $id_rez = $_GET['obrisi'];

    $rezStatus = mysqli_query($db, "SELECT status FROM racun WHERE ID_rezervacija=".$id_rez);
    if($rezStatus && mysqli_num_rows($rezStatus)>0){
        $s = mysqli_fetch_array($rezStatus);
        if($s['status']=="placen"){
            header("Location: modul_rezervacije.php?msg=placen");
        }else{
            mysqli_query($db, "DELETE FROM racun WHERE ID_rezervacija=".$id_rez);
            $rezDel = mysqli_query($db, "DELETE FROM rezervacija WHERE ID_rezervacija=".$id_rez);

            if(!$rezDel){
                header("Location: modul_rezervacije.php?msg=db");
            }else{
                header("Location: modul_rezervacije.php?msg=obrisano");
            }
        }
    }else{
        $rezDel = mysqli_query($db, "DELETE FROM rezervacija WHERE ID_rezervacija=".$id_rez);
        if(!$rezDel){
            header("Location: modul_rezervacije.php?msg=db");
        }else{
            header("Location: modul_rezervacije.php?msg=obrisano");
        }
    }
}


if(isset($_POST['unos'])){

    $ID_klijent = $_POST['ID_klijent'];
    $ID_aranzmana = $_POST['ID_aranzmana'];
    $datum_rezervacije = $_POST['datum_rezervacije'];
    $broj_osoba = $_POST['broj_osoba'];
    $status = $_POST['status'];

    if($ID_klijent!="" && $ID_aranzmana!="" && $datum_rezervacije!="" && $broj_osoba!="" && $status!=""){

        $datum_rezervacije = mysqli_real_escape_string($db,$datum_rezervacije);
        $status = mysqli_real_escape_string($db,$status);

        $rezCena = mysqli_query($db, "SELECT cena_po_osobi FROM aranzman WHERE ID_aranzmana=".$ID_aranzmana);
        if(!$rezCena || mysqli_num_rows($rezCena)!=1){
            header("Location: modul_rezervacije.php?msg=nema");
        }else{
            $row = mysqli_fetch_array($rezCena);
            $cena_po_osobi = $row['cena_po_osobi'];
            $ukupna_cena = $broj_osoba * $cena_po_osobi;

            $upitRez = "INSERT INTO rezervacija (ID_klijent, ID_zaposleni, ID_aranzmana, datum_rezervacije, broj_osoba, status)
                        VALUES (".$ID_klijent.", ".$id_zaposleni.", ".$ID_aranzmana.", '".$datum_rezervacije."', ".$broj_osoba.", '".$status."')";
            $rezUpis = mysqli_query($db, $upitRez);

            if(!$rezUpis){
                header("Location: modul_rezervacije.php?msg=db");
            }else{
                $novi_id_rez = mysqli_insert_id($db);

                $datum_racuna = date("Y-m-d");
                $status_racuna = "kreiran";

                $upitRacun = "INSERT INTO racun (ID_rezervacija, datum, status, ukupna_cena)
                              VALUES (".$novi_id_rez.", '".$datum_racuna."', '".$status_racuna."', ".$ukupna_cena.")";
                $rezRacun = mysqli_query($db, $upitRacun);

                if(!$rezRacun){
                    mysqli_query($db, "DELETE FROM rezervacija WHERE ID_rezervacija=".$novi_id_rez);
                    header("Location: modul_rezervacije.php?msg=db");
                }else{
                    header("Location: modul_rezervacije.php?msg=sacuvano");
                }
            }
        }

    }else{
        header("Location: modul_rezervacije.php?msg=greska");
    }
}


$edit = 0;
$edit_status = "";

if(isset($_GET['izmeni'])){
    $edit = $_GET['izmeni'];
    $rezEdit = mysqli_query($db, "SELECT status FROM rezervacija WHERE ID_rezervacija=".$edit);
    if($rezEdit && mysqli_num_rows($rezEdit)==1){
        $e = mysqli_fetch_array($rezEdit);
        $edit_status = $e['status'];
    }else{
        $edit = 0;
    }
}


if(isset($_POST['izmena'])){
    $id_rez = $_POST['id_rez'];
    $novi_status = $_POST['status'];

    if($id_rez!="" && $novi_status!=""){

        $novi_status = mysqli_real_escape_string($db,$novi_status);

       
        if($novi_status=="otkazana"){
            $rezStatus = mysqli_query($db, "SELECT status FROM racun WHERE ID_rezervacija=".$id_rez);
            if($rezStatus && mysqli_num_rows($rezStatus)>0){
                $s = mysqli_fetch_array($rezStatus);
                if($s['status']=="placen"){
                    header("Location: modul_rezervacije.php?msg=placen");
                }
            }
        }

        $rezUpd = mysqli_query($db, "UPDATE rezervacija SET status='".$novi_status."' WHERE ID_rezervacija=".$id_rez);

        if(!$rezUpd){
            header("Location: modul_rezervacije.php?msg=db");
        }else{
           
            if($novi_status=="otkazana"){
                mysqli_query($db, "UPDATE racun SET status='otkazan' WHERE ID_rezervacija=".$id_rez." AND status<>'placen'");
            }
            header("Location: modul_rezervacije.php?msg=izmenjeno");
        }

    }else{
        header("Location: modul_rezervacije.php?msg=greska");
    }
}

$rezKlijenti = mysqli_query($db, "SELECT ID_klijent, ime, prezime, email FROM klijent ORDER BY prezime, ime");

$rezAranzmani = mysqli_query($db, "SELECT ID_aranzmana, naziv, cena_po_osobi FROM aranzman ORDER BY datum_polaska DESC");

$upitLista = "
SELECT 
    r.ID_rezervacija,
    r.datum_rezervacije,
    r.broj_osoba,
    r.status,
    k.ime AS k_ime,
    k.prezime AS k_prezime,
    a.naziv AS a_naziv,
    a.cena_po_osobi,
    ra.status AS status_racuna
FROM rezervacija r
JOIN klijent k ON r.ID_klijent = k.ID_klijent
JOIN aranzman a ON r.ID_aranzmana = a.ID_aranzmana
LEFT JOIN racun ra ON ra.ID_rezervacija = r.ID_rezervacija
ORDER BY r.ID_rezervacija DESC
";
$rezLista = mysqli_query($db, $upitLista);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modul - Rezervacije</title>

<style>
body { font-family: Arial, sans-serif; margin: 0; background-color: #e6eef5; }
header { background-color: #1f3a5f; color: white; padding: 20px; text-align: center; }
.container { max-width: 980px; margin: 30px auto; padding: 0 12px; }
.card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); margin-bottom: 20px; }
h2 { margin-top: 0; color: #1f3a5f; }
label { font-weight: bold; display:block; margin-top: 10px; }
input, select { width: 100%; padding: 10px; margin: 6px 0 12px; border: 1px solid #bbb; border-radius: 5px; box-sizing: border-box; }
button { padding: 10px 15px; background-color: #1f7a8c; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background-color: #145a66; }
table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.15); border-radius: 8px; overflow: hidden; }
th { background-color: #254a73; color: white; }
th, td { padding: 10px; border-bottom: 1px solid #ddd; font-size: 14px; }
.actions a { text-decoration: none; font-weight: bold; margin-right: 10px; }
.delete { color: #c0392b; }
.edit { color: #1f7a8c; }
.back { display: inline-block; margin-bottom: 12px; text-decoration: none; font-weight: bold; color: #1f3a5f; }
.small { color:#555; font-size: 12px; }
.msg { background:#fff3cd; border:1px solid #ffeeba; color:#856404; padding:10px; border-radius:6px; margin-bottom:12px; text-align:center; }
</style>
</head>

<body>

<header>
    <h1>Turistička agencija</h1>
    <p>Modul: Rezervacije</p>
</header>

<div class="container">
<a class="back" href="pocetna.php">← Nazad na meni</a>

<?php if($poruka!="") echo "<div class='msg'>".$poruka."</div>"; ?>

<div class="card">

<?php if($edit==0){ ?>
    <h2>Unos rezervacije</h2>

    <form method="post">
        <label>Klijent</label>
        <select name="ID_klijent" required>
            <option value="">-- izaberi klijenta --</option>
            <?php
            if($rezKlijenti && mysqli_num_rows($rezKlijenti)>0){
                while($k = mysqli_fetch_array($rezKlijenti)){
                    echo "<option value='".$k['ID_klijent']."'>".$k['prezime']." ".$k['ime']." (".$k['email'].")</option>";
                }
            }
            ?>
        </select>

        <label>Aranžman</label>
        <select name="ID_aranzmana" required>
            <option value="">-- izaberi aranžman --</option>
            <?php
            if($rezAranzmani && mysqli_num_rows($rezAranzmani)>0){
                while($a = mysqli_fetch_array($rezAranzmani)){
                    echo "<option value='".$a['ID_aranzmana']."'>".$a['naziv']." (cena/osobi: ".$a['cena_po_osobi'].")</option>";
                }
            }
            ?>
        </select>

        <label>Datum rezervacije</label>
        <input type="date" name="datum_rezervacije" required>

        <label>Broj osoba</label>
        <input type="number" name="broj_osoba" min="1" required>

        <label>Status rezervacije</label>
        <select name="status" required>
            <option value="nova">nova</option>
            <option value="potvrdjena">potvrđena</option>
            <option value="otkazana">otkazana</option>
        </select>

        <button type="submit" name="unos">Sačuvaj rezervaciju</button>
        <p class="small">Napomena: nakon unosa rezervacije automatski se kreira račun (status: kreiran).</p>
    </form>

<?php } else { ?>
    <h2>Izmena rezervacije (otkazivanje)</h2>

    <form method="post">
        <input type="hidden" name="id_rez" value="<?php echo $edit; ?>">

        <label>Status rezervacije</label>
        <select name="status" required>
            <option value="nova" <?php if($edit_status=="nova") echo "selected"; ?>>nova</option>
            <option value="potvrdjena" <?php if($edit_status=="potvrdjena") echo "selected"; ?>>potvrđena</option>
            <option value="otkazana" <?php if($edit_status=="otkazana") echo "selected"; ?>>otkazana</option>
        </select>

        <button type="submit" name="izmena">Sačuvaj izmenu</button>
        <p class="small">Ako postaviš status na <b>otkazana</b>, račun prelazi u status <b>otkazan</b> (osim ako je račun plaćen).</p>
    </form>

<?php } ?>

</div>

<h2>Lista rezervacija</h2>
<table>
    <tr>
        <th>Klijent</th>
        <th>Aranžman</th>
        <th>Datum</th>
        <th>Broj osoba</th>
        <th>Status rez.</th>
        <th>Status računa</th>
        <th>Ukupno</th>
        <th>Akcije</th>
    </tr>

    <?php
    if($rezLista && mysqli_num_rows($rezLista)>0){
        while($r = mysqli_fetch_array($rezLista)){
            $ukupno = $r['broj_osoba'] * $r['cena_po_osobi'];
            $status_racuna = $r['status_racuna'];
            if($status_racuna=="") $status_racuna = "nema";

            echo "<tr>";
            echo "<td>".$r['k_prezime']." ".$r['k_ime']."</td>";
            echo "<td>".$r['a_naziv']."</td>";
            echo "<td>".$r['datum_rezervacije']."</td>";
            echo "<td>".$r['broj_osoba']."</td>";
            echo "<td>".$r['status']."</td>";
            echo "<td>".$status_racuna."</td>";
            echo "<td>".$ukupno."</td>";
            echo "<td class='actions'>
                    <a class='edit' href='modul_rezervacije.php?izmeni=".$r['ID_rezervacija']."'>Izmeni</a>
                    <a class='delete' href='modul_rezervacije.php?obrisi=".$r['ID_rezervacija']."' onclick=\"return confirm('Obrisati rezervaciju i račun?');\">Obriši</a>
                  </td>";
            echo "</tr>";
        }
    }else{
        echo "<tr><td colspan='8'>Nema rezervacija.</td></tr>";
    }
    ?>
</table>

</div>
</body>
</html>
