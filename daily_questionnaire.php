<?php
session_start();
// If the user is not logged in redirect to the login page:
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.html');
	exit;
}

// Database connection 
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// show error if no connection is possible
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
$day_attribute= '';
//SQL command to insert a new diary entry -> pushing all values from the form to the database
$stmt = $con->prepare("INSERT INTO diary_entries (user_id, date, feeling, sleep, sleep_time, sports, sports_kind, weight, individual_entry, score)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssissdsd", $user_id, $date, $feeling, $sleep, $sleep_time, $sports, $sports_kind, $weight, $individual_entry, $score);

//Accessing all values from the HTML form (questionnaire)
$user_id = $_SESSION['id'];
$date = $_GET['date'];
$feeling = $_POST['feeling'];
$sleep = $_POST['sleep'];
$sleep_time = $_POST['sleep_time'];
$sports = $_POST['sports'];
//setting a defeault for not required questions -> make shure all field in the databse are filled in correctly
if (array_key_exists('sports_kind', $_POST)){
    if ($_POST['sports_kind'] == NULL) {$sports_kind = $_POST['other'];} 
    else {$sports_kind = $_POST['sports_kind'];}
    }
else{$sports_kind = NULL;}
$weight = $_POST['weight'];
//only pushing a personal entry if the user typed in a text -> otherwise NULL
if (array_key_exists('individual_entry', $_POST)){
    $individual_entry = $_POST['individual_entry'];}
else{$individual_entry = NULL;}

//Defining a sleep score
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

// scorring the overal well-being 
$score = $_POST['feeling'] + ($_POST['sports'] * 2) + (($_POST['sleep'] + $sleep_time_score)/2);

// executing the SQL Insert command
$stmt->execute();

//defining a personal feedback based on the calculated score
if($score > 7.5){$day_attribute="Seems like today was a very good day! Congratulations! Keep going like this. Todays score is amazing and you had ".$sleep_time." hours of sleep tonight.";}
elseif($score> 5){$day_attribute ="Seems like today was a good day! Nice! Keep going and stay motivated. Todays score is good and you had ".$sleep_time." hours of sleep tonight.";}
elseif($score > 2.5){$day_attribute="Seems like today was not the best day in your life... but never give up and stay motivated. Tomorrow is a new chance: use it! Maybe you could also improve your ".$sleep_time." hours of sleep tonight.";}
elseif($score > 0){$day_attribute="Seems like today was horrible... but never give up, stay motivated and think of your goals. Tomorrow is a new chance: use it to improve yourself! Maybe there are also more than ".$sleep_time." hours of sleep waiting for you this night.";}

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
		<div class="header">
			<div class="ellipses">
				<div class="ellipse9"></div>
				<div class="ellipse10"></div>
				<div class="ellipse11"></div>
			</div>
			<div class="ellipse12"></div>
		</div>
		<nav class="navtop">
			<div>
				<div class="navlinks"><a href="home.php"><i class="fas fa-home"></i>Home</a></div>
				<div class='navlinks'><a href='statistics.php'><i class='fas fa-chart-line'></i>Statistics</a></div>
				<div class="navlinks"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></div>
			</div>
		</nav>
	<!-- show the personal feedback after calculating the score via PHP -->
		<div class="content">
			<h2>Thank you,  <?=$_SESSION['name']?>!</h2>
			<p><?= $day_attribute ?></p>
		</div>
		<div class="footer">
        <p> &copy; Copyright 2021 | Linea Schmidt, Simon Shabo
            <a href="About_this_website.html"> About this website</a>
        </p>
    </div>
	</body>
</html>