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

$day_attribute= '';
$stmt = $con->prepare("SELECT * FROM diary_entries WHERE user_id = ? AND date = ?");
$thisdate = DateTime::createFromFormat('Y-m-d', $_GET['date'])->format('Y-m-d');
$stmt->bind_param("is", $_SESSION['id'], $thisdate );
$stmt->execute();
$result = $stmt->get_result();


while($row = mysqli_fetch_array($result))
{
    $weight= $row['weight'];
    $stmt = $con->prepare("SELECT rating FROM rating WHERE id = ?");
    $stmt->bind_param("i", $row['feeling'] );
    $stmt->execute();
    $feeling = $stmt->get_result();
    $feeling = mysqli_fetch_row($feeling);
    $feeling = implode($feeling);

    $stmt = $con->prepare("SELECT rating FROM rating WHERE id = ?");
    $stmt->bind_param("i", $row['sleep'] );
    $stmt->execute();
    $sleep_attribute = $stmt->get_result();
    $sleep_attribute = mysqli_fetch_row($sleep_attribute);
    $sleep_attribute = implode($sleep_attribute);
    
    $hours_sleep = $row['sleep_time'];

    if($row['sports']==1){
        $sports= 'did';
    }
    else{
        $sports = 'didn´t do';
    }
    if($row['sports_kind'] != ""){
        $stmt = $con->prepare("SELECT sports_kind FROM sports WHERE id = ?");
        $stmt->bind_param("i", $row['sports_kind'] );
        $stmt->execute();
        $sportskind = $stmt->get_result();
        $sportskind = mysqli_fetch_row($sportskind);
        $sportskind = ' ('.implode($sportskind).')';
    }
    else{$sportskind ='';}

    $personal_com = $row['individual_entry'];

}

$message = 'It was a '.$feeling.' day. In the neight you had '.$hours_sleep.' hours of '.$sleep_attribute.' sleep. You '.$sports.' sports'.$sportskind.'.';



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
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="statistics.php"><i class="fas fa-chart-line"></i>Statistics</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>		
		<div class="content">
            <h2><?=$thisdate?></h2>
            <p>Weight: <?= $weight ?> kg</p>            
            <p><?= $message ?></p>
            <?php
            if($personal_com != ''){
                echo '<p>Your note: '.$personal_com.'</p>';
            }
            ?>   
        </div>
        
        <div class="delete">
            <a href=<?php echo "delete_entry.php?date=".$thisdate ?>><i class="fas fa-trash-alt"></i> Delete Entry</a>
        </div>
	</body>
</html>