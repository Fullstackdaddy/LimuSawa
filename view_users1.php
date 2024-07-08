<?php //This script retrieves all the records from the user table


//page title
$page_title = 'View the Current Users'; 

include ('includes/header.html');

// page header
echo '<h1>View Registered Users</h1>'; 

//connect to the db with exception handler
try {
    require('../../db/ullman_connect/sitename_config.php'); 
} catch (PDOException $e) {
    echo "Failed to connect to database. Please try again";
}

//prepare the query
$sql = "SELECT last_name, first_name, "
        . "DATE_FORMAT(registration_date, '%M %d %Y') AS dr, user_id "
        . "FROM sitename.users "
        . "ORDER BY registration_date ASC"; 
try {
    $stmt = $pdo->query($sql); //creates pdo objects representing result set  
} catch (PDOException $e) {
    echo "Could not execute query. Please contact admin.";
}

$users = $stmt->fetchAll(); //collects all rows of date from result set as arry
$rowCount = count($users);

//create table if result set is not empty
if ($rowCount > 0) {
    
    //print how many users there are
    echo "<p>There are currently $rowCount registered users in pennypot.</p>\n";
    
    //table creation
    echo '<table width="60%">'
        . '<thead>'
            . '<tr>'
                . '<th align="left"><strong>Edit</strong></th>'
                . '<th align="left"><strong>Delete</strong></th>'
                . '<th align="left"><strong>Last Name</strong></th>'
                . '<th align="left"><strong>First Name</strong></th>'
                . '<th align="left">Date Registered</th>'
            . '</tr>'
        . '</thead>'
        . '<tbody>';
    
    foreach ($users as $user) {
        echo "<tr>"
              . "<td align=\"left\"><a href=\"edit_user.php?"
                . "id={$user['user_id']}\">Edit</a></td>"
              . "<td align=\"left\"><a href=\"delete_user.php?"
                . "id={$user['user_id']}\">Delete</a></td>"
              . "<td align=\"left\">{$user['last_name']}</td>"
              . "<td align=\"left\">{$user['first_name']}</td>"
              . "<td align=\"left\">{$user['dr']}</td>"
           . "</tr>";
    }
    
    echo '</tbody>'
        . '</table>';
} else {
    echo "<p class=\"error\">There are currently no registered users.</p>"; 
}

include ('includes/footer.html');