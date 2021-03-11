<?php
session_start();
// Connection info -> needs to be changed in case other connection infos are used
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'weight-tracker-diary';
// Try and connect to the given database by using the connection info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// Display error in case the connection could not be created
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Check if the data from the login form was submitted (username and password), isset() checks if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Show erroror if not all data is provided (username or password is missing)
	exit('Please fill both the username and password fields!');
}
// Prepare SQL query -> preparing the SQL statement prevents SQL injection! -> data security
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // If the account exists-> password verification 
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has loggedin!
            // Create a new session if the user is logged in -> remember the user data on the server
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: home.php');
        } else {
            // -> Incorrect password -> error showen
            echo 'Incorrect username and/or password!';
        }
    } else {
        // -> Incorrect username -> error to tell the user the problem
        echo 'Incorrect username and/or password!';
    }
	$stmt->close();
}
?>