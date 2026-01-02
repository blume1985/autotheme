<?

  $version = "26.1";
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_requesthandler.php");
  if(empty(session_id)) session_start();

  class model
  {

  }

  class controller 
  {
    $name  = null;
    $html  = null;

    $model = null;
    $view  = null;

    public function __construct($name)
    {
      //DEMAND

      //SOLVE
      $this->name  = $name;
      $this->model = new model();
      $this->view  = new view();

      //EVALUATE

    }

    public function set_rules_from_array($rules)
    {
      //DEMAND

      //SOLVE
      $rules = array();

      $rules[1]['info']                = "Neu würfeln";
      $rules[1]['xpath']               = "/html/body/input";
      $rules[1]['session']             = null;
      $rules[1]['provide']['function'] = null;
      $rules[1]['provide']['map']      = null;
      $rules[1]['release']['function'] = 'roll_dice';
      $rules[1]['release']['pass']     = array();
      $rules[1]['release']['event']    = 'click';

      $rules[2]['info']                = "Neu würfeln";
      $rules[2]['xpath']               = "/html/body/div[@id='dice_board']/div";
      $rules[2]['session']             = null;
      $rules[2]['provide']['function'] = 'get_dices';
      $rules[2]['provide']['map']['@id'] = '@id{dice_number}';
      $rules[2]['provide']['map'][''] = '/';
      $rules[2]['release']['function'] = 'roll_dice';
      $rules[2]['release']['pass']     = array();
      $rules[2]['release']['event']    = 'click';

      //EVALUATE
      if(empty($rules))  return false;
      if(!empty($rules)) return $rules;
      return false;
    }

    public function run_app()
    {

    }
  }
  class view 
  {
    $xhtml_document_dom = null;

    public function add_rule_release()
    {

    }

    public function add_rule_provide()
    {

    }

    public function get_main($rules_provide,$rules_release) //generic function
    {
      //DEMAND
      if(!empty(ob_list_handlers())) return false;

      //SOLVE
      ob_start();
      $has_shown_xhtml_template = $this->show_xhtml_template();
      $xhtml = ob_get_clean();



      //EVALUATE
      if($has_shown == false)        return false;
      if(empty($xhtml))              return false;
      if(!empty($xhtml))             return $xhtml;
      return false;
    }

    public function show_xhtml_template() //indidual function
    {
      //DEMAND
      if(empty(ob_list_handlers())) return false;

      //SOLVE
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
        ?></head><?

        ?><body><?
          ?><header><?
            ?><marquee style="font-size:100px;font-family:Tahoma;">Roll the dices!</marquee><?
          ?></header><?
        
          ?><input type="button" class="elementToFadeInAndOut" style="height:150px;width:150px;font-size:20px;font-family:Courier;background-color:#fff;border:3px #aaa solid;" value="Roll now!" onclick="include_shiftxml.register();"><?
          ?></input><br/><br/><?
    
          ?><div id="dice_board"><?
            ?><div id="dice_place_" class="dice rotating" data-dice="1"><?
            ?></div><?
          ?></div><?
          ?><div id="clear" style="clear:left;"></div><?
    
        ?></body><?
      ?></html><?

      //EVALUATE
      return true;
    }

    function get_rules($rules)
    {
      //DEMAND
      if(!empty(ob_list_handlers())) return false;

      //SOLVE
      ob_start();
      $has_shown = $this->show_rules;
      $xhtml = ob_get_clean();

      //EVALUATE
      if($has_shown == false)        return false;
      if(empty($xhtml))              return false;
      if(!empty($xhtml))             return $xhtml;
      return false;
    }

    function show_rules($rules)
    {
      //DEMAND
      if(!is_array($rules))        return false;

      //SOLVE
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


  }

?>