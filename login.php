<?php
    session_unset();
    session_start();

    include 'db.php';

    if(isset($_POST['username']) && !(isset($_POST['logout']))){
	    $username = $_POST['username'];
	$pword = $_POST['password'];
        $sql = $conn->prepare("select * from users where username=?");
        $sql->bind_param("s", $uname);
        $uname = $username;
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
            $records = $result->fetch_row();
            if($records[1] == $pword){
                    $_SESSION['username'] = $username;
                    header("Location: fridge.php");
                    die();
            }else{
                $_SESSION['fail'] = true;
            }
        }else{
            $_SESSION['fail'] = true;
        }
    }
    elseif(isset($_POST['logout'])){
        echo "Signed out successfully";
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
                height: 700px;
                width: 100%;
                font-family: "Pixelify Sans", sans-serif;
                flex-direction: column;

            }

            .button-box{
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .err-msg{
                color: red;
            }

            .login-button, a{
                font-family: "Pixelify Sans", sans-serif;

            }
        </style>
    </head>
    <body>
        <a href='index.php'>Back to home</a>
        <div class="form-box">
            <?php
                if(isset($_SESSION['fail'])){
                    if($_SESSION['fail']) echo "<p class='err-msg'>Incorrect UserID/Password</p><br>";
                }

                if(isset($_SESSION['signup-success'])){
                    if($_SESSION['signup-success']) echo "<p>Account created successfully, please log in to continue</p>";
                }

                session_unset();
            ?>
            <form action='login.php' method='post'>
                    <label for="id">User ID:</label>
                    <div><input type="text" name="username" required></div>
                    <br>
                    <label for="password">Password:</label>
                    <div><input type="password" name="password" required></div>
                    <br>
                    <div class="button-box">
                        <button type="submit" class='login-button'>Login</button>
                    </div>
            </form>
        </div>
    </body>
</html>
