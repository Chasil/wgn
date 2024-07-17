<?php
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 26.08.2018
 * Time: 11:52
 */
namespace App\OfferBundle\Tools;

class OfferTitleBuilder
{
  private static function getTypeParts($id)
  {
      $type = array(
          '1'=>['Mieszkanie','w bloku'],
          '2'=>['Mieszkanie','w nowym budynku'] ,
          '3'=>['Mieszkanie','w kamienicy'] ,
          '4'=>['Mieszkanie willowe',''] ,
          '5'=>['Adaptacja strychu ',] ,
          '6'=>['Apartament'],
          '7'=>['Loft'],
          '8'=>['Pokój'],
          '9'=>['Dom wolnostojący',''],
          '10'=>['Dom bliźniak',''],
          '11'=>['Dom w zabudowie szeregowej',''] ,
          '12'=>['Dom z warsztatem',''] ,
          '13'=>['Dom gospodarczy',''] ,
          '14'=>['Dom letniskowy',''] ,
          '15'=>['Willa'],
          '16'=>['Rezydencja'],
          '17'=>['Dwór'],
          '18'=>['Pałac'],
          '19'=>['Zamek'],
          '20'=>['Gospodarstwo rolne',''],
          '21'=>['Działka budowlana', ''],
          '22'=>['Działka rekreacyjna', ''],
          '23'=>['Grunt rolny', ''],
          '24'=>['Grunt leśny', ''],
          '25'=>['Grunt inwestycyjny',''],
          '26'=>['Lokal biurowy',''],
          '27'=>['Lokal usługowy',''],
          '28'=>['Lokal gastronomiczny',''],
          '29'=>['Lokal handlowy',''],
          '30'=>['Biurowiec'],
          '31'=>['Kamienica'],
          '32'=>['Budynek wielorodzinny',''],
          '33'=>['Budynek handlowy',''],
          '34'=>['Budynek usługowy',''],
          '35'=>['Hotel'],
          '36'=>['Motel'],
          '37'=>['Hostel'],
          '38'=>['Pensjonat'],
          '39'=>['Ośrodek wypoczynkowy',''],
          '40'=>['Restauracja'],
          '41'=>['Stadnina'],
          '42'=>['Magazyn'],
          '43'=>['Hala produkcyjna',''],
          '44'=>['Hala przemysłowa', ''],
          '45'=>['Warsztat'],
          '46'=>['Inny komercyjny', ''],
          '47'=>['Garaż na placu', ''],
          '48'=>['Garaż w budynku', ''],
          '49'=>['Garaż podziemny', ''],
          '50'=>['Garaż wolnostojący', ''],
          '51'=>['Miejsce parkingowe', ''],
          '52'=>['Nieruchomość komercyjna', ''],
          '55'=>['Mieszkanie w budynku mieszkalnym', ''],
      );
      if(isset($type[$id])){
          return $type[$id];
      }

      return null;
  }
  private static function getTransactionName($id)
  {
      return $transaction = ($id==1)? 'na sprzedaż' : 'na wynajem';
  }
  private static function getCategoryName($id)
  {
      switch($id){
          case 1:
              $category = 'Mieszkanie';
              break;
          case 2:
              $category = 'Dom';
              break;
          case 3:
              $category = 'Działka';
              break;
          case 4:
              $category = 'Lokal';
              break;
          case 5:
              $category = 'Nieruchomość komercyjna';
              break;
          case 6:
              $category = 'Garaż';
              break;
          default:
              $category = '';
              break;
      }

      return $category;
  }
  public static function prepareTitle($params)
  {
      $name = '';

      $type = self::getTypeParts($params['idType']);
      $transaction = self::getTransactionName($params['idTransaction']);
      $category = self::getCategoryName($params['idCategory']);

      if($type){
          $name = $type [0] . ' ' . $transaction;
          if(!empty($type[1]))
          {
              $name .= ' ' .$type[1];
          }
      }else {
          $name = $transaction .' ' .$category;  
      }

      if($params['city']!=''){
          $name .= ' '.$params['city'];
      }
      if($params['dzielnica']!='' && $params['dzielnica']!=$params['city']){
          $name .= ', '.$params['dzielnica'];
      }
      if($params['osiedle']!='' && $params['osiedle']!=$params['city'] && $params['osiedle']!=$params['dzielnica']){
          $name .= ', '.$params['osiedle'];
      }
      //$name .= ' | ';
      // czytli tutaj zmiany tytułu przy imporcie. 
      return $name;
  }

}