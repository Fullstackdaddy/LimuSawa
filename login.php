<?php 
// Display the form:
// Check if the form has been submitted:
$page_title = "Login"; 
include('includes/welcome_header.html');
include('includes/login_functions.php');

// Check for form submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Minimal form validation:
    if (!empty($_POST['email']) && !empty($_POST['pass'])) {

        // Need the database connection:
        require('../../db/ullman_connect/sitename_config.php');

        // Check the login:
        list($check, $data) = check_login($pdo, $_POST['email'], $_POST['pass']);

        if ($check) { // OK!

            // Set the cookies:
            setcookie('user_id', $data['user_id'], 0, '/', '', 0, 0);
            setcookie('first_name', $data['first_name'], 0, '/', '', 0, 0);

            // Call the redirect_user function to make a decision
            redirect_user('loggedin.php');

        } else { // Unsuccessful!
            // Display errors:
            foreach ($data as $error) {
                    echo '<h1>Error!</h1>';
                    echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
        }
    } else {   
        echo '<h1>Error!</h1>';
        echo '<p class="error">Please complete all fields. Try again.</p>';
    } 
} 
?>

<!-- The HTML Login Form -->
<h1>Login</h1>
<form action="login.php" method="post">
    <p>Email Address: <input type="email" name="email" id="email" size="20" maxlength="60"> </p>
    <p>Password: <input type="password" name="pass" id="pass" size="20" maxlength="20"></p>
    <p><input type="submit" name="submit" value="Login" onclick="return login()"></p>
</form>

<?php include('includes/footer.html'); ?>