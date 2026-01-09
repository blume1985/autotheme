<?php

  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.
  
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_mvc_controller.php");

  //init
  $name                = "template";
  $model_parts         = array();
  $view_xhtml_template = null;
  $controller_rules    = array();

  //model parts
  class model_template
  {
    const BOARD_X = 10;
    const BOARD_Y = 8;
    public function __construct()
    {
      $this->init_robot_position();
      $this->init_board();
    }
    public function init_board($reinit = false)
    {
      for($x=0;$x<self::BOARD_X;$x++)
      {
        for($y=0;$y<self::BOARD_Y;$y++)
        {
          if(  ($x%2==0)
             &&($y%2==0))
          {
            $field = rand(1,2);
          }
          else
          {
            $field = 1;
          }
          if(!(isset($_SESSION['robot']['board'][$x][$y]))||($reinit==true))
          {
            $_SESSION['robot']['board'][$x][$y] = $field;
          }
        }
      }
    }

    public function init_robot_position()
    {
      if(!isset($_SESSION['robot']['robot']['x']))
        $_SESSION['robot']['robot']['x'] = 5;
      if(!isset($_SESSION['robot']['robot']['y']))
      $_SESSION['robot']['robot']['y'] = 4;
    }

    public function get_board()
    {
      $bricks = array();
      for($y=0;$y<self::BOARD_Y;$y++)
      {
        for($x=0;$x<self::BOARD_X;$x++)
        {
        
          if(($_SESSION['robot']['robot']['x'] == $x)&&($_SESSION['robot']['robot']['y'] == $y))
          {
            $field = 3;
          }
          else
          {
            $field = $_SESSION['robot']['board'][$x][$y];
          }
          
          if($field == 1) $unicode = "&#x20;";
          else if($field == 2) $unicode ="&#x1F9F1;";
          else if($field == 3) $unicode ="&#x1F916";

          if(($x == 0)&&($y > 0)) $class="newline brick";
          else $class="flow brick";

          $bricks[] = array("x"=>$x,"y"=>$y,"unicode"=>$unicode,"title"=>"x:".$x.",y:".$y,"class"=>$class);
        }
      }
      return $bricks;
    }

    public function click_field($x,$y)
    {
      if(($x>$_SESSION['robot']['robot']['x'])&&($y==$_SESSION['robot']['robot']['y'])) $new_x = $x + 1;
      else if(($x<$_SESSION['robot']['robot']['x'])&&($y==$_SESSION['robot']['robot']['y'])) $new_x = $x - 1;
      else $new_x = $x;

      if(($y>$_SESSION['robot']['robot']['y'])&&($x==$_SESSION['robot']['robot']['x'])) $new_y = $y + 1;
      else if(($y<$_SESSION['robot']['robot']['y'])&&($x==$_SESSION['robot']['robot']['x'])) $new_y = $y - 1;
      else $new_y = $y;
    }

    public function reinit()
    {
      $this->init_board(true);
    }
  }
  $model = new model_template();

  //view
  //view template
  $view_xhtml_template = <<<EOF
  <html>
    <head>
    </head>
    <body>
      <header>robot simulation <input type="button" value="new simulation"/></header>
      <main>
        <div id="board">
          <div class="" data_x="" data_y="" title=""> </div>
        </div>
      </main>
    </body>
  </html>
  EOF;

  //view css
  $view_css = <<<EOF
    .brick { width: 30px; height: 30px; border: 1px solid #000; font-size: 20px; };
    .newline { clear: left; }
    .flow {float: left; }
  EOF;

  $controller_rules[1]['info']                                                  = "Simulationsfeld";
  $controller_rules[1]['xpath']                                                 = "/html/body/main/div[@id='board']/div";
  $controller_rules[1]['request'][1]['model']                                   = 'get_board';
  $controller_rules[1]['request'][1]['modeldata2dom']['@class']                 = "class"; 
  $controller_rules[1]['request'][1]['modeldata2dom']['@data_x']                = "x"; 
  $controller_rules[1]['request'][1]['modeldata2dom']['@data_y']                = "y"; 
  $controller_rules[1]['request'][1]['modeldata2dom']['@title']                 = "title"; 
  $controller_rules[1]['request'][1]['modeldata2dom']['text()']                 = "unicode"; 
  $controller_rules[1]['user-action'][1]['event']                               = '@click';
  $controller_rules[1]['user-action'][1]['domdata2model']                       = null;
  //$controller_rules[1]['user-action'][1]['domdata2model']['x']                  = '@data_x';
  //$controller_rules[1]['user-action'][1]['domdata2model']['y']                  = '@data_y';
  //$controller_rules[1]['user-action'][1]['model']                               = 'click_field';
  $controller_rules[1]['user-action'][1]['model']                               = 'reinit';

  $controller_rules[2]['info']                                                  = "New simulation";
  $controller_rules[2]['xpath']                                                 = "/html/body/header/input";
  $controller_rules[2]['user-action'][1]['event']                               = '@click';
  $controller_rules[2]['user-action'][1]['domdata2model']                       = null;
  $controller_rules[2]['user-action'][1]['model']                               = 'reinit';

  $template = new autotheme_mvc_controller($name,$model,$view_xhtml_template,$view_css,$controller_rules);
  $template->run();

?>