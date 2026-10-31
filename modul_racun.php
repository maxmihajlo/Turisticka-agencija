<?php
session_start();
include_once('konekcija.php');

$poruka = "";

if(isset($_POST['promeni_status'])){
    $id_rez = $_POST['ID_rezervacija'];
    $novi_status = $_POST['status'];


    if($id_rez!="" && ($novi_status=="kreiran" || $novi_status=="placen" || $novi_status=="storniran")){
        $novi_status = mysqli_real_escape_string($db,$novi_status);

        $rez = mysqli_query($db, "UPDATE racun SET status='".$novi_status."' WHERE ID_rezervacija=".$id_rez);

        if(!$rez){
            header("Location: modul_racun.php?msg=db");
        }else{
            header("Location: modul_racun.php?msg=izmenjen");
        }
    }else{
        header("Location: modul_racun.php?msg=greska");
    }
}


if(isset($_GET['print'])){
    $id_rez = $_GET['print'];

    $upit = "
    SELECT
        ra.ID_rezervacija,
        ra.datum AS datum_racuna,
        ra.status AS status_racuna,
        ra.ukupna_cena,
        r.datum_rezervacije,
        r.broj_osoba,
        k.ime AS k_ime, k.prezime AS k_prezime, k.email AS k_email,
        a.naziv AS a_naziv, a.cena_po_osobi
    FROM racun ra
    JOIN rezervacija r ON ra.ID_rezervacija = r.ID_rezervacija
    JOIN klijent k ON r.ID_klijent = k.ID_klijent
    JOIN aranzman a ON r.ID_aranzmana = a.ID_aranzmana
    WHERE ra.ID_rezervacija = ".$id_rez."
    LIMIT 1
    ";
    $rez = mysqli_query($db,$upit);

    if(!$rez || mysqli_num_rows($rez)!=1){
        header("Location: modul_racun.php?msg=nema");
    }else{
        $x = mysqli_fetch_array($rez);
        ?>
        <!DOCTYPE html>
        <html lang="sr">
        <head>
            <meta charset="UTF-8">
            <title>Mini račun</title>
            <style>
                body { font-family: Arial; padding: 20px; }
                .box { max-width: 700px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
                h2 { margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                td { padding: 8px; border-bottom: 1px solid #eee; }
                .right { text-align: right; }
                .small { color: #555; font-size: 12px; }
                @media print { .noprint { display: none; } }
                .btn { display: inline-block; padding: 10px 14px; background:#1f7a8c; color:#fff; border-radius:6px; text-decoration:none; }
            </style>
        </head>
        <body>
            <div class="box">
                <h2>Turistička agencija — Račun</h2>
                <p class="small">Interni sistem</p>

                <table>
                    <tr><td><b>Datum računa</b></td><td class="right"><?php echo $x['datum_racuna']; ?></td></tr>
                    <tr><td><b>Status</b></td><td class="right"><?php echo $x['status_racuna']; ?></td></tr>
                    <tr><td><b>Klijent</b></td><td class="right"><?php echo $x['k_prezime']." ".$x['k_ime']; ?></td></tr>
                    <tr><td><b>Email</b></td><td class="right"><?php echo $x['k_email']; ?></td></tr>
                    <tr><td><b>Aranžman</b></td><td class="right"><?php echo $x['a_naziv']; ?></td></tr>
                    <tr><td><b>Broj osoba</b></td><td class="right"><?php echo $x['broj_osoba']; ?></td></tr>
                    <tr><td><b>Cena po osobi</b></td><td class="right"><?php echo $x['cena_po_osobi']; ?></td></tr>
                    <tr><td><b>Ukupno</b></td><td class="right"><b><?php echo $x['ukupna_cena']; ?></b></td></tr>
                </table>

                <br>
                <a class="btn noprint" href="#" onclick="window.print();return false;">Štampaj</a>
                <a class="btn noprint" href="modul_racun.php" style="background:#6c757d;">Nazad</a>
            </div>
        </body>
        </html>
        <?php
        die(); 
    }
}

if(isset($_GET['msg'])){
    if($_GET['msg']=="izmenjen") $poruka = "Status računa je izmenjen.";
    if($_GET['msg']=="db") $poruka = "Greška u bazi.";
    if($_GET['msg']=="greska") $poruka = "Greška: neispravan unos.";
    if($_GET['msg']=="nema") $poruka = "Račun ne postoji.";
}


$upitLista = "
SELECT
    ra.ID_rezervacija,
    ra.datum,
    ra.status,
    ra.ukupna_cena,
    r.datum_rezervacije,
    k.ime AS k_ime, k.prezime AS k_prezime,
    a.naziv AS a_naziv
FROM racun ra
JOIN rezervacija r ON ra.ID_rezervacija = r.ID_rezervacija
JOIN klijent k ON r.ID_klijent = k.ID_klijent
JOIN aranzman a ON r.ID_aranzmana = a.ID_aranzmana
ORDER BY ra.datum DESC
";
$rezLista = mysqli_query($db,$upitLista);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modul - Račun</title>
<style>
body{font-family:Arial;margin:0;background:#e6eef5;}
header{background:#1f3a5f;color:#fff;padding:20px;text-align:center;}
.container{max-width:980px;margin:30px auto;padding:0 12px;}
.back{display:inline-block;margin-bottom:12px;text-decoration:none;font-weight:bold;color:#1f3a5f;}
.msg{background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,.15);border-radius:8px;overflow:hidden;}
th{background:#254a73;color:#fff;}
th,td{padding:10px;border-bottom:1px solid #ddd;font-size:14px;vertical-align:top;}
select{padding:7px;width:140px;}
button{padding:7px 10px;background:#1f7a8c;color:#fff;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#145a66;}
a.link{font-weight:bold;text-decoration:none;color:#1f7a8c;}
.small{color:#555;font-size:12px;}
</style>
</head>
<body>

<header>
  <h1>Turistička agencija</h1>
  <p>Modul: Račun</p>
</header>

<div class="container">
  <a class="back" href="pocetna.php">← Nazad na meni</a>

  <?php if($poruka!="") echo "<div class='msg'>".$poruka."</div>"; ?>

  <table>
    <tr>
      <th>Klijent</th>
      <th>Aranžman</th>
      <th>Datum računa</th>
      <th>Status</th>
      <th>Ukupno</th>
      <th>Akcije</th>
    </tr>

    <?php
    if($rezLista && mysqli_num_rows($rezLista)>0){
        while($r = mysqli_fetch_array($rezLista)){

            echo "<tr>";
            echo "<td>".$r['k_prezime']." ".$r['k_ime']."</td>";
            echo "<td>".$r['a_naziv']."</td>";
            echo "<td>".$r['datum']."</td>";

            echo "<td>
                    <form method='post' style='margin:0;display:flex;gap:6px;align-items:center;flex-wrap:wrap;'>
                      <input type='hidden' name='ID_rezervacija' value='".$r['ID_rezervacija']."'>
                      <select name='status'>
                        <option value='kreiran' ".($r['status']=="kreiran"?"selected":"").">kreiran</option>
                        <option value='placen' ".($r['status']=="placen"?"selected":"").">placen</option>
                        <option value='storniran' ".($r['status']=="storniran"?"selected":"").">storniran</option>
                      </select>
                      <button type='submit' name='promeni_status'>Sačuvaj</button>
                    </form>
                    <div class='small'>rezervacija: ".$r['datum_rezervacije']."</div>
                  </td>";

            echo "<td><b>".$r['ukupna_cena']."</b></td>";

            echo "<td>
                    <a class='link' href='modul_racun.php?print=".$r['ID_rezervacija']."'>Mini račun</a>
                  </td>";
            echo "</tr>";
        }
    }else{
        echo "<tr><td colspan='6'>Nema računa.</td></tr>";
    }
    ?>
  </table>

  <p class="small" style="margin-top:10px;">
    Napomena: plaćeni računi se ne brišu (evidencija), samo se menja status.
  </p>
</div>

</body>
</html>
