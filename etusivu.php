<!DOCTYPE html>

<?php

	session_start();

    
    $error ='';
    if(isset($_POST['submit'])) {
        if(empty($_POST['nimi'] || empty($_POST['tunnus']))) {
            $error = "KÃ¤yttÃ¤jÃ¤nimi tai tunnus/opnro vÃ¤Ã¤rÃ¤";
        }
        else {
            $nimi = $_POST['nimi'];
            $tunnus = $_POST['tunnus'];

            $conn = pg_connect("host=dbstud2.sis.uta.fi port=5432 dbname=pd98589 user=pd98589 password=5opwfah10");
            
            $kysely1 = pg_query("SELECT * FROM oppimisymparisto.opiskelija WHERE nimi='$nimi' AND opnro='$tunnus'");
            $kysely2 = pg_query("SELECT * FROM oppimisymparisto.opettaja WHERE nimi='$nimi' AND tunnus='$tunnus'");
            
            $rows1 = pg_num_rows($kysely1);
            $rows2 = pg_num_rows($kysely2);
            
            if($rows1 == 1) {
                $_SESSION['login_user']=$tunnus;
                $_SESSION['yrityksia'] = 0;
                header('location: tehtava1.php');
            }
            else if($rows2 == 1) {
                $_SESSION['login_user']=$tunnus;
                $_SESSION['yrityksia'] = 0;
                header('location: omattiedot.php');
            }
            else {
                $error = "Nimi tai tunnus on väärä";
            }
            pg_close($conn);
        }
    }
	
	if(isset($_POST['rekisteroidy'])) {
		header('Location: rekisteroidy.php');
	}
	
	if(isset($_POST['rekisteroidy2'])) {
		header('Location: rekisteroidy2.php');
	}

?>

<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>Kirjaudu</title>
		<link href="tyylit.css" rel="stylesheet" type="text/css">
    </head>
    <body>
	<h3>Kirjaudu sisään tai rekisteröidy</h3>
        <p>Jos olet jo rekisteröitynyt käyttäjä, voit kirjautua suoraan sisään käyttäen
            opiskelijanumeroasi, jos olet opiskelija, tai tunnustasi, jos olet opettaja.
            Muuten voit siirtyä rekisteröitymiseen alla olevilla painikkeilla.
        </p>
        <form method="post" action="">
            <table>
                <tr><td>Nimi: </td><td><input id="nimi" type="text" name="nimi" value=""/></td></tr>
                <tr><td>Tunnus/opnro: </td><td><input id="tunnus" type="text" name="tunnus" value="" /></td></tr>
                <tr><td></td><td><input type="submit" name="submit" value="Kirjaudu"/></td></tr>
				<tr><td>Opiskelijat: </td><td><input type="submit" name="rekisteroidy" value="Rekisteröityminen"/></td></tr>
				<tr><td>Opettajat: </td><td><input type="submit" name="rekisteroidy2" value="Rekisteröityminen"/></td></tr>
                <span><?php echo $error; ?></span>
            </table>
        </form>
    </body>
</html>