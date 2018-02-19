<!DOCTYPE html>

<?php

    session_start();
    $conn = pg_connect("host=dbstud2.sis.uta.fi port=5432 dbname=pd98589 user=pd98589 password=5opwfah10");

    
    $kayttaja = $_SESSION['login_user'];
    // if käyttäjä on opiskelija
    if(pg_query("SELECT * FROM oppimisymparisto.opiskelija WHERE Opnro = $kayttaja")){
        $yritykset = pg_query("SELECT Suorittaja, Teth_alku, Teht_loppu, Teht_oikein FROM oppimisymparisto.sessio WHERE suorittaja = $kayttaja");
    }

    // if käyttäjä on opettaja
    if(pg_query("SELECT * FROM oppimisymparisto.opettaja WHERE Tunnus = $kayttaja")){
        $tehtavat = pg_query("SELECT * FROM oppimisymparisto.tehtavat WHERE Teht_luoja = $kayttaja");
        $tehtavakok = pg_query("SELECT * FROM oppimisymparisto.tehtavalista WHERE List_luoja = $kayttaja");
    }

    if(isset($_POST['luo'])){
        $kuvaus = $_POST['kuvaus'];
        $teht1 = $_POST['teht1'];
        $teht2 = $_POST['teht2'];
        $teht3 = $_POST['teht3'];
        $date = date('Y-m-d');
        $maxlista = pg_query("SELECT MAX(Id) FROM oppimisymparisto.tehtavalista");
        $suurinrow = pg_fetch_row($maxlista);
        $maxlista = $suurinrow[0] + 1;
        pg_query("INSERT INTO oppimisymparisto.tehtavalista VALUES ($maxlista, '$kuvaus', '$date', '$kayttaja', 3)");
        pg_query("INSERT INTO oppimisymparisto.muodostaa VALUES ($maxlista, $teht1)");
        pg_query("INSERT INTO oppimisymparisto.muodostaa VALUES ($maxlista, $teht2)");
        pg_query("INSERT INTO oppimisymparisto.muodostaa VALUES ($maxlista, $teht3)");

    }

    // Poimitaan painikkeen käsky
    if (isset($_POST['tehtaviin'])) {

        // siirrytaan tehtäviin
        header('Location: tehtava1.php');
    }

    // Poimitaan painikkeen käsky
    if (isset($_POST['ulos_kirj'])) {
        // tuhotaan sessio
        session_destroy();

        // siirrytään etusivulle
        header('Location: etusivu.php');
    }
	

?>

<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>Omat tiedot</title>
		<link href="tyylit.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
        if(pg_query("SELECT * FROM opiskelija WHERE Opnro = $kayttaja")){
            echo "Sessio";
            echo "<table border=1>";
            echo "<tr><th>Suorittaja</th><th>Aloitus</th><th>Lopetus</th><th>Tehtäviä oikein</th></tr>";
            while ($row = pg_fetch_row($yritykset)) {
                echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td> <td>$row[3]</td></tr>";
            }
            echo "</table>";
            echo "<br />\n";
            ?>
            <form method="post" action="">
                <input type="submit" name="tehtaviin" value="Tehtäviin"/>
            </form>
            <?php
        }
        if(pg_query("SELECT * FROM opettaja WHERE Tunnus = $kayttaja")){
            echo "Luomasi tehtävät";
            echo "<table border=1>";
            while ($row = pg_fetch_row($tehtavat)) {
                echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td> <td>$row[3]</td> <td>$row[4]</td> <td>$row[5]</td></tr>";
            }
            echo "</table>";
            echo "<br />\n";
            echo "Luomasi tehtäväkokonaisuudet";
            echo "<table border=1>";
            while ($row = pg_fetch_row($tehtavakok)) {
                echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td> <td>$row[3]</td> <td>$row[4]</td></tr>";
            }
            echo "</table>";
            echo "<br />\n";

            ?>
            Luo tehtävälista
            <form method="post" action="">
                <table>
                    <tr><td>Kuvaus: </td><td><input id="kuvaus" type="text" name="kuvaus" value=""/></td></tr>
                    <tr><td>Tehtävä 1: </td><td><input id="tunnus" type="text" name="teht1" value="" /></td></tr>
                    <tr><td>Tehtävä 2: </td><td><input id="tunnus" type="text" name="teht2" value="" /></td></tr>
                    <tr><td>Tehtävä 3: </td><td><input id="tunnus" type="text" name="teht3" value="" /></td></tr>
                    <tr><td>Opettajat: </td><td><input type="submit" name="luo" value="Luo"/></td></tr>
                </table>
            </form>
        <?php
        }
        ?>
        <form method="post" action="">
            <input type="submit" name="kirjaudu_ulos" value="Uloskirjautuminen"/>
        </form>
        
    </body>
</html>
<?php pg_close($conn); ?>