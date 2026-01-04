<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

<?php

  if(!isset($version)) $version = "1.0.0.ready";

  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_pagecache.php");
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_xhtml_differ.php");

  class dontcode_requesthandler
  {
    const REQUEST_MODE_FULL = "full_request";
    const REQUEST_MODE_DIFF = "differential_request";
    const REQUEST_VAR       = "tab_id";

    private $request_mode = false;
    private $is_started = false;
    private $is_stopped = false;

    public function __construct($startnow = true)
    {
      //DEMAND
      if(!isset($this->request_mode))  die("no contract@".__FILE__.'@'.__LINE__);
      if(!isset($this->is_started))    die("no contract@".__FILE__.'@'.__LINE__);
      if(!isset($this->is_stopped))    die("no contract@".__FILE__.'@'.__LINE__);
      if($this->request_mode != false) die("no contract@".__FILE__.'@'.__LINE__);
      if($this->is_started != false)   die("no contract@".__FILE__.'@'.__LINE__);
      if($this->is_stopped != false)   die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $has_init_session           = $this->init_session();

      if(!isset($_REQUEST[self::REQUEST_VAR]))
        $this->request_mode = self::REQUEST_MODE_FULL;
      else
        $this->request_mode = self::REQUEST_MODE_DIFF;

      if($startnow == false) $this->start_request();

      //EVALUATE
      if($has_init_session != true)                          die("no contract@".__FILE__.'@'.__LINE__);
      if(  ($this->request_mode != self::REQUEST_MODE_FULL)
         &&($this->request_mode != self::REQUEST_MODE_DIFF)) die("no contract@".__FILE__.'@'.__LINE__);
    }

    public function is_request_mode_diff()
    {
      //DEMAND
      if(!isset($this->request_mode)) return false;

      //SOLVE
      $is_request_mode_diff = null;
      if($this->request_mode === self::REQUEST_MODE_DIFF)
      {
        $is_request_mode_diff = true;
      }//if
      else 
      {
        $is_request_mode_diff = false;
      }
      
      //EVALUATE
      if(empty($is_request_mode_diff))   return false;
      if($is_request_mode_diff !== true) return false;
      if($is_request_mode_diff === true) return true;
      return false;
    }

    private function init_session()
    {
      //DEMAND
      if(!empty(session_id())) return true;
    
      //SOLVE
      $has_session_started = session_start();
      
      //EVALUATE
      if(empty($has_session_started))  return false;
      if($has_session_started == true) return true;
      return false;
    }

    public function start_request()
    {
      //DEMAND
      if(!isset($this->is_started)) return false;
      if(!isset($this->is_stopped)) return false;
      if($this->is_started != false) return false;
      if($this->is_stopped != false) return false;

      //SOLVE
      if(ob_start())
      {
        $is_started = true;
      }

      //EVALUATE
      if($this->is_started != true)  return false;
      if($this->is_stopped != false) return false;
      if($this->is_started == true)  return true;
      return false;
    }

    public function stop_request()
    {
      //DEMAND
      if(!isset($this->is_started)) return false;
      if(!isset($this->is_stopped)) return false;
      if($this->is_started != false) return false;
      if($this->is_stopped != false) return false;

      //SOLVE
      $this->is_started = false;
      $xhtml_doc = ob_get_contents();
      $this->is_stopped = $this->handle_request($xhtml_doc);

      //EVALUATE
      if($this->is_started != false) return false;
      if($this->is_stopped != true)  return false;
      if($this->is_stopped == true)  return true;
      return false;
    }

    private function handle_request($xhtml_doc)
    {
      //DEMAND
      if(!isset($xhtml_doc))     return false;
      if(!is_string($xhtml_doc)) return false;

      //SOLVE
      $pagecache = new dontcode_pagecache();
      $has_handled_request = null;
      if($this->request_mode == self::REQUEST_MODE_FULL)
        $has_handled_request = $this->handle_request_full($xhtml_doc,$pagecache);
      else if($this->request_mode == self::REQUEST_MODE_DIFF)
        $has_handled_request = $this->handle_request_diff($xhtml_doc,$pagecache);

      //EVALUATE
      if(is_null($has_handled_request)) return false;
      if($has_handled_request == false) return false;
      if($has_handled_request == true)  return true;
      return false;
    }
  

    private function handle_request_full(&$xhtml_doc_active,&$pagecache)
    {
      //DEMAND
      if(!isset($xhtml_doc_active))                   return false;
      if(!isset($pagecache))                          return false;
      if(!is_string($xhtml_doc_active))               return false;
      if(!($pagecache instanceof dontcode_pagecache)) return false;

      //SOLVE
      $tab_id = $pagecache->catch_tab_id_by_write_page($xhtml_doc_active);
      if((bool)$tab_id == true) 
        if(ob_end_clean()==true)
          echo $xhtml_doc_active;

      //EVALUATE
      if(empty($has_tab_id)) return false;
      if($has_tab_id == true) return true;
      return false;
    }

    private function handle_request_diff(&$xhtml_doc_sequent,&$pagecache)
    {
      //DEMAND
      if(!isset($xhtml_doc_sequent))                  return false;
      if(!isset($pagecache))                          return false;
      if(!is_string($xhtml_doc_sequent))              return false;
      if(!($pagecache instanceof dontcode_pagecache)) return false;

      //SOLVE
      $has_rewritten_page_sequent = null;
      $shiftxml = null;
      if(ob_end_clean()==true)
      {
        $xhtml_doc_active = $pagecache->request_page();
        if(is_string($xhtml_doc_active))
        {
          $xhtml_differ = new dontcode_xhtml_differ();
          //echo "catch_shiftxml_out_of_xhtml_documents@".__LINE__."<br/>";
          $has_rewritten_page_sequent = $pagecache->rewrite_page($xhtml_doc_sequent);
          $shiftxml = $xhtml_differ->catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent);
          if($shiftxml != false)
            echo $shiftxml;
        }
      }

      //EVALUATE
      if(is_null($shiftxml))                      return false;
      if($shiftxml == false)                      return false;
      if(is_null($has_rewritten_page_sequent))    return false;
      if($has_rewritten_page_sequent == false)    return false;
      if(  ($shiftxml != false)
         &&($has_rewritten_page_sequent == true)) return true;
      return false;
    }
  }

  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    //echo $version."<br/>";

    $testcases = ['request'=>1,
                  'start'=>2,
                  'startandclean'=>3,
                  'full'=>4,
                  'diff'=>5];

    $requesthandler = new dontcode_requesthandler(false);

    $testcase = null;
    if(isset($_REQUEST['testcase']))$testcase = $_REQUEST['testcase'];
    switch($testcase)
    {
      case $testcases['request']:
        if($requesthandler instanceof dontcode_requesthandler)
        {
          echo "requesthandler has been constructed<br/>";
        }
        else {
          echo "requesthandler has been NOT constructed<br/>";
        }
        break;
      case $testcases['start']:
        $requesthandler->start_request();
        ?><html><?
        ?><body><?
        ?><p>Das ist wie Äpfel mit Birnen zu vergleichen.</p><?
        ?></body><?
        ?></html><?
        ob_end_flush();
        break;
      case $testcases['startandclean']:
        $requesthandler->start_request();
        ?><html><?
        ?><body><?
        ?><p>Das ist wie Äpfel mit Birnen zu vergleichen.</p><?
        ?></body><?
        ?></html><?
        ob_end_clean();
        break;
      case $testcases['full']: //test success on 31.Jan 24 18:52
        $requesthandler->start_request();
        ?><html><?
        ?><head/><?
        ?><body><?
        ?><p>Fullrequesting ist wie Äpfel mit Birnen zu vergleichen.</p><?
        ?></body><?
        ?></html><?
        $requesthandler->stop_request();
        break;
      case $testcases['diff']:
        if(!isset($_REQUEST['tab_id']))
        {
          echo "Keine Variable tab_id in GET ODER POST-Anfrage.<br/>";
        }
        else 
        {
          $requesthandler->start_request(); 
          ?><html><?
          ?><head/><?
          ?><body><?
          ?><h1>Oder anders herum?</h1><?
          ?><p>Diffrequesting ist wie Birnen mit Äpfeln zu vergleichen.</p><?
          ?></body><?
          ?></html><?
          $requesthandler->stop_request();
        }
        break;
    }
  }

?>
