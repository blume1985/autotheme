<?

  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.

  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_requesthandler.php");
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_mvc_model.php");
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_mvc_view.php");
  if(empty(session_id())) session_start();

  class autotheme_mvc_controller 
  {
    private $name  = null;

    private $requesthandler = null;
    private $controller_rules = null;

    private $model = null;
    private $view  = null;

    public function __construct($name,$model,$view_xhtml_template,$view_css,$controller_rules)
    {
      //DEMAND
      if($this->requesthandler !== null) die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($name))                   die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $this->name  = $name;

      $this->requesthandler = new autotheme_requesthandler(false);
      if($this->check_rules_if_they_are_formally_valid($controller_rules) === true)
      {
        $this->controller_rules = $controller_rules;
      }
      
      $this->model = new autotheme_mvc_model($name,$model);
      $this->view  = new autotheme_mvc_view($name,$view_xhtml_template);

      $has_deposit_css = $this->deposit_css($view_css);
      $has_deposit_rules_user_action = $this->deposit_rules_user_action();

      //EVALUATE
      if(!($this->requesthandler instanceof autotheme_requesthandler)) die("no contract@".__FILE__.'@'.__LINE__);
      if(!is_string($this->name))                                     die("no contract@".__FILE__.'@'.__LINE__);
      if($this->controller_rules === null)                            die("no contract@".__FILE__.'@'.__LINE__);
      if(!($this->model instanceof autotheme_mvc_model))               die("no contract@".__FILE__.'@'.__LINE__);
      if(!($this->view instanceof autotheme_mvc_view))                 die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_deposit_css))                                     die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_deposit_rules_user_action))                       die("no contract@".__FILE__.'@'.__LINE__);
    }//function

    private function deposit_css($view_css)
    {
      //DEMAND
      if(!is_string($this->name)) return false;

      //SOLVE
      $has_deposit_css = null;
      try 
      {
        $_SESSION['autotheme'][$this->name]['css'] = $view_css;
        $has_deposit_css = true;
      }//try 
      catch (\Throwable $th) 
      {
        $has_deposit = false;
      }//catch

      //EVALUATE
      if($has_deposit_css === null)  return false;
      if($has_deposit_css === false) return false;
      if($has_deposit_css !== true)  return false;
      if($has_deposit_css === true)  return true;
      return false;
    }

    private function deposit_rules_user_action()
    {
      //DEMAND
      if($this->controller_rules === null)         return false;

      //SOLVE
      $has_deposit_rules_user_action = null;
      try 
      {
        foreach($this->controller_rules as $rule_number=>$rule) //e.g. $controller_rules[1]
        {
          if(isset($rule['user-action']))
          {
            if(is_array($rule['user-action']))
            {
              $rule_xpath = $rule['xpath']; //@explain: $controller_rules[1]['xpath']
              foreach($rule['user-action'] as $rule_user_action_number=>$rule_request) //@explain: e.g. $controller_rules[1]['request'][1]
              {
                $rule_event = $rule_request['event'];
                $rules_pass = $rule_request['domdata2model'];
                $this->view->deposit_rule_release_2dom($rule_number,$rule_xpath,$rule_user_action_number,$rules_pass,$rule_event);
              }
            }//if
          }//if
        }//foreach
        $has_deposit_rules_user_action = true;
      }//try 
      catch (\Throwable $th) 
      {
        $has_deposit_rules_user_action  = false;
      }//catch

      //EVALUATE
      if($has_deposit_rules_user_action === null)  return false;
      if($has_deposit_rules_user_action === false) return false;
      if($has_deposit_rules_user_action !== true)  return false;
      if($has_deposit_rules_user_action === true)  return true;
      return false;
    }

    public function check_rules_if_they_are_formally_valid($controller_rules)
    {
      //DEMAND
      if($controller_rules === null)   return false;
      if(!is_array($controller_rules)) return false;

      //SOLVE
      $rules_are_valid = null;
      $error_line      = null;

      foreach($controller_rules as $rule_index=>$rule)              //e.g. $controller_rules[1]
      {
        if(  (array_key_exists('xpath',$rule))                            //$controller_rules[1]['xpath']
           &&(  (array_key_exists('request',$rule))                       //$controller_rules[1]['request']
              ||(array_key_exists('user-action',$rule))))                 //$controller_rules[1]['user-action']
        {
          if(!empty($rule['request']))                                    //$controller_rules[1]['request']
          {
            foreach($rule['request'] as $rule_request_key=>$rule_request) //e.g. $controller_rules[1]['request'][1]
            {
              if(!is_int($rule_request_key))                              //$controller_rules[$rule_request_key]
              {
                $error_line=__LINE__;$rules_are_valid = false;break;break;
              }//if

              if(array_key_exists('model',$rule_request))                 //$controller_rules[1]['model']
              {
                if(!is_string($rule_request['model']))                    //$controller_rules[1]['model']
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//if
                else if(preg_match('/\s/',$rule_request['model']))        //$controller_rules[1]['model']
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//if

                if(!array_key_exists('modeldata2dom',$rule_request))      //$controller_rules[1]['modeldata2dom']
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//if
                else if(!is_array($controller_rules[$rule_index]['request'][$rule_request_key]['modeldata2dom'])) //$controller_rules[1]['modeldata2dom']
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if
                else 
                {
                  foreach($rule_request['modeldata2dom'] as $rule_request_dom=>$rule_request_modeldata) //e.g. $controller_rules[1]['modeldata2dom'][1]
                  {
                    if(!is_string($rule_request_dom))                     //key of $controller_rules[1]['modeldata2dom'][1] should be a xpath
                    {
                      $error_line=__LINE__;$rules_are_valid = false;break;break;break;
                    }//if
                    if(!is_string($rule_request_modeldata)&&!is_null($rule_request_modeldata)) //value of $controller_rules[1]['modeldata2dom'][1] should be variable for model
                    {
                      $error_line=__LINE__;$rules_are_valid = false;break;break;break;
                    }//if
                  }//foreach
                }//else
              }//if
              else 
              {
                $error_line=__LINE__;$rules_are_valid = false;break;break;
              }//else
            }//foreach
          }//if
          else 
          {
            //dont do this because having no request while having user-action is allowed: $error_line=__LINE__;$rules_are_valid = false;break;
          }

          if(!empty($rule['user-action']))
          {
            foreach($rule['user-action'] as $rule_user_action_key=>$rule_user_action)
            {
              if(!is_int($rule_user_action_key))
              {
                $error_line=__LINE__;$rules_are_valid = false;break;break;
              }//else if

              if(  (array_key_exists('event',$rule_user_action))
                 &&(array_key_exists('model',$rule_user_action)))
              {
                if(!is_string($rule_user_action['model']))             
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if
                else if(preg_match('/\s/',$rule_user_action['model'])) 
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if

                if(!is_string($rule_user_action['event']))             
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if
                else if(preg_match('/\s/',$rule_user_action['model'])) 
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if

                if(!array_key_exists('domdata2model',$rule_user_action))
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if
                else if(!(is_array($rule_user_action['domdata2model']))&&!(empty($rule_user_action['domdata2model'])))
                {
                  $error_line=__LINE__;$rules_are_valid = false;break;break;
                }//else if
                else if(is_array($rule_user_action['domdata2model'])&&!(empty($rule_user_action['domdata2model'])))
                {
                  foreach($rule_user_action['domdata2model'] as $rule_user_action_model=>$rule_user_action_domdata)
                  {
                    if(!is_string($rule_user_action_model)) 
                    {
                      $error_line=__LINE__;$rules_are_valid = false;break;break;break;
                    }//if
                    if(!is_string($rule_user_action_domdata)&&!is_null($rule_user_action_domdata))
                    {
                      $error_line=__LINE__;$rules_are_valid = false;break;break;break;
                    }//if
                  }//foreach
                }//else
              }//if
              else 
              {
                $error_line=__LINE__;$rules_are_valid = false;break;break;
              }//function
            }//foreach
          }//if
          else 
          {
            //dont do this because having no user-action while having request is allowed: $error_line=__LINE__;$rules_are_valid = false;break;
          }//else
        }//if
        else 
        {
          $error_line=__LINE__;$rules_are_valid = false;break;
        }//else
      }//foreach
      if($rules_are_valid !== false)
      {
        $rules_are_valid = true;
      }//if
      else
      {
        //echo "error@function:check_if_rules_are_formally_correct@LINE:".$error_line."<br/>";
      }
      
      //EVALUATE
      if($rules_are_valid === null)  return false;
      if($rules_are_valid === false) return false;
      if($rules_are_valid !== true)  return false;
      if($rules_are_valid === true)  return true;
      return false;
    }//function

    public function apply_rules_user_action()
    {
      //DEMAND
      if(!($this->requesthandler instanceof autotheme_requesthandler)) return false;
      if($this->controller_rules === null)                            return true;
      if(!($this->model instanceof autotheme_mvc_model))               return false;
      if(!($this->view instanceof autotheme_mvc_view))                 return false;

      //SOLVE
      $has_applied_rules = null;

      //handle rules for ajax/diff-request (user-action rules)
      if($this->requesthandler->is_request_mode_diff() === true)
      {
        //echo "apply_rules_user_action()@".__LINE__."<br/>";
        $rule_number = $this->model->get_rule_number_by_client();
        //echo "test@".__LINE__." rule_number:".$rule_number."<br/>";
        if(isset($this->controller_rules[$rule_number]))
        {
          $rule = $this->controller_rules[$rule_number]; //$controller_rules[1]
          
          $rule_number_user_action = $this->model->get_rule_number_user_action_by_client();
          //echo "test@".__LINE__." rule_number_user_action:".$rule_number_user_action."<br/>";
          if(isset($rule['user-action'][$rule_number_user_action]))
          {
            $rule_user_action = $rule['user-action'][$rule_number_user_action];

            $model_function_name = $rule_user_action['model'];
            //echo "test@".__LINE__." function:".$model_function_name."<br/>";
            //$event               = $rule_user_action['event'];
            $domdata2model       = $rule_user_action['domdata2model'];
            $this->model->set_data_by_function($model_function_name,$domdata2model);
          }    

          $has_applied_rules = true;
        }//if
        else
        {
          $has_applied_rules = false;
        }//else
      }//if
      else 
      {
        $has_applied_rules = true;
      }
      
      //EVALUATE
      if($has_applied_rules === null)  return false;
      if($has_applied_rules === false) return false;
      if($has_applied_rules !== true)  return false;
      if($has_applied_rules === true)  return true;
      return false;
    }//function

    public function apply_rules_request()
    {
      //DEMAND
      if(!($this->requesthandler instanceof autotheme_requesthandler)) return false;
      if($this->controller_rules === null)                            return true;
      if(!($this->model instanceof autotheme_mvc_model))               return false;
      if(!($this->view instanceof autotheme_mvc_view))                 return false;

      //SOLVE
      $has_applied_rules = null;
      try 
      {
        foreach($this->controller_rules as $rule_index=>$rule) //e.g. $controller_rules[1]
        {
          if(array_key_exists('request',$rule))
          {
            if(is_array($rule['request']))
            {
              $xpath_template = $rule['xpath']; //@explain: $controller_rules[1]['xpath']
              foreach($rule['request'] as $rule_number=>$rule_request) //@explain: e.g. $controller_rules[1]['request'][1]
              {
                //echo "rule_number:".$rule_number."<br/>";
                $model_function_name = $rule_request['model']; //@example: get_dices
                //@manual log: echo "model_function_name".$model_function_name."<br/>";
                $data = $this->model->get_data_by_function($model_function_name); //@example: Array ( [0] => Array ( [dice_number] => 0 [dice_value] => 6 [dice_unicode] => ⚅ ) [1] => ...
                //print_r($data);
                //@manual log: echo "<br/>";
                $modeldata2dom = $rule_request['modeldata2dom']; //@example: Array ( [div/@data_dice_number] => dice_number [div/@data_dice_value] => dice_value [div/text()] => dice_unicode ) 
                //print_r($modeldata2dom);
                $this->view->integrate_data_into_xpath_given_elements_of_dom($xpath_template,$data,$modeldata2dom); //e.g.$controller_rules[2]['request'][1]['modeldata2dom']
              }//foreach  
              $has_applied_rules = true; //@advice: checking could be better
            }//if
          }//if
        }//foreach
        $has_applied_rules = true;
      }
      catch (\Throwable $th) 
      {
        $has_applied_rules = false;
      }//catch

      //EVALUATE
      if($has_applied_rules === null)  return false;
      if($has_applied_rules === false) return false;
      if($has_applied_rules !== true)  return false;
      if($has_applied_rules === true)  return true;
      return false;
    }//function

    public function run()
    {
      //DEMAND
      if(!($this->requesthandler instanceof autotheme_requesthandler)) return false;

      //SOLVE
      $has_run = null;
      try 
      {
        //echo "test@".__LINE__."<br/>";
        $this->apply_rules_user_action();
        if($this->apply_rules_request() === true)
        {
          //echo "test@".__LINE__."<br/>";
          $this->requesthandler->start_request();
          $this->view->show();
          $this->requesthandler->stop_request();
          $has_run = true;
        }//if
        else 
        {
          $has_run = false;
        }//else
      }//try
      catch (\Throwable $th) 
      {
        $has_run = false;
      }//catch
      
      //EVALUATE
      if($has_run === null)  return false;
      if($has_run === false) return false;
      if($has_run !== true)  return false;
      if($has_run === true)  return false;
      return false;
    }//function

    public function show_view()
    {
      //DEMAND
      if(!($this->view instanceof autotheme_mvc_view))                 return false;
      
      //SOLVE
      $has_shown = $this->view->show();

      //EVALUATE
      if($has_shown === null)  return false;
      if($has_shown === false) return false;
      if($has_shown !== true)  return false;
      if($has_shown === true)  return true;
      return false;
    }

    public function get_view()
    {
      //DEMAND
      if(!($this->view instanceof autotheme_mvc_view))                 return false;
      
      //SOLVE
      $view = $this->view->get();

      //EVALUATE
      if($view === null)    return false;
      if($view === false)   return false;
      if(!is_string($view)) return false;
      if(is_string($view))  return $view;
      return false;
    }
  }//class

  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    $testcases = ['ruleset_a'=>1,
                  'ruleset_b'=>2,
                  'ruleset_c'=>3,
                  'ruleset_d'=>4,
                  'ruleset_e'=>5,
                  'ruleset_f'=>6];
    
    $testcase = null;
    $testcase = null;
    if(array_key_exists($_REQUEST['testcase'],$testcases))
    {
      $testcase = $testcases[$_REQUEST['testcase']];
    }//if
    else 
    {
      $testcase = $_REQUEST['testcase'];
    }//else

    class dice
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
          if(isset($_SESSION['autotheme'][$dice_number]))
            $dice = $_SESSION['autotheme'][$dice_number];
          else
          {
            $dice = rand(self::DICE_MIN,self::DICE_MAX);
            $_SESSION['autotheme'][$dice_number] = $dice;
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
        $_SESSION['autotheme'][$dice_number] = rand(self::DICE_MIN,self::DICE_MAX);
        return true;
      }//public function
    }//class

    switch($testcase)
    {
      case $testcases['ruleset_a']:
        $controller_rules[2]['info']                                           = "Einzeln würfeln";
        $controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
        $controller_rules[2]['request'][1]['model']                            = 'get_dices';
        $controller_rules[2]['request'][1]['modeldata2dom']['div/@data-dice']  = "dice_number"; 
        $controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
        $controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
        $controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_b']:
        $controller_rules[2]['info']                                           = "Einzeln würfeln";
        $controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
        $controller_rules[2]['request'][1]['model']                            = 'get_dices';
        $controller_rules[2]['request'][1]['modeldata2dom']['div/@data-dice']  = "dice_number"; 
        //wrong: $controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
        $controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
        $controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen falsch sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_c']:
        $controller_rules[2]['info']                                           = "Einzeln würfeln";
        $controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
        $controller_rules[2]['request'][1]['model']                            = 'get_dices';
        $controller_rules[2]['request'][1]['modeldata2dom']['div/@data-dice']  = "dice_number"; 
        //right: $controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
        //right: $controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
        //right: $controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_d']:
        $controller_rules[2]['info']                                           = "Einzeln würfeln";
        $controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
        //right: $controller_rules[2]['request'][1]['model']                            = 'get_dices';
        //right: $controller_rules[2]['request'][1]['modeldata2dom']['div/@data-dice']  = "dice_number"; 
        $controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
        $controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
        $controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_e']:
        $controller_rules[2]['info']                                           = "Einzeln würfeln";
        //wrong: $controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
        $controller_rules[2]['request'][1]['model']                            = 'get_dices';
        $controller_rules[2]['request'][1]['modeldata2dom']['div/@data-dice']  = "dice_number"; 
        wrong: $controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
        $controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
        $controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_f']:
        $controller_rules[1]['info']                              = "Alle würfeln";
        $controller_rules[1]['xpath']                             = "/html/body/input";
        $controller_rules[1]['request']                           = null;
        $controller_rules[1]['user-action'][1]['event']           = 'div/@click';
        $controller_rules[1]['user-action'][1]['domdata2model']   = null;
        $controller_rules[1]['user-action'][1]['model']           = 'roll_all_dices';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
      case $testcases['ruleset_f']:
        $controller_rules[1]['info']                              = "Alle würfeln";
        $controller_rules[1]['xpath']                             = "/html/body/input";
        $controller_rules[1]['request']                           = null;
        $controller_rules[1]['user-action'][1]['event']           = 'div/@click';
        $controller_rules[1]['user-action'][1]['domdata2model']   = null;
        $controller_rules[1]['user-action'][1]['model']           = 'roll_all_dices';

        //($name,$model,$view_xhtml_template,$view_css,$controller_rules)
        $controller = new autotheme_mvc_controller("unittest",null,"<html/>",null,$controller_rules);
        $rules_are_formally_valid = $controller->check_if_rules_are_formally_valid();

        echo "Controller-Regeln:<br/>";
        print_r($controller_rules);
        echo "<br/><br/>";
        echo "Die Regeln des Controllers sollen richtig sein:<br/>";
        if($rules_are_formally_valid === true)       echo "[Prüfung] Die Regeln sind korrekt!<br/>";
        else if($rules_are_formally_valid === false) echo "[Prüfung] Die Regeln sind falsch!<br/>";
        echo "<br/>";
        echo "<pre>";
        echo htmlentities(str_replace(">",">\n",$controller->get_view()));
        echo "<pre>";
        break;
    }//switch
  }//if

?>