<?php
    /** SETUP **/
    // include('database_connection.php');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Extra Error Printing
    // $db = new mysqli($dbserver, $dbuser, $dbpass, $dbdatabase);
    $db = new mysqli("localhost", "root", "", "trivia_game"); // XAMPP    
    
    $db->query("drop table if exists tv_questions;");
    $db->query("create table tv_questions (
        id int not null auto_increment,
        question text not null,
        answer text not null,
        points int not null,
        done bool default false,
        primary key (id));");
    
    $db->query("drop table if exists videogames_questions;");
    $db->query("create table videogames_questions (
        id int not null auto_increment,
        question text not null,
        answer text not null,
        points int not null,
        done bool default false,
        primary key (id));");

    $db->query("drop table if exists history_questions;");
    $db->query("create table history_questions (
        id int not null auto_increment,
        question text not null,
        answer text not null,
        points int not null,
        done bool default false,
        primary key (id));");
    
    $db->query("drop table if exists celeb_questions;");
    $db->query("create table celeb_questions (
        id int not null auto_increment,
        question text not null,
        answer text not null,
        points int not null,
        done bool default false,
        primary key (id));");

    $db->query("drop table if exists science_questions;");
    $db->query("create table science_questions (
        id int not null auto_increment,
        question text not null,
        answer text not null,
        points int not null,
        done bool default false,
        primary key (id));");
    
    $db->query("drop table if exists user;");
    $db->query("create table user (
        id int not null auto_increment,
        email text not null,
        name text,
        password text not null,
        score int default 0,
        last_category text,
        primary key (id));");
   
    $tv_data = json_decode(file_get_contents("https://opentdb.com/api.php?amount=10&category=14&difficulty=medium&type=multiple"), true);
        
    $points = 10;
    $stmt = $db->prepare("insert into tv_questions (question, answer, points) values (?,?,?);");
    foreach($tv_data["results"] as $qn) {
        $stmt->bind_param("ssi", $qn["question"], $qn["correct_answer"], $points);
        if (!$stmt->execute()) {
            echo "Could not add question: {$qn["question"]}\n";
        }
    }

    $vg_data = json_decode(file_get_contents("https://opentdb.com/api.php?amount=10&category=15&difficulty=medium&type=multiple"), true);
        
    $points = 10;
    $stmt = $db->prepare("insert into videogames_questions (question, answer, points) values (?,?,?);");
    foreach($vg_data["results"] as $qn) {
        $stmt->bind_param("ssi", $qn["question"], $qn["correct_answer"], $points);
        if (!$stmt->execute()) {
            echo "Could not add question: {$qn["question"]}\n";
        }
    }

    $history_data = json_decode(file_get_contents("https://opentdb.com/api.php?amount=10&category=23&difficulty=medium&type=multiple"), true);
        
    $points = 10;
    $stmt = $db->prepare("insert into history_questions (question, answer, points) values (?,?,?);");
    foreach($history_data["results"] as $qn) {
        $stmt->bind_param("ssi", $qn["question"], $qn["correct_answer"], $points);
        if (!$stmt->execute()) {
            echo "Could not add question: {$qn["question"]}\n";
        }
    }

    $celeb_data = json_decode(file_get_contents("https://opentdb.com/api.php?amount=10&category=26&difficulty=medium&type=multiple"), true);
        
    $points = 10;
    $stmt = $db->prepare("insert into celeb_questions (question, answer, points) values (?,?,?);");
    foreach($celeb_data["results"] as $qn) {
        $stmt->bind_param("ssi", $qn["question"], $qn["correct_answer"], $points);
        if (!$stmt->execute()) {
            echo "Could not add question: {$qn["question"]}\n";
        }
    }

    $science_data = json_decode(file_get_contents("https://opentdb.com/api.php?amount=10&category=17&difficulty=medium&type=multiple"), true);
        
    $points = 10;
    $stmt = $db->prepare("insert into science_questions (question, answer, points) values (?,?,?);");
    foreach($science_data["results"] as $qn) {
        $stmt->bind_param("ssi", $qn["question"], $qn["correct_answer"], $points);
        if (!$stmt->execute()) {
            echo "Could not add question: {$qn["question"]}\n";
        }
    }