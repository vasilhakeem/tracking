<!DOCTYPE html>
<html>

  <head>
    <title>TRACKING Reciever</title>
  </head>

  <body>

  <h1 align="center">BUS TRACKING</h1>
    <div align="center" >
      <form name="form1" action="#" method="post" >
        <span>
          <input type="text" name="busno" id="busno" required="true" placeholder="Enter bus number">
        </span>
        <span>
          <input type="submit" name="track" value="Track bus"><br><br>
        </span>
      </form>
    </div>

    <div align="center">
      <?php
        $server = 'localhost';
        $user = 'root';
        $password = 'root';
        $dbname = 'vasil';

        if(isset($_POST['busno'])){
          $bus_no=$_POST['busno'];
        }else{
          $bus_no=NULL;
        }

        $pdoOptions = array(
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_EMULATE_PREPARES => false
        );

        $pdo = new PDO("mysql:host=$server;dbname=$dbname", $user, $password, $pdoOptions);

        $sql = "SELECT `busno`,`latitude`,`longitude` FROM `location` WHERE `busno` = :busno";

        $statement = $pdo->prepare($sql);
        $statement->bindValue(':busno', $bus_no);

        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $exists=false;

        foreach($rows as $row){
          if($bus_no==$row['busno']){
            $exists=true;
            $lat=$row['latitude'];
            $lon=$row['longitude'];
          }
        }

        if($exists==false && $bus_no!=NULL){
          echo "Bus Not Found";
        }else if($exists==true && $bus_no!=NULL) {
          echo "Showing the current location of Bus no : $bus_no";
        }
        echo "<br><br>";
      ?>
    </div>
    <div align="center">
      <iframe height="600px" width="900px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=$lon?>%2C<?=$lat?>%2C<?=$lon?>%2C<?=$lat?>&amp;layer=mapnik&amp;marker=<?=$lat?>%2C<?=$lon?>" style="border: 1px solid black"></iframe><br/><small><a href="https://www.openstreetmap.org/?mlat=<?=$lat?>&amp;mlon=<?=$lon?>#map=19/<?=$lat?>/<?=$lon?>">View Larger Map</a></small>
    </div>
  </body>
  
</html>