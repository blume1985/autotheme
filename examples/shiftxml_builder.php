<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

<?php

$version = "24.1";

class shiftxml_document
{
  private $document       = null;
  private $document_ready = null;

  const SHIFTXML_COMMANDS = ['replace','insert','remove','text'];
  const SHIFTXML_ATTRIBUTES_COMMANDS = ['replace','insert','remove'];

  function __construct()
  {
    if(!is_null($this->document))                         die("no contract@".__LINE__."@".__FILE__);
    if(!is_null($this->document_ready))                   die("no contract@".__LINE__."@".__FILE__);
 
    $this->document = new DOMDocument('1.0');

    $xml = '<shiftxml:diffoperations xmlns:shiftxml="namespace://dontcode.de/ns/shiftxml"/>';
    $this->document->loadXml($xml);
    $this->document_ready = false;

    if(!get_class($this->document)=='DOMDocument') die("no contract@".__LINE__."@".__FILE__);
    if($this->document_ready != false)             die("no contract@".__LINE__."@".__FILE__);
  }

  private function get_element($xpath_query)
  {
    if(!$this->check_if_xpath_exists($xpath_query)) die("no contract@".__LINE__."@".__FILE__);

    $xpath = new DOMXPath($this->document);
    $document_elements = $xpath->query($xpath_query);
  
    $element = null;
    if(isset($document_elements[0]))
      $element = $document_elements[0];
    
    if(empty($element))  return false;
    if(!empty($element)) return $element;
    return false;
  }

  public function append_child($xpath_query,$element_code)
  {
    if(!$this->check_if_xpath_exists($xpath_query)) die("no contract@".__LINE__."@".__FILE__);
    if(!is_string($element_code))                   die("no contract@".__LINE__."@".__FILE__);
  
    $template = $this->document->createDocumentFragment();
    $template->appendXML($element_code);

    $has_element_appended = null;
    $element = $this->get_element($xpath_query);
    if(!empty($element))
      $has_element_appended = $element->appendChild($template);
    
    if(empty($has_element_appended))  return false;
    if($has_element_appended != true) return false;
    if($has_element_appended == true) return true;
    return false;
  }

  public function append_child_shiftxml($xpath_query,$command,$attributes,$child)
  {
    if(!$this->check_if_xpath_exists($xpath_query))   die("no contract@".__LINE__."@".__FILE__);
    if(!in_array($command,self::SHIFTXML_COMMANDS))   die("no contract@".__LINE__."@".__FILE__);
    if(($child!=null)&&(!is_string($child)))          die("no contract@".__LINE__."@".__FILE__);
    if((!is_array($attributes))&&($attributes!=null)) die("no contract@".__LINE__."@".__FILE__);

    $xpath = new DOMXPath($this->document);
    $document_elements = $xpath->query($xpath_query);
    
    $shiftxml_tag = 'shiftxml:'.$command;
    $template = $this->document->createElementNS('namespace://dontcode.de/ns/shiftxml',$shiftxml_tag,'');
    $newnode = $document_elements[0]->appendChild($template);
       
    $has_child_appended_if_given = false;
    if(empty($child))
      $has_child_appended_if_given  = true;
    else if(is_string($child))
    {
      $has_child_appended_if_given  = false;
      $xpath_query_child = $xpath_query.'/'.$shiftxml_tag.'[last()]';
      $has_child_appended_if_given = $this->append_child($xpath_query_child,$child);        
    }

    if($newnode == false)                     return false;
    if($has_child_appended_if_given == false) return false;   
    
    if($newnode instanceof DOMNode )          return true;
    return false;
  }

  public function append_attributes_shiftxml($xpath_query,$set_attributes)
  {
    if(!$this->check_if_xpath_exists($xpath_query))   die("no contract@".__LINE__."@".__FILE__);
    if(!is_array($set_attributes))                    die("no contract@".__LINE__."@".__FILE__);
    if(empty($set_attributes))                        die("no contract@".__LINE__."@".__FILE__);

    $element = $this->get_element($xpath_query);

    if($element != false)
    {
      $set_attributes_has_valid_commands = null;
      $atttibutes_have_only_strings = null;
      $all_elements_inserted = null;
      foreach($set_attributes as $command=>$attributes)
      {
        if(in_array($command,self::SHIFTXML_ATTRIBUTES_COMMANDS))
        {
          $set_attributes_has_valid_commands = true;

          foreach($attributes as $attribute_name=>$attribute_value)
            if(is_string($attribute_name)&&((is_string($attribute_value)||($attribute_value==null))))
            {
              $attrNS = $this->document->createAttributeNS('namespace://dontcode.de/ns/'.$command, $command.':'.$attribute_name ); 
              if(is_string($attribute_value))
                $attrNS->value = $attribute_value;
              if($element->appendChild($attrNS)==false)
              {
                $all_elements_inserted = false;
                break;
                break;
              }
              else {
                $all_elements_inserted = true;
              }
              $atttibutes_have_only_strings = true;
            }
            else 
            {
              $atttibutes_have_only_strings = false;
              break;
              break;
            }
        }
        else 
        {
          $set_attributes_has_valid_commands = false;
          break;
        }
      }
    }

    if($element                              == false)return false;
    if($atttibutes_have_only_strings         != true) return false;
    if($set_attributes_has_valid_commands    != true) return false;
    if($all_elements_inserted                != true) return false;
    if(  ($element                           != false)
       &&($atttibutes_have_only_strings      == true)
       &&($set_attributes_has_valid_commands == true) 
       &&($all_elements_inserted             == true))return true;
    return false;
  }

  public function check_if_xpath_exists($xpath_query)
  {
    if(!is_string($xpath_query)) die("no contract@".__LINE__."@".__FILE__);
    if(!get_class($this->document)=='DOMDocument') die("no contract@".__LINE__."@".__FILE__);
    
    $xpath = new DOMXPath($this->document);
    $evaluate = $xpath->query($xpath_query);
    $evaluate_count = count($evaluate);

    if($evaluate == false)       return false;
    if(!is_int($evaluate_count)) return false;
    if($evaluate_count <= 0)     return false;
    if($evaluate_count  > 0)     return true;
    return false;
  }

  public function get_document()
  {
    return $this->document->saveXml();
  }
}

//UNIT-TEST
if($_SERVER['SCRIPT_FILENAME']==__FILE__)
{    
  $testcases = ['document1'=>1];

  $testcase = null;
  if(isset($_REQUEST['testcase']))$testcase = $testcases[$_REQUEST['testcase']];
  $shiftxml = new shiftxml_document();
  switch($testcase)
  {
    case $testcases['document1']:
      $shiftxml->append_child("/shiftxml:diffoperations","<html/>");
      $shiftxml->append_child("/shiftxml:diffoperations/html","<head/>");
      $shiftxml->append_child("/shiftxml:diffoperations/html","<body/>");
      $shiftxml->append_attributes_shiftxml("/shiftxml:diffoperations/html/body",array('replace'=>array('id'=>'document2')));
      $shiftxml->append_child_shiftxml("/shiftxml:diffoperations/html/body","insert",null,'<h1 title="caption">Intro in ShiftXML</h1>');
      $shiftxml->append_child("/shiftxml:diffoperations/html/body","<p/>");
      $shiftxml->append_attributes_shiftxml("/shiftxml:diffoperations/html/body/p[1]",array('remove'=>array('title'=>null)));
      $shiftxml->append_child("/shiftxml:diffoperations/html/body","<h1/>");
      $shiftxml->append_attributes_shiftxml("/shiftxml:diffoperations/html/body/h1[1]",array('insert'=>array('title'=>'the same heading with new content.')));
      $shiftxml->append_child("/shiftxml:diffoperations/html/body","<p/>");
      $shiftxml->append_child_shiftxml("/shiftxml:diffoperations/html/body/p[2]","replace",null,"A newer statement.");
      $shiftxml->append_child("/shiftxml:diffoperations/html/body","<p/>");
      $shiftxml->append_child_shiftxml("/shiftxml:diffoperations/html/body","remove",null,"<h1/>");
      $shiftxml->append_child_shiftxml("/shiftxml:diffoperations/html/body","remove",null,"<p/>");
    break;
    default:
      $shiftxml->append_tag("/shiftxml:diffoperations","html");
      $shiftxml->append_shiftxml("/shiftxml:diffoperations/html","replace",null,"<body>Default document for unittest of shiftxml</body>");
    break;
  }
  //echo '<!--version'.$version.'-->'."\n";
  echo $shiftxml->get_document();

}

?>
