<?php
// Script 3.4 - index.php
// This script displays a welcome page and prompts the user to sign in if they are not logged in

$page_title = 'SimpReg Home!';
include('includes/welcome_header.html');

 
// Display welcome message if user is not logged in
if (!isset($_COOKIE['user_id'])) {
    echo '<h4>Welcome to SimpReg! Please login</h4>';
}

include('includes/footer.html');
