<!DOCTYPE html>

<?php

    session_start();
	include 'connection.php';

    // Luetaan syote ja lisätään tietokantaan.
    if (isset($_POST['rekisteroidy'])) {
		$nimi = $_POST['nimi'];
		$tunnus = $_POST['tunnus'];
		pg_query("INSERT INTO opettaja VALUES ($nimi, $tunnus);");

        // ja siirrytään omien tietojen sivulle
        header('Location: etusivu.php');
    }
	

?>

<html>
    <head>
        <meta charset="utf-8" />
        <link href="/style.css" rel="stylesheet" />
        <title>Rekisteröidy</title>
		<link href="tyylit.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <form method="post">
            <table>
                <tr><td>Nimi: </td><td><input type="text" name="nimi" value=""/></td></tr>
                <tr><td>Tunnus: </td><td><input type="text" name="op_nro" value=""/></td></tr>
                <tr><td></td><td><input type="submit" name="rekisteroidy" value="Jatka"/></td></tr>
            </table>
        </form>
    </body>
</html>