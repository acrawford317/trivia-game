<!--
    Sources used: https://cs4640.cs.virginia.edu/lectures/examples/trivia_game_redux/
-->

<?php
/** DATABASE SETUP **/
// include('database_connection.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Extra Error Printing
// $mysqli = new mysqli($dbserver, $dbuser, $dbpass, $dbdatabase);
$mysqli = new mysqli("localhost", "root", "", "trivia_game"); // XAMPP

$error_msg = "";

// logout component
setcookie("email", "", time()-3600);
setcookie("score", "", time()-3600);
setcookie("last_category", "", time()-3600);
// logout component -- reset all questions to not being done yet 
$question_complete = 0;
$stmt = $mysqli->prepare("update celeb_questions set done = ?;");
$stmt->bind_param("i", $question_complete);
$stmt->execute();
$stmt = $mysqli->prepare("update tv_questions set done = ?;");
$stmt->bind_param("i", $question_complete);
$stmt->execute();
$stmt = $mysqli->prepare("update history_questions set done = ?;");
$stmt->bind_param("i", $question_complete);
$stmt->execute();
$stmt = $mysqli->prepare("update videogames_questions set done = ?;");
$stmt->bind_param("i", $question_complete);
$stmt->execute();
$stmt = $mysqli->prepare("update science_questions set done = ?;");
$stmt->bind_param("i", $question_complete);
$stmt->execute();

// validate email/password coming in 
if (isset($_POST["email"]) && isset($_POST['password'])) {
    $stmt = $mysqli->prepare("select * from user where email = ?;");
    $stmt->bind_param("s", $_POST["email"]);

    if (!$stmt->execute()) {
        $error_msg = "Error checking for user";
    } else { 
        // result succeeded
        $res = $stmt->get_result();
        $data = $res->fetch_all(MYSQLI_ASSOC);
        
        if (!empty($data)) {
            // user was found
            // check if password is correct for the email
            if(password_verify($_POST["password"], $data[0]["password"])){
                // set user info into cookies to use later
                setcookie("email", $data[0]["email"], time()+3600);
                setcookie("score", $data[0]["score"], time()+3600);
                setcookie("last_category", $data[0]["last_category"], time()+3600);

                header("Location: trivia_instructions.php");
                exit();
            } else {
                // user found, but password was incorrect
                $error_msg = "Incorrect password for email {$_POST["email"]}.";
            }
        } else {
            // user was not found, create an account
            $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $insert = $mysqli->prepare("insert into user (email, password) values (?, ?);");
            $insert->bind_param("ss", $_POST["email"], $hash);

            if (!$insert->execute()) {
                $error_msg = "Error creating new user";
            } 

            // set user info into cookies to use later
            setcookie("email", $_POST["email"], time()+3600);
            setcookie("score", 0, time()+3600);
            setcookie("last_category", "n/a", time()+3600);

            // Send them to the game
            header("Location: trivia_instructions.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="your Ashley Crawford">
        <meta name="description" content="trivia game login page">  

        <title>Trivia Game Login</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>

    <body>
        <div class="container" style="margin-top: 15px;">
            <div class="row col-xs-8">
                <h1>CS4640 Trivia Game - Get Started</h1>
                <p> Welcome to our trivia game!  To get started, login below or enter a new email and password to create an account</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-4">
                <?php
                    if (!empty($error_msg)) {
                        echo "<div class='alert alert-danger'>$error_msg</div>";
                    }
                ?>
                <form action="index.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"/>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"/>
                    </div>
                    <div class="text-center">                
                    <button type="submit" class="btn btn-primary">Log in / Create Account</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    </body>
</html>