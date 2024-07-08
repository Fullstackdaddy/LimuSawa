<?php # Script 12.4 - loggedin.php
// The user is redirected here from login.php.




// If no cookie is present, redirect the user:
if (!isset($_COOKIE['user_id'])) {

    // Need the page to call the  redirect_user function:
    $url = 'http://localhost:8888/php_larryU_practice/index.php'; 
    header("Location: $url");
    exit();

}

// Set the page title and include the HTML header:
$page_title = 'Logged In!';
include('includes/header.html');

echo "<h4>Logged In!</h4>
<p>You are now logged in, {$_COOKIE['first_name']}!</p>
<p><a href=\"logout.php\">Logout</a></p>";

include('includes/footer.html');
?>
