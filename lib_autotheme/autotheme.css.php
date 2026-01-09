<?
  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.

  if(empty(session_id())) session_start();

  class autotheme_css
  {
    private $name = null;
    private $css = "";

    public function __construct()
    {
      //DEMAND
      if($this->css != null)       die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $has_loaded_css = $this->load_css_from_session_variable();
      
      $has_shown = null;
      if($this->has_loaded_css() == true)
        $has_shown = $this->show();
      else 
        $has_shown = $this->show_error();

      //EVALUATE
      if(is_null($has_loaded_css)) die("no contract@".__FILE__.'@'.__LINE__);
      if(is_null($has_shown))      die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_loaded_css))   die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_shown))        die("no contract@".__FILE__.'@'.__LINE__);
      if($has_loaded_css !== true) die("no contract@".__FILE__.'@'.__LINE__);
      if($has_shown !== true)      die("no contract@".__FILE__.'@'.__LINE__);
    }

    public function load_css_from_session_variable()
    {
      //DEMAND
      if(!isset($_REQUEST['_autotheme_name']))                               return false;
      if(!isset($_SESSION['autotheme'][$_REQUEST['_autotheme_name']]))        return false;
      if(!isset($_SESSION['autotheme'][$_REQUEST['_autotheme_name']]['css'])) return false;

      //SOLVE
      $this->css = $_SESSION['autotheme'][$_REQUEST['_autotheme_name']]['css'];

      //EVALUATE
      if(!isset($this->css))     return false;
      if(empty($this->css))      return false;
      if(!is_string($this->css)) return false;
      if(is_string($this->css))  return true;
      return false;
    }

    public function has_loaded_css()
    {
      //DEMAND
      if(!isset($this->css))     return false;
      if(empty($this->css))      return false;
      if(!is_string($this->css)) return false;

      //SOLVE
      $has_loaded_css = true;
        
      //EVALUATE
      if(!isset($has_loaded_css))   return false;
      if(!is_bool($has_loaded_css)) return false;
      if($has_loaded_css == false)  return false;
      if($has_loaded_css == true)   return true;
      return false;
    }

    public function show()
    {
      //DEMAND
      if(!$this->has_loaded_css()) return $this->show_error();

      //SOLVE
      $has_shown = null;
      try
      {
        echo "/*".$GLOBALS['version']."*/\n\n";
        echo $this->css;
        $has_shown = true;
      }
      catch(e)
      {
        $has_shown = false;
      }

      //EVALUATE
      if(!isset($has_shown))   return false;
      if(!is_bool($has_shown)) return false;
      if($has_shown == false)  return false;
      if($has_shown == true)   return true;
      return false;
    }

    public function show_error()
    {
      //DEMAND
      if($this->has_loaded_css()) return false;

      //EVALUATE
      $has_shown = null;
      try
      {
        echo "/*".$GLOBALS['version']."*/\n\n";
        echo "/*[CSS ERROR]*/";
        $has_shown = true;
      }
      catch(e)
      {
        $has_shown = false;
      }
      //EVALUATE
      if(!isset($has_shown))   return false;
      if(!is_bool($has_shown)) return false;
      if($has_shown == false)  return false;
      if($has_shown == true)   return true;
      return false;
    }
  }
  $css = new autotheme_css();
?>