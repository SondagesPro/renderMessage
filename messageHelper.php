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

class messageHelper{



  /**
   * @var array $language language to be used in public
   */
  public $sLanguage;
  /**
   * @var the survey id
   */
  public $iSurveyId;
  /**
   * @var the templateName
   */
  public $sTemplate;
  /**
   * @var boolean for version lesser than 3.0 : usage of completed.pstpl
   */
  public $useCompletedTemplate=true;

  public function __construct() {
    /* Find actual survey id */
    $this->iSurveyId=(int)Yii::app()->request->getParam('surveyid',Yii::app()->request->getParam('sid'));
    if(!$this->iSurveyId){
      $this->iSurveyId=(int) Yii::app()->getConfig('surveyID'); // 0 if not set
    }
    /* Find actual template*/
    $oSurvey=\Survey::model()->findByPk($this->iSurveyId);
    if($oSurvey){
      $this->sTemplate=$oSurvey->template;
    } else {
      $this->sTemplate=Yii::app()->getConfig('defaulttemplate');
    }
    /* Find actual language*/
    $this->sLanguage=Yii::app()->request->getParam('lang');
    if(!$this->sLanguage){
      if($oSurvey){
        $this->sLanguage=$oSUrvey->language;
      } else {
        $this->sLanguage=Yii::app()->getConfig('defaultlang');
      }
    }
  }
  /**
   * render the error to be shown
   * @param $setting : the setting to be used
   *
   * return @void
   */
  public function render($message)
  {
    // Set the language for templatereplace
    SetSurveyLanguage($this->iSurveyId, $this->sLanguage);
    $lsVersion=App()->getConfig("versionnumber");
    $aVersion=explode(".",$lsVersion);
    Yii::app()->setConfig('surveyID',$this->iSurveyId);
    SetSurveyLanguage($this->iSurveyId, $this->sLanguage);
    $reData=array(
      's_lang'=>$this->sLanguage
    );
    $message=templatereplace($message,array(),$reData);
    if($aVersion[0]==2 && $aVersion[1]<=6)
    {
      $this->_renderMessage206($message,$this->sTemplate);
    }
    else
    {
      $this->_renderMessage250($message,$this->sTemplate);
    }
  }

  /**
   * render a public message for 2.06 and lesser
   */
  private function _renderMessage206($message,$template)
  {
    $templateDir=Template::getTemplatePath($template);
    $renderData['message']=$message;
    Yii::app()->controller->layout='bare';
    $renderData['language']=$this->sLanguage;
    if (getLanguageRTL($this->sLanguage))
    {
      $renderData['dir'] = ' dir="rtl" ';
    }
    else
    {
      $renderData['dir'] = '';
    }
    $renderData['templateDir']=$templateDir;
    $renderData['useCompletedTemplate']=$this->useCompletedTemplate;
    Yii::app()->controller->render("renderMessage.views.2_06.public",$renderData);
    Yii::app()->end();
  }
  /**
   * render a public message for 2.50 and up
   */
  private function _renderMessage250($message,$template)
  {
    $oTemplate = \Template::model()->getInstance($template);
    $templateDir= $oTemplate->viewPath;
    $renderData['message']=$message;
    Yii::app()->controller->layout='bare';
    $renderData['language']=$this->sLanguage;
    if (getLanguageRTL($this->sLanguage))
    {
      $renderData['dir'] = ' dir="rtl" ';
    }
    else
    {
      $renderData['dir'] = '';
    }
    $renderData['templateDir']=$templateDir;
    $renderData['useCompletedTemplate']=$this->useCompletedTemplate;
    Yii::app()->controller->render("renderMessage.views.2_50.public",$renderData);
    Yii::app()->end();
  }

}
