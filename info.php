<?php 
        include "includes/conn.php";
        $userId = 1;

        $stmt = $pdo->query("SELECT agenda.id, activiteit.beschrijving, categorie.kleur, agenda.begin_tijd, agenda.eind_tijd, activiteit.categorie_id, activiteit.user_id
        FROM ((agenda 
        INNER JOIN activiteit ON activiteit.id = agenda.activiteit_id) 
        INNER JOIN categorie ON categorie.id = activiteit.categorie_id)
        WHERE agenda.user_id='$userId';");
        
        header('Content-Type: application/json');
        $data = array();
        
        if ($stmt->rowCount() > 0) {
          while ($row = $stmt->fetch()) {
            
            $agendaId = $row["id"];
            $beschrijving = $row["beschrijving"];
            $kleur = $row["kleur"];
            $beginTijd = $row["begin_tijd"];
            $eindTijd = $row["eind_tijd"];

            $data[] =               [
                'id' => ''.$agendaId.'',
               'title' => ''.$beschrijving.'',
               'start' =>  ''.$beginTijd.'',
               'end' =>  ''.$eindTijd.'',
               'backgroundColor' =>  ''.$kleur.'',
               

              ];
          }
        }
            print json_encode($data);
      ?>