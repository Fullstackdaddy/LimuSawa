<?php # Script 10.2 - delete_user.php
// This page is for deleting a user record.
// This page is accessed through view_users.php.

$page_title = 'Delete a User';
require ('includes/login_functions.php');

// Check if the user is logged in
if (isset($_COOKIE['user_id'])) {
    // User is logged in, include the logged-in header
    include('includes/header.html');
} else {
    // User is not logged in, include the basic header, send user to homepage
    include('includes/welcome_header.html');
    redirect_user();
}

echo '<h1>Delete a User</h1>';

// Check for a valid user ID through GET or POST superglobal arrays:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.php
	$id = htmlspecialchars(trim($_GET['id']));
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = htmlspecialchars(trim($_POST['id']));
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include('includes/footer.html');
	exit(); // halt connectiona and exit
}

//Try to connect to db 
try {
    require('../../db/ullman_connect/sitename_config.php'); 
    } catch (PDOException $e) {
        echo "Failed to connect to database. Please try again";
    }

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['delete'] == 'Yes') { // Delete the record.

        // Create the query and prepared statement to delete the record
        $sql = "DELETE FROM sitename.users WHERE user_id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);

        // Execute the statement
        $stmt->execute([$id]);

        // Check if exactly one row was affected
        if ($stmt->rowCount() == 1) { // If it ran OK.

            // Print a message:
            echo '<p>The user has been deleted.</p>';

        } else { // If the query did not run OK.
                echo '<p class="error">The user could not be deleted due'
                    . ' to a system error.</p>'; // Public message.
                echo '<p>' . errorInfo() . '<br>Query: ' . $sql . '</p>'; // Debugging message.
        }

    } else { // No confirmation of deletion.
            echo '<p>The user has NOT been deleted.</p>';
    }

} else { // Show the form.

        // Retrieve the user's information:
        $sql = "SELECT CONCAT(last_name, ', ', first_name) FROM sitename.users "
                . "WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);

        // Execute the statement
        $stmt->execute([$id]);

        if ($stmt->rowCount() == 1) { // Valid user ID, show the form.

            // Get the user's information:
            $row = $stmt->fetch(PDO::FETCH_NUM);

            // Display the record being deleted:
            echo "<h3>Name: $row[0]</h3>
            You have elected to delete this user. Do you want to proceed?";

            // Create the form:
            echo '<form action="delete_user.php" method="post" onsubmit="return confirmDelete()">
            <input type="radio" name="delete" value="Yes"> Yes
            <input type="radio" name="delete" value="No" checked="checked"> No
            <input type="submit" name="submit" value="Delete">
            <input type="hidden" name="id" value="' . $id . '">
            </form>';

        } else { // Not a valid user ID.
                echo '<p class="error">This page has been accessed in error.</p>';
        }

} // End of the main submission conditional.

include('includes/footer.html');
?>