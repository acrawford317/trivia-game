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

// if current game is not over, then continue getting questions
if($_COOKIE["count"]<11){
    // Read a random question from the database that hasn't been answered this game
    $res = $mysqli->query("select id, question from celeb_questions where done=0 order by rand() limit 1;");
    if ($res === false) {
       die("MySQL database failed");
    }
    $data = $res->fetch_all(MYSQLI_ASSOC);
    if (!isset($data[0])) {
        die("No questions in the database");
    }
    $question = $data[0];

    $question_complete = 1;
    $id = $data[0]["id"];

    // set question has answered for this round (make done=true)
    $stmt = $mysqli->prepare("update celeb_questions set done = ? where id = ?;");
    $stmt->bind_param("is", $question_complete, $id);
    $stmt->execute();
}  

if($_COOKIE["count"]==11){
    // game is over, reset all questions to not being done yet 
    $question_complete = 0;
    $stmt = $mysqli->prepare("update celeb_questions set done = ?;");
    $stmt->bind_param("i", $question_complete);
    $stmt->execute();
}

// Message variable to display if needed
$message = "";

//update question count in cookies
setcookie("count", $_COOKIE["count"]+1, time()+3600);

// If the user submitted (POST) an answer to a question, we should check
// to see if they got it right!
if (isset($_POST["questionid"])) {
    $qid = $_POST["questionid"];
    $answer = $_POST["answer"];
    
    // Use prepare with parameter binding to avoid SQL injection and
    // other attacks.  This will ensure that MySQL correctly escapes
    // the passed value and ensure that it is an integer.
    $stmt = $mysqli->prepare("select * from celeb_questions where id = ?;");
    $stmt->bind_param("i", $qid);
    if (!$stmt->execute()) {
        // did not work
        $message = "<div class='alert alert-info'>Error: could not find previous question</div>";
    } else {
        // worked
        $res = $stmt->get_result();
        $data = $res->fetch_all(MYSQLI_ASSOC);

        if (!isset($data[0])) {
            $message = "<div class='alert alert-info'>Error: could not find previous question</div>";
        } else {
            // found question

            // update previous correct answers in cookies
            if($_COOKIE["count"]==2){
                setcookie("answer1", "1. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==3) {
                setcookie("answer2", "2. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==4) {
                setcookie("answer3", "3. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==5) {
                setcookie("answer4", "4. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==6) {
                setcookie("answer5", "5. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==7) {
                setcookie("answer6", "6. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==8) {
                setcookie("answer7", "7. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==9) {
                setcookie("answer8", "8. " . $data[0]["answer"], time()+3600);
            } elseif($_COOKIE["count"]==10) {
                setcookie("answer9", "9. " . $data[0]["answer"], time()+3600);
            } 

            // check if answer correct
            if ($data[0]["answer"] == $answer) {
                $message = "<div class='alert alert-success'><b>$answer</b> was correct!</div>";
                
                // Update the score in cookies
                setcookie("score", $_COOKIE["score"] += $data[0]["points"], time()+3600);

                // Update the score in the database using the SQL UPDATE query
                $stmt = $mysqli->prepare("update user set score  = ? where email = ?;");
                $stmt->bind_param("is", $_COOKIE["score"], $_COOKIE["email"]);
                $stmt->execute();

            } else { 
                $message = "<div class='alert alert-danger'><b>$answer</b> was incorrect! The answer was: {$data[0]['answer']}</div>";
            }

        }
    }
}

// set user information for the page
$user = [
    "email" => $_COOKIE["email"],
    "score" => $_COOKIE["score"],
    "last_category" => $_COOKIE["last_category"]
];

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Ashley Crawford">
        <meta name="description" content="trivia game celebrity questions">  

        <title>Trivia Game - Celebrities</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>

    <body>
        <div class="container" style="margin-top: 15px;">
            <div class="row">
                <div class="col-8">
                    <h1>CS4640 Trivia Game - Celebrities</h1>
                    <h5>Hello <?=$user["email"]?>, answer 10 questions in this category!</h5>
                </div>
                <div class="col-4">
                    <a href="index.php" class="btn btn-danger" style="margin-left:300px;">Log out</a>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 mx-auto">
                <form action="questions_celeb.php" method="post">
                    <div class="h-100 p-5 bg-light border rounded-3" style="margin-top: 50px;">
                        <?php if($_COOKIE["count"]<11) : ?>
                            <h2>Question <?=$_COOKIE["count"]?></h2>
                            <p><?=$question["question"]?></p>
                            <input type="hidden" name="questionid" value="<?=$question["id"]?>"/>
                        <?php else : ?>
                            <h2>Game Complete!</h2>
                        <?php endif; ?>
                    </div>
                    <?=$message?>
                    <div class="text-center">    
                        <?php if($_COOKIE["count"]<11) : ?>
                            <div class="h-10 p-5 mb-3">
                                <input type="text" class="form-control" id="answer" name="answer" placeholder="Type your answer here">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        <?php else : ?>
                            <button class="btn btn-primary"><a href= "trivia_instructions.php" style="color:white"> Return Home</a></button>
                        <?php endif; ?>   
                    </div>
                </form>
                </div>
            </div>
            <div class="row col-xs-8">
                <h4>Correct Answers:</h4>
                <?=$_COOKIE["answer1"]?> <br>
                <?=$_COOKIE["answer2"]?> <br>
                <?=$_COOKIE["answer3"]?> <br>
                <?=$_COOKIE["answer4"]?> <br>
                <?=$_COOKIE["answer5"]?> <br>
                <?=$_COOKIE["answer6"]?> <br>
                <?=$_COOKIE["answer7"]?> <br>
                <?=$_COOKIE["answer8"]?> <br>
                <?=$_COOKIE["answer9"]?> <br>
            </div> 
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    </body>
</html>