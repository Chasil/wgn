<?php

$dbhost = "localhost";
$dbname = "wgn_test";
$dbname2 = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";

try {
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db2 = new PDO("mysql:host=$dbhost;dbname=$dbname2",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

//$sql = "SELECT * FROM `terc` m  JOIN terc p ON m.pow = p.pow AND m.woj=p.woj JOIN terc w ON p.woj = w.woj JOIN simc d ON  WHERE m.nazdod='miasto' AND p.nazdod='powiat' AND w.nazdod = 'województwo'";
$sql = "SELECT * FROM `terc` WHERE nazdod LIKE  '%powiat%'";
$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);
echo count($locales);
} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO powiat (nazwa,woj,pow,id_woj) VALUES(:name,:woj,:pow,:id_woj)');
$sthWoj = $db->prepare("SELECT * FROM wojewodztwo WHERE woj=:woj LIMIT 1");
foreach($locales as $locale){
  $sthWoj->execute(array(':woj'=>$locale['woj']));
  $wojewodztwo = $sthWoj->fetch(PDO::FETCH_ASSOC);

  $statement2->execute(array(':name'=>mb_strtolower($locale['nazwa'],'UTF-8'),':woj'=>$locale['woj'],':pow'=>$locale['pow'],':id_woj'=>$wojewodztwo['id']));
  print_r($statement2->errorInfo());
}