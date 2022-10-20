<?php
include "includes/conn.php";

$userId = 1;

$therapeutId = 2;

if (!empty($_POST["agendaButtonInfo"])){
  $agendaButtonInfo = $_POST["agendaButtonInfo"];
  $agendaDatumBegin = $_POST["agendaDatumBegin"];
  $agendaDatumEinde = $_POST["agendaDatumEinde"];
}else{
  $agendaButtonInfo = false;
}


if (isset($_POST["clientNotitie"])){

  $clientNotitieDatum = $_POST["clientNotitieDatum"];

  // create nieuwe client notitie
  if (empty($_POST["idClientNotitie"]) && !empty($_POST["ingevuldNotitie"])){
    $notitie = $_POST["ingevuldNotitie"];

    try {
      $sql = "INSERT INTO notitie_client (user_id, notitie, datum)
      VALUES ('$userId', '$notitie', '$clientNotitieDatum')";
      $pdo->exec($sql);
      echo "success";
    } catch(PDOException $e) {
      echo $e;
    }
  }

  // update client notitie
  if (!empty($_POST["idClientNotitie"]) && !empty($_POST["ingevuldNotitie"])){
    $idClientNotitie = $_POST["idClientNotitie"];
    $notitie = $_POST["ingevuldNotitie"];

    try {
      $sql = "UPDATE notitie_client SET notitie='$notitie' WHERE id=$idClientNotitie AND datum='$clientNotitieDatum' AND user_id=$userId";
        $pdo->exec($sql);
        echo "success";
      } catch(PDOException $e) {
        echo $e;
      }
  }

  // delete client notitie
  if (!empty($_POST["idClientNotitie"]) && empty($_POST["ingevuldNotitie"])){
    $idClientNotitie = $_POST["idClientNotitie"];

    try {
      $sql = "DELETE FROM notitie_client WHERE id='$idClientNotitie' AND user_id=$userId";
      $pdo->exec($sql);
      echo "success";
    } catch(PDOException $e) {
      echo $e;
    }
  }
}


// dag notitie zowel client als ergotherapeut
if ($agendaButtonInfo == "timeGridDay"){

  // Notitie van de client
  $stmt = $pdo->query("SELECT * FROM notitie_client WHERE user_id='$userId' AND datum='$agendaDatumBegin';");        
  if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch()) {

      echo 'Notitie client van ' .$row['datum'];
      echo '<br>';
      echo '
      <form action="test.php" method="post" id="notitieForm">
        <input type="text" value="'.$row['id'].'" name="idClientNotitie" hidden>
        <input type="text" value="'.$row['datum'].'" name="clientNotitieDatum" hidden>
        <textarea rows="4" cols="50" name="ingevuldNotitie">'.$row['notitie'].'</textarea>
        <input type="submit" name="clientNotitie" value="Notitie opslaan" >
      </form>
      ';
    }
  }else{

    // $agendaDatumBegin omrekenen naar goede datum bijvoorbeeld 2022-04-23
    $datumOmgerekend = date('Y-m-d', strtotime($agendaDatumBegin));

    echo 'Notitie client van ' .$datumOmgerekend;
    echo '<br>';
    echo '
    <form action="test.php" method="post" id="notitieForm">
      <input type="text" value="'.$datumOmgerekend.'" name="clientNotitieDatum" hidden>
      <textarea rows="4" cols="50" name="ingevuldNotitie"></textarea>
      <input type="submit" name="clientNotitie" value="Notitie opslaan" >
    </form>
    ';
  }

  // Notitie van de therapeut
  $stmt = $pdo->query("SELECT * FROM notitie_therapeut WHERE client_id='$userId' AND datum='$agendaDatumBegin';");        
  if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch()) {

      echo 'Notitie therapeut';
      echo '<br>';
      echo '
      <textarea rows="4" cols="50" name="notitie">'.$row['notitie'].'</textarea>
    ';
    }
  }else{

    echo 'Notitie therapeut';
    echo '<br>';
    echo '
    <textarea rows="4" cols="50" name="notitie"></textarea>
  ';
  }


}


// alle week notities zowel client als ergotherapeut
if ($agendaButtonInfo == "timeGridWeek"){

  $userNotities = array();
  $dagenTussen = array();

  $period = new DatePeriod(
    new DateTime($agendaDatumBegin),
    new DateInterval('P1D'),
    new DateTime($agendaDatumEinde)
  );

  foreach ($period as $key => $value) {

    $dagenTussen[$value->format('Y-m-d')] =  $value->format('Y-m-d');
  }

  // Client notities
  $stmt = $pdo->query("SELECT notitie_client.id, notitie_client.user_id, notitie_client.notitie, notitie_client.datum, user.user_role
  FROM (notitie_client 
  INNER JOIN user ON notitie_client.user_id = user.id) 
  WHERE notitie_client.user_id='$userId' AND notitie_client.datum BETWEEN '$agendaDatumBegin' AND '$agendaDatumEinde' ORDER BY notitie_client.datum ASC;");
  
  if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch()) {
 
      $userNotities[$row["datum"]] = array( "id" => $row["id"], "user_id" => $row["user_id"], "notitie" => $row["notitie"], "datum" => $row["datum"], "user_role" => $row["user_role"]);
    }
  }

  $therapeutId = 2;

  // ErgoTherapeut notities
  $stmt = $pdo->query("SELECT notitie_therapeut.id, notitie_therapeut.client_id , notitie_therapeut.therapeut_id, notitie_therapeut.notitie, notitie_therapeut.datum
  FROM (notitie_therapeut 
  INNER JOIN user ON notitie_therapeut.client_id = user.id) 
  WHERE notitie_therapeut.client_id='$userId' AND notitie_therapeut.datum BETWEEN '$agendaDatumBegin' AND '$agendaDatumEinde' ORDER BY notitie_therapeut.datum ASC;");
    
  if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch()) {
      $therapeutNotities[$row["datum"]] = array( "id_therapeut" => $row["id"], "client_id" => $row["client_id"], "notitie_therapeut" => $row["notitie"], "datum_therapeut" => $row["datum"]);
    }
  }



  $userNotities = array_map(fn($v) => $userNotities[$v] ?? ['datumUserNotitieLeeg' => $v],$dagenTussen);
  $therapeutNotities = array_map(fn($v) => $therapeutNotities[$v] ?? ['datumTherapeutNotitieLeeg' => $v],$dagenTussen);


  $results = [];
  foreach ($userNotities as $date => $row) {
    $results[$date][] = [$date => $row];
  }

  foreach ($therapeutNotities as $date => $row) {
    $results[$date][] = [$date => $row];
  }



  foreach ($results as $result) {
    foreach ($result as $dieperResult) {
      foreach ($dieperResult as $rest)  {

        // code beneden als userNotitie NIET LEEG is
        if (!empty($rest['datum'])){
          echo "<br>";
          if (!empty($rest['id'])){

            echo 'Notitie client van ' .$rest['datum'];
            echo '<br>';
            echo '
            <form action="test.php" method="post" id="notitieForm">
              <input type="text" value="'.$rest['id'].'" name="idClientNotitie" hidden>
              <input type="text" value="'.$rest['datum'].'" name="clientNotitieDatum" hidden>
              <textarea rows="4" cols="50" name="ingevuldNotitie">'.$rest['notitie'].'</textarea>
              <input type="submit" name="clientNotitie" value="Notitie opslaan" >
            </form>
            ';
          }
        }

        // code beneden als therapeutNotitie NIET LEEG is
        if (!empty($rest['datum_therapeut'])){
          if (!empty($rest['id_therapeut'])){

            echo 'Notitie therapeut';
            echo '<br>';
            echo '
              <textarea rows="4" cols="50" name="notitie">'.$rest['notitie_therapeut'].'</textarea>
            ';
            echo '<br>';
            echo '<br>';
          }
        } 

        // code beneden als userNotitie WEL LEEG is
        if (!empty($rest['datumUserNotitieLeeg'])){
          echo '<br>';
          echo 'Notitie client van ' .$rest['datumUserNotitieLeeg'];
          echo '<br>';
          echo '
            <form action="test.php" method="post" id="notitieForm">
              <input type="text" value="'.$rest['datumUserNotitieLeeg'].'" name="clientNotitieDatum" hidden>
              <textarea rows="4" cols="50" name="ingevuldNotitie"></textarea>
              <input type="submit" name="clientNotitie" value="Notitie opslaan" >
            </form>
          ';
        }

        /// code beneden als therapeutNotitie WEl LEEG is
        if (!empty($rest['datumTherapeutNotitieLeeg'])){

          echo 'Notitie therapeut';
          echo '<br>';
          echo '
            <textarea rows="4" cols="50" name="notitie"></textarea>
          ';
          echo '<br>';
          echo '<br>';
        }
      }
    }
  }
}


