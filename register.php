<?php # Script 9.3 - register.php
// This script performs an INSERT query to add a record to the users table.

$page_title = 'Register';

require ('includes/login_functions.php');

// Check if the user is logged in
if (isset($_COOKIE['user_id'])) {
    // User is logged in, include the logged-in header
    include('includes/header.html');
} else {
    // User is not logged in, include the basic header
    include('includes/welcome_header.html');
    redirect_user();
}

//try to connect to db
try {
    require('../../db/ullman_connect/sitename_config.php'); 
} catch (PDOException $e) {
    echo "Failed to connect to database. Please try again";
}


// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = []; // Initialize an error array.

    // Check for a first name:
    if (empty($_POST['first_name'])) {
            $errors[] = 'You forgot to enter your first name.';
    } else {
            $fn = htmlspecialchars(trim($_POST['first_name']));
    }

    // Check for a last name:
    if (empty($_POST['last_name'])) {
            $errors[] = 'You forgot to enter your last name.';
    } else {
            $ln = htmlspecialchars(trim($_POST['last_name']));
    }

    // Check for an email address:
    if (empty($_POST['email'])) {
            $errors[] = 'You forgot to enter your email address.';
    } else {
            $em = trim($_POST['email']);
            
            // Check if the email already exists in the db before registering
            $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM sitename.users WHERE email = ?");
            $stmt1->execute([$em]);
            $ecount = $stmt1->fetchColumn();

            // Check if the email already exists
            if ($ecount > 0) {
                $errors[] = 'This email address is already in use.';
            }      
            
    }

    // Check for a password and match against the confirmed password:
    if (!empty($_POST['pass1'])) {
            if ($_POST['pass1'] != $_POST['pass2']) {
                    $errors[] = 'Your password did not match the confirmed password.';
            } else {
                    $p = trim($_POST['pass1']);
            }
    } else {
            $errors[] = 'You forgot to enter your password.';
    }

    if (empty($errors)) { // If everything's OK.

        // Register the user in the database...

        
        // Create the query:
        $sql = "INSERT INTO sitename.users (first_name, last_name, email, pass, registration_date) "
                . "VALUES (?, ?, ?, SHA2(?, 512), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fn, $ln, $em, $p]);

        if ($stmt) { // If it ran OK.

                // Print a message:
                echo '<h1>Thank you!</h1>
        <p class="success">You are now registered with SimpReg! Soon you will actually be 
           able to log in!</p><p><br></p>';

        } else { // If it did not run OK.

                // Public message:
                echo '<h1>System Error</h1>
                <p class="error">You could not be registered due to a system error.
                We apologize for any inconvenience.</p>';

                // Debugging message:
                echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $q . '</p>';

        } // End of if ($r) IF.

        // Include the footer and quit the script:
        include('includes/footer.html');
        exit();

	} else { // Report the errors.

            echo '<h1>Error!</h1>
            <p class="error">The following error(s) occurred:<br>';
            foreach ($errors as $msg) { // Print each error.
                    echo " - $msg<br>\n";
            }
            echo '</p><p class="error">Please try again.</p><p><br></p>';

	} // End of if (empty($errors)) IF.

} // End of the main Submit conditional.
?>

<!-- The HTML form -->
<h1>Register New User</h1>
<form id="registrationForm" action="register.php" method="post">
    <p>
        First Name: 
        <input type="text" name="first_name" id="fname" size="15" maxlength="20" 
               value="<?php if (isset($_POST['first_name'])) {echo $_POST['first_name'];} ?>" autofocus required>
    </p>
    <p>
        Last Name: 
        <input type="text" name="last_name" id="lname" size="15" maxlength="40" 
               value="<?php if (isset($_POST['last_name'])) {echo $_POST['last_name'];} ?>" required>
    </p>
    <p>
        Email Address: 
        <input type="email" name="email" id="email" size="20" maxlength="60" 
               value="<?php if (isset($_POST['email'])) {echo $_POST['email'];} ?>" required>
    </p>
    <p>
        Password: 
        <input type="password" name="pass1" id="createpass" size="10" maxlength="20" 
               value="<?php if (isset($_POST['pass1'])) {echo $_POST['pass1'];} ?>" required>
    </p>
    <p>
        Confirm Password: 
        <input type="password" name="pass2" id="confirmpass" size="10" maxlength="20" 
               value="<?php if (isset($_POST['pass2'])) {echo $_POST['pass2'];} ?>" required>
    </p>
    <p>
        <input type="submit" name="submit" onclick="return validateReg()" value="Register">
    </p>
</form>
<?php include('includes/footer.html'); ?>