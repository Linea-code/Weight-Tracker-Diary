<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.html');
	exit;
}

//Database connection 
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
//Error if connection is not possible
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Delete account -> Delete SQL command 
$stmt = $con->prepare ("DELETE FROM accounts WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
//redirecting to the index-page
echo header('Location: index.html');

?>