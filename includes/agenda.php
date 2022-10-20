<?php

include "conn.php";

$userId = 1;


$crud = $_POST['crud'];


//  Create Agenda   \\
if ($crud == "toevoegen"){

  $activiteitId = $_POST['activiteitId'];
  $startTijd = $_POST['startTijd'];
  $eindTijd = $_POST['eindTijd'];
  

  try {
    $sql = "INSERT INTO agenda (begin_tijd, eind_tijd, user_id, activiteit_id)
    VALUES ('$startTijd', '$eindTijd', '$userId', '$activiteitId')";
    $pdo->exec($sql);
    echo "success";
  } catch(PDOException $e) {
    echo $e;
  }
}

//  Update Agenda   \\
if ($crud == "update"){

  $agendaId = $_POST['agendaId'];
  $startTijd = $_POST['startTijd'];
  $eindTijd = $_POST['eindTijd'];

  try {
    $sql = "UPDATE agenda SET begin_tijd='$startTijd', eind_tijd='$eindTijd' WHERE id=$agendaId AND user_id=$userId";
      $pdo->exec($sql);
      echo "success";
    } catch(PDOException $e) {
      echo $e;
    }
}


//  Delete Agenda   \\
if ($crud == "delete"){

  $agendaId = $_POST['agendaId'];

  try {
    $sql = "DELETE FROM agenda WHERE id='$agendaId' AND user_id=$userId";
    $pdo->exec($sql);
    echo "success";
  } catch(PDOException $e) {
    echo $e;
  }
}
