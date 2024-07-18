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
$sql = "SELECT * FROM `simc` WHERE rm = 96";
$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO miasto (nazwa,woj,pow,gm,rodz,id_woj,id_pow,id_gm) VALUES(:name,:woj,:pow,:gm,:rodz,:id_woj,:id_pow,:id_gm)');
$sthWoj = $db->prepare("SELECT * FROM wojewodztwo WHERE woj=:woj LIMIT 1");
$sthPow = $db->prepare("SELECT * FROM powiat WHERE woj=:woj AND pow = :pow LIMIT 1");
$sthGm = $db->prepare("SELECT * FROM gmina WHERE woj=:woj AND pow = :pow AND gm = :gm AND rodz = :rodz LIMIT 1");
foreach($locales as $locale){
  $sthWoj->execute(array(':woj'=>$locale['woj']));
  $wojewodztwo = $sthWoj->fetch(PDO::FETCH_ASSOC);

  $sthPow->execute(array(':woj'=>$locale['woj'],':pow'=>$locale['pow']));
  $powiat = $sthPow->fetch(PDO::FETCH_ASSOC);
  echo $locale['nazwa'] .' woj '.$locale['woj'].' pow '.$locale['pow'].' gmi '.$locale['gmi'].' rodz'.$locale['rodz_gmi'];
  $sthGm->execute(array(':woj'=>$locale['woj'],':pow'=>$locale['pow'],':gm'=>$locale['gmi'],':rodz'=>$locale['rodz_gmi']));
  $gmina = $sthGm->fetch(PDO::FETCH_ASSOC);
  echo '###### id powiatu '.$powiat['id'];
  $statement2->execute(array(':name'=>ucwords($locale['nazwa'],'UTF-8'),
                             ':woj'=>$locale['woj'],
                             ':pow'=>$locale['pow'],
                             ':id_woj'=>$wojewodztwo['id'],
                             ':id_pow'=>$powiat['id'],
                             ':id_gm'=>$gmina['id'],
                             ':gm'=>$locale['gmi'],
                             ':rodz'=>$locale['rodz_gmi']));
  print_r($statement2->errorInfo());
}