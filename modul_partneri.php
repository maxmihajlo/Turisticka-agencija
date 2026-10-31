<?php
session_start();
include_once('konekcija.php');

$id_zaposleni = $_SESSION['id_zaposleni'];

$tab = "partneri";
if(isset($_GET['tab'])){
    $tab = $_GET['tab'];
}
if($tab!="partneri" && $tab!="ugovori"){
    $tab = "partneri";
}

$poruka = "";

$p_edit = 0;
$p_naziv = "";
$p_tip = "";
$p_email = "";


if($tab=="partneri" && isset($_GET['obrisi'])){
    $id = $_GET['obrisi'];
    $rez = mysqli_query($db, "DELETE FROM partner WHERE ID_partner=".$id);

    if(!$rez){
        $poruka = "Partner se ne može obrisati (možda ima ugovore).";
    }else{
        header("Location: modul_partneri.php?tab=partneri");
    }
}


if($tab=="partneri" && isset($_POST['unos_partner'])){
    $naziv = $_POST['naziv'];
    $tip = $_POST['tip'];
    $email = $_POST['email'];

    if($naziv!="" && $tip!="" && $email!=""){
        $naziv = mysqli_real_escape_string($db,$naziv);
        $tip = mysqli_real_escape_string($db,$tip);
        $email = mysqli_real_escape_string($db,$email);

        mysqli_query($db, "INSERT INTO partner (naziv, tip, email) VALUES ('$naziv','$tip','$email')");
        header("Location: modul_partneri.php?tab=partneri");
    }else{
        $poruka = "Popuni sva polja.";
    }
}


if($tab=="partneri" && isset($_GET['izmeni'])){
    $p_edit = $_GET['izmeni'];
    $rez = mysqli_query($db, "SELECT naziv, tip, email FROM partner WHERE ID_partner=".$p_edit." LIMIT 1");

    if($rez && mysqli_num_rows($rez)==1){
        $p = mysqli_fetch_array($rez);
        $p_naziv = $p['naziv'];
        $p_tip = $p['tip'];
        $p_email = $p['email'];
    }else{
        $p_edit = 0;
    }
}

if($tab=="partneri" && isset($_POST['izmeni_partner'])){
    $id = $_POST['id'];
    $naziv = $_POST['naziv'];
    $tip = $_POST['tip'];
    $email = $_POST['email'];

    if($id!="" && $naziv!="" && $tip!="" && $email!=""){
        $naziv = mysqli_real_escape_string($db,$naziv);
        $tip = mysqli_real_escape_string($db,$tip);
        $email = mysqli_real_escape_string($db,$email);

        mysqli_query($db, "UPDATE partner SET naziv='$naziv', tip='$tip', email='$email' WHERE ID_partner=".$id);
        header("Location: modul_partneri.php?tab=partneri");
    }else{
        $poruka = "Popuni sva polja.";
    }
}


$rezPartneri = mysqli_query($db, "SELECT ID_partner, naziv, tip, email FROM partner ORDER BY naziv");


$u_edit = 0;
$u_partner = "";
$u_pocetak = "";
$u_kraj = "";
$u_tip = "";
$u_tarifa = "";


if($tab=="ugovori" && isset($_GET['obrisi'])){
    $id = $_GET['obrisi'];
    mysqli_query($db, "DELETE FROM ugovor_partner WHERE ID_ugovor=".$id);
    header("Location: modul_partneri.php?tab=ugovori");
}


if($tab=="ugovori" && isset($_POST['unos_ugovor'])){
    $ID_partner = $_POST['ID_partner'];
    $pocetak = $_POST['pocetak_ugovora'];
    $kraj = $_POST['kraj_ugovora'];
    $tip_usluge = $_POST['tip_usluge'];

    $tarifa = $_POST['tarifa'];
    $tarifa = str_replace(",", ".", $tarifa); 

    if($ID_partner!="" && $pocetak!="" && $kraj!="" && $tip_usluge!="" && $tarifa!=""){
        $pocetak = mysqli_real_escape_string($db,$pocetak);
        $kraj = mysqli_real_escape_string($db,$kraj);
        $tip_usluge = mysqli_real_escape_string($db,$tip_usluge);
        $tarifa = mysqli_real_escape_string($db,$tarifa);

        mysqli_query($db, "INSERT INTO ugovor_partner (ID_partner, ID_zaposleni, pocetak_ugovora, kraj_ugovora, tip_usluge, tarifa)
                           VALUES (".$ID_partner.", ".$id_zaposleni.", '$pocetak', '$kraj', '$tip_usluge', '$tarifa')");
        header("Location: modul_partneri.php?tab=ugovori");
    }else{
        $poruka = "Popuni sva polja ispravno.";
    }
}


if($tab=="ugovori" && isset($_GET['izmeni'])){
    $u_edit = $_GET['izmeni'];

    $rez = mysqli_query($db, "SELECT ID_partner, pocetak_ugovora, kraj_ugovora, tip_usluge, tarifa
                              FROM ugovor_partner WHERE ID_ugovor=".$u_edit." LIMIT 1");
    if($rez && mysqli_num_rows($rez)==1){
        $u = mysqli_fetch_array($rez);
        $u_partner = $u['ID_partner'];
        $u_pocetak = $u['pocetak_ugovora'];
        $u_kraj = $u['kraj_ugovora'];
        $u_tip = $u['tip_usluge'];
        $u_tarifa = $u['tarifa'];
    }else{
        $u_edit = 0;
    }
}


if($tab=="ugovori" && isset($_POST['izmeni_ugovor'])){
    $id = $_POST['id'];
    $ID_partner = $_POST['ID_partner'];
    $pocetak = $_POST['pocetak_ugovora'];
    $kraj = $_POST['kraj_ugovora'];
    $tip_usluge = $_POST['tip_usluge'];

    $tarifa = $_POST['tarifa'];
    $tarifa = str_replace(",", ".", $tarifa);  

    if($id!="" && $ID_partner!="" && $pocetak!="" && $kraj!="" && $tip_usluge!="" && $tarifa!=""){
        $pocetak = mysqli_real_escape_string($db,$pocetak);
        $kraj = mysqli_real_escape_string($db,$kraj);
        $tip_usluge = mysqli_real_escape_string($db,$tip_usluge);
        $tarifa = mysqli_real_escape_string($db,$tarifa);

        mysqli_query($db, "UPDATE ugovor_partner SET
                           ID_partner=".$ID_partner.",
                           ID_zaposleni=".$id_zaposleni.",
                           pocetak_ugovora='$pocetak',
                           kraj_ugovora='$kraj',
                           tip_usluge='$tip_usluge',
                           tarifa='$tarifa'
                           WHERE ID_ugovor=".$id);

        header("Location: modul_partneri.php?tab=ugovori");
    }else{
        $poruka = "Popuni sva polja ispravno.";
    }
}

/* dropdown partneri */
$rezPartneriDD = mysqli_query($db, "SELECT ID_partner, naziv, tip FROM partner ORDER BY naziv");

/* lista ugovora (JOIN) */
$rezUgovori = mysqli_query($db, "
    SELECT u.ID_ugovor, u.pocetak_ugovora, u.kraj_ugovora, u.tip_usluge, u.tarifa,
           p.naziv AS partner_naziv, p.tip AS partner_tip
    FROM ugovor_partner u
    JOIN partner p ON u.ID_partner = p.ID_partner
    ORDER BY u.ID_ugovor DESC
");
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Partneri + Ugovori</title>
<style>
body{font-family:Arial;margin:0;background:#e6eef5;}
header{background:#1f3a5f;color:#fff;padding:20px;text-align:center;}
.container{max-width:1050px;margin:30px auto;padding:0 12px;}
.back{display:inline-block;margin-bottom:12px;text-decoration:none;font-weight:bold;color:#1f3a5f;}
.tabs{display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
.tablink{padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:bold;color:#fff;background:#6c757d;}
.tablink.active{background:#1f7a8c;}
.card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.15);margin-bottom:20px;}
h2{margin-top:0;color:#1f3a5f;}
label{font-weight:bold;display:block;margin-top:10px;}
input,select{width:100%;padding:10px;margin:6px 0 12px;border:1px solid #bbb;border-radius:5px;box-sizing:border-box;}
button,.btn{padding:10px 15px;background:#1f7a8c;color:#fff;border:none;border-radius:5px;cursor:pointer;text-decoration:none;display:inline-block;}
.btn-cancel{background:#6c757d;}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 10px rgba(0,0,0,.15);border-radius:8px;overflow:hidden;}
th{background:#254a73;color:#fff;}
th,td{padding:10px;border-bottom:1px solid #ddd;font-size:14px;vertical-align:top;}
.actions a{margin-right:10px;text-decoration:none;font-weight:bold;}
.edit{color:#1f7a8c;}
.delete{color:#c0392b;}
.small{font-size:12px;color:#555;}
.msg{background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:700px){.grid2{grid-template-columns:1fr;}}
</style>
</head>
<body>

<header>
  <h1>Turistička agencija</h1>
  <p>Modul: Partneri + Ugovori</p>
</header>

<div class="container">
  <a class="back" href="pocetna.php">← Nazad na meni</a>

  <div class="tabs">
    <a class="tablink <?php echo ($tab=='partneri')?'active':''; ?>" href="modul_partneri.php?tab=partneri">Partneri</a>
    <a class="tablink <?php echo ($tab=='ugovori')?'active':''; ?>" href="modul_partneri.php?tab=ugovori">Ugovori</a>
  </div>

  <?php if($poruka!="") echo "<div class='msg'>".$poruka."</div>"; ?>

  <?php if($tab=="partneri"){ ?>

    <div class="card">
      <?php if($p_edit==0){ ?>
        <h2>Unos partnera</h2>
        <form method="post">
          <label>Naziv</label>
          <input type="text" name="naziv" required>

          <label>Tip</label>
          <select name="tip" required>
            <option value="">-- izaberi --</option>
            <option value="hotel">hotel</option>
            <option value="prevoznik">prevoznik</option>
            <option value="vodic">vodič</option>
          </select>

          <label>Email</label>
          <input type="email" name="email" required>

          <button type="submit" name="unos_partner">Sačuvaj</button>
        </form>
      <?php } else { ?>
        <h2>Izmena partnera</h2>
        <form method="post">
          <input type="hidden" name="id" value="<?php echo $p_edit; ?>">

          <label>Naziv</label>
          <input type="text" name="naziv" value="<?php echo $p_naziv; ?>" required>

          <label>Tip</label>
          <select name="tip" required>
            <option value="hotel" <?php echo ($p_tip=="hotel")?"selected":""; ?>>hotel</option>
            <option value="prevoznik" <?php echo ($p_tip=="prevoznik")?"selected":""; ?>>prevoznik</option>
            <option value="vodic" <?php echo ($p_tip=="vodic")?"selected":""; ?>>vodič</option>
          </select>

          <label>Email</label>
          <input type="email" name="email" value="<?php echo $p_email; ?>" required>

          <button type="submit" name="izmeni_partner">Sačuvaj izmenu</button>
          <a class="btn btn-cancel" href="modul_partneri.php?tab=partneri">Otkaži</a>
        </form>
      <?php } ?>
    </div>

    <h2>Lista partnera</h2>
    <table>
      <tr><th>Naziv</th><th>Tip</th><th>Email</th><th>Akcije</th></tr>
      <?php
      if($rezPartneri && mysqli_num_rows($rezPartneri)>0){
          while($p = mysqli_fetch_array($rezPartneri)){
              echo "<tr>
                      <td><b>".$p['naziv']."</b></td>
                      <td>".$p['tip']."</td>
                      <td>".$p['email']."</td>
                      <td class='actions'>
                        <a class='edit' href='modul_partneri.php?tab=partneri&izmeni=".$p['ID_partner']."'>Izmeni</a>
                        <a class='delete' href='modul_partneri.php?tab=partneri&obrisi=".$p['ID_partner']."' onclick=\"return confirm('Obrisati partnera?');\">Obriši</a>
                      </td>
                    </tr>";
          }
      }else{
          echo "<tr><td colspan='4'>Nema partnera.</td></tr>";
      }
      ?>
    </table>

  <?php } else { ?>

    <div class="card">
      <?php if($u_edit==0){ ?>
        <h2>Unos ugovora</h2>
        <form method="post">
          <label>Partner</label>
          <select name="ID_partner" required>
            <option value="">-- izaberi partnera --</option>
            <?php
            if($rezPartneriDD && mysqli_num_rows($rezPartneriDD)>0){
                while($p = mysqli_fetch_array($rezPartneriDD)){
                    echo "<option value='".$p['ID_partner']."'>".$p['naziv']." (".$p['tip'].")</option>";
                }
            }
            ?>
          </select>

          <div class="grid2">
            <div>
              <label>Početak ugovora</label>
              <input type="date" name="pocetak_ugovora" required>
            </div>
            <div>
              <label>Kraj ugovora</label>
              <input type="date" name="kraj_ugovora" required>
            </div>
          </div>

          <label>Tip usluge</label>
          <input type="text" name="tip_usluge" placeholder="" required>

          <label>Tarifa</label>
          <input type="text" name="tarifa" placeholder="" required>

          <button type="submit" name="unos_ugovor">Sačuvaj</button>
        </form>
      <?php } else { ?>
        <h2>Izmena ugovora</h2>
        <form method="post">
          <input type="hidden" name="id" value="<?php echo $u_edit; ?>">

          <label>Partner</label>
          <select name="ID_partner" required>
            <?php
            $rezDD2 = mysqli_query($db, "SELECT ID_partner, naziv, tip FROM partner ORDER BY naziv");
            if($rezDD2 && mysqli_num_rows($rezDD2)>0){
                while($p = mysqli_fetch_array($rezDD2)){
                    $sel = ($u_partner==$p['ID_partner']) ? "selected" : "";
                    echo "<option value='".$p['ID_partner']."' ".$sel.">".$p['naziv']." (".$p['tip'].")</option>";
                }
            }
            ?>
          </select>

          <div class="grid2">
            <div>
              <label>Početak ugovora</label>
              <input type="date" name="pocetak_ugovora" value="<?php echo $u_pocetak; ?>" required>
            </div>
            <div>
              <label>Kraj ugovora</label>
              <input type="date" name="kraj_ugovora" value="<?php echo $u_kraj; ?>" required>
            </div>
          </div>

          <label>Tip usluge</label>
          <input type="text" name="tip_usluge" value="<?php echo $u_tip; ?>" required>

          <label>Tarifa</label>
          <input type="text" name="tarifa" value="<?php echo $u_tarifa; ?>" required>

          <button type="submit" name="izmeni_ugovor">Sačuvaj izmenu</button>
          <a class="btn btn-cancel" href="modul_partneri.php?tab=ugovori">Otkaži</a>
        </form>
      <?php } ?>
    </div>

    <h2>Lista ugovora</h2>
    <table>
      <tr>
        <th>Partner</th>
        <th>Tip usluge</th>
        <th>Period</th>
        <th>Tarifa</th>
        <th>Akcije</th>
      </tr>
      <?php
      if($rezUgovori && mysqli_num_rows($rezUgovori)>0){
          while($u = mysqli_fetch_array($rezUgovori)){
              echo "<tr>
                      <td><b>".$u['partner_naziv']."</b><br><span class='small'>".$u['partner_tip']."</span></td>
                      <td>".$u['tip_usluge']."</td>
                      <td><span class='small'>Od:</span> ".$u['pocetak_ugovora']."<br><span class='small'>Do:</span> ".$u['kraj_ugovora']."</td>
                      <td>".$u['tarifa']."</td>
                      <td class='actions'>
                        <a class='edit' href='modul_partneri.php?tab=ugovori&izmeni=".$u['ID_ugovor']."'>Izmeni</a>
                        <a class='delete' href='modul_partneri.php?tab=ugovori&obrisi=".$u['ID_ugovor']."' onclick=\"return confirm('Obrisati ugovor?');\">Obriši</a>
                      </td>
                    </tr>";
          }
      }else{
          echo "<tr><td colspan='5'>Nema ugovora.</td></tr>";
      }
      ?>
    </table>

  <?php } ?>

</div>
</body>
</html>
