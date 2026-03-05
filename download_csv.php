<?php
$conn = new mysqli("localhost",
"smartgre_smart_agri",
"YRuBB6X9GPdXRzq7CkmF",
"smartgre_smart_agri");

if ($conn->connect_error) {
    die("Connection failed");
}

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sensor_data.csv"');

$output=fopen("php://output","w");
fputcsv($output,['Temp','Humidity','Soil','Light','MQ135','MQ7','Date','Time']);

$stmt = $conn->prepare("SELECT temperature,humidity,soil,light,mq135,mq7,date,time FROM sensor_data WHERE date=?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();
while($row=$result->fetch_assoc()){
    fputcsv($output,$row);
}
$stmt->close();
fclose($output);
$conn->close();
?>