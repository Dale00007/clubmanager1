<?php
// Include config file
require_once 'include/config.php';
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            session_start();
                            $_SESSION['username'] = $username;
                            $today = date("Y-m-d H:i:s"); 
	                        $sql = "UPDATE users SET last_login='$today' WHERE username='$username'";
  	                        $result = $link->query($sql);
  							$user=$_SESSION['username'];
  							$sql = "SELECT teams_id,teamname,users_teams.admin as admin, users.mainadmin as mainadmin FROM users,teams,users_teams WHERE users.username='$user' AND users.id=users_teams.users_id AND teams.id=users_teams.teams_id and users_teams.def=1";
  							$result = $link->query($sql);
 		 					$row = $result->fetch_assoc();
  						    $teamname=$row['teamname'];
							$admin=$row['admin'];	
							$mainadmin=$row['mainadmin'];
							$active=$row['active'];	
							$teams_id=$row['teams_id'];	
							$_SESSION['teamname'] = $teamname;
							$_SESSION['team_id'] = $teams_id;
							if ($admin=="1") {$_SESSION['admin']=1;}
							if ($mainadmin=="1") {$_SESSION['mainadmin']=1;}
							if ($active=="0") {session_destroy();}
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">

<?php
include "include/header.php";
//<head>
//    <meta charset="UTF-8">
//    <title>Login</title>
//    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
//    <style type="text/css">
//        body{ font: 14px sans-serif; }
//        .wrapper{ width: 350px; padding: 20px; }
//    </style>
//</head>
echo "<body>";
include "include/navigation.php";
?>
	

    <div>
        <h2>Login</h2>
        <p>Website can be used only after login. Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username:<sup>*</sup></label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password:<sup>*</sup></label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div> 

<?php
include "include/navigationend.php";
?>
	
</body>
</html>