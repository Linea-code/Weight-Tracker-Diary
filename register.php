<?php
// database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	//Error if no connection possible
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Check if all field are filled in 
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
	// Error if a field is missing
	exit('Please complete the registration form!');
}
// Check if one of the values is empty
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}
// Check if the account with that username exists and validate the input Data 
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	exit('Email is not valid!');
}
if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    exit('Username is not valid!');
}
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	exit('Password must be between 5 and 20 characters long!');
}
if ($_POST['password'] != $_POST['confirm_password']){
	exit('Both passwords (password and confirmed password) must be the same!');
}

// Check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result to check if the account exists in the database
	if ($stmt->num_rows > 0) {
		// If username already exists -> error so that user could try another one
		echo 'Username exists, please choose another!';
	} else {
		// If username does not exists --> create new account
        if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
            // Passords are hashed to do not show them in the database
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
            $stmt->execute();
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: home.php');
        } else {
            // Some error occured
            echo 'Could not prepare statement!';
        }
	}
	$stmt->close();
} else {
	// Some error occured
	echo 'Could not prepare statement!';
}
$con->close();
?>