<?php
$dbhost = "localhost";
$dbname = "wgn_db";
$dbusername = "wgn_dbadmin";
$dbpassword = "FBUX2#i]taG&";
$db = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbusername,$dbpassword,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$category = 71;
include('simple_html_dom.php');
$page = 1;
while($page>1){
//$url = "http://www.komercja24.pl/category/biurowce-2/page/".$page."/";
$url = "http://www.komercja24.pl/category/tereny-inwestycyjne-2/";
$html = file_get_html($url);
$lis = $html->find('ul.category-list li');
$arts = array_reverse($lis);
foreach($arts as $li){
    $art = $li->find('a')[0]->href;
    $img = $li->find('a')[0]->find('img')[0]->src;
    $html2 = file_get_html($art);
    $title = $html2->find('h1.entry-title')[0]->innertext;
    $intro = $html2->find('div.article-content p')[0]->innertext;
    $intro = preg_replace("/<img[^>]+\>/i", "", $intro);
    $content = '';
    $i = 0;
    foreach($html2->find('div.article-content p') as $p){
        if($i>0){
            $content .= $p->innertext;
        }
        $i++;
    }
    $statement = $db->prepare('select Max(ordering) AS ordering FROM article WHERE category_id=:category_id LIMIT 1');
    $statement->execute(array(':category_id'=>$category));
    $max = $statement->fetch(PDO::FETCH_ASSOC);
    $ordering = $max['ordering'];
    $ordering++;
    echo $ordering;
    $info = pathinfo($img);
    //print_r($info);
    $statement2 = $db->prepare('INSERT INTO article (category_id,name,intro,content,createDate,publishDate,isPublish,ordering,mainPhoto) VALUES (:category_id,:name,:intro,:content,NOW(),NOW(),1,:ordering,:photo)');
    $statement2->execute(array(':category_id'=>$category,':name'=>$title,':intro'=>$intro,':content'=>$content,':ordering'=>$ordering,':photo'=>$info['basename']));
    $id = $db->lastInsertId();

    $path = __DIR__ . '/../uploads/articles/'.$id;
    if(!file_exists($path)){
       mkdir($path);
    }
     @copy($img,$path.'/'.$info['basename']);
    echo $id;
}
    $page--;
}


