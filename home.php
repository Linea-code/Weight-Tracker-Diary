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

$today = new DateTime();

$date = new DateTime('first day of this month');
if (isset($_GET['date']) && DateTime::createFromFormat('Y-m',$_GET['date'])){
	$date = DateTime::createFromFormat('Y-m-d',$_GET['date'].'-01');
}
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
				<div class="navlinks"><a href="statistics.php"><i class="fas fa-chart-line"></i>Statistics</a></div>
				<div class="navlinks"><a href="daily_questionnaire.html"><i class="fas fa-question"></i>Quest</a></div>
				<div class="navlinks"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></div>
			</div>
		</nav>

		<div class="container">
			<div class="calendar">
				<div class="month">
					<a href=
						<?php 
							$prev= (clone $date)->sub(new DateInterval('P1M'));
							echo "home.php?date=".$prev->format('Y-m'); 
						?>
					>
						<i class="fas fa-angle-left"></i>
					</a>
				<div class="date">
					<h1><?php 
					$months = [
						"January",
						"February",
						"March",
						"April",
						"May",
						"June",
						"July",
						"August",
						"September",
						"October",
						"November",
						"December",
					  ];

					  echo $months[(((int) $date->format('m')) - 1) ];
					?></h1>
					<p><?php 
					echo $today->format('Y-m-d');
					?></p>
				</div>
				<a href=
					<?php 
						$next = (clone $date)->add(new DateInterval('P1M'));
						echo "home.php?date=".$next->format('Y-m'); 
					?>
				>
			    	<i class="fas fa-angle-right"></i>
				</a>
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
				<div class="days">
					<?php
					
					$prevLastDay = new DateTime("last day of last month");

					for($i = (int) $date->format('w') ; $i > 0; $i--){
					
						echo '<div class="prev-date">'.((int) $prevLastDay->format('d') - $i +1).'</div>';
					}

					for($i =1; $i <= (int) $date->format('t'); $i++) {
						if(($i == (int) $today->format('d')) and ((int) $date->format('m')) == ((int) $today->format('m'))) {
							echo '<div class="today"> <a href="daily_questionnaire.html" class="fill-calendarday">'.$i.'</a></div>';
						} else {
							$stmt = $con->prepare ("SELECT score FROM diary_entries WHERE user_id = ? AND date = ?");
							$thisday = clone $date;
							$thisday->setDate($date->format('Y'), $date->format('m'), $i);
							$thisday = $thisday->format('Y-m-d');
							$stmt->bind_param("is", $_SESSION['id'], $thisday );
							$stmt->execute();
							$color_array = $stmt->get_result();
							$color='no_entry';

							while($row = mysqli_fetch_array($color_array))
							{
							if($row['score'] >= 7.5){$color = 'darkgreen';}
							elseif($row['score'] >= 5){$color = 'green';}
							elseif($row['score'] >= 2.5){$color = 'orange';}
							elseif($row['score'] > 0){$color = 'red';}
							}
							
							if($color !='no_entry'){
								echo '<div class="'.$color.'"><a href="visit_entry.php?date='.$thisday.'">'.$i.'</a></div>';
							}
							else{
								echo '<div class="'.$color.'">'.$i.'</div> ';
							}
						}
					}

					$lastDay = new DateTime($date->format('Y-m-t'));

					for($i= 1; $i <= (6 - (int) $lastDay->format('w')); $i++){
						echo '<div class="next-date">'.$i.'</div>';
					}
					?>
				</div>
			</div>
		</div>
		<a href="statistics.php"></i><div id="chart" class= "line_chart"> </div></a>

	<!-- <script src="calendar.js"></script> -->
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
	<div class="footer">
			<p> © Copyright 2021 | Linea Schmidt, Simon Shabo
				<a href="About this website.html"> About this website </a>
			</p>
	</div>
  </body>
	</body>
</html>