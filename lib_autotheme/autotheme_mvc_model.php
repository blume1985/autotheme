<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->
   
<?

  if(!isset($version))
  {
    $version = "1.0.0.ready";
  }//if
  if(empty(session_id())) session_start();

  class autotheme_mvc_model
  {
    const RULE_NUMBER             = '_autotheme_rule_number';
    const RULE_NUMBER_USER_ACTION = '_autotheme_rule_number_user_action';

    private $name  = null;
    private $model = null;

    public function __construct($name,$model)
    {
      //DEMAND
      if(!is_string($name))                                    die("no contract@".__FILE__.'@'.__LINE__);
      if((!is_object($model))&&(!is_null($model)))             die("no contract@".__FILE__.'@'.__LINE__);
      if($this->name !== null)                                 die("no contract@".__FILE__.'@'.__LINE__);
      if($this->model !== null)                                die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $this->name  = $name;
      $this->model = $model;

      //EVALUATE
      if(!is_string($this->name))                              die("no contract@".__FILE__.'@'.__LINE__);
      if((!is_object($this->model))&&(!is_null($this->model))) die("no contract@".__FILE__.'@'.__LINE__);
    }//function

    public function get_rule_number_by_client()
    {
      //DEMAND
      if(!isset($_REQUEST[self::RULE_NUMBER])) return false;

      //SOLVE
      $rule_number = (int)$_REQUEST[self::RULE_NUMBER]; //in url set ?_autotheme_rulenumber=

      //EVALUATE
      if($rule_number === null) return false;
      if(!is_int($rule_number)) return false;
      if(is_int($rule_number))  return $rule_number;
      return false;
    }//function

    public function get_rule_number_user_action_by_client()
    {
      //DEMAND
      if(!isset($_REQUEST[self::RULE_NUMBER_USER_ACTION])) return false;

      //SOLVE
      $rule_number = (int)$_REQUEST[self::RULE_NUMBER_USER_ACTION]; //in url set ?_autotheme_rulenumber=

      //EVALUATE
      if($rule_number === null) return false;
      if(!is_int($rule_number)) return false;
      if(is_int($rule_number))  return $rule_number;
      return false;
    }//function

    private function get_data_of_client_request($variables_of_rules_of_user_action)
    {
      //DEMAND
      if(!is_array($variables_of_rules_of_user_action)) return false;
      
      //SOLVE
      $data = array();
      foreach ($variables_of_rules_of_user_action as $variable_name=>$relative_xpath)
      {
        if(isset($_REQUEST[$variable_name]))
        {
          $data[$variable_name] = $_REQUEST[$variable_name]; 
        }//if
      }//foreach

      //EVALUATE
      if(!isset($data))    return false;
      if(!is_array($data)) return false;
      if(is_array($data))  return $data;
      return false;
    }//function

    private function get_parameters_of_model_function($model_function_name)
    {
      //DEMAND
      if(!isset($model_function_name))                      return false;
      if(!is_string($model_function_name))                  return false;
      if(!isset($this->model))                              return false;
      if(!is_object($this->model))                          return false;
      if(!method_exists($this->model,$model_function_name)) return false;

      //SOLVE
      $list = array();
      $r = new ReflectionMethod($this->model, $model_function_name);
      $params = $r->getParameters();
      foreach ($params as $param) 
      {
        $list[] = $param->getName();
      }//foreach

      //EVALUATE
      if(!isset($list))    return false;
      if(!is_array($list)) return false;
      if(is_array($list))  return $list;
      return false;
    }
    
    //$controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
    //$controller_rules[2]['user-action'][1]['event']                        = '@click';
    //$controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = 'div/@data-dice';
    //$controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

    //$controller_rules[2]['xpath']                                          = "/html/body/div[@id='dice_board']";
    //$controller_rules[2]['user-action'][1]['event']                        = 'div/@click';
    //$controller_rules[2]['user-action'][1]['domdata2model']['dice_number'] = '@data-dice';
    //$controller_rules[2]['user-action'][1]['model']                        = 'roll_single_dice';

    public function set_data_by_function($model_function_name,$variables_of_rules_of_user_action)
    {
      //DEMAND
      if(!isset($this->model))                                    return false;
      if(!is_object($this->model))                                return false;
      if(!method_exists($this->model,$model_function_name))       return false;

      //SOLVE
      //print_r($variables_of_rules_of_user_action);
      $data = $this->get_data_of_client_request($variables_of_rules_of_user_action);
      //print_r($data);
      $parameters = array_fill_keys($this->get_parameters_of_model_function($model_function_name),null);
      foreach($parameters as $parameter=>$value)
      {
        if(array_key_exists($parameter,$data))
        {
          $parameters[$parameter] = $data[$parameter];
        }
        else 
        {
          $parameters[$parameter] = null;
        }
      }

      $return_value = $this->model->$model_function_name(...$parameters);
      //EVALUATE
      if(!isset($return_value)) return null;
      if(isset($return_value))  return $return_value;
      return false;
    }

    public function get_data_by_function($model_function_name)
    {
      //DEMAND
      if(!isset($this->model))                              return false;
      if(!is_object($this->model))                          return false;
      if(!method_exists($this->model,$model_function_name)) return false;

      //SOLVE
      $data = $this->model->$model_function_name();

      //EVALUATE
      if(!isset($data)) return null;
      if(isset($data))  return $data;
      return false;
    }
  }

  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    echo $version."<br/>";

    $testcases = ['array'=>1,
                  'existingfunction'=>2,
                  'nonexistingfunction'=>3,
                  'setfunction'=>4,
                  'rollalldices'=>5
                  ];
    
    $testcase = null;
    if(array_key_exists($_REQUEST['testcase'],$testcases))
    {
      $testcase = $testcases[$_REQUEST['testcase']];
    }//if
    else 
    {
      $testcase = $_REQUEST['testcase'];
    }//else

    echo "test: autotheme_mvc_model.php<br/>";

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
          {
            $dice = $_SESSION['autotheme'][$dice_number];
          }//if
          else
          {
            $dice = rand(self::DICE_MIN,self::DICE_MAX);
            $_SESSION['autotheme'][$dice_number] = $dice;
          }//else
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
      case $testcases['array']:
        $actual_link = 'http://'.$_SERVER['HTTP_HOST']."/lib_autotheme/autotheme_mvc_model.php?testcase=array&brett[1][2]=kreis&brett[5][4]=quadrat";
        echo "<a href=".$actual_link.">".$actual_link."</a><br/>";
        echo "<pre>";
        print_r($_REQUEST);
        echo "</pre>";
        break;
      case $testcases['existingfunction']:
        echo "function: get_dices<br/>";
        $model = new autotheme_mvc_model("dice",new dice());
        $data = $model->get_data_by_function('get_dices');
        print_r($data);
        break;
      case $testcases['nonexistingfunction']:
        $model = new autotheme_mvc_model("dice",new dice());
        $data = $model->get_data_by_function('get_dices2');
        echo "Erwartung: Funktion soll nicht liefern, da es get_dices2 nicht gibt<br/>";
        if(!empty($data))
        {
          echo "Funktion hat geliefert<br/>";
        }
        else {
          echo "Funktion hat nicht geliefert<br/>";
        }
        break;
      case $testcases['setfunction']:
        $model = new autotheme_mvc_model("dice",new dice());
        $set_data = $model->set_data_by_function('roll_single_dice',array('dice_number'));
        if($set_data == true)
        {
          echo "Hat einzelnen Würfel gewürfelt<br/>";
        }
        else {
          echo "Hat keinen einzelnen Würfel gewürfelt<br/>";
        }
        break;
      case $testcases['rollalldices']:
        $model = new autotheme_mvc_model("dice",new dice());
        $set_data = $model->set_data_by_function('roll_all_dices',array());
        if($set_data == true)
        {
          echo "Alle Würfel gewürfelt<br/>";
        }
        else {
          echo "Nicht alle Würfel gewürfelt<br/>";
        }
        break;
    }
  }

?>