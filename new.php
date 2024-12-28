<?php
    session_start();

    include 'db.php';

    if(isset($_POST['fname'])){

        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $username = preg_replace('/\s+/', '', $_POST['username']);
        $password = $_POST['password'];

        $sql = $conn->prepare("select * from pixelfridge.users where username=?");
        $sql->bind_param("s", $uname);
        $uname = $username;
        $sql->execute();
        $result = $sql->get_result();

        if(!strcmp($fname, '') || !strcmp($lname, '') || !strcmp($username, '')){
            $_SESSION['signup-err'] = 'empty';
        } 
        else if (!(strlen($password) >= 8) || !preg_match('/[0-9]/', $password) 
            || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)){
                $_SESSION['signup-err'] = 'pword-err';
        }
        else if ($result->num_rows > 0){
            $_SESSION['signup-err'] = 'user-taken';
        }
        else{
            $sql = $conn->prepare("insert into pixelfridge.users values (?, ?, ?, ?, default)");
            $sql->bind_param("ssss", $uname, $pword, $fn, $ln);
            $uname = $username;
            $pword = $password;
            $fn = $fname;
            $ln = $lname;
            $sql->execute();
            $_SESSION['signup-success'] = true;
            header('Location: login.php');
            die();
        }
        
    }
?>
<!DOCTYPE html>
<html>
    <head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
        <style>

            .form-box{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }
        </style>
    </head>
    <body>
    <a href='index.php'>Back to home</a>

        <div class="form-box">
            <?php
                if(isset($_SESSION['signup-err'])){
                    if ($_SESSION['signup-err'] == 'empty'){
                        echo "<p>Non-whitespace characters required</p>";
                    }
                    else if($_SESSION['signup-err'] == 'pword-err'){
                        echo "<p>Please enter a valid password</p>";
                    }
                    else if($_SESSION['signup-err'] == 'user-taken'){
                        echo "<p>Username is taken</p>";
                    }
                }
                session_destroy();
            ?>
            <p>Some details about yourself:</p>
            <form action='new.php' method='post'>
                    <label for="id">First Name:</label>
                    <div><input type="text" name="fname" required></div>
                    <br>
                    <label for="id">Last Name:</label>
                    <div><input type="text" name="lname" required></div>
                    <br>
                    <label for="id">New Username:</label>
                    <div><input type="text" name="username" required></div>
                    <br>
                    <label for="password">New Password:</label>
                    <div><input type="password" name="password" required></div>
                    <br>
                    <div class="button-box">
                        <button type="submit" class='login-button'>Continue</button>
                    </div>
                    <p>Password must contain: </p>
                    <ul>
                        <li>Minimum of 8 characters</li>
                        <li>At least one numeric character</li>
                        <li>At least one alphabetic character</li>
                        <li>At least one uppercase character</li>
                        <li>At least one lowercase character</li>
                    </ul>
            </form>
        </div>
    </body>
</html>
