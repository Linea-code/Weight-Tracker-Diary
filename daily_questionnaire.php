<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
/* // If the user is not registered do not open homepage
if ($account['activation_code'] == 'activated') {
	header('Location: index.html');
	exit;
}else{
    header('Location: not_registered.html');
	exit;
} */
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$stmt = $con->prepare("INSERT INTO diary_entries (user_id, date, feeling, sleep, sleep_time, sports, sports_kind, weight, individual_entry, score)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssissdsd", $user_id, $date, $feeling, $sleep, $sleep_time, $sports, $sports_kind, $weight, $individual_entry, $score);

$user_id = $_SESSION['id'];
$date = date("Y-m-d");
$feeling = $_POST['feeling'];
$sleep = $_POST['sleep'];
$sleep_time = $_POST['sleep_time'];
$sports = $_POST['sports'];
if (array_key_exists('sports_kind', $_POST)){
    if ($_POST['sports_kind'] == NULL) {$sports_kind = $_POST['other'];} 
    else {$sports_kind = $_POST['sports_kind'];}
    }
else{$sports_kind = NULL;}
$weight = $_POST['weight'];
if (array_key_exists('individual_entry', $_POST)){
    $individual_entry = $_POST['individual_entry'];}
else{$individual_entry = NULL;}

if ($_POST['sleep_time'] <= 3 || $_POST['sleep_time'] >= 13){
	$sleep_time_score = 0;
}
elseif ($_POST['sleep_time'] == 4 || $_POST['sleep_time'] == 12) {
	$sleep_time_score = 1;
}
elseif ($_POST['sleep_time'] == 5 || $_POST['sleep_time'] == 11) {
	$sleep_time_score = 2;
}
elseif ($_POST['sleep_time'] == 6 || $_POST['sleep_time'] == 10) {
	$sleep_time_score = 3;
}
else{$sleep_time_score = 4;}

$score = $_POST['feeling'] + ($_POST['sports'] * 2) + (($_POST['sleep'] + $sleep_time_score)/2);
$stmt->execute();



?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home</title>
		<link href="styles/main.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                <a href="daily_questionnaire.html">Quest</a>
                
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
		</div>
	</body>
</html>