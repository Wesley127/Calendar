      <!--Link website ajax werkend? https://tutorial101.blogspot.com/2021/02/jquery-fullcalandar-crudcreate-read_7.html-->

<?php
include "includes/conn.php";
$userId = 1;
?>

<!DOCTYPE html>
<html>
<head>
  <script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
  
<meta charset='utf-8' />
<link href='lib/main.css' rel='stylesheet' />
<script src='lib/main.js'></script>
<script>

  
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var Calendar = FullCalendar.Calendar;
    var Draggable = FullCalendar.Draggable;

    var containerEl = document.getElementById('external-events');
    var calendarEl = document.getElementById('calendar');
    var checkbox = document.getElementById('drop-remove');


    new Draggable(containerEl, {
      itemSelector: '.item-class'
    });
    
    
    
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      allDaySlot: false,
      eventOverlap: false,
      //slotMinTime: "01:00:00",
      //slotMaxTime: "18:00:00",
      nowIndicator: true,
      slotDuration: '00:15',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,timeGridDay,listWeek'
      },
      locale: 'nl',
      navLinks: true, // can click day/week names to navigate views
      editable: true,
      droppable: true, // this allows things to be dropped onto the calendar
      selectable: false,
      selectMirror: true,
      dayMaxEvents: true, // allow "more" link when too many events
      events: 'info.php'
      ,
   
        
      
        
      // Hier staan beneden de callbacks zie drop https://fullcalendar.io/docs/drop
      // link om events te consol loggen https://fullcalendar.io/docs/event-object

      //  Activiteit toevoegen   \\
      eventReceive: function(info, fetchInfo, successCallback, failureCallback) {
        
        var activiteitId = info.event.id;
        var startTijd = info.event.startStr;
        var eindTijd = info.event.endStr;
        
        $.ajax ({
        
          type:'post',
          url:'includes/agenda.php',
          data:{
           crud:"toevoegen",
           activiteitId:activiteitId,
           startTijd:startTijd,
           eindTijd:eindTijd,
          },
          success:function(response) {
          if(response=="success")
          {
            calendar.refetchEvents()
            info.event.remove();
          }
          else
          {
            console.log (response);
          }
          }
        });

      
      },


      //  Activiteit dag en starttijd veranderen   \\
      eventDrop: function(info) {

        var agendaId = info.event.id;
        var startTijd = info.event.startStr;
        var eindTijd = info.event.endStr;

        $.ajax ({
        
          type:'post',
          url:'includes/agenda.php',
          data:{
           crud:"update",
           agendaId:agendaId,
           startTijd:startTijd,
           eindTijd:eindTijd,
          },
          success:function(response) {
          if(response=="success")
          {
          }
          else
          {
            console.log (response);
          }
          }
        });
      },


      //  Activiteit duur veranderen   \\
      eventResize: function(info) {
      
        var agendaId = info.event.id;
        var startTijd = info.event.startStr;
        var eindTijd = info.event.endStr;

        $.ajax ({
        
          type:'post',
          url:'includes/agenda.php',
          data:{
           crud:"update",
           agendaId:agendaId,
           startTijd:startTijd,
           eindTijd:eindTijd,
          },
          success:function(response) {
          if(response=="success")
          {
            
          }
          else
          {
            console.log (response);
          }
          }
        });
      },


      //  Activiteit verwijderen   \\
      eventClick: function(info) {
        var agendaId = info.event.id;
       
        if (confirm("Weet u zeker dat u dit event wilt verwijderen?")) {
          
          $.ajax ({
        
          type:'post',
          url:'includes/agenda.php',
          data:{
          crud:"delete",
          agendaId:agendaId,
          },
          success:function(response) {
          if(response=="success")
          {
            info.event.remove();
          }
          else
          {
            console.log (response);
          }
          }
        });
        }
      },
        
      datesSet: function (dateInfo) {
        
        var view = dateInfo.view;
        var agendaDatumBegin = dateInfo.startStr;
        var agendaDatumEinde = dateInfo.end;
        var agendaButtonInfo = view.type;
        
        var agendaDatumEinde = Date.parse(agendaDatumEinde);
        var d = new Date(agendaDatumEinde);
        var agendaDatumEinde = d.setDate(d.getDate() - 0);
        var agendaDatumEinde = new Date(agendaDatumEinde);
        var agendaDatumEinde = d.toJSON();


        //console.log (test); //Laat zien wanneer de week eindigt plus 1 dag ?!!!!
        //console.log (view.type); // Laat zien welke button is gedrukt

        $.ajax ({
        
        type:'post',
        url:'test.php',
        data:{
          agendaButtonInfo:agendaButtonInfo,
          agendaDatumBegin:agendaDatumBegin,
          agendaDatumEinde:agendaDatumEinde,
        },
        success:function(response) {
        if(response=="success")
        {
          console.log ("WERKT!");

          console.log (response);
        }
        else
        {
          console.log (response);

          $( "#child_response" ).remove();
          
          $("#response").append('<div id="child_response">'+response+'</div>');
        }
        }
      });



    },

                
            

    });
    
    calendar.render();

    
    // Krijg datum van vandaag
    //console.log (calendar.getDate());
    
  });

  

</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 0 auto;
  }

  #external-events {
    position: fixed;
    z-index: 2;
    top: 20px;
    left: 20px;
    width: 150px;
    padding: 0 10px;
    border: 1px solid #ccc;
    background: #eee;
  }

  #external-events .fc-event {
    cursor: move;
    margin: 3px 0;
  }


</style>
</head>
<body>



  
  
  <div id='external-events'>
    <p>
      <strong>Activiteiten</strong>
    </p>

      <!--Je kan ook een start tijd toegvoegen aan de activiteiten met startTime Link: https://fullcalendar.io/docs/external-dragging-->

      <?php
      
         $stmt = $pdo->query("SELECT activiteit.beschrijving, activiteit.duur, categorie.kleur, categorie.id, activiteit.user_id, activiteit.id
         FROM (activiteit 
         INNER JOIN categorie ON categorie.id = activiteit.categorie_id)
         WHERE activiteit.user_id='$userId';");
         
         if ($stmt->rowCount() > 0) {
           while ($row = $stmt->fetch()) {
             
             $activiteitId = $row["id"];
             $beschrijving = $row["beschrijving"];
             $duur = $row["duur"];
             $kleur = $row["kleur"];
             
             echo "<div style='background-color:$kleur;' class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>";
             echo "<div class='item-class'";
             echo " data-event='";
             echo '{ "id": "'.$activiteitId.'", "title": "'.$beschrijving.'", "duration": "'.$duur.'", "backgroundColor": "'.$kleur.'" }';
             echo "'>$beschrijving</div>";
             echo "</div>";
             
           }
         }
         
      ?>

<!--

    <div style='background-color:red;' class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
      <div class='item-class' data-event='{ "id": "1", "title": "Rennen 30", "duration": "00:30", "backgroundColor": "red" }'>Rennen 30</div>
    </div>

    <div style='background-color:blue;' class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
      <div class='item-class' data-event='{ "id": "2", "title": "tv kijken 1 uur", "duration": "01:00", "backgroundColor": "blue" }'>Tv kijken 1 uur</div>
    </div>

    <div style='background-color:purple;' class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
      <div class='item-class' data-event='{ "id": "3", "title": "Stofzuigen 2 uur", "duration": "02:00", "backgroundColor": "purple" }'>Stofzuigen 2 uur</div>
    </div>

    <div style='background-color:orange;' class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
      <div class='item-class' data-event='{ "id": "4", "title": "Licht 1 uur", "duration": "01:00", "backgroundColor": "orange" }'>licht 1 uur</div>
    </div>

-->
  </div>



  
  <div id='calendar'></div>

  <div id="response"></div>


</body>
</html>
