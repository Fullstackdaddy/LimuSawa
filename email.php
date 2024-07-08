<?php # Script 9.7 - email.php
// This page lets a user contact admin via email

$page_title = 'Contact Me';

require ('includes/login_functions.php');

// Check if the user is logged in
if (isset($_COOKIE['user_id'])) {
    // User is logged in, include the logged-in header
    include('includes/header.html');
} else {
    // User is not logged in, include the basic header
    include('includes/welcome_header.html');
}


// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Minimal form validation:
    if (!empty($_POST['name']) && !empty($_POST['email']) && 
            !empty($_POST['comments'])) {
        
        function spam_scrubber($value) {
            
        }

        // Validate and Sanitize user input:
        $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $comments = htmlspecialchars(trim($_POST['comments']), ENT_QUOTES);
        
        // Create variables to hold recipient email and subject line
        $to = 'dgala@hawaii.edu';
        $subject = 'Contact Form Submission';

        // Validate email:
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            // Create the body:
            $body = "Name: $name\n\nComments: $comments";

            // Make it no longer than 70 characters long:
            $body = wordwrap($body, 70);

            // Check to see if arguments are presented and then send the email:
            if (mail($to, $subject, $body, "From: $email")) {
                // Print a success message:
                echo '<p class="success">'
                . '<em>Thank you for contacting me. I will reply some day.'
                . '</em></p>';
                
                // Include the footer and quit the script, form is sent:
                include('includes/footer.html');
                exit();
                
            } else {
                // Print an error message:
                echo '<p class="error>'
                . 'There was an error sending your message. Please try again '
                        . 'later.</p>';
            }

            // Clear $_POST (so that the form's not sticky):
            $_POST = [];

        } else {
            echo '<p class="error">Please enter a valid '
            . 'email address.</p>';
        }

    } else {
        echo '<p class="error">Please fill out the form'
        . ' completely.</p>';
    }
    

} // End of main isset() IF.

?>
<!-- Create the HTML form. Sticky form that is -->
<h1>Contact Me</h1>
<p>Please fill out this form to contact me.</p>
<form id="emailme" action="email.php" method="post" >
    <p>Name: <input type="text" name="name" id="name" size="30" maxlength="60" 
                    value="<?php if (isset($_POST['name']))
                    {echo htmlspecialchars($_POST['name']);} ?>" autofocus></p>
    <p>Email: <input type="text" name="email" id="mail" size="30" maxlength="80" 
                     value="<?php if (isset($_POST['email'])) 
                    {echo htmlspecialchars($_POST['email']);} ?>"></p>
    <p>Comments:</p> 
        <p><textarea name="comments" id="comments" rows="5" cols="30" 
           placeholder="enter comments here..."> <?php if (isset($_POST['comments'])) 
        {echo htmlspecialchars($_POST['comments']);} ?>
        </textarea>
        </p>
    <p><input type="submit" name="submit" value="Send" 
              onclick="return validateEmail();" ></p>
</form>

<?php include('includes/footer.html'); ?>

