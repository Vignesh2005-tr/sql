<?php

// MYSQL CONNECT
$conn = new mysqli("localhost","root","200459","ascend");

if($conn->connect_error){
 die("db_error");
}

// GET DATA
$ic = $_POST['ic'];
$dc = $_POST['dc'];
$gpay = $_POST['gpay'];
$pack = $_POST['pack'];
$amount = $_POST['amount'];
$date = $_POST['date'];

// EMPTY CHECK
if(!$ic || !$dc || !$gpay || !$pack || !$amount || !$date){
 echo "empty";
 exit;
}

// SAVE TO MYSQL
$sql = "INSERT INTO ascend_order_list 
(IC_NAME,DC_NAME,GPAY_NAME,PACK_NAME,AMOUNT,PURCHASE_DATE)
VALUES ('$ic','$dc','$gpay','$pack','$amount','$date')";

if(!$conn->query($sql)){
 echo "db_error";
 exit;
}

// DISCORD WEBHOOK
$webhook = "https://discord.com/api/webhooks/1469751200604881039/KAQFQHeeCiGgpaCZlduoq1vAPRiWZ6FxrI3FGPGtiln7d5KC9gL40ARADuoRhpZhQ1Dc";

$data = [
 "content" => "🛒 NEW ASCEND ORDER",
 "embeds" => [[
   "title" => "ASCEND STORE ORDER",
   "color" => 16753920,
   "fields" => [
     ["name"=>"IC NAME","value"=>$ic],
     ["name"=>"DC NAME","value"=>$dc],
     ["name"=>"GPAY NAME","value"=>$gpay],
     ["name"=>"PACK","value"=>$pack],
     ["name"=>"AMOUNT","value"=>"₹".$amount],
     ["name"=>"DATE","value"=>$date]
   ],
   "footer"=>["text"=>"ASCEND ORDER SYSTEM"]
 ]]
];

$options = [
 "http"=>[
  "header"=>"Content-Type: application/json",
  "method"=>"POST",
  "content"=>json_encode($data)
 ]
];

$context = stream_context_create($options);
file_get_contents($webhook,false,$context);

echo "success";
?>
