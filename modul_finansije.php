<?php
session_start();
include_once('konekcija.php');

$poruka = "";

if(isset($_GET['set_status']) && isset($_GET['ID_provizija'])){

    $ID_provizija = $_GET['ID_provizija'];
    $status = $_GET['set_status'];

    if($status!="nije_placeno" && $status!="u_obradi" && $status!="placeno"){
        header("Location: modul_finansije.php");
    }else{

        $datum_sql = "NULL";
        if($status=="u_obradi" || $status=="placeno"){
            $datum_sql = "'".date("Y-m-d")."'";
        }

        $pp = mysqli_query($db, "SELECT ID_partner, ID_racun FROM provizija WHERE ID_provizija=".$ID_provizija."");
        if($pp && mysqli_num_rows($pp)>0){
            $rowp = mysqli_fetch_array($pp);
            $ID_partner = $rowp['ID_partner'];

           
            $postoji = mysqli_query($db, "SELECT ID_isplata FROM partner_isplata WHERE ID_provizija=".$ID_provizija."");

            if($postoji && mysqli_num_rows($postoji)>0){
                mysqli_query($db, "UPDATE partner_isplata
                                   SET status='".$status."', datum_isplate=".$datum_sql."
                                   WHERE ID_provizija=".$ID_provizija);
            }else{
                mysqli_query($db, "INSERT INTO partner_isplata (ID_provizija, ID_partner, datum_isplate, status)
                                   VALUES (".$ID_provizija.", ".$ID_partner.", ".$datum_sql.", '".$status."')");
            }

          
        }

        header("Location: modul_finansije.php");
    }
}

if(isset($_GET['obrisi_proviziju'])){

    $ID_provizija = $_GET['obrisi_proviziju'];

    $qq = mysqli_query($db, "SELECT ID_racun FROM provizija WHERE ID_provizija=".$ID_provizija." LIMIT 1");
    $ID_rezervacija = 0;
    if($qq && mysqli_num_rows($qq)>0){
        $rr = mysqli_fetch_array($qq);
        $ID_rezervacija = $rr['ID_racun'];
    }

    mysqli_query($db, "DELETE FROM partner_isplata WHERE ID_provizija=".$ID_provizija);
    mysqli_query($db, "DELETE FROM provizija WHERE ID_provizija=".$ID_provizija);

    if($ID_rezervacija!=0){

        $q1 = mysqli_query($db, "SELECT ukupna_cena FROM racun WHERE ID_rezervacija=".$ID_rezervacija." LIMIT 1");
        $ukupno = 0;
        if($q1 && mysqli_num_rows($q1)>0){
            $r1 = mysqli_fetch_array($q1);
            $ukupno = $r1['ukupna_cena'];
        }

        $q2 = mysqli_query($db, "SELECT SUM(iznos_partner) AS suma FROM provizija WHERE ID_racun=".$ID_rezervacija);
        $suma_partner = 0;
        if($q2 && mysqli_num_rows($q2)>0){
            $r2 = mysqli_fetch_array($q2);
            $suma_partner = $r2['suma'];
            if($suma_partner=="") $suma_partner = 0;
        }

        $iznos_agencija = $ukupno - $suma_partner;
        if($iznos_agencija<0) $iznos_agencija = 0;

        mysqli_query($db, "UPDATE provizija SET iznos_agencija=".$iznos_agencija." WHERE ID_racun=".$ID_rezervacija);
    }

    header("Location: modul_finansije.php");
}

if(isset($_POST['submit_provizija'])){

    $ID_rezervacija = $_POST['ID_rezervacija'];
    $ID_ugovor = $_POST['ID_ugovor'];

    if($ID_rezervacija!="" && $ID_ugovor!=""){

        $qrez = mysqli_query($db, "SELECT broj_osoba FROM rezervacija WHERE ID_rezervacija=".$ID_rezervacija." LIMIT 1");
        if(!$qrez || mysqli_num_rows($qrez)==0){
            $poruka = "Ne postoji rezervacija.";
        }else{
            $rrez = mysqli_fetch_array($qrez);
            $broj_osoba = $rrez['broj_osoba'];

            $qug = mysqli_query($db, "SELECT ID_partner, tarifa FROM ugovor_partner WHERE ID_ugovor=".$ID_ugovor." LIMIT 1");
            if(!$qug || mysqli_num_rows($qug)==0){
                $poruka = "Ugovor ne postoji.";
            }else{
                $rug = mysqli_fetch_array($qug);
                $ID_partner = $rug['ID_partner'];
                $tarifa = $rug['tarifa'];


                $tarifa = str_replace(",", ".", $tarifa);

                $iznos_partner = $tarifa * $broj_osoba;
                $danas = date("Y-m-d");

                $chk = mysqli_query($db, "SELECT ID_provizija FROM provizija WHERE ID_partner=".$ID_partner." AND ID_racun=".$ID_rezervacija."");
                if($chk && mysqli_num_rows($chk)>0){
                    $poruka = "Ovaj partner je već dodat na ovu rezervaciju/račun.";
                }else{

                    $ins = mysqli_query($db, "INSERT INTO provizija (ID_partner, ID_racun, iznos_partner, iznos_agencija, datum)
                                              VALUES (".$ID_partner.", ".$ID_rezervacija.", ".$iznos_partner.", 0, '".$danas."')");

                    if(!$ins){
                        $poruka = "Greška pri upisu provizije.";
                    }else{

                        $q1 = mysqli_query($db, "SELECT ukupna_cena FROM racun WHERE ID_rezervacija=".$ID_rezervacija."");
                        $ukupno = 0;
                        if($q1 && mysqli_num_rows($q1)>0){
                            $r1 = mysqli_fetch_array($q1);
                            $ukupno = $r1['ukupna_cena'];
                        }

                        $q2 = mysqli_query($db, "SELECT SUM(iznos_partner) AS suma FROM provizija WHERE ID_racun=".$ID_rezervacija);
                        $suma_partner = 0;
                        if($q2 && mysqli_num_rows($q2)>0){
                            $r2 = mysqli_fetch_array($q2);
                            $suma_partner = $r2['suma'];
                            if($suma_partner=="") $suma_partner = 0;
                        }

                        $iznos_agencija = $ukupno - $suma_partner;
                        if($iznos_agencija<0) $iznos_agencija = 0;

                        mysqli_query($db, "UPDATE provizija SET iznos_agencija=".$iznos_agencija." WHERE ID_racun=".$ID_rezervacija);

                        header("Location: modul_finansije.php");
                    }
                }
            }
        }

    }else{
        $poruka = "Popunite sva polja.";
    }
}

$ddRacuni = mysqli_query($db, "
    SELECT ra.ID_racun, ra.ID_rezervacija, ra.datum, ra.status, ra.ukupna_cena,
           k.ime AS k_ime, k.prezime AS k_prezime,
           a.naziv AS a_naziv,
           rez.broj_osoba
    FROM racun ra
    JOIN rezervacija rez ON ra.ID_rezervacija = rez.ID_rezervacija
    JOIN klijent k ON rez.ID_klijent = k.ID_klijent
    JOIN aranzman a ON rez.ID_aranzmana = a.ID_aranzmana
    ORDER BY ra.ID_racun DESC
");

$ddUgovori = mysqli_query($db, "
    SELECT u.ID_ugovor, u.tip_usluge, u.tarifa, p.naziv AS partner_naziv, p.tip AS partner_tip
    FROM ugovor_partner u
    JOIN partner p ON u.ID_partner = p.ID_partner
    ORDER BY p.naziv, u.tip_usluge
");


$lista = mysqli_query($db, "
    SELECT pr.ID_provizija, pr.ID_racun AS ID_rezervacija, pr.iznos_partner, pr.iznos_agencija, pr.datum,
           p.naziv AS partner_naziv, p.tip AS partner_tip,
           ra.ID_racun AS pravi_racun_id, ra.ukupna_cena, ra.status AS racun_status, ra.datum AS racun_datum,
           rez.broj_osoba,
           k.ime AS k_ime, k.prezime AS k_prezime,
           a.naziv AS a_naziv,
           pi.status AS isplata_status, pi.datum_isplate
    FROM provizija pr
    JOIN rezervacija rez ON pr.ID_racun = rez.ID_rezervacija
    JOIN racun ra ON ra.ID_rezervacija = rez.ID_rezervacija
    JOIN klijent k ON rez.ID_klijent = k.ID_klijent
    JOIN aranzman a ON rez.ID_aranzmana = a.ID_aranzmana
    JOIN partner p ON pr.ID_partner = p.ID_partner
    LEFT JOIN partner_isplata pi ON pr.ID_provizija = pi.ID_provizija
    ORDER BY pr.ID_provizija DESC
");
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modul - Finansije</title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#e6eef5;}
header{background:#1f3a5f;color:#fff;padding:20px;text-align:center;}
.container{max-width:1100px;margin:25px auto;padding:0 12px;}
.back{display:inline-block;margin-bottom:12px;text-decoration:none;font-weight:bold;color:#1f3a5f;}
.card{background:#fff;padding:18px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.15);margin-bottom:18px;}
h2{margin:0 0 10px;color:#1f3a5f;}
label{font-weight:bold;display:block;margin-top:8px;}
select, button{padding:10px;margin:6px 0 12px;border:1px solid #bbb;border-radius:5px;box-sizing:border-box;width:100%;}
button{background:#1f7a8c;color:#fff;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#145a66;}
.msg{background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:10px;border-radius:6px;margin-bottom:12px;}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,.15);border-radius:8px;overflow:hidden;margin-top:18px;}
th{background:#254a73;color:#fff;}
th,td{padding:10px;border-bottom:1px solid #ddd;font-size:14px;vertical-align:top;}
.small{font-size:12px;color:#555;}
.actions a{margin-right:10px;text-decoration:none;font-weight:bold;display:inline-block;}
.pay{color:#1f7a8c;}
.delete{color:#c0392b;}
.badge{display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px;color:#fff;}
.b1{background:#6c757d;} .b2{background:#f39c12;} .b3{background:#27ae60;}
</style>
</head>
<body>

<header>
  <h1>Turistička agencija</h1>
  <p>Modul: Finansije (provizije + isplate)</p>
</header>

<div class="container">
  <a class="back" href="pocetna.php">← Nazad na meni</a>

  <?php if($poruka!="") echo "<div class='msg'>".$poruka."</div>"; ?>

  <div class="card">
    <h2>Novi obračun provizije</h2>

    <form method="post">
      <label>Račun</label>
      <select name="ID_rezervacija" required>
        <option value="">-- izaberi račun --</option>
        <?php
        if($ddRacuni && mysqli_num_rows($ddRacuni)>0){
            while($r = mysqli_fetch_array($ddRacuni)){
                $val = $r['ID_rezervacija']; 
                echo "<option value='".$val."'>Račun #".$r['ID_racun']." (rezervacija #".$r['ID_rezervacija'].") | ".$r['k_prezime']." ".$r['k_ime']." | ".$r['a_naziv']." | osobe: ".$r['broj_osoba']." | ukupno: ".$r['ukupna_cena']." | status: ".$r['status']."</option>";
            }
        }
        ?>
      </select>

      <label>Ugovor (partner + usluga + tarifa)</label>
      <select name="ID_ugovor" required>
        <option value="">-- izaberi ugovor --</option>
        <?php
        if($ddUgovori && mysqli_num_rows($ddUgovori)>0){
            while($u = mysqli_fetch_array($ddUgovori)){
                echo "<option value='".$u['ID_ugovor']."'>".$u['partner_naziv']." (".$u['partner_tip'].") | ".$u['tip_usluge']." | tarifa: ".$u['tarifa']."</option>";
            }
        }
        ?>
      </select>

      <button type="submit" name="submit_provizija">Sačuvaj obračun</button>
      <p class="small">Iznos partneru se računa: <b>tarifa × broj_osoba</b>. Možeš dodati više partnera za isti račun/rezervaciju.</p>
    </form>
  </div>

  <h2>Pregled provizija</h2>
  <table>
    <tr>
      <th>Rezervacija / Račun</th>
      <th>Partner</th>
      <th>Iznos partner</th>
      <th>Iznos agencija</th>
      <th>Status isplate</th>
      <th>Akcije</th>
    </tr>

    <?php
    if($lista && mysqli_num_rows($lista)>0){
        while($x = mysqli_fetch_array($lista)){

            $st = $x['isplata_status'];
            if($st=="") $st = "nije_placeno";

            $badge = "b1";
            if($st=="u_obradi") $badge = "b2";
            if($st=="placeno") $badge = "b3";

            $datumIspl = $x['datum_isplate'];
            if($datumIspl=="") $datumIspl = "-";

            echo "<tr>";

            echo "<td>
                    <b>Račun #".$x['pravi_racun_id']."</b> (rezervacija #".$x['ID_rezervacija'].")<br>
                    ".$x['k_prezime']." ".$x['k_ime']." | ".$x['a_naziv']."<br>
                    <span class='small'>datum računa: ".$x['racun_datum']." | osobe: ".$x['broj_osoba']." | ukupno: ".$x['ukupna_cena']." | status računa: ".$x['racun_status']."</span>
                  </td>";

            echo "<td><b>".$x['partner_naziv']."</b><br><span class='small'>".$x['partner_tip']."</span></td>";
            echo "<td>".$x['iznos_partner']."</td>";
            echo "<td><b>".$x['iznos_agencija']."</b></td>";

            echo "<td>
                    <span class='badge ".$badge."'>".$st."</span><br>
                    <span class='small'>datum: ".$datumIspl."</span>
                  </td>";

            echo "<td class='actions'>
                    <a class='pay' href='modul_finansije.php?set_status=nije_placeno&ID_provizija=".$x['ID_provizija']."'>Nije plaćeno</a>
                    <a class='pay' href='modul_finansije.php?set_status=u_obradi&ID_provizija=".$x['ID_provizija']."'>U obradi</a>
                    <a class='pay' href='modul_finansije.php?set_status=placeno&ID_provizija=".$x['ID_provizija']."'>Plaćeno</a>
                    <a class='delete' href='modul_finansije.php?obrisi_proviziju=".$x['ID_provizija']."' onclick=\"return confirm('Obrisati proviziju?');\">Obriši</a>
                  </td>";

            echo "</tr>";
        }
    }else{
        echo "<tr><td colspan='6'>Nema provizija.</td></tr>";
    }
    ?>
  </table>

</div>
</body>
</html>
