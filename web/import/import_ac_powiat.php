<?php

$dbhost = "localhost";
$dbname = "wgn_test";
$dbname2 = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";

try {
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db2 = new PDO("mysql:host=$dbhost;dbname=$dbname2",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$sql = "SELECT NULL AS id, 1 AS isMain, NULL AS city, p.nazwa AS district, w.nazwa AS province, NULL AS lat, NULL AS lng, CONCAT_WS(  ', ', w.nazwa, p.nazwa ) AS name, NULL AS region, NULL AS subregion, NULL AS street
FROM powiat p
JOIN wojewodztwo w ON p.id_woj = w.id";

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