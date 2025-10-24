<?php
echo $_SERVER['PHP_SELF'];
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $email_address = $_REQUEST['email_address'];
        if (empty($email_address))
        {
            echo "Email address is empty";
        }
        else
        {
            echo $email_address;
        }
    }
?>