<?
  $version = "29.125.test";
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_mvc_controller.php");

  //init
  $name                = "dices";
  $model_parts         = array();
  $view_xhtml_template = null;
  $controller_rules    = array();

  //model parts
  class model_dices
  {
    private $dices = array();
    const DICES_NUMBER   = 5;
    const DICE_MIN       = 1;
    const DICE_MAX       = 6;
    const UNICODE_DICE_1 = 9856;

    public function get_dices()
    {
      $dices = array();
      for($dice_number=0;$dice_number<self::DICES_NUMBER;$dice_number++)
      {
        if(isset($_SESSION['dontcode'][$dice_number]))
          $dice = $_SESSION['dontcode'][$dice_number];
        else
        {
          $dice = rand(self::DICE_MIN,self::DICE_MAX);
          $_SESSION['dontcode'][$dice_number] = $dice;
        }
        $dices[$dice_number]['dice_number']   = $dice_number;
        $dices[$dice_number]['dice_value']    = $dice;
        $dices[$dice_number]['dice_unicode']  = "&#".(self::UNICODE_DICE_1+$dice-1).";";
      }//for
      return $dices;
    }//public function

    public function roll_all_dices()
    { 
      for($dice_number=0;$dice_number<self::DICES_NUMBER;$dice_number++)
        $this->roll_single_dice($dice_number);
      return true;
    }//public function

    public function roll_single_dice($dice_number)
    {
      $_SESSION['dontcode'][$dice_number] = rand(self::DICE_MIN,self::DICE_MAX);
      return true;
    }//public function
  }//class
  $model = new model_dices();

  //view
  //view template
  $view_xhtml_template = <<<EOF
  <html>
    <head>
      <title>Roll the dices!</title>
    </head>
    <body>
      <header>
        <marquee class="laufschrift">Roll the dices!</marquee>
      </header>
      <input type="button" class="action elementToFadeInAndOut" value="Alles würfeln!"/>
      <main id="game_board">
        <div id="game_dice" class="dice rotating" data_dice_value="" data_dice_number=""></div>
      </main>
      <div id="clear" style="clear:left;"><div/></div>
    </body>
  </html>
  EOF;

  //view css
  $view_css = <<<EOF

  .laufschrift { 
    font-family: Arial;
    font-size: 100px;
  }
  div.dice { font-size:200px;float:left;}
  @-webkit-keyframes rotating {
    from{-webkit-transform: rotate(0deg);}
    to{-webkit-transform: rotate(360deg);}}
  .rotating {-webkit-animation: rotating 2s linear;}}
  .elementToFadeInAndOut:active {opacity: 1;animation: fade 1s linear;background-color:#fff;}
  .action {font-size: 20px;font-family:Tahoma;width:150px;height:150px;}
  @keyframes fade { 
    0%,100% { opacity: 0 }
    50% { opacity: 1 }}
  EOF;

  //controller rules
  $controller_rules[1]['info']                              = "Alle würfeln";
  $controller_rules[1]['xpath']                             = "/html/body/input";
  $controller_rules[1]['request']                           = null;
  $controller_rules[1]['user-action'][1]['event']           = '@click';
  $controller_rules[1]['user-action'][1]['domdata2model']   = null;
  $controller_rules[1]['user-action'][1]['model']           = 'roll_all_dices';

  $controller_rules[2]['info']                                                  = "Einzeln würfeln";
  $controller_rules[2]['xpath']                                                 = "/html/body/main[@id='game_board']/div[@id='game_dice']";
  $controller_rules[2]['request'][1]['model']                                   = 'get_dices';
  $controller_rules[2]['request'][1]['modeldata2dom']['@data_dice_number']      = "dice_number"; 
  $controller_rules[2]['request'][1]['modeldata2dom']['@data_dice_value']       = "dice_value"; 
  $controller_rules[2]['request'][1]['modeldata2dom']['text()']                 = "dice_unicode"; 
  $controller_rules[2]['user-action'][1]['event']                               = '@click';
  $controller_rules[2]['user-action'][1]['domdata2model']['dice_number']        = '@data_dice_number';
  $controller_rules[2]['user-action'][1]['model']                               = 'roll_single_dice';
  
  $dices = new dontcode_mvc_controller($name,$model,$view_xhtml_template,$view_css,$controller_rules);

  $dices->run();
?>