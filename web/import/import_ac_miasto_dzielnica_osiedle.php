<?php

$dbhost = "localhost";
$dbname = "wgn_test";
$dbname2 = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";

try {
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db2 = new PDO("mysql:host=$dbhost;dbname=$dbname2",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$sql = "SELECT NULL AS id, IF( m.nazwa = p.nazwa, 1, 0 ) AS isMain, m.nazwa AS city, p.nazwa AS district, w.nazwa AS province, NULL AS lat, NULL AS lng, IF( m.nazwa = p.nazwa, CONCAT_WS(  ', ', w.nazwa, m.nazwa, concat_ws('-',doe.dzielnica, doe.osiedle) ) , CONCAT_WS(  ', ', w.nazwa, p.nazwa, m.nazwa, concat_ws('-',doe.dzielnica, doe.osiedle) ) ) AS name, doe.dzielnica AS section, doe.osiedle AS subsection, NULL AS street
FROM miasto m
JOIN powiat p ON m.id_pow = p.id
JOIN wojewodztwo w ON m.id_woj = w.id
JOIN dzielnicaosiedle doe ON doe.id_miasta = m.id
WHERE doe.dzielnica != doe.osiedle
ORDER BY `name` ASC";

$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO location_autocomplete (name,province,district,city,section,subsection,isMain) VALUES(:name,:province,:district,:city,:section,:subsection,:isMain)');
foreach($locales as $locale){

    $statement2->execute(array(':name'=>$locale['name'],
                               ':province'=>$locale['province'],
                               ':district'=>$locale['district'],
                               ':city'=>$locale['city'],
                               ':isMain'=>$locale['isMain'],
                               ':section'=>$locale['section'],
                               ':subsection'=>$locale['subsection'],
                                ));
    print_r($statement2->errorInfo());
}