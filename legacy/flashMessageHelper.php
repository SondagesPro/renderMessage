<?php
/**
 * messageHelper part of renderMessage Plugin
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 0.0.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
namespace renderMessage;
use Yii;

class flashMessageHelper{

  /**
   * @var Singleton
   * @access private
   */
  private static $_instance = null;
  /**
   * @var array[]
   * @access private
   */
  private $messages = array();
  /**
  * Construct
  * @param void
  * @return void
  */
  private function __construct() {

  }
  /* Init
  * @param void
  * @return Singleton
  */
  public static function getInstance() {
     if(is_null(self::$_instance)) {
       self::$_instance = new flashMessageHelper();
     }
     return self::$_instance;
  }
  /**
   * add a flash message to existing flash
   * @param string $message : message to be added
   * @param string $type : (default|success|warning|error)
   * return @void
   */
  public function addFlashMessage($message,$type='info'){
    $this->messages[]=array('message'=>$message,'type'=>$type);
  }

  /**
   * render the existing public message at this time
   * @return string
   */
  public function renderFlashMessage(){
    if(empty($this->messages)){
      return;
    }
    $lsApiVersion=\renderMessage\messageHelper::rmLsApiVersion();
    $renderData['messages']=$this->messages;
    $renderData['assetUrl']=Yii::app()->assetManager->publish(Yii::getpathOfAlias("renderMessage.assets.{$lsApiVersion}"));
    return Yii::app()->controller->renderPartial("renderMessage.views.{$lsApiVersion}.flashContainer",$renderData,1);
  }

}
