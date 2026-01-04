<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->
   
<?php

  $version = "0.1.3";
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_mvc_controller.php");

  //init
  $name                = "finicky";
  $model_parts         = array();
  $view_xhtml_template = null;
  $controller_rules    = array();

  //model parts
  class model_finicky
  {
    const DICES_AMOUNT = 5;
    const DICE_MIN    = 1;
    const DICE_MAX    = 6;
    const UNICODE_DICE_1 = 9856;

    const NUMBER_OF_TRIALS = 3;

    public function __construct()
    {
      $this->init_dices();
      $this->init_scorelist();
      $this->init_trials();
    }

    private function init_dices($reinit = false)
    {

      for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
      {
        if(  (!isset($_SESSION['finicky']['dices'][$dice_number]['value']))
           ||($reinit === true))
        {
          $_SESSION['finicky']['dices'][$dice_number]['value'] = -1;
          $_SESSION['finicky']['dices'][$dice_number]['cup']   = true;
        }
      }
    }

    private function init_scorelist($reinit = false)
    {
      for($category = self::DICE_MIN; $category <= self::DICE_MAX; $category++)
      {
        if(  (!isset($_SESSION['finicky']['score'][$category]))
           ||($reinit === true))
        {
          $_SESSION['finicky']['score'][$category] = "";
        }
      }
    }

    private function init_trials($reinit = false)
    {
      if(  (!isset($_SESSION['finicky']['trials']))
         ||($reinit === true))
      {
        $_SESSION['finicky']['trials'] = self::NUMBER_OF_TRIALS;
      }
    }

    //GETTERS
    public function get_dices()
    {
      $dices = array();
      foreach($_SESSION['finicky']['dices'] as $dice_number=>$dice)
      {
        if($dice['cup'] === true)
        {
          $dice_value = -1;
          $dice_show = "";
        }
        else if($dice['cup'] === false)
        {
          $dice_value = $dice['value'];
          $dice_show = "&#".(self::UNICODE_DICE_1+$dice_value-1).";";
        }
        $dices[] = array("dice_number"=>$dice_number,"dice_value"=>$dice_value,"dice_show"=>$dice_show);
      }
    }

    public function get_scorelist()
    {
      $scores = array();
      foreach($_SESSION['finicky']['score'] as $category=>$score)
      {
        $scores[] = array("category"=>$category,"score"=>$score);
      }
      return $scores;
    }

    public function get_trials()
    {
      return array("trials"=>$_SESSION['finicky']['trials']);
    }

    //SETTERS
    public function new_game()
    {
      $this->init_dices(true);
      $this->init_scorelist(true);
      $this->init_trials(true);
    }

    public function roll_dices_out_of_cup($dice_number)
    {
      $_SESSION['finicky']['trials'] = $_SESSION['finicky']['trials'] - 1;
      for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
      {
        $_SESSION['finicky']['dices'][$dice_number]['value'] = rand(self::DICE_MIN,self::DICE_MAX);
        $_SESSION['finicky']['dices'][$dice_number]['cup'] = false;
      }
    }

    public function throw_dice_in_cup($dice_number)
    {
      if($_SESSION['finicky']['trials'] > 0)
      {
        $_SESSION['finicky']['dices'][$dice_number]['cup'] = true;
      }
    }

    public function record_score_and_switch_to_new_round($category)
    {
      $dices_are_ready = true;
      $score = 0;
      for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
      {
        if($_SESSION['finicky']['dices'][$dice_number]['cup'] == true)
        {
          $dices_are_ready = false;
        }
        else 
        {
          if($category == $score = $score + $_SESSION['finicky']['dices'][$dice_number]['value'])
          {
            $score = $score + $_SESSION['finicky']['dices'][$dice_number]['value'];
          }
        }
      }
      if($dices_are_ready === true)
      {
        $_SESSION['finicky']['score'][$category] = $score;
      }
    }
  }
  $model = new model_finicky();

  //view
  //view template
  $view_xhtml_template = <<<EOF
  <html>
    <head>
      <title>FiNiCkY</title>
    </head>
    <body>
      <header></header>
      <main></main>
    </body>
  </html>
  EOF;

  //view css
  $view_css = <<<EOF
  EOF;

  //controller rules
  /*
  $controller_rules[1]['info']                                                  = "Just for you";
  $controller_rules[1]['xpath']                                                 = "/html/body/element";
  $controller_rules[1]['request'][1]['model']                                   = 'get_method';
  $controller_rules[1]['request'][1]['modeldata2dom']['@attribute']             = "variable"; 
  $controller_rules[1]['user-action'][1]['event']                               = '@event';
  $controller_rules[1]['user-action'][1]['domdata2model']['variable']           = '@attribute';
  $controller_rules[1]['user-action'][1]['model']                               = 'set_method';
  */

  $template = new autotheme_mvc_controller($name,$model,$view_xhtml_template,$view_css,$controller_rules);
  $template->run();

?>