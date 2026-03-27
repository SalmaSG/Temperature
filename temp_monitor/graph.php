<?php

include "db.php";
include "header.php";
$result = mysqli_query($conn,"SELECT * FROM temperature_humidity");

$temp = [];
$hum = [];
$date = [];

while($row=mysqli_fetch_assoc($result))
{

$temp[] = $row['temperature'];
$hum[] = $row['humidity'];
$date[] = $row['date'];

}

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="chart"></canvas>

<script>

var ctx = document.getElementById('chart');

new Chart(ctx, {

type:'line',

data:{

labels: <?php echo json_encode($date); ?>,

datasets:[

{

label:'Temperature',

data: <?php echo json_encode($temp); ?>,

borderColor:'red',

fill:false

},

{

label:'Humidity',

data: <?php echo json_encode($hum); ?>,

borderColor:'blue',

fill:false

}

]

}

});

</script>