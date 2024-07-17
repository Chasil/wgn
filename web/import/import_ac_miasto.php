<?php

$dbhost = "localhost";
$dbname = "wgn_test";
$dbname2 = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";

try {
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db2 = new PDO("mysql:host=$dbhost;dbname=$dbname2",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$sql = "SELECT
	null  as id,
	if( m.nazwa=p.nazwa, 1, 0 ) as isMain,
	m.nazwa as city,
	p.nazwa as district,
	w.nazwa as province,
	null as lat,
	null as lng,
	if( m.nazwa=p.nazwa, CONCAT_WS(', ',w.nazwa,m.nazwa) , CONCAT_WS(', ',w.nazwa,p.nazwa,m.nazwa) )  as name,
	null as region,
	null as subregion,
	null as street
FROM miasto m join powiat p on m.id_pow = p.id join wojewodztwo w on m.id_woj = w.id";

$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO location_autocomplete (name,province,district,city,isMain) VALUES(:name,:province,:district,:city,:isMain)');
foreach($locales as $locale){

    $statement2->execute(array(':name'=>$locale['name'],
                               ':province'=>$locale['province'],
                               ':district'=>$locale['district'],
                               ':city'=>$locale['city'],
                               ':isMain'=>$locale['isMain'],
                                ));
    print_r($statement2->errorInfo());
}