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
//if connection not possoble: Error message
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//SQL query to get all weight data (weight plus date of measurement)
$stmt = $con->prepare ("SELECT date, weight FROM diary_entries WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

// creating the data needed to pass on to the Morris Chart -> Date + label and weight + label --> done by looping over the SQL result
$chart_data = '';
while($row = mysqli_fetch_array($result))
{
	$chart_data .="{ date:'".$row["date"]."', weight:".$row["weight"]."}, ";
}
$chart_data = substr ($chart_data, 0, -2);

//define today
$today = new DateTime();

//define the showen month of the calender (if nothing passed on by the url we use the actual month(default))
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
				<?php if($chart_data !=""){echo "<div class='navlinks'><a href='statistics.php'><i class='fas fa-chart-line'></i>Statistics</a></div>";}?>
				<div class="navlinks"><a href=<?php echo "daily_questionnaire_steps.php?date=".$today->format('Y-m-d') ?>><i class="fas fa-question"></i>Quest</a></div>
				<div class="navlinks"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></div>
			</div>
		</nav>

		<div class="flexbox">
			<!-- creating the calendar by using php to generate HTML elements based on the given data from the database and the URL -->
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
					<div>
						<!-- creating the month -->
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
					<!-- creating the weekdays -->
					<div class="weekdays">
					<div>Sun</div>
					<div>Mon</div>
					<div>Tue</div>
					<div>Wed</div>
					<div>Thu</div>
					<div>Fri</div>
					<div>Sat</div>
					</div>
					<!-- creating all days one by one -->
					<div class="days">
						<?php
						
						$prevLastDay = new DateTime("last day of last month");
						// days from the last month
						for($i = (int) $date->format('w') ; $i > 0; $i--){
						
							echo '<div class="date prev-date">'.((int) $prevLastDay->format('d') - $i +1).'</div>';
						}
						// days from the actual month 
						for($i =1; $i <= (int) $date->format('t'); $i++) {
							// if the day is today -> HTML element with a link to create a new entry
							if(($i == (int) $today->format('d')) and ((int) $date->format('m')) == ((int) $today->format('m'))) {
								echo '<a href="daily_questionnaire_steps.php?date='.$today->format('Y-m-d').'" class="date today"><div> '.$i.'</div></a>';
							} else {
								// if the day is not today --> look if an entry already excists for this day 
								$stmt = $con->prepare ("SELECT score FROM diary_entries WHERE user_id = ? AND date = ?");
								$thisday = clone $date;
								$thisday->setDate($date->format('Y'), $date->format('m'), $i);
								$thisday_formatted = $thisday->format('Y-m-d');
								$stmt->bind_param("is", $_SESSION['id'], $thisday_formatted);
								$stmt->execute();
								$color_array = $stmt->get_result();
								$color = null;
								// define the color for days in the past were an entry is available -> green good and rend bad etc.
								while($row = mysqli_fetch_array($color_array))
								{
								if($row['score'] > 7.5){$color = 'darkgreen';}
								elseif($row['score'] > 5){$color = 'green';}
								elseif($row['score'] > 2.5){$color = 'orange';}
								elseif($row['score'] > 0){$color = 'red';}
								}
								
								if($color){
									//for all days in the past with an existing entry -> day is presented in the color based on the score and field is clickable to reach the entry 
									echo '<a href="visit_entry.php?date='.$thisday_formatted.'" class="'.$color.' date"><div>'.$i.'</div></a>';
								}
								elseif($today >= $thisday){ 
									// clickable field for days in the past -> enable to create diary entries for the past (if a user forgot to create an entry)
									echo '<a href="daily_questionnaire_steps.php?date='.$thisday_formatted.'" class="date"><div>'.$i.'</div></a>';
								}
								else{
									//if day is in the future -> HTML element not clickable -> prevent wrong entries
									echo '<a class="date"><div>'.$i.'</div></a> ';
								}
							}
						}

						$lastDay = new DateTime($date->format('Y-m-t'));
						// days in the puture (next  month)
						for($i= 1; $i <= (6 - (int) $lastDay->format('w')); $i++){
							echo '<div class="date next-date">'.$i.'</div>';
						}
						?>
					</div>
				</div>
			</div>	
			<?php
			// if no entrys exists = no weight data : default page -> otherwise -> show diagram
			if ($chart_data == ""){
				echo " <h1> Add a new diary entry to see your weight <br> and how you improve! <br> Your weight development will be depicted in a diagram <br> after you create the first entry. <br> Also the menu point  <i class='fas fa-chart-line'></i>  Statistics will be available <br> if you provided some information!</h1>";
			}
			else{
				// using Morris Chart to present a diagram of the weight data -> clickable -> reach the statistics page 
				echo "<div class='line_chart'>
							<h1 class='statistic_header'>Weight - Overview</h1>
							<a href='statistics.php'>
								<div id='chart'></div>
							</a>
						</div>";
			}
			?>
		</div>
	<!-- delete account function -->
	<div class="delete_account">
		<div class="delete">
			<a class="delete" href="delete_account.php"><i class="fas fa-trash-alt"></i> Delete Account</a>
		</div>
	</div>
		
	<div class="footer">
			<p> &copy; Copyright 2021 | Linea Schmidt, Simon Shabo
				<a href="About_this_website.html"> About this website </a>
			</p>
	</div>
		
	<script> 
	// Morris Chart -> Area chart in the colors of the website
		Morris.Area({
			element: 'chart',
			data:[<?php echo $chart_data; ?>],
			parseTime: false,
			fillOpacity: 0.1,
			xkey: 'date',
			xLabelAngle: 60,
			xLabels: "month",
			ykeys: ['weight'],
			labels: ['Weight'],
			hideHover:'auto',
			lineColors:['#3BBBB3'],

		});
		
	</script>

	
  </body>
	</body>
</html>