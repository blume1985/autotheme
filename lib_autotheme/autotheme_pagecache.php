<?php
 
  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.
  
  class autotheme_pagecache
  {
    const UNITTEST_MYSQL_HOST = 'localhost';
    const UNITTEST_MYSQL_USER = 'autotheme_de_fix';
    const UNITTEST_MYSQL_PASS = 'v?u1rp&sDJzOOo!N';
    const UNITTEST_MYSQL_BANK = 'autotheme_de_fix';

    private $dir_cache = "./_cache/";
    private $mysqli_connection = null;

    public function __construct($mysqli_connection = null)
    {
      //DEMAND
      if(!(($mysqli_connection == null)||($mysqli_connection instanceof mysqli)))       die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      if($mysqli_connection == null) $has_connected = $this->database_connect();
      $has_init_table_cache_pages = $this->init_table_cache_pages();
      $has_init_session           = $this->init_session();

      //EVALUATE
      if(!isset($has_connected))              die("no contract@".__FILE__.'@'.__LINE__);
      if(!isset($has_init_table_cache_pages)) die("no contract@".__FILE__.'@'.__LINE__);
      if($has_init_table_cache_pages != true) die("no contract@".__FILE__.'@'.__LINE__);
      if($has_init_session != true)           die("no contract@".__FILE__.'@'.__LINE__);
      if($has_connected == false)             die("no contract@".__FILE__.'@'.__LINE__);
      if($this->mysqli_connection == null)    die("no contract@".__FILE__.'@'.__LINE__);
    }

    private function database_connect()
    {
      //DEMAND
      if($this->mysqli_connection != null) return false;
      
      //SOLVE
      $warningmessage = null;
      try 
      {
        $mysqli = new mysqli(self::UNITTEST_MYSQL_HOST, 
                             self::UNITTEST_MYSQL_USER,
                             self::UNITTEST_MYSQL_PASS,
                             self::UNITTEST_MYSQL_BANK);
        $this->mysqli_connection = $mysqli;
        $connected = $this->mysqli_connection->ping();
      } catch (Exception $e) 
      {
        $warningmessage = $e->getMessage();
        echo $warningmessage;
      }
      
      //EVALUATE
      if(is_string($warningmessage))       return false;
      if(empty($mysqli))                   return false;
      if($this->mysqli_connection == null) return false;
      if($connected == false)              return false;
      if($connected == true)               return true;
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

    private function init_table_cache_pages()
    {
      //DEMAND
      if($this->mysqli_connection == null)              die("no contract@".__FILE__.'@'.__LINE__);
      if(!($this->mysqli_connection instanceof mysqli)) die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $result_create_table = null;
      $sql = "CREATE TABLE IF NOT EXISTS `autotheme_de_fix`.`_autotheme_cache_pages` \n";
      $sql.= "(`id` INT(64) NOT NULL AUTO_INCREMENT , \n";
      $sql.= " `session_id` TEXT NOT NULL , \n";
      $sql.= " `tab_id` INT(8) NOT NULL , \n";
      $sql.= " `xhtml_document` TEXT NOT NULL , \n";
      $sql.= " `lastchange` TIMESTAMP NOT NULL ,  \n";
      $sql.= " PRIMARY KEY (`id`)) \n";
      $sql.= "ENGINE = InnoDB; \n";
  
      $result_create_table = $this->mysqli_connection->query($sql);
      
      //EVALUATE
      if(empty($result_create_table))  return false;
      if($result_create_table == true) return true;
      return false;
    }

    public function request_page($tab_id = -1)
    {
      //DEMAND
      if(empty($session_id = session_id()))              return false;
      if(!isset($this->mysqli_connection))               return false;
      if(!($this->mysqli_connection instanceof mysqli))  return false;
      if(!isset($tab_id))                                return false;
      if(!is_int($tab_id))                               return false;
      if($tab_id==0)                                     return false;
      if($tab_id<-1)                                     return false;

      //SOLVE
      if($tab_id == -1) $tab_id = $this->get_tab_id();

      $result_request = false;
      $sql = "SELECT `xhtml_document` ";
      $sql.= "FROM `_autotheme_cache_pages` ";
      $sql.= "WHERE `session_id`='".$session_id."' AND `tab_id`='".$tab_id."';";
      $result_request = $this->mysqli_connection->query($sql);

      $page = null;
      if($result_request->num_rows==1)
      {
        $row = $result_request->fetch_array(MYSQLI_NUM);
        $page = $row[0];
      }

      //EVALUATE
      if(!isset($result_request))  return false;
      if($result_request == false) return false;
      if(!isset($page))            return false;
      if($page == null)            return false;
      if($page == false)           return false;
      if(is_string($page))         return $page;
      return false;
    }

    public function update_tab_id_on_xhtml_document(&$xhtml_document,$tab_id)
    {
      //DEMAND
      if(!isset($xhtml_document))     return false;
      if(!is_string($xhtml_document)) return false;
      if(!isset($tab_id))             return false;
      if(!is_int($tab_id))            return false;
      if($tab_id<=0)                  return false;

      //SOLVE
      $xhtml_dom = new DOMDocument();
      $xhtml_dom->loadXML($xhtml_document,LIBXML_NOXMLDECL);
      //$xhtml_dom->loadXML($xhtml_document);
      $xhtml_dom_root_name = strtolower($xhtml_dom->documentElement->tagName);
      if($xhtml_dom_root_name == 'html')
      {
        $xhtml_xpath = new DOMXpath($xhtml_dom);
        $element_tab_id = $xhtml_xpath->query("/html/head/meta[@name='tab_id']");
        if(count($element_tab_id)==0)
        {
          $element_head = $xhtml_xpath->query('/html/head');
          if(count($element_head)==0)
          {
            $domElement_head = $xhtml_dom->createElement('head');
            $xhtml_dom->documentElement->appendChild($domElement_head);
            $element_head = $xhtml_xpath->query('/html/head');
          }

          $domElement_child = $xhtml_dom->createElement('meta');
          $domAttribute1 = $xhtml_dom->createAttribute('name');
          $domAttribute1->value = 'tab-id';
          $domAttribute2 = $xhtml_dom->createAttribute('content');
          $domAttribute2->value = $tab_id;
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $element_head[0]->appendChild($domElement_child);

          //eventuall has to be shifted to view-class/view-extension-class
          $domElement_child = $xhtml_dom->createElement('script',' ');
          $domAttribute1 = $xhtml_dom->createAttribute('type');
          $domAttribute1->value = 'text/javascript';
          $domAttribute2 = $xhtml_dom->createAttribute('src');
          $domAttribute2->value = "https://".$_SERVER['HTTP_HOST']."/lib_autotheme/autotheme_diffrequesthandler.js";
          $domAttribute3 = $xhtml_dom->createAttribute('defer');
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $domElement_child->appendChild($domAttribute3);
          $element_head[0]->appendChild($domElement_child);

          //eventuall has to be shifted to view-class/view-extension-class
          $domElement_child = $xhtml_dom->createElement('script',' ');
          $domAttribute1 = $xhtml_dom->createAttribute('type');
          $domAttribute1->value = 'text/javascript';
          $domAttribute2 = $xhtml_dom->createAttribute('src');
          $domAttribute2->value = "https://".$_SERVER['HTTP_HOST']."/lib_autotheme/autotheme_eventhandler.js";
          $domAttribute3 = $xhtml_dom->createAttribute('defer');
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $domElement_child->appendChild($domAttribute3);
          $element_head[0]->appendChild($domElement_child);

          //$xhtml_document = $xhtml_dom->saveXML();
          $xhtml_document = $xhtml_dom->saveXML($xhtml_dom->documentElement);
        }
        else if(count($element_tab_id)==1)
        {
          $element_tab_id[0]->setAttribute('content',$tab_id);
          //$xhtml_document = $xhtml_dom->saveXML();
          $xhtml_document = $xhtml_dom->saveXML($xhtml_dom->documentElement);
        }
      }

      //EVALUATE
      if($xhtml_dom_root_name != 'html') return false;
      if($xhtml_document == false)       return false;
      if($xhtml_document != false)       return true;
      return false;
    }

    public function catch_tab_id_by_write_page(&$xhtml_document)
    {
      //DEMAND
      if(empty($session_id = session_id()))              return false;
      if(!isset($this->mysqli_connection))               return false;
      if(!($this->mysqli_connection instanceof mysqli))  return false;

      //SOLVE
      $result_tab_id_max = null;
      $sql = "SELECT max(`tab_id`) as tab_id_max ";
      $sql.= "FROM `_autotheme_cache_pages` ";
      $sql.= "WHERE `session_id` = '$session_id'";
      $result_tab_id_max = $this->mysqli_connection->query($sql);

      $result_delete_cache_page = null;
      $result_insert_cache_page = null;
      if($result_tab_id_max)
      {
        $tab_id = 1;
        if($result_tab_id_max->num_rows == 1) 
        {
          $row = mysqli_fetch_array($result_tab_id_max);
          $tab_id = $row['tab_id_max'] + 1;
        }

        $this->update_tab_id_on_xhtml_document($xhtml_document,$tab_id);
        
        $sql = "DELETE FROM `_autotheme_cache_pages` ";
        $sql.= "WHERE `session_id`='".$session_id."' AND `tab_id`='".$tab_id."';";
        $result_delete_cache_page = $this->mysqli_connection->query($sql);

        $sql = "INSERT INTO `_autotheme_cache_pages` ";
        $sql.= "(`id`,`session_id`,`tab_id`,`xhtml_document`,`lastchange`) ";
        $sql.= "VALUES ";
        $sql.= "(null,'".$session_id."','".$tab_id."','".$this->mysqli_connection->real_escape_string($xhtml_document)."',now());";

        $result_insert_cache_page = $this->mysqli_connection->query($sql);
      }
      
      //EVALUATE
      if($result_tab_id_max == null)         return false;
      if($result_tab_id_max == false)        return false;
      if($result_delete_cache_page == null)  return false;
      if($result_delete_cache_page == false) return false;
      if($result_insert_cache_page == null)  return false;
      if($result_insert_cache_page == false) return false;
      if($result_insert_cache_page != false) return $tab_id;
      return false;
    }

    public function rewrite_page(&$xhtml_document,$tab_id = -1)
    {
      //DEMAND
      if(empty($session_id = session_id()))              return false;
      if(!isset($this->mysqli_connection))               return false;
      if(!($this->mysqli_connection instanceof mysqli))  return false;
      if(!isset($xhtml_document))     return false;
      if(!is_string($xhtml_document)) return false;
      if(!isset($tab_id))             return false;
      if(!is_int($tab_id))            return false;
      if($tab_id==0)                  return false;
      if($tab_id<-1)                  return false;

      //SOLVE
      if($tab_id == -1) $tab_id = $this->get_tab_id();

      $this->update_tab_id_on_xhtml_document($xhtml_document,$tab_id);
        
      $result_update = null;
      $sql = "UPDATE `_autotheme_cache_pages` ";
      $sql.= "SET `xhtml_document`='".$this->mysqli_connection->real_escape_string($xhtml_document)."', `lastchange`=now() ";
      $sql.= "WHERE `session_id`='".$session_id."' AND `tab_id`='".$tab_id."';";
      
      $result_update = $this->mysqli_connection->query($sql);

      //EVALUATE
      if($result_update == null)  return false;
      if($result_update == false) return false;
      if($result_update != false) return true;
      return false;
    }

    public function get_tab_id()
    {
      //DEMAND
      if(!isset($_REQUEST['tab_id'])) return false;
      
      //SOLVE
      $tab_id = (int)$_REQUEST['tab_id'];
          
      //EVALUATE;
      if(empty($tab_id))   return false;
      if(!is_int($tab_id)) return false;
      if(is_int($tab_id))  return $tab_id;
      return false;
    }

    public function clean_cache()
    {
      //DEMAND
      if(!isset($this->mysqli_connection))               return false;
      if(!($this->mysqli_connection instanceof mysqli))  return false;
     
      //SOLVE
      //echo (file_exists(session_save_path().'/sess_'.session_id()) ? 1 : 0);
      $result_session_ids = null;
      $sql = "SELECT `session_id` ";
      $sql.= "FROM `_autotheme_cache_pages` ";
      $sql.= "GROUP BY `session_id`;";
      $result_session_ids = $this->mysqli_connection->query($sql);

      $delete_success = true;
      foreach($result_session_ids as $result_session_id)
      {
        $session_id = $result_session_id['session_id'];
        if(!file_exists(session_save_path().'/sess_'.$session_id))
        {
          $sql = "DELETE FROM `_autotheme_cache_pages` ";
          $sql.= "WHERE `session_id`='".$session_id."';";
          $result_delete_cache_page = $this->mysqli_connection->query($sql);
          if(!$result_delete_cache_page)
          {
            $delete_success = false;
            break;
          }
        }
      }
      //EVALUATE
      if($result_session_ids == null)  return false;
      if($result_session_ids == false) return false;
      if($delete_success != true)      return false;
      if($delete_success == true)      return true;
      return false;
    }
  }

  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    $path = pathinfo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $dir = $path['dirname'];
    
    if(empty(session_id()))
    {
      session_start();
      echo "SESSION STARTED: ".session_id()."<br/>";
    }
    else {
      echo "SESSION EXISTS: ".session_id()."<br/>";
    }
    
    $testcases = ['xhtmltabid'=>1,
                  'writepage'=>2,
                  'requestpage'=>3,
                  'rewritepage'=>4,
                  'cleancache'=>5,
                  'coding'=>6];

    $pagecache = new autotheme_pagecache();

    $testcase = null;
    if(isset($_REQUEST['testcase']))$testcase = $_REQUEST['testcase'];
    switch($testcase)
    {
      case $testcases['xhtmltabid']:
        $xhtml_doc1 = '<html>';
        $xhtml_doc1.= '<head>';
        $xhtml_doc1.= '<title>Pagecache</title>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_serverfile.js" defer></script>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_shiftxml.js" defer></script>';
        $xhtml_doc1.= '<style>';
        $xhtml_doc1.= 'h1   {color: blue;font-weight:bold;font-size:20px;}';
        $xhtml_doc1.= 'p    {color: red;}';
        $xhtml_doc1.= '</style>';
        $xhtml_doc1.= '</head>';
        $xhtml_doc1.= '<body id="document">';
        $xhtml_doc1.= '<p title="Intro in ShiftXML">Diese Website testet ShiftXML</p>';
        $xhtml_doc1.= '<h1>Funktionsumfang</h1>';
        $xhtml_doc1.= '<p>ShiftXML beherrscht Inserts, Replacments und Removes sowohl von Elementen als auch Attributen.</p>';
        $xhtml_doc1.= '<p>ShiftXML ist sowohl zum Modifizieren für generelle XML-Dokumente als auch XHTML-Dokumente geeignet.</p>';
        $xhtml_doc1.= '<h1>Alternative XUpdate</h1>';
        $xhtml_doc1.= '<p>XUpdate ist nicht wirklich eine Alternative, da es mehrdeutig ist und keine eindeutigen Spezifikationen vorweist.</p>';
        $xhtml_doc1.= '</body>';
        $xhtml_doc1.= '</html>';
        $pagecache->update_tab_id_on_xhtml_document($xhtml_doc1,1);
        echo "<pre>";
        print_r(htmlspecialchars(str_replace(['><'], [">\r\n<"], $xhtml_doc1)));
        echo "</pre>";
        break;
      case $testcases['writepage']:
        $xhtml_doc1 = '<html>';
        $xhtml_doc1.= '<head>';
        $xhtml_doc1.= '<title>Pagecache</title>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_serverfile.js" defer></script>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_shiftxml.js" defer></script>';
        $xhtml_doc1.= '<style>';
        $xhtml_doc1.= 'h1   {color: blue;font-weight:bold;font-size:20px;}';
        $xhtml_doc1.= 'p    {color: red;}';
        $xhtml_doc1.= '</style>';
        $xhtml_doc1.= '</head>';
        $xhtml_doc1.= '<body id="document">';
        $xhtml_doc1.= '<p title="Intro in ShiftXML">Diese Website testet ShiftXML</p>';
        $xhtml_doc1.= '<h1>Funktionsumfang</h1>';
        $xhtml_doc1.= '<p>ShiftXML beherrscht Inserts, Replacments und Removes sowohl von Elementen als auch Attributen.</p>';
        $xhtml_doc1.= '<p>ShiftXML ist sowohl zum Modifizieren für generelle XML-Dokumente als auch XHTML-Dokumente geeignet.</p>';
        $xhtml_doc1.= '<h1>Alternative XUpdate</h1>';
        $xhtml_doc1.= '<p>XUpdate ist nicht wirklich eine Alternative, da es mehrdeutig ist und keine eindeutigen Spezifikationen vorweist.</p>';
        $xhtml_doc1.= '</body>';
        $xhtml_doc1.= '</html>';
        $tab_id = $pagecache->catch_tab_id_by_write_page($xhtml_doc1);
        if($tab_id == false)
        {
          echo "Could not write Page!<br/>";
        }
        else {
          echo "Could write Page with tab_id = ".$tab_id."<br/>";
        }
        break;
      case $testcases['requestpage']:
        $tab_id = 1;
        if(isset($_REQUEST['tab_id'])) $tab_id = $_REQUEST['tab_id'];
        $xhtml_doc_requested = $pagecache->request_page($tab_id);
        echo "<pre>";
        print_r(htmlspecialchars(str_replace(['><'], [">\r\n<"], $xhtml_doc_requested)));
        echo "</pre>";
        break;
      case $testcases['rewritepage']:
        $tab_id = 1;
        if(isset($_REQUEST['tab_id']))
        {
          $tab_id = (int)$_REQUEST['tab_id'];
          echo "Other tab_id=".$tab_id."<br/><br/>";
        }
        $xhtml_doc2 = '<html>';
        $xhtml_doc2.= '<head>';
        $xhtml_doc2.= '<title>Pagecache</title>';
        $xhtml_doc2.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_serverfile.js" defer></script>';
        $xhtml_doc2.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_includer_shiftxml.js" defer></script>';
        $xhtml_doc2.= '<style>';
        $xhtml_doc2.= 'h1   {color: blue;font-weight:bold;font-size:20px;}';
        $xhtml_doc2.= 'p    {color: red;}';
        $xhtml_doc2.= '</style>';
        $xhtml_doc2.= '</head>';
        $xhtml_doc2.= '<body id="document2">';
        $xhtml_doc2.= '<h1 title="caption">Intro in ShiftXML</h1>';
        $xhtml_doc2.= '<p>Diese Website testet ShiftXML</p>';
        $xhtml_doc2.= '<h1 title="the same heading with new content.">Funktionsumfang</h1>';
        $xhtml_doc2.= '<p>A newer statement.</p>';
        $xhtml_doc2.= '<p>ShiftXML ist sowohl zum Modifizieren für generelle XML-Dokumente als auch XHTML-Dokumente geeignet.</p>';
        $xhtml_doc2.= '</body>';
        $xhtml_doc2.= '</html>';
        if($pagecache->rewrite_page($xhtml_doc2,$tab_id)==true)
        {
          echo "Page is rewritten with tab_id=".$tab_id.".<br/><br/>";
          echo "<pre>";
          print_r(htmlspecialchars(str_replace(['><'], [">\r\n<"], $xhtml_doc2)));
          echo "</pre>";
        }
        else {
          echo "Page is NOT rewritten!<br/>";
        }
        break;
      case $testcases['cleancache']:
        $pagecache->cleancache();
        break;
      case $testcases['coding']:  //test success on 31.Jan24 v23.50
        $xhtml_doc2 = '<html>';
        $xhtml_doc2.= '<head>';
        $xhtml_doc2.= '</head>';
        $xhtml_doc2.= '<body>';
        $xhtml_doc2.= '<p>Das ist wie Äpfel mit Birnen vergleichen.</p>';
        $xhtml_doc2.= '</body>';
        $xhtml_doc2.= '</html>';
        $tab_id = $pagecache->catch_tab_id_by_write_page($xhtml_doc2);
        if($tab_id == false)
        {
          echo "Could not write Page!<br/>";
        }
        else {
          echo "Could write Page with tab_id = ".$tab_id."<br/>";
        }
        break;
    }
  }

?>