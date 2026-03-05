<?php

$conn = new mysqli("localhost",
"smartgre_smart_agri",
"YRuBB6X9GPdXRzq7CkmF",
"smartgre_smart_agri");

if ($conn->connect_error) {
    die("Connection failed");
}

// Validate and sanitize inputs
$temp = isset($_GET['temp']) ? floatval($_GET['temp']) : 0;
$hum = isset($_GET['hum']) ? floatval($_GET['hum']) : 0;
$soil = isset($_GET['soil']) ? intval($_GET['soil']) : 0;
$light = isset($_GET['light']) ? floatval($_GET['light']) : 0;
$mq135 = isset($_GET['mq135']) ? floatval($_GET['mq135']) : 0;
$mq7 = isset($_GET['mq7']) ? floatval($_GET['mq7']) : 0;

$alert="SAFE";

if($temp>40 || $mq135>700 || $mq7>400){
    $alert="DANGER";

    $message="🚨 ALERT 🚨\n".
             "Temp: $temp C\n".
             "MQ135: $mq135\n".
             "MQ7: $mq7\n".
             "Time: ".date("Y-m-d H:i:s");

    // TELEGRAM
    $botToken="8650358630:AAHJyyQbtPYWgWevP7-gkp-oSpZBcuuM2J8";
    $chatId="6393811148";

    // Send Telegram notification
    @file_get_contents(
    "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=".urlencode($message)
    );

    // Send EMAIL alert
    @mail("sitvicky20045@gmail.com","Smart Agri Alert",$message);
}

$stmt = $conn->prepare("INSERT INTO sensor_data (temperature,humidity,soil,light,mq135,mq7,alert_status,date,time) VALUES (?,?,?,?,?,?,?,CURDATE(),CURTIME())");
$stmt->bind_param("dddidds", $temp, $hum, $soil, $light, $mq135, $mq7, $alert);
$stmt->execute();
$stmt->close();
$conn->close();

echo "OK";
?>