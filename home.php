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
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$stmt = $con->prepare ("SELECT date, weight FROM diary_entries WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

/* $query = "SELECT date, weight FROM diary_entries WHERE user_id = 16";
$result = mysqli_query($con, $query); */
$chart_data = '';

while($row = mysqli_fetch_array($result))
{
	$chart_data .="{ date:'".$row["date"]."', weight:".$row["weight"]."}, ";
}
$chart_data = substr ($chart_data, 0, -2);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="styles/main.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
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
		
		<div id="chart" class= "chart"> </div>

	<script src="calendar.js"></script>
	<script> 
		
		Morris.Line({
			element: 'chart',
			data:[<?php echo $chart_data; ?>],
			parseTime: false,
			xkey: 'date',
			/* xLabelFormat: function(d) {
				return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov', 'Dec'][d.getMonth()] + ' ' + d.getDate();
			} */
			xLabelAngle: 60,
			xLabels: "month",
			ykeys: ['weight'],
			labels: ['Weight'],
			hideHover:'auto',
			lineColors:['#3BBBB3']
		});
		
	</script>
  </body>
	</body>
</html>