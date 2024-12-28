<DOCTYPE! html>
    <html>
        <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
            <style>
                .intro{
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    font-family: "Pixelify Sans", sans-serif;
                    font-size: 23px
                }


                .intro .fridge-pic{
                    height: 610px;
                }

                .intro h1{
                    margin-bottom: 0px;
                }

                .fridge-div{
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                }

                .fridge-div a{
                    position: relative;
                    top: -500px;
                    margin-bottom: 25px; 
                    font-family: "Pixelify Sans", sans-serif;
                    text-decoration: none;
                    background-color: white;
                    padding: 10px;
                    color: black;
                }

                .fridge-div a:hover{
                    color: pink;
                    cursor: pointer;
                }

                .magnet1{
                    position: relative;
                    top: -727px;
                    right: 40px;
                    height: 20px;
                }

                .magnet2{
                    position: relative;
                    top: -700px;
                    right: -40px;
                    height: 20px;
                }

                .magnet3{
                    position: relative;
                    top: -695px;
                    right: -80px;
                    height: 20px;
                }

                .magnet4{
                    position: relative;
                    top: -715px;
                    right: 80px;
                    height: 20px;
                }

                .magnet5{
                    position: relative;
                    top: -660px;
                    right: 40px;
                    height: 20px;
                }

                .magnet6{
                    position: relative;
                    top: -680px;
                    right: -40px;
                    height: 20px;
                }

                .magnet7{
                    position: relative;
                    top: -727px;
                    right: 80px;
                    height: 20px;
                }
                
            </style>
        </head>
        <body>
            <div class='intro'>
                <h1>Welcome to Pixel Fridge!</h1>
                <h2>Your virtual catalyst to combating food waste</h2>
                <div class='fridge-div'>
                    <img src='pics/fridge2.png' class='fridge-pic'>
                    <a href='login.php'>Log in</a>
                    <a href='new.php'>Get Started</a>
                    <a>FAQ's</a>
                    <img class='magnet1' src='pics/magnet.png'>
                    <img class='magnet2' src='pics/magnet.png'>
                    <img class='magnet3' src='pics/magnet.png'>
                    <img class='magnet4' src='pics/magnet.png'>
                    <img class='magnet5' src='pics/magnet.png'>
                    <img class='magnet6' src='pics/magnet.png'>
                    <img class='magnet7' src='pics/magnet.png'>
                </div>
            </div>
        </body>
    </html>
