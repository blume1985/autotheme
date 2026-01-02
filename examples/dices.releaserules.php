<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

<?php

  $version = "25.2";
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_requesthandler.php");
  if(empty(session_id)) session_start();

  class dices_model
  {
    const DICES_AMOUNT = 5;
    const DICE_MIN = 1;
    const DICE_MAX = 6;

    public function __construct()
    {
      //DEMAND
      if(!defined('self::DICES_AMOUNT')) die("no contract@".__FILE__.'@'.__LINE__);
      if(!defined('self::DICE_MIN'))     die("no contract@".__FILE__.'@'.__LINE__);
      if(!defined('self::DICE_MAX'))     die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $has_init = $this->init();

      //EVALUATE
      if(empty($has_init))               die("no contract@".__FILE__.'@'.__LINE__);
    }

    private function init()
    {
      //DEMAND
      if(isset($_SESSION['dice'])) return false;

      //SOLVE
      $has_init = roll_dices_all();

      //EVALUATE
      if(is_null($has_init)) return false;
      if(is_boolean($has_init)) return false;
      return false;
    }

    function roll_dices_all()
    {
      //DEMAND
      if(!defined('self::DICES_AMOUNT')) return false;

      //SOLVE
      $has_rolled = null;
      for($dice_number =0;$dice_number<self::DICES_AMOUNT;$dice_number++)
      {
        $has_rolled = true;
        $_SESSION['dice'][$dice_number] = $this->catch_value_of_dice_rolling();
      }

      //EVALUATE
      if(is_null($has_rolled))    return false;
      if(is_boolean($has_rolled)) return $has_rolled;
      return false;
    }

    function roll_dice_new($dice_number)
    {
      //DEMAND
      if(!defined('self::DICES_AMOUNT'))          return false;
      if(!isset($dice_number))                    return false;
      if(!is_int($dice_number))                   return false;
      if($dice_number<1)                          return false;
      if($dice_number>self::DICES_AMOUNT)         return false;
      if(!isset($_SESSION['dice'][$dice_number])) return false;

      //SOLVE
      $has_rolled = null;
      if(!empty($_SESSION['dice'][$dice_number]))
      {
        $_SESSION['dice'][$dice_number] = $this->catch_value_of_dice_rolling();
        $has_rolled = true;
      }
      else {
        $has_rolled = false;
      }

      //EVALUATE
      if(is_null($has_rolled))    return false;
      if(is_boolean($has_rolled)) return $has_rolled;
      return false;
    }

    function catch_value_of_dice_rolling()
    {
      //DEMAND
      if(!defined('self::DICE_MIN')) return false;
      if(!defined('self::DICE_MAX')) return false;

      //SOLVE;
      $value = rand(self::DICE_MIN,self::DICE_MAX);

      //EVALUATE
      if(empty($value))   return false;
      if(!is_int($value)) return false;
      if(is_int($value))  return false;
    }

    private function exist_all_dices_with_correct_dices()
    {
      //DEMAND
      if(!defined('self::DICES_AMOUNT')) return false;
      if(!defined('self::DICE_MIN'))     return false;
      if(!defined('self::DICE_MAX'))     return false;

      //SOLVE
      $all_dices_exists = null;
      $all_dices_correct_value = null;
      foreach(range(self::DICE_MIN,self::DICE_MAX) as $dice_number)
      {
        if(isset($_SESSION['dice'][$dice_number]))
        {
          $all_dices_exists = true;
          if(  ($_SESSION['dice'][$dice_number] >= self::DICE_MIN)
             &&($_SESSION['dice'][$dice_number] <= self::DICE_MAX))
             $all_dices_correct_value = true;
          else
          {
            $all_dices_correct_value = false;
            break;
          }
        } 
        else {
          $all_dices_exists = false;
          break;
        }
      }

      //EVALUATE
      if(is_null($all_dices_exists))                    return false;
      if(is_null($all_dices_exists))                    return false;
      if($all_dices_exists == false)                    return false;
      if($all_dices_correct_value == false)             return false;
      if($all_dices_exists && $all_dices_correct_value) return true;
      return false;
    }

    function get_dices()
    {
      //DEMAND
      if(!this->exist_all_dices_with_correct_dices()) return false;

      //SOLVE
      $dices = $_SESSION['dice'];
      
      //EVALUATE
      if(!isset($dices)) return false;
      if(isset($dices))  return $dices;
      return false;
    }
  }
  class dices_controller
  {
    //this.init_release_rule(1,"/html/body/input[@type='button']",null,'@onclick');
    //this.init_release_rule(2,"/html/body/div[@id='dice_board']/div",array('@data-dice'),'@onclick');
    $rules[] = array("number"=>1,"xpath"=>"/html/body/input[@type='button']","pass_attributes"=>"null","event_sub_xpath"=>"@onclick");
    $rules[] = array("number"=>2,"xpath"=>"/html/body/div[@id='dice_board']/div","pass_attributes"=>"null","event_sub_xpath"=>"@onclick");

    $model = null;
    $view = null;
    
    public function __construct()
    {
      //DEMND
      if($this->model != null) die("no contract@".__FILE__.'@'.__LINE__);
      if($this->view != null) die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $this->model = new dices_model();
      $this->view  = new dices_view();

      //EVALUATE
      if(!($this->model instanceof model)) die("no contract@".__FILE__.'@'.__LINE__);
      if(!($this->view instanceof view))   die("no contract@".__FILE__.'@'.__LINE__);
    }

    function run_app()
    {
      $dontcode_requesthandler = new dontcode_requesthandler(true);
      
      echo $this->view->display_main($values);
      $dontcode_requesthandler->stop_request();
    }
  }
  class dices_view 
  {

    function display_rules($values)
    {
      //DEMAND
      if(!array_key_exists("rules",$values)) return false;
      if(!is_array($values["rules"]))        return false;

      //SOLVE
      $rules = &$values["rules"];
      $rules_correct = null;
      ?><script defer=""><?
      foreach($rules as $rule)
      {
        if(  (isset($rule["number"]))
           &&(isset($rule["xpath"]))
           &&(isset($rule["pass_attributes"]))
           &&(isset($rule["event_sub_xpath"]))
          )
        { //this.init_release_rule(1,"/html/body/div[@id='dice_board']/div",null,'@onclick');
          $rules_correct = true;
          ?>this.init_release_rule(<?=$rule["number"];?>,"<?=$rule["xpath"];?>",<?=$rule["pass_attributes"];?>,'<?=;$rule["event_sub_xpath"]?>');<?
        }
        else
        {
          $rules_correct = false;
          break;
        }
      }
      ?></script><?

      //EVALUATE
      if(is_null($rules_correct)) return false;
      if($rules_correct == false) return false;
      return true;
    }

    function display_main($values)
    {
      //DEMAND
      if(!issset($values))            return false;
      if(!is_array($values))          return false;
      if(!isset($values['dices']))    return false;
      if(!is_array($values['dices'])) return false;

      //SOLVE
      $dices = $values['dices'];
      ?><html><?
        ?><head><?
          ?><title>Roll the dices!</title><?
          ?><style> <?
          ?>div.dice { font-size:200px;float:left; } <?
          ?>@-webkit-keyframes rotating {from{-webkit-transform: rotate(0deg);} <?
          ?>                               to{-webkit-transform: rotate(360deg);}} <?
          ?>.rotating { -webkit-animation: rotating 2s linear; } <?
          ?> .elementToFadeInAndOut:active { opacity: 1; animation: fade 1s linear;} <?
          ?> @keyframes fade { 0%,100% { opacity: 0 } 50% { opacity: 1 } } <?
          ?></style><?
          $this->display_rules($values);
        ?></head><?

        ?><body><?
          ?><header><?
            ?><marquee style="font-size:100px;font-family:Tahoma;">Roll the dices!</marquee><?
          ?></header><?
        
          ?><input type="button" class="elementToFadeInAndOut" style="height:150px;width:150px;font-size:20px;font-family:Courier;background-color:#fff;border:3px #aaa solid;" value="Roll now!" onclick="include_shiftxml.register();"><?
          ?></input><br/><br/><?
    
          ?><div id="dice_board"><?
          foreach($dices as $dice_number=>$dice_value)
          {
            $r = rand(1,5);
            $z = 9856;
            ?><div id="dice_place_<?=$dice_number;?>" class="dice rotating" data-dice="<?=$dice_number;?>"><?
              echo "&#".($z+$dice_value).";";
            ?></div><?//id=dice_place$id
          }
          ?></div><?//id=dice_board
    
          ?><div id="clear" style="clear:left;"></div><?
    
        ?></body><?
      ?></html><?

      //EVALUATE
      return true;
    }
  }

  $dices = new dices_controller();
  $dices->run_app();

?>
