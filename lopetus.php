<!DOCTYPE html>

<?php

	session_start();
    $conn = pg_connect("host=dbstud2.sis.uta.fi port=5432 dbname=pd98589 user=pd98589 password=5opwfah10");
	
	if(isset($_POST['kirjaudu_ulos'])) {
		if(session_destroy()) {
			header("Location: etusivu.php");
		}
	}

?>

<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>Lopetus</title>
		<link href="tyylit.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <p>
			Yrityskerta päättynyt. Näet alla tiedot päättyneestä sessiosta.
		</p>
		<?php
			$sessio_id = $_SESSION['sessioid'];
			$sessio = pg_query("SELECT Suorittaja, Teth_alku, Teht_loppu, Teht_oikein FROM oppimisymparisto.sessio WHERE Id=$sessio_id");
			echo "Sessio";
			echo "<table border=1>";
			echo "<tr><th>Suorittaja</th><th>Aloitus</th><th>Lopetus</th><th>Tehtäviä oikein</th></tr>";
			$row = pg_fetch_row($sessio);
			echo "<tr><td>$row[0]</td> <td>$row[1]</td> <td>$row[2]</td> <td>$row[3]</td></tr>";
			echo "</table>";
			echo "<br />\n";
		?>
		<form method="post" action="">
			<input type="submit" name="kirjaudu_ulos" value="Uloskirjautuminen"/>
		</form>
    </body>
</html>
<?php pg_close($conn); ?>