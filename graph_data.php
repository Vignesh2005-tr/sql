<?php
$conn = new mysqli("localhost",
"smartgre_smart_agri",
"YRuBB6X9GPdXRzq7CkmF",
"smartgre_smart_agri");

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

$stmt = $conn->prepare("SELECT time,mq135,mq7 FROM sensor_data WHERE date=?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$labels=[];
$mq135=[];
$mq7=[];

while($row=$result->fetch_assoc()){
$labels[]=$row['time'];
$mq135[]=$row['mq135'];
$mq7[]=$row['mq7'];
}

$stmt->close();
echo json_encode([
"labels"=>$labels,
"mq135"=>$mq135,
"mq7"=>$mq7
]);
$conn->close();
?>