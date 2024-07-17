<?php

$dbhost = "localhost";
$dbname = "wgn_test";
$dbname2 = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";

try {
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db2 = new PDO("mysql:host=$dbhost;dbname=$dbname2",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$sql = "SELECT w.nazwa AS province FROM wojewodztwo w";

$statement = $db->prepare($sql);
$statement->execute();
$locales = $statement->fetchAll(PDO::FETCH_ASSOC);

} catch(\Exception $e){
    echo $e->getMessage();
}
$statement2 = $db->prepare('INSERT INTO location_autocomplete (name,province,district,city,isMain) VALUES(:name,:province,:district,:city,:isMain)');
foreach($locales as $locale){

    $statement2->execute(array(':name'=>$locale['province'],
                               ':province'=>$locale['province'],
                               ':district'=>null,
                               ':city'=>null,
                               ':isMain'=>1,
                                ));
    print_r($statement2->errorInfo());
}