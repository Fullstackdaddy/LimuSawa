<?php # Script 10.3 - edit_user.php
// This page is for editing a user record.
// This page is accessed through view_users.php.

$page_title = 'Edit Member Information';

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

echo '<h1>Edit Member Information</h1>';

// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.php
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include('includes/footer.html');
	exit();
}

//try to connect to db
try {
    require('../../db/ullman_connect/sitename_config.php'); 
} catch (PDOException $e) {
    echo "Failed to connect to database. Please try again";
}

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    $errors = [];

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
            $em = htmlspecialchars(trim($_POST['email']));
    }

    // If everything's OK, Test for a unique email address
    if (empty($errors)) {

        $sql = "SELECT user_id FROM sitename.users "
                . "WHERE email= ? AND user_id = ? "
                . "AND first_name = ? AND last_name=?";

        try {
            $stmt = $pdo->prepare($sql); // Prepare the SQL statement
            $stmt->execute([$em, $id, $fn, $ln]); // Execute the statement with parameters
        } catch (PDOException $e) {
            echo "Could not execute query. Please contact admin.";
        }     

        $ecount = $stmt->fetchColumn();


        //  Test for unique email address:
        if ($ecount == 0) {

            // Create the Update Query:
            $sql = "UPDATE sitename.users SET first_name=?, last_name=?, email=? "
                    . "WHERE user_id=? LIMIT 1";

            try {
                $stmt = $pdo->prepare($sql); // Prepare the SQL statement
                $stmt->execute([$fn, $ln, $em, $id]); // Execute the statement with parameters
            } catch (PDOException $e) {
                echo "Could not execute query. Please contact admin.";
            }     

                if ($stmt->rowCount() == 1) { // If it ran OK.
                    // Print a message:
                    echo '<p class="success">The user information was successfully updated.</p>';

                } else { // If it did not run OK.
                        echo '<p class="error">The user could not be edited '
                                 . 'due to a system error. We apologize for '
                                . 'any inconvenience.'
                            . '</p>'; // Public message
                }

        } else { // Already registered.
                echo '<p class="error">The email address has already been registered.</p>';
        }

    } else { // Report the errors.

            echo '<p class="error">The following error(s) occurred:<br>';
            foreach ($errors as $msg) { // Print each error.
                    echo " - $msg<br>\n";
            }
            echo '</p><p class="error">Please try again.</p>';

    } // End of if (empty($errors)) IF.

} // End of submit conditional.

// Always show the form...

// Retrieve the user's information:
$sql = "SELECT first_name, last_name, email FROM sitename.users WHERE user_id=?";
try {
    $stmt = $pdo->prepare($sql); // Prepare the SQL statement
    $stmt->execute([$id]); // Execute the statement with parameters
} catch (PDOException $e) {
    echo "Could not execute query. Please contact admin.";
}  

if ($stmt->rowCount() == 1) { // Valid user ID, show the form.

    // Get the user's information:
    $row = $stmt->fetch(PDO::FETCH_NUM);

    // Create the form:
    echo "<form action='edit_user.php' method='post' onsubmit='return updateUser()'>
        <p>First Name: <input type='text' name='first_name' size='15' maxlength='15' 
            value='" . htmlspecialchars($row[0]) . "'></p>
        <p>Last Name: <input type='text' name='last_name' size='15' maxlength='30' 
            value='" . htmlspecialchars($row[1]) . "'></p>
        <p>Email Address: <input type='email' name='email' size='20' maxlength='60' 
            value='" . htmlspecialchars($row[2]) . "'></p>
        <p><input type='submit' name='submit' value='Submit'></p>
        <input type='hidden' name='id' value='{$id}'>
        </form>";

} else { // Not a valid user ID.
	echo '<p class="error">This page has been accessed in error.</p>';
}

include('includes/footer.html');
?>