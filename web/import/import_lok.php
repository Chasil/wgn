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
$sql = "SELECT * FROM `terc` WHERE nazdod =  'wojewodztwo'";
$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);
echo count($locales);
} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO wojewodztwo (nazwa,woj) VALUES(:name,:woj)');
foreach($locales as $locale){
 echo $locale['nazwa'];
  $statement2->execute(array(':name'=>mb_strtolower($locale['nazwa'],'UTF-8'),':woj'=>$locale['woj']));
  print_r($statement2->errorInfo());
}