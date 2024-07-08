<?php
// This script retrieves all the records from the user table
// This new version paginates and allows column-based sorting as well

// Page title
$page_title = 'View the Current Users'; 

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

// Connect to the db 
require_once('../../db/ullman_connect/sitename_config.php'); 

// Page header
echo '<h1>Registered Users</h1>'; 

// Number of records to show per page
$display = 20; 

// Count the total number of records (users)
$sql1 = "SELECT COUNT(user_id) FROM sitename.users";
$stmt1 = $pdo->query($sql1);
$row = $stmt1->fetch(PDO::FETCH_NUM);
$records = $row[0];

// Determine how many pages there are based on display and record numbers
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined
    $pages = htmlspecialchars($_GET['p']); 
} else { // Need to be determined
    if ($records > $display) {
        $pages = ceil($records / $display); 
    } else {
        $pages = 1; 
    }
}

// Determine where in the database to start returning results
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
    $start = htmlspecialchars($_GET['s']);
} else {
    $start = 0; 
}

// Determine the sort. Default is by registration date
$sort = (isset($_GET['sort'])) ? htmlspecialchars($_GET['sort']) : 'rd';

// Determine the sorting order:
switch ($sort) {
    case 'ln':
        $order_by = 'last_name ASC';
        break;
    case 'fn':
        $order_by = 'first_name ASC';
        break;
    case 'rd':
        $order_by = 'registration_date DESC';
        break;
    default:
        $order_by = 'registration_date ASC';
        $sort = 'rd';
        break;
}

// Prepare the query
$sql3 = "SELECT last_name, first_name, "
        . "DATE_FORMAT(registration_date, '%M %d %Y') AS dr, user_id "
        . "FROM sitename.users "
        . "ORDER BY $order_by LIMIT ?, ?"; 

$stmt3 = $pdo->prepare($sql3); // Creates PDO objects representing result set 
$stmt3->execute([$start, $display]);

$users = $stmt3->fetchAll(); // Collects all rows of data as array
$rowCount = count($users);

// Create table if result set is not empty
if ($rowCount > 0) {
    
    // Print how many users there are
    echo "<p>There are currently $records registered users.</p>\n";
    
    // Table creation
    echo '<table>'
        . '<thead>'
            . '<tr>'
                . '<th><strong>Edit</strong></th>'
                . '<th><strong>Delete</strong></th>'
                . '<th><strong><a href="view_users.php?sort=ln">'
                        . 'Last Name</a></strong></th>'
                . '<th><strong><a href="view_users.php?sort=fn">'
                        . 'First Name</a></strong></th>'
                . '<th><strong><a href="view_users.php?sort=rd">'
                        . 'Date Registered</a></strong></th>'
            . '</tr>'
        . '</thead>'
        . '<tbody>';
    
    foreach ($users as $user) {
        echo "<tr>"
              . "<td><a href=\"edit_user.php?"
                . "id={$user['user_id']}\">Edit</a></td>"
              . "<td><a href=\"delete_user.php?"
                . "id={$user['user_id']}\">Delete</a></td>"
              . "<td>{$user['last_name']}</td>"
              . "<td>{$user['first_name']}</td>"
              . "<td>{$user['dr']}</td>"
           . "</tr>";
    }
    
    echo '</tbody>'
        . '</table>';
} else {
    echo "<p class=\"error\">There are currently no registered users.</p>"; 
}

// Make the links to other pages, if necessary
if ($pages > 1) {
    
    // Add some spacing and start a paragraph
    echo '<div class="pagination">';
    
    // Determine what page the script is on
    $current_page = ($start / $display) + 1; 
    
    // If it is not the first page, make a previous link
    if ($current_page != 1) {
        echo '<a href="view_users.php?s=' . ($start - $display) . '&p=' 
                . $pages . '&sort=' . $sort . '">Previous</a> ';
    }

    // Make all the numbered pages:
    for ($i = 1; $i <= $pages; $i++) {
        if ($i != $current_page) {
            echo '<a href="view_users.php?s=' . ($display * ($i - 1)) . '&p=' 
                    . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
        } else {
            echo '<span>' . $i . '</span> ';
        }
    }
    // If it's not the last page, make a Next button:
    if ($current_page != $pages) {   
        echo '<a href="view_users.php?s=' . ($start + $display) . '&p=' 
                . $pages . '&sort=' . $sort . '">Next</a>';
    }
    echo '</div>'; // Close the pagination div.

} // End of links section.

include('includes/footer.html');
?>
