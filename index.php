<html>
  <head>
    <title>BUS TRACKING</title>
  </head>
  <body>
<div align="center">
  <h1 class="status" style="vertical-align:middle;"></h1>
      <input id="busno" required="true" placeholder="Enter bus no" />
      <input type="submit" value="start sending location" onclick="sendloc()" />
    <script>
        const getloc = () => {

            const status = document.querySelector('.status');

            const success = (position) => {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                var busno = document.getElementById("busno").value;

                status.innerHTML = '<iframe height="600px" width="900px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox='+longitude+'%2C'+latitude+'%2C'+longitude+'%2C'+latitude+'&amp;layer=mapnik&amp;marker='+latitude+'%2C'+longitude+'" style="border: 1px solid black"></iframe><br/><small><a href="https://www.openstreetmap.org/?mlat='+latitude+'&amp;mlon='+longitude+'#map=19/'+latitude+'/'+longitude+'">View Larger Map</a></small>';
                const data = new FormData();
                data.append("lat",latitude);
                data.append("lon",longitude);
                data.append("busno",busno);
                fetch('/',{method:"POST",body:data})
            }

            const error = () => {
                status.textContent = 'Unable to get location';
            }

            navigator.geolocation.getCurrentPosition(success, error);
        }
        function sendloc(){
          getloc();
          window.setInterval(getloc,5000)
        }
    </script>
</div>

<?php
$server = 'localhost';
$user = 'root';
$password = 'root';
$dbname = 'vasil';


if(isset($_POST['lat']) && isset($_POST['lon']) && isset($_POST['busno'])){
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $bus_no = $_POST['busno'];
}else{
    $lat=NULL;$lon=NULL;$bus_no=NULL;
}

$pdoOptions = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false
);

$pdo = new PDO("mysql:host=$server;dbname=$dbname", $user, $password, $pdoOptions);

$sql = "SELECT `busno` FROM `location` WHERE `busno` = :busno";

$statement = $pdo->prepare($sql);
$statement->bindValue(':busno', $bus_no);

$statement->execute();
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

$exists=false;

foreach($rows as $row){
  if($bus_no==$row['busno']){
    $exists=true;
  }
}

if($exists==false){
    $sql = "INSERT INTO `location` (`busno`, `latitude`, `longitude`) VALUES (:busno, :latitude, :longitude)";

    $statement = $pdo->prepare($sql);

    $statement->bindValue(':busno', $bus_no);
    $statement->bindValue(':latitude', $lat);
    $statement->bindValue(':longitude', $lon);

    $inserted = $statement->execute();
    if($inserted){
        echo 'Row inserted!<br>';
    }
}
else{
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date('Y-m-d H:i:s');
    $sql = "UPDATE `location` SET `latitude` = :latitude,`longitude` = :longitude, `time_stamp` = :time_stamp WHERE busno = :busno";

    $statement = $pdo->prepare($sql);

    $statement->bindValue(':busno', $bus_no);
    $statement->bindValue(':latitude', $lat);
    $statement->bindValue(':longitude', $lon);
    $statement->bindValue(':time_stamp', $timestamp);

    $update = $statement->execute();
    echo 'Row updated';
}
?>
  </body>
</html>