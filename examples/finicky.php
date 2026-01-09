<?php

  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.
  
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
          $_SESSION['finicky']['dices'][$dice_number]['location'] = "cup";
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
      for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
      {
        if(  ($_SESSION['finicky']['trials'] == self::NUMBER_OF_TRIALS)
           ||($_SESSION['finicky']['dices'][$dice_number]['location'] == "cup"))
        {
          $dice_value = -1;
          $dice_show = "#";
        }
        else if($_SESSION['finicky']['dices'][$dice_number]['location'] == "table")
        {
          $dice_value = $_SESSION['finicky']['dices'][$dice_number]['value'];
          $dice_show = "&#".(self::UNICODE_DICE_1+$dice_value-1).";";
          
        }
        else 
        {
          $dice_value = -1;
          $dice_show = "#";
        }
        $dices[] = array("dice_number"=>$dice_number,"dice_value"=>$dice_value,"dice_show"=>$dice_show);
      }
      return $dices;
    }

    public function get_scorelist()
    {
      $scores = array();
      foreach($_SESSION['finicky']['score'] as $category=>$score)
      {
        if($score == "")
        {
          $scoretext = "-";
        }
        else {
          $scoretext = (string)$score." points";
        }
        $action = "Throw all ".$category.": ".$scoretext;
        $scores[] = array("action"=>$action,"category"=>$category,"score"=>$scoretext);
      }
      return $scores;
    }

    public function get_trials()
    {
      return array(0=>array("trials"=>$_SESSION['finicky']['trials']));
    }

    //SETTERS
    public function new_game()
    {
      $this->init_dices(true);
      $this->init_scorelist(true);
      $this->init_trials(true);
    }

    public function roll_dices_out_of_cup()
    {
      if($_SESSION['finicky']['trials'] > 0)
      {
        $_SESSION['finicky']['trials'] = $_SESSION['finicky']['trials'] - 1;

        for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
        {
          if($_SESSION['finicky']['dices'][$dice_number]['location'] == "cup")
          {
            $_SESSION['finicky']['dices'][$dice_number]['value'] = rand(self::DICE_MIN,self::DICE_MAX);
            $_SESSION['finicky']['dices'][$dice_number]['location'] = "table";
          }
        }
      }
    }

    public function throw_dice_in_cup($dice_number)
    {
      if($_SESSION['finicky']['trials'] > 0)
      {
        $_SESSION['finicky']['dices'][$dice_number]['location'] = "cup";
      }
    }

    public function record_score_and_switch_to_new_round($category)
    {
      $dices_are_ready = true;
      $score = 0;
      for($dice_number = 0; $dice_number < self::DICES_AMOUNT; $dice_number++)
      {
        if($_SESSION['finicky']['dices'][$dice_number]['location'] == "cup")
        {
          $dices_are_ready = false;
        }
        else 
        {
          if($category == $_SESSION['finicky']['dices'][$dice_number]['value'])
          {
            $score = $score + $_SESSION['finicky']['dices'][$dice_number]['value'];
          }
        }
      }
      if($dices_are_ready === true)
      {
        $_SESSION['finicky']['score'][$category] = $score;
        $this->init_dices(true);
        $this->init_trials(true);
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
      <header>FiNiCkY <input type="button" value="New Game"/></header>
      <main>
        <div id="trials">
          <span>You can still throw the dices </span><span id="number"><span></span></span><span> times</span>
        </div>
        <div id="scoreboard">
          <div class="score" data_score_category="" data_score_value=""></div>
        </div>
        <div id="cup" title="Click me to roll the dices!">&#x1FAA3;</div>
        <div id="dices">
          <div class="dice" data_dice_number="" data_dice_value=""></div>
        </div>
      </main>
      <footer>made by Sebastian Blume in 2024 for application research at TU Dresden</footer>
    </body>
  </html>
  EOF;

  //view css
  $view_css = <<<EOF
    body { background-color: #59D5E0; font-family: Tahoma;}
    header { border: 10px #fff dashed; font-size: 120px; text-align: center; background-color: #99F5F0; font-family: Courier;}
    #trials { font-weight: bold; font-size: 30px; color: #ffefc7; }
    #scoreboard { min-height: 150px; }
    #scoreboard .score    { cursor: pointer; padding: 3px; background-color: #fff; border: 3px #55f solid; font-size: 18px; font-weight: bold; min-height: 24px; width: 40%; }
    #dices { height: 150px; }
    #dices .dice { float:left; font-size: 100px; margin: 20px; cursor: pointer; width: 120px;} 
    #cup { font-size: 100px; cursor: pointer; width: 120px; height: 120px; }
    @keyframes mymove { 100% {transform: rotate(90deg);}}
    #cup:active { animation: mymove 0.5s 1 forwards; }
  EOF;

  $controller_rules[1]['info']                                                  = "Show amount of trials";
  $controller_rules[1]['xpath']                                                 = "/html/body/main/div[@id='trials']/span[@id='number']/span";
  $controller_rules[1]['request'][1]['model']                                   = 'get_trials';
  $controller_rules[1]['request'][1]['modeldata2dom']['text()']                 = "trials"; 

  $controller_rules[2]['info']                                                  = "Show all dices on the board (not in the cup)";
  $controller_rules[2]['xpath']                                                 = "/html/body/main/div[@id='dices']/div[@class='dice']";
  $controller_rules[2]['request'][1]['model']                                   = 'get_dices';
  $controller_rules[2]['request'][1]['modeldata2dom']['@data_dice_number']      = "dice_number"; 
  $controller_rules[2]['request'][1]['modeldata2dom']['@data_dice_value']       = "dice_value"; 
  $controller_rules[2]['request'][1]['modeldata2dom']['text()']                 = "dice_show"; 
  $controller_rules[2]['user-action'][1]['event']                               = '@click';
  $controller_rules[2]['user-action'][1]['domdata2model']['dice_number']        = '@data_dice_number';
  $controller_rules[2]['user-action'][1]['model']                               = 'throw_dice_in_cup';

  $controller_rules[3]['info']                                                  = "Show the scorelist";
  $controller_rules[3]['xpath']                                                 = "/html/body/main/div[@id='scoreboard']/div[@class='score']";
  $controller_rules[3]['request'][1]['model']                                   = 'get_scorelist';
  $controller_rules[3]['request'][1]['modeldata2dom']['text()']                 = "action"; 
  $controller_rules[3]['request'][1]['modeldata2dom']['@data_score_category']   = "category"; 
  $controller_rules[3]['request'][1]['modeldata2dom']['@data_score_value']      = "score"; 
  $controller_rules[3]['user-action'][1]['event']                               = '@click';
  $controller_rules[3]['user-action'][1]['domdata2model']['category']           = '@data_score_category';
  $controller_rules[3]['user-action'][1]['model']                               = 'record_score_and_switch_to_new_round';

  $controller_rules[4]['info']                                                  = "Roll the dices";
  $controller_rules[4]['xpath']                                                 = "/html/body/main/div[@id='cup']";
  $controller_rules[4]['user-action'][1]['event']                               = '@click';
  $controller_rules[4]['user-action'][1]['domdata2model']                       = null;
  $controller_rules[4]['user-action'][1]['model']                               = 'roll_dices_out_of_cup';

  $controller_rules[5]['info']                                                  = "New game";
  $controller_rules[5]['xpath']                                                 = "/html/body/header/input";
  $controller_rules[5]['user-action'][1]['event']                               = '@click';
  $controller_rules[5]['user-action'][1]['domdata2model']                       = null;
  $controller_rules[5]['user-action'][1]['model']                               = 'new_game';

  $template = new autotheme_mvc_controller($name,$model,$view_xhtml_template,$view_css,$controller_rules);
  $template->run();

?>