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
$chart_data = '';

while($row = mysqli_fetch_array($result))
{
	$chart_data .="{ date:'".$row["date"]."', score:".$row["weight"]."}, ";
}
$chart_data = substr ($chart_data, 0, -2);


$stmt = $con->prepare ("SELECT score FROM diary_entries WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result2 = $stmt->get_result();
$donut = '';

$verygood = 0;
$good = 0;
$moderate = 0;
$bad = 0;
$noscore = 0;

while($row = mysqli_fetch_array($result2))
{
	if($row['score'] >= 7.5){$verygood += 1;}
	elseif($row['score'] >= 5){$good += 1;}
	elseif($row['score'] >= 2.5){$moderate += 1;}
	elseif($row['score'] > 0){$bad += 1;}
	else{$noscore += 1;}
}

/* $donut_array= mysqli_fetch_array($result2);
foreach($donut_array as $value):{
	if($value >= 7.5){$verygood += 1;}
	elseif($value >= 5){$good += 1;}
	elseif($value >= 2.5){$moderate += 1;}
	elseif($value > 0){$bad += 1;}
	else{$noscore += 1;}
}endforeach; */

	
$donut .="{ label:'Very Good Days', value:".$verygood."}, ";
$donut .="{ label:'Good Days', value:".$good."}, ";
$donut .="{ label:'Moderate Days', value:".$moderate."}, ";
$donut .="{ label:'Bad Days', value:".$bad."}, ";
$donut .="{ label:'No Rating Available', value:".$noscore."}, ";

$donut = substr ($donut, 0, -2);

$stmt = $con->prepare ("SELECT  sleep_time, sports  FROM diary_entries WHERE date >= DATE(NOW()) - INTERVAL 6 DAY AND user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result3 = $stmt->get_result();
$avg_sleep_data = '';
$sports_data='';
$number_of_entries ='';

$avg_sleep_time = 0;
$count_sleep_data = 0;
$sport_amount = 0;
$number_of_dates= 0;

while($row = mysqli_fetch_array($result3))
{
	$avg_sleep_time += $row['sleep_time'];
	$count_sleep_data += 1;
	$sport_amount += $row['sports'];
	$number_of_dates +=1;
}

$avg_sleep_time = $avg_sleep_time/$count_sleep_data;

$avg_sleep_data .="{ label:'Average Sleep Time', value:".$avg_sleep_time."}, ";

$avg_sleep_data = substr ($avg_sleep_data, 0, -2);



$sports_data .="{ label:'Sport Sessions', value:".$sport_amount."}, ";

$sports_data = substr ($sports_data, 0, -2);


$number_of_entries .="{ label:'Diary Entries', value:".$number_of_dates."}, ";

$number_of_entries = substr ($number_of_entries, 0, -2);



$stmt = $con->prepare ("SELECT  sports.sports_kind, count(sports.sports_kind)  FROM sports, diary_entries WHERE sports.id = diary_entries.sports_kind AND date >= DATE(NOW()) - INTERVAL 6 DAY AND user_id = ? GROUP BY sports.sports_kind");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result4 = $stmt->get_result();
$sports_kind = '';

while($row = mysqli_fetch_array($result4))
{
	$sports_kind .="{ label:'".$row["sports_kind"]."', value:".$row[1]."}, ";
}
$sports_kind = substr ($sports_kind, 0, -2);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Statistics</title>
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
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="daily_questionnaire.html"><i class="fas fa-question"></i>Quest</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>		
		<div class="overall_stat"> 
			<h1>Your overall Statistic</h1>
			<div id="chart" class= "line_chart"></div>
			<div id="donut" class= "donut_chart"> </div>
		</div>
			
		<div class="last_week_stat"> 
			<h1>Your last week</h1>
			<div id="num_of_entries" class= "donut_sports"> </div>
			<div id="avg_sleep" class= "donut_sleep"> </div>
			<div id="sports" class= "donut_sports"> </div>
			<div id="sports_kind" class= "donut_sports"> </div>
		</div>
		

	
	<script> 
		
		Morris.Line({
			element: 'chart',
			data:[<?php echo $chart_data; ?>],
			parseTime: false,
			xkey: 'date',
			xLabelAngle: 60,
			xLabels: "month",
			ykeys: ['score'],
			labels: ['Score'],
			hideHover:'auto',
			lineColors:['#3BBBB3'],
			resize: false,
		});

		Morris.Donut({
			element: 'donut',
			data:[<?php echo $donut; ?>],
			colors: ["darkgreen", "lightgreen", "orange", "red", "gray"],
			resize: false,
		});

		Morris.Donut({
			element: 'avg_sleep',
			data:[<?php echo $avg_sleep_data; ?>],
			colors:["#377AAF"],
		});
		Morris.Donut({
			element: 'sports',
			data:[<?php echo $sports_data; ?>],
			colors:["#3BBBB3"],
		});
		Morris.Donut({
			element: 'num_of_entries',
			data:[<?php echo $number_of_entries; ?>],
			colors:["#3BBBB3"],
		});
		
		Morris.Donut({
			element: 'sports_kind',
			data:[<?php echo $sports_kind; ?>],
			colors: ["darkgreen", "lightgreen", "orange", "red"],
			resize: false,
		});
	</script>
		
	</body>
	
</html>