<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

<?
    /*
    $controller_rules[2]['info']                              = "Einzeln würfeln";
    $controller_rules[2]['xpath']                             = "/html/body/div[@id='dice_board']";
    $controller_rules[2]['request'][1]['model']               = 'get_dices';
    $controller_rules[2]['request'][1]['modeldata2dom'][]     = array("data"=>'dice_number',"dom"=>'div/@data-dice'); //id ist verboten!*/

      //COMMENT: SOLUTION-DESIGN
      //0. Bekomme alle Elemente von xpath_root_template
      //1. Extrahier Template aus Dom
      //2a Suche im Template nach einem Element mit gleichen tagnamen und der gleichen id (dem nesting element), wenn ja, hinterlege wo
      //2b Wenn keine id beim template-root-element dann appende child zu template-root-element mit dem tag-name des root-elements und einer id, wenn keine id, dann eine nestmarkierung machen
      //3a ermittle xpath_extend - der pfad zwischen template-root-element und template-nest-element
      //4b mappe data ab template-root-xpath,
      //4b-1 wenn data keine elemente im root hat, lösche knoten im dom
      //4b-2 wenn data elemente hat, schau ob diese daten oder arrays sind
      //4b-3 wenn es daten sind füge ein template in den dom ein und mappe diese daten im dom
      //          die restlichen arrays müssen rekursiv behandelt werden
      //          -> restliche arrays sind auch keine arrays, damit template-nest-element noch gelöscht werden kann
      //4b-4 wenn nur arrays die elemente sind dann füge von xpath_root / xpath_selected sequentiell ein
      //4b-5 

      //$data = array("name"=>"Blume","Beruf"=>"Student");
      //$data = array(array("name"=>"Blume","Beruf"=>"Student"));
      //$data = array(array("name"=>"Blume","Beruf"=>"Informatikstudent"),array("name"=>"Böhmer","Beruf"=>"Wirtschaftsstudent"))
      //$data = array(array("name"=>"Uni",array("name"=>"Profs",array("name"=>"Aßmann"),array("name"=>"Weber"))));


      $has_integrated_modeldata2dom = null;
      if(count($data)==0)
      {
        //remove node from template
        $has_integrated_modeldata2dom = true;
      }
      else if(count($data=>1))
      {
        $data_surface = array();
        $data_nest  = array();
        foreach($data as $data_key=>$data_element)
        {
          if(is_scalar($data_element))
            $data_surface[$data_key] = $data_element;
          else if if(is_array($data_element))
            $data_nest[] = $data_element;
        }
        if((count($data_surface)=>1)&&(count($data_nest)==0))
        {
          //node insert: xpath/template
          //data insert: xpath/template/@data  (foreach)
          //data insert: xpath/template/text() (if has.text)
        }
        else if((count($data_surface)==0)&&(count($data_nest)>=1))
        {
          //node insert: xpath/template        (foreach)->recursion
          //data insert: xpath/template/@data  (foreach)
          //data insert: xpath/template/text() (if has.text)
        }
        else if((count($data_surface)=>1)&&(count($data_nest)>=1))
        {
          //ein Template anlegen und Daten von data-surface einpflegen
          //dazu noch in alle data_nests springen und parallel Templates einpflegen
        }
        $has_integrated_modeldata2dom = true;
      }
      else {
        $has_integrated_modeldata2dom = false;
      }
      
      //EVALUATE
      if(empty($has_integrated_modeldata2dom)) return false;
      if($has_integrated_modeldata2dom == true) return true;
      return false;
?>

<?php

//other testcode
$html = "<html><body><h1 title='versuch'>test</h1><div>artikeltext bla bla</div><h1>test</h1></body></html>";
//echo $html;

$dom = new DOMDocument();
$dom->loadXML($html,LIBXML_NOXMLDECL);
$xhtml_xpath = new DOMXpath($dom);
$xpath_template = "/html/body/h1[@title='versuch']";
$template_elements = $xhtml_xpath->query($xpath_template);

foreach($template_elements as $element)
{
  $element->parentNode->removeChild($element); //setAttribute("align", "left");
}
echo $dom->saveXML();
?>

<?php
  $html = "<html><body><h1 title='versuch'>test</h1><div>artikeltext bla bla</div><h1>test</h1></body></html>";
  //echo $html;
  
  $dom = new DOMDocument();
  $dom->loadXML($html,LIBXML_NOXMLDECL);
  $xhtml_xpath = new DOMXpath($dom);
  $xpath_template = "/html/body/h1[@title='versuch']";
  $template_elements = $xhtml_xpath->query($xpath_template);
  
  foreach($template_elements as $element)
  {
  	$element_clone = $element->cloneNode(false);
  	$element_clone->setAttribute('id','test');
  	$element->parentNode->appendChild($element_clone);
  }
  echo $dom->saveXML();
?>
