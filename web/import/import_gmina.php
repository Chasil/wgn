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
$sql = "SELECT * FROM `terc` WHERE nazdod like 'gmina%' OR nazdod like 'miasto' OR nazdod like 'delegatura' OR nazdod like 'dzielnica'";
$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO gmina (nazwa,typ,woj,pow,id_wojewodztwa,id_powiatu,gm,rodz) VALUES(:name,:typ,:woj,:pow,:id_woj,:id_pow,:gm,:rodz)');
$sthWoj = $db->prepare("SELECT * FROM wojewodztwo WHERE woj=:woj LIMIT 1");
$sthPow = $db->prepare("SELECT * FROM powiat WHERE woj=:woj AND pow = :pow LIMIT 1");
foreach($locales as $locale){
  $sthWoj->execute(array(':woj'=>$locale['woj']));
  $wojewodztwo = $sthWoj->fetch(PDO::FETCH_ASSOC);

  $sthPow->execute(array(':woj'=>$locale['woj'],':pow'=>$locale['pow']));
  $powiat = $sthPow->fetch(PDO::FETCH_ASSOC);
    echo 'woj '.(int)$locale['woj'] . ' pow '.$locale['pow'].'<br>';

  $statement2->execute(array(':name'=>mb_strtolower($locale['nazwa'],'UTF-8'),
                             ':typ'=>mb_strtolower($locale['nazdod'],'UTF-8'),
                             ':woj'=>$locale['woj'],
                             ':pow'=>$locale['pow'],
                             ':id_woj'=>$wojewodztwo['id'],
                             ':id_pow'=>$powiat['id'],
                             ':gm'=>$locale['gmi'],
                             ':rodz'=>$locale['rodz']));
  print_r($statement2->errorInfo());
}