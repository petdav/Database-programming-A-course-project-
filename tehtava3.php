<!DOCTYPE html>

<?php

    session_start();
    $conn = pg_connect("host=dbstud2.sis.uta.fi port=5432 dbname=pd98589 user=pd98589 password=5opwfah10");

	$lista_id = $_SESSION['lista'];
	$tehtavat = pg_query("SELECT * FROM oppimisymparisto.muodostaa WHERE Lista_id='$lista_id'");
	$teht3 = pg_fetch_row($tehtavat, $_SESSION['monesko']);
    $sessio_id = $_SESSION['sessioid'];

	// Haetaan tehtävä
	$teht3 = $teht3[1];
    $tehtava = pg_query("SELECT kuvaus FROM oppimisymparisto.tehtavat WHERE Id='$teht3'");
	if (!$tehtava) {
		echo "Virhe kyselyssä.\n";
		exit;
	}
	$tehtava_kuvaus = pg_fetch_row($tehtava);
	
	// Haetaan esim. vastaus
	$vastaus = pg_query("SELECT Esim_vastaus FROM oppimisymparisto.tehtavat WHERE Id = '$teht3'");
	if (!$vastaus) {
		echo "Virhe kyselyssä.\n";
		exit;
	}

	$yrityksia = $_SESSION['yrityksia'];

	$vastaus_row = pg_fetch_row($vastaus);
	$vastaus_muuttunut = str_replace(';', '', $vastaus_row[0]);
	$vastaus_viela = str_replace('"', "'", $vastaus_muuttunut);
	$vastaus_kys = pg_query($vastaus_viela);
	$vastaus_valuet = pg_fetch_all($vastaus_kys);

	if(isset($_POST['syote1'])){
		$syote = $_POST['syote'];
		$syote_muuttunut = str_replace(';', '', $syote);
		$syote_kys = pg_query($syote_muuttunut);
	    $syote_valuet = pg_fetch_all($syote_kys);

	    // Luetaan syÃ¶te ja verrataan vastausvaihtoehtoihin
	    if (!pg_query($syote_muuttunut)) {
	     	echo "Syntaksivirhe";
			$_SESSION['yrityksia'] = $_SESSION['yrityksia'] + 1;
	     	$yrityksia = $_SESSION['yrityksia'];
	     	pg_query("INSERT INTO oppimisymparisto.yritys VALUES ('$sessio_id', '$teht3', '$yrityksia')");
	     	$riveja = pg_query("SELECT * FROM oppimisymparisto.yritys WHERE Sessio_id = $sessio_id AND Tehtavan_id = $teht3");
		 	if(pg_num_rows($riveja) > 2){
		 		$_SESSION['yrityksia'] = 0;
		 		$_SESSION['monesko']=$_SESSION['monesko']+1;
		 		$date = date('Y-m-d H:i:s');
	            pg_query("UPDATE oppimisymparisto.sessio SET Teht_loppu = '$date' WHERE Id='$sessio_id'");
		 		header('Location: lopetus.php');
		 	}
	    }
	    else{
	     	if ($vastaus_valuet === $syote_valuet) {
	 			//SiirrytÃ¤Ã¤n seuraavaan tehtÃ¤vÃ¤Ã¤n
		     	pg_query("UPDATE oppimisymparisto.sessio SET Teht_oikein = Teht_oikein + 1 WHERE Id='$sessio_id'");
	            $_SESSION['yrityksia'] = 0;
	            $_SESSION['monesko']=$_SESSION['monesko']+1;
	            $date = date('Y-m-d H:i:s');
	            pg_query("UPDATE oppimisymparisto.sessio SET Teht_loppu = '$date' WHERE Id='$sessio_id'");
	            header('Location: lopetus.php');
	        } else {
		 		// PidetÃ¤Ã¤n lukua vastauskerroista
		 		$_SESSION['yrityksia'] = $_SESSION['yrityksia'] + 1;
		 		$yrityksia = $_SESSION['yrityksia'];
				pg_query("INSERT INTO oppimisymparisto.yritys VALUES ('$sessio_id', '$teht3', '$yrityksia')");
		 		echo "Vastaus on vÃ¤Ã¤rÃ¤, yritÃ¤ uudelleen";
		 		// Jos vastauksia on ollut alle 3, ladataan tehtÃ¤vÃ¤ uusiksi
				// muuten siirrytÃ¤Ã¤n seuraavaan tehtÃ¤vÃ¤Ã¤n
		 		$riveja = pg_query("SELECT * FROM oppimisymparisto.yritys WHERE Sessio_id = '$sessio_id' AND Tehtavan_id = '$teht3'");
		 		if(pg_num_rows($riveja) > 2){
		 			$_SESSION['yrityksia'] = 0;
		 			$_SESSION['monesko']=$_SESSION['monesko']+1;
		 			$date = date('Y-m-d H:i:s');
	            	pg_query("UPDATE oppimisymparisto.sessio SET Teht_loppu = '$date' WHERE Id='$sessio_id'");
		 			header('Location: lopetus.php');
		 		}
	        }
	    }
    }

?>

<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>Kolmas tehtävä</title>
    </head>
    <body>
		<p>
			Lue tehtävänanto ja kirjoita vastauksesi alla olevaan tekstikenttään. Vastauksen syntaksi tulee olla oikea pääosin, 
			mutta isoja ja pieniä kirjaimia ei tarkisteta. Vastausyrityksiä on kolme, jonka jälkeen siirryt automaattisesti
			seuraavaan tehtävään.
		</p>
		<?php
			$esimkanta1 = pg_query("SELECT * FROM opiskelija");
			$esimkanta2 = pg_query("SELECT * FROM kurssi");
			$esimkanta3 = pg_query("SELECT * FROM suoritukset");
			echo "opiskelija";
			echo "<table border=1>";
			echo "<tr><th>nro</th><th>nimi</th><th>p_aine</th></tr>";
			echo "<br />\n";
			while ($row = pg_fetch_row($esimkanta1)) {
				echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td></tr>";
			}
			echo "</table>";
			echo "<br />\n";
			echo "kurssi";
			echo "<table border=1>";
			echo "<tr><th>id</th><th>nimi</th><th>opettaja</th></tr>";
			while ($row = pg_fetch_row($esimkanta2)) {
				echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td></tr>";
			}
			echo "</table>";
			echo "<br />\n";
			echo "suoritukset";
			echo "<table border=1>";
			echo "<tr><th>k_id</th><th>op_nro</th><th>arvosana</th></tr>";
			while ($row = pg_fetch_row($esimkanta3)) {
				echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td></tr>";
			}
			echo "</table>";
			echo "<br />\n";
			echo $tehtava_kuvaus[0];
			echo "<br />\n";
		?>
        <form method="post" action="tehtava3.php">
			<label>Vastauksesi:</label><br />
            <textarea name="syote" rows="10" cols="50"></textarea><br />
            <input type="submit" name ="syote1" value="Lisää"/>
        </form>
    </body>
</html>
<?php pg_close($conn); ?>