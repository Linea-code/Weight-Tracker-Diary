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

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="styles/main.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link rel="stylesheet" href="style.css" />
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                <a href="daily_questionnaire.html"><i class="fas fa-question"></i>Quest</a>
			</div>
		</nav>

		<div class="container">
			<div class="calendar">
				<div class="month">
				<i class="fas fa-angle-left prev"></i>
				<div class="date">
					<h1></h1>
					<p></p>
				</div>
				<i class="fas fa-angle-right next"></i>
				</div>
				<div class="weekdays">
				<div>Sun</div>
				<div>Mon</div>
				<div>Tue</div>
				<div>Wed</div>
				<div>Thu</div>
				<div>Fri</div>
				<div>Sat</div>
				</div>
				<div class="days"></div>
			</div>
		</div>

    <script src="calendar.js"></script>
  </body>
	</body>
</html>