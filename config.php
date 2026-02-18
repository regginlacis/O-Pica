<?php
// Sākotnējais konfigurācijas fails
// Piemērs: datubāzes pieslēguma parametri
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "opica";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Savienojuma kļūda: " . $conn->connect_error);
}

define('ADMIN_PASSWORD', 'parole123');
