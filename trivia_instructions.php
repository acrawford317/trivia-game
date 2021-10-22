<?php
/** DATABASE SETUP **/
// include('database_connection.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Extra Error Printing
// $mysqli = new mysqli($dbserver, $dbuser, $dbpass, $dbdatabase);
$mysqli = new mysqli("localhost", "root", "", "trivia_game"); // XAMPP
$user = null;

// If the user's email is not set in the session, then it's not
// a valid session (they didn't get here from the login page),
// so we should send them over to log in first before doing
// anything else!
if (!isset($_COOKIE["email"])) {
    header("Location: index.php");
    exit();
}

// reset all questions to not being done yet 
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

// set user information for the page
$user = [
    "email" => $_COOKIE["email"],
    "score" => $_COOKIE["score"],
    "last_category" => $_COOKIE["last_category"],
];

$message = "";

if (isset($_POST['submit']))
{
    if (isset($_POST['opt']))
    {
        // update last category in cookies
        setcookie("last_category", $_POST['opt'], time()+3600);
        // update last category in database 
        $stmt = $mysqli->prepare("update user set last_category = ? where email = ?;");
        $stmt->bind_param("ss", $_COOKIE["last_category"], $_COOKIE["email"]);
        $stmt->execute();

        //update question count in cookies
        setcookie("count", 1, time()+3600);

        // update previous answers in cookies
        setcookie("answer1", " ", time()+3600);
        setcookie("answer2", " ", time()+3600);
        setcookie("answer3", " ", time()+3600);
        setcookie("answer4", " ", time()+3600);
        setcookie("answer5", " ", time()+3600);
        setcookie("answer6", " ", time()+3600);
        setcookie("answer7", " ", time()+3600);
        setcookie("answer8", " ", time()+3600);
        setcookie("answer9", " ", time()+3600);
        setcookie("answer10", " ", time()+3600);

        if ($_POST['opt'] == 'Television') {header('Location: questions_tv.php'); }
        elseif ($_POST['opt'] == 'Celebrities') { header('Location: questions_celeb.php'); }
        elseif ($_POST['opt'] == 'History') { header('Location: questions_history.php'); }
        elseif ($_POST['opt'] == 'Video Games') { header('Location: questions_games.php'); }
        elseif ($_POST['opt'] == 'Science & Nature') { header('Location: questions_science.php'); }
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="your name">
        <meta name="description" content="include some description about your page">  

        <title>Trivia Game</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>

    <body>
        <div class="container" style="margin-top: 15px;">
            <div class="row">
                <div class="col-8">
                    <h1>CS4640 Trivia Game</h1>
                    <h2>Hello <?=$user["email"]?>!</h2>
                    <h3>Total Score: <?=$user["score"]?></h3>
                    <h3>Last Category Played: <?=$user["last_category"]?></h3>
                </div>
                <div class="col-4">
                    <a href="index.php" class="btn btn-danger" style="margin-left:300px;">Log out</a>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 mx-auto">
                <form action="" method="post">
                    <div class="h-100 p-5 bg-light border rounded-3" style="margin-top:40px;">
                        <h4>Instructions: </h4>
                        <p>Select a category and start the game! Earn points for each correct answer.</p>
                        <select class="form-select" name="opt" aria-label="select category">
                            <option selected disabled="true">Select a category</option>
                            <option value="Television">Television</option>
                            <option value="Celebrities">Celebrities</option>
                            <option value="History">History</option>
                            <option value="Video Games">Video Games</option>
                            <option value="Science & Nature">Science & Nature</option>
                        </select>
                    </div>
                    <?=$message?>
                    <div class="text-center" style="margin-top:40px;">                
                        <button type="submit" name="submit" class="btn btn-primary">Start Game</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    </body>
</html>