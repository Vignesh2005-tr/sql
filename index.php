<?php
$conn = new mysqli("localhost",
"smartgre_smart_agri",
"YRuBB6X9GPdXRzq7CkmF",
"smartgre_smart_agri");

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

if ($conn->connect_error) {
    die("Connection failed");
}

$stmt = $conn->prepare("SELECT * FROM sensor_data WHERE date=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Smart Agriculture Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
background:#121212;
color:white;
font-family:Arial;
text-align:center;
margin:0;
padding:0;
}

.card{
background:#1e1e1e;
padding:20px;
margin:15px;
border-radius:15px;
box-shadow:0 0 15px #00ffcc;
}

.danger{
background:#b00020 !important;
}

canvas{
max-width:100%;
margin:20px auto;
}

button{
padding:10px 15px;
border:none;
border-radius:8px;
background:#00c896;
color:white;
cursor:pointer;
}

input{
padding:8px;
border-radius:8px;
border:none;
}

h2{
margin-top:20px;
}

.controls{
display:flex;
justify-content:center;
gap:20px;
align-items:center;
flex-wrap:wrap;
margin:20px 0;
padding:15px;
background:#1e1e1e;
border-radius:10px;
box-shadow:0 0 10px #00ffcc;
}

.filter-form{
display:flex;
gap:10px;
align-items:center;
}

.filter-form label{
font-weight:bold;
color:#00ffcc;
}

.filter-form input{
padding:10px;
border-radius:8px;
border:2px solid #00c896;
}

.filter-form button{
padding:10px 20px;
background:#00c896;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
font-weight:bold;
transition:all 0.3s;
}

.filter-form button:hover{
background:#00ffcc;
color:#121212;
transform:scale(1.05);
}

.download-btn{
text-decoration:none;
}

.download-btn button{
padding:10px 20px;
background:#ff6b6b;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
font-weight:bold;
font-size:14px;
transition:all 0.3s;
}

.download-btn button:hover{
background:#ff8787;
transform:scale(1.05);
box-shadow:0 0 15px #ff6b6b;
}
</style>
</head>

<body>

<h2>🌱 Smart Agriculture Monitoring</h2>

<div class="controls">
<form class="filter-form">
<label for="dateInput">📅 Select Date:</label>
<input type="date" id="dateInput" name="date" value="<?php echo $date; ?>">
<button type="submit">Filter</button>
</form>

<a href="download_csv.php?date=<?php echo $date; ?>" class="download-btn">
<button>📥 Download CSV</button>
</a>
</div>

<div class="card <?php if($row['alert_status']=="DANGER") echo 'danger'; ?>">

<h3>Live Sensor Values</h3>

Temperature: <?php echo $row['temperature']; ?> °C <br>
Humidity: <?php echo $row['humidity']; ?> % <br>
Soil Moisture: <?php echo $row['soil']; ?> <br>
Light Level: <?php echo $row['light']; ?> <br>
MQ135: <?php echo $row['mq135']; ?> <br>
MQ7: <?php echo $row['mq7']; ?> <br>
Status: <?php echo $row['alert_status']; ?> <br>
Date: <?php echo $row['date']; ?> <?php echo $row['time']; ?>

</div>

<!-- GAUGES -->

<h3>🌡 Temperature Gauge</h3>
<canvas id="tempGauge"></canvas>

<h3>🧪 MQ135 Gas Gauge</h3>
<canvas id="mq135Gauge"></canvas>

<h3>🔥 MQ7 Gas Gauge</h3>
<canvas id="mq7Gauge"></canvas>

<!-- LINE GRAPH -->

<h3>📊 MQ Gas Graph (Daily)</h3>
<canvas id="lineChart"></canvas>

<script>

// ---------- GAUGE FUNCTION ----------
function createGauge(canvasId, value, maxValue){

let color="green";

if(value > maxValue*0.7) color="orange";
if(value > maxValue*0.85) color="red";

new Chart(document.getElementById(canvasId), {
type: 'doughnut',
data: {
datasets: [{
data: [value, maxValue-value],
backgroundColor: [color,"#2c2c2c"],
borderWidth:0
}]
},
options:{
rotation:-90,
circumference:180,
cutout:"70%",
plugins:{
legend:{display:false},
tooltip:{enabled:false}
}
}
});
}

// Create Gauges
createGauge("tempGauge", <?php echo $row['temperature']; ?>, 60);
createGauge("mq135Gauge", <?php echo $row['mq135']; ?>, 1000);
createGauge("mq7Gauge", <?php echo $row['mq7']; ?>, 1000);

// ---------- LINE GRAPH ----------
fetch("graph_data.php?date=<?php echo $date; ?>")
.then(res=>res.json())
.then(data=>{
new Chart(document.getElementById("lineChart"),{
type:"line",
data:{
labels:data.labels,
datasets:[
{
label:"MQ135",
data:data.mq135,
borderColor:"green",
fill:false
},
{
label:"MQ7",
data:data.mq7,
borderColor:"red",
fill:false
}
]
}
});
});

</script>

</body>
</html>