<?php # Script 9.7 - password.php
// This page lets a user change their password.

$page_title = 'Change Your Password';
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

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //try to connect to db
    try {
        require('../../db/ullman_connect/sitename_config.php'); 
    } catch (PDOException $e) {
        echo "Failed to connect to database. Please try again";
    }
    
    $errors = []; // Initialize an error array.

    // Check for an email address:
    if (empty($_POST['email'])) {
            $errors[] = 'You forgot to enter your email address.';
    } else {
            $e = htmlspecialchars(trim($_POST['email']));
    }

    // Check for the current password:
    if (empty($_POST['pass'])) {
            $errors[] = 'You forgot to enter your current password.';
    } else {
            $p = htmlspecialchars(trim($_POST['pass']));
    }

    // Check for a new password and match
    // against the confirmed password:
    if (!empty($_POST['pass1'])) {
        if ($_POST['pass1'] != $_POST['pass2']) {
                $errors[] = 'Your new password did not match the confirmed '
                        . 'password.';
        } else {
                $np = htmlspecialchars(trim($_POST['pass1']));
        }
    } else {
            $errors[] = 'You forgot to enter your new password.';
    }

    if (empty($errors)) { // If everything's OK.
    // Check that they've entered the right email address/password combination:
        
        $sql = "SELECT user_id FROM sitename.users "
                . "WHERE (email=? AND pass=SHA2(?, 512))";

        try {
            $stmt = $pdo->prepare($sql); // Prepare the SQL statement
            $stmt->execute([$e, $p]); // Execute the statement with parameters
        } catch (PDOException $e) {
            echo "Could not execute query. Please contact admin.";
        }

        $users = $stmt->fetchAll(); // FetchAll returns result set as array
        $rowCount = count($users);

        if ($rowCount == 1) { // Match was made.

            $user_id = $users[0]['user_id']; // Get the user_id

            // Make the UPDATE query:
            $sql = "UPDATE sitename.users SET pass=SHA2(?, 512) WHERE user_id=?";
            try {
                $stmt = $pdo->prepare($sql);
                // Execute the statement with new password and user_id
                $stmt->execute([$np, $user_id]); 
            } catch (PDOException $e) {
                // If it did not run OK.
                // Public message:
                echo '<h1>System Error</h1>
                      <p class="error">Your password could not be changed due to a 
                      system error. We apologize for any inconvenience.</p>';
            }

            if ($stmt->rowCount() == 1) { // If the update was successful.

                // Print a message.
                echo '<h1>Thank you!</h1>
                      <p class="success">Your password has been updated. <br>
                      </p>';

            } else { // If it did not run OK.

                    // Public message:
                    echo '<h1>System Error</h1>
                    <p class="error">Your password could not be changed due to a 
                     system error. We apologize for any inconvenience.</p>';
                    }

                // Include the footer and quit the script (to not show the form).
                include('includes/footer.html');
                exit();

        } else { // Invalid email address/password combination.
                    echo '<h1>Error!</h1>
                    <p class="error">The email address and password do not match
                    those on file.</p>';
                }

    } else { // Report the errors.

            echo '<h1>Error!</h1>
            <p class="error">
                The following error(s) occurred:<br>';
            foreach ($errors as $msg) { // Print each error.
                    echo " - $msg<br>\n";
            }
            echo '</p><p class="error">Please try again.</p><p><br></p>';

    } // End of if (empty($errors)) IF.

} // End of the main Submit conditional.
?>

<!-- The HTML form. Sticky form to be precise -->
<h1>Change Your Password</h1>
<form id="updatePassword" action="password.php" method="post">
    <p>
        Email Address: 
        <input type="email" name="email" id="email" size="20" maxlength="60" 
               value="<?php if (isset($_POST['email'])) 
                                {echo $_POST['email'];} ?>" autofocus required>
    </p>
    <p>
        Current Password: 
        <input type="password" name="pass" id="currentpasswd" size="10" maxlength="20" 
               value="<?php if (isset($_POST['pass'])) 
                                {echo $_POST['pass'];} ?>" required>
    </p>
    <p>
        New Password: 
        <input type="password" name="pass1" id="newpasswd" size="10" maxlength="20" 
               value="<?php if (isset($_POST['pass1'])) 
                            {echo $_POST['pass1'];} ?>" required>
    </p>
    <p>Confirm New Password: 
        <input type="password" name="pass2" id="confirmnewpasswd" size="10" 
               maxlength="20" value="<?php if (isset($_POST['pass2'])) 
                                            {echo $_POST['pass2'];} ?>" required></p>
    <p>
        <input type="submit" name="submit" onclick=" return validatePass()" 
               value="Change Password">
    </p>
</form>

<?php include('includes/footer.html'); ?>