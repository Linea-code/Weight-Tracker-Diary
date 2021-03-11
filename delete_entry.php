<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.html');
	exit;
}

//database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
//if connection not possible -> show error message
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// delete SQL command to delete one entry from the database 
$stmt = $con->prepare ("DELETE FROM diary_entries WHERE user_id = ? AND date=?");
//using the data passed on through the URL
$thisdate = $_GET['date'];
$stmt->bind_param("is", $_SESSION['id'],$thisdate);
$stmt->execute();
//redirect to the Home-Page /overview 
echo header('Location: home.php');

?>