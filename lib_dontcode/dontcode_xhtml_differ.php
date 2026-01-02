<?php
 
  if(!isset($version)) $version = "1.0.0.ready";

  class dontcode_xhtml_differ
  {

    function catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent)
    {
      //DEMAND
      if(empty($xhtml_doc_active))                            return false;
      if(empty($xhtml_doc_sequent))                           return false;
      if($xhtml_doc_active  == strip_tags($xhtml_doc_active)) return false;
      if($xhtml_doc_sequent == strip_tags($xhtml_doc_sequent))return false;

      //SOLVE
      $shiftxml = null;
      $simplexml_doc_active  = simplexml_load_string($xhtml_doc_active);
      $simplexml_doc_sequent = simplexml_load_string($xhtml_doc_sequent);
      if(  ($simplexml_doc_active  != false)
         &&($simplexml_doc_sequent != false))
      {
        $shiftxml = '<?xml version="1.0"?>';
        $shiftxml.= '<shiftxml:diffoperations xmlns:shiftxml="namespace://dontcode.de/ns/shiftxml" xmlns:replace="namespace://dontcode.de/ns/replace" xmlns:remove="namespace://dontcode.de/ns/remove" xmlns:insert="namespace://dontcode.de/ns/insert">';
        if(   strtolower($simplexml_doc_active->getName())   
          == strtolower($simplexml_doc_sequent->getName()))
        {
          $shiftxml_children = $this->catch_shiftxml_by_walkover_xhtml_trees($simplexml_doc_active,$simplexml_doc_sequent);
          if(empty($shiftxml_children)) 
          {
            $shiftxml.= "<".$simplexml_doc_active->getName()."/>"; 
          }
          else 
          {
            $shiftxml.= "<".$simplexml_doc_active->getName().">"; 
            $shiftxml.= $shiftxml_children;
            $shiftxml.= "</".$simplexml_doc_active->getName().">"; 
          }
        }
        $shiftxml.= '</shiftxml:diffoperations>';
      }

      //EVALUATE
      if(empty($shiftxml)) return false;
      if(isset($shiftxml)) return $shiftxml;
      return false;
    }

    function catch_shiftxml_by_walkover_xhtml_trees(&$simplexml_tree_active,&$simplexml_tree_sequent)
    {
      //DEMAND
      if(!($simplexml_tree_active  instanceof SimpleXMLElement)) return false;
      if(!($simplexml_tree_sequent instanceof SimpleXMLElement)) return false;
      if(   strtolower($simplexml_tree_active->getName())   
         != strtolower($simplexml_tree_sequent->getName()))      return false;
      
      //SOLVE

      $elements_active  = $this->extract_elements_from_simplexmlelement($simplexml_tree_active);
      $elements_sequent = $this->extract_elements_from_simplexmlelement($simplexml_tree_sequent);

      $diffoperations = $this->catch_diffoperations_from_xhtml_children($elements_active,$elements_sequent);

      $has_differences = false;
      $shiftxml = "";
      foreach($diffoperations as $diffoperation)
      {
        switch($diffoperation['name'])
        {
          case 'match':
            $simplexml_subtree_active  = $diffoperation['simplexml_active'];
            $simplexml_subtree_sequent = $diffoperation['simplexml_sequent'];
            if($diffoperation['tag']!=null)
            {
              $shiftxml_subtree = $this->catch_shiftxml_by_walkover_xhtml_trees($simplexml_subtree_active,$simplexml_subtree_sequent);
              $shiftxml_attributes = $this->catch_attribute_differences_from_simplexml_as_shiftxml($simplexml_subtree_active,$simplexml_subtree_sequent);
            
              $shiftxml.= "<".$diffoperation['tag'];
              if(!empty($shiftxml_attributes))
              {
                $shiftxml.= " ".$shiftxml_attributes;
                $has_differences = true;
              }
              if(!empty($shiftxml_subtree))
              {
                if($diffoperation['tag']!=null)
                {
                  $shiftxml.= ">";
                  $shiftxml.= $shiftxml_subtree;
                  $shiftxml.= "</".$diffoperation['tag'].">";
                  $has_differences = true;
                }
              }
              else {
                $shiftxml.= "/>";
              }
            }
            else 
            {
              if($diffoperation['text_active']!=$diffoperation['text_sequent'])
              {
                $shiftxml.= "<shiftxml:replace>";
                $shiftxml.= $diffoperation['text_sequent'];
                $shiftxml.= "</shiftxml:replace>";
                $has_differences = true;
              }
              else {
                $shiftxml.= "<shiftxml:text/>";
              }
            }
            break;
          case 'insert':
            $shiftxml.= "<shiftxml:insert>";
            if($diffoperation['tag']!=null)
            {
              $shiftxml.= $diffoperation['simplexml_sequent']->asXML();
            }
            else {
              $shiftxml.= $diffoperation['text_sequent'];
            }
            $shiftxml.= "</shiftxml:insert>";
            $has_differences = true;
            break;
          case 'remove':
            if($diffoperation['tag']!=null)
            {
              $shiftxml.= "<shiftxml:remove>";
              $shiftxml.= "<".$diffoperation['tag']."/>";
              $shiftxml.= "</shiftxml:remove>";
            }
            else {
              $shiftxml.= "<shiftxml:remove/>";
            }
            $has_differences = true;
            break;
        }
      }

      if($has_differences == false) $shiftxml = "";

      //EVALUATE
      //if(!isset($shiftxml))     return false;
      //if(!is_string($shiftxml)) return false;
      if(is_string($shiftxml))  return $shiftxml;
      return false;
    }

    private function catch_attribute_differences_from_simplexml_as_shiftxml(&$element_simplexml_active,&$element_simplexml_sequent)
    {
      //DEMAND
      if(!($element_simplexml_active  instanceof SimpleXMLElement)) return false;
      if(!($element_simplexml_sequent instanceof SimpleXMLElement)) return false;
      
      //SOLVE
      $shiftxml = "";

      $attributes_active = (array)$element_simplexml_active->attributes();
      $attributes_sequent = (array)$element_simplexml_sequent->attributes();

      if(!isset($attributes_active['@attributes']))  $attributes_active  = array('@attributes'=>array());
      if(!isset($attributes_sequent['@attributes'])) $attributes_sequent = array('@attributes'=>array());

      foreach($attributes_active['@attributes'] as $attribute_name_active=>$attribute_value_active)
      {
        if(array_key_exists($attribute_name_active,$attributes_sequent['@attributes']))
        {
          $attribute_value_sequent = $attributes_sequent['@attributes'][$attribute_name_active];
          if($attribute_value_active != $attribute_value_sequent)
          {
            $shiftxml.= "replace:".$attribute_name_active."=".'"'.$attribute_value_sequent.'"'." ";
          }
        }
        else {
          $shiftxml.= "remove:".$attribute_name_active."=".'""'." ";
        }
        unset($attributes_sequent['@attributes'][$attribute_name_active]);
      }

      foreach($attributes_sequent['@attributes'] as $attribute_name_sequent=>$attribute_value_sequent)
      {
        if(!array_key_exists($attribute_name_sequent,$attributes_active['@attributes']))
        {
          $shiftxml.= "insert:".$attribute_name_sequent.'="'.$attribute_value_sequent.'" ';
        }
      }
      
      $shiftxml = rtrim($shiftxml);

      //EVALUATE
      if(!isset($shiftxml))     return false;
      if(!is_string($shiftxml)) return false;
      if(is_string($shiftxml))  return $shiftxml;
      return false;
    }

    private function extract_elements_from_simplexmlelement($simplexmlelement)
    {
      //DEMAND
      if(!($simplexmlelement instanceof SimpleXMLElement)) return false;
     
      //SOLVE
      $elements = array();
      $dom_element = dom_import_simplexml($simplexmlelement);
      $tags_count = array();
      foreach($dom_element->childNodes as $dom_child)
      {
        switch($dom_child->nodeType)
        {
          case XML_TEXT_NODE:
            $elements[] = array('tag'=>null,'id'=>null,'simplexml'=>null,'text'=>$dom_child->nodeValue);
          break;
          case XML_ELEMENT_NODE:
            $dom_name = $dom_child->nodeName;
            if(!array_key_exists($dom_name,$tags_count)) $tags_count[$dom_name] = 0;
                                                    else $tags_count[$dom_name]++;
            $dom_id   = $dom_child->getAttribute('id');
            $simplexmlelement_child = simplexml_import_dom($dom_child);

            $elements[] = array('tag'=>$dom_name,'id'=>$dom_id,'simplexml'=>$simplexmlelement_child,'text'=>null);
          break;
        }
      }
      
      //EVALUATE
      if(!isset($elements)) return false;
      if(is_array($elements)) return $elements;
      return false;
    }

    function catch_diffoperations_from_xhtml_children(array &$source_elements,array &$target_elements)
    {
      //DEMAND
      if(empty($source_elements)) return array();
      if(empty($target_elements)) return array();
      if(!is_array($source_elements_by_id = $this->filter_elements_by_id($source_elements))) return false;
      if(!is_array($target_elements_by_id = $this->filter_elements_by_id($target_elements))) return false;

      //SOLVE
      $lcs_of_ids = $this->catch_lcs_from_xhtml_children_filtered($source_elements_by_id,$target_elements_by_id,true);

      $diffoperations = array();
      $count_lcs_of_ids = count($lcs_of_ids);
      for($i_lcs_of_ids = -1;$i_lcs_of_ids < $count_lcs_of_ids; $i_lcs_of_ids++)
      {
        $source_element_id_start = -1;
        $target_element_id_start = -1;
        if($i_lcs_of_ids >= 0)
        {           
          $source_element_id_start = $lcs_of_ids[$i_lcs_of_ids]['source_index'];
          $target_element_id_start = $lcs_of_ids[$i_lcs_of_ids]['target_index'];
          $diffoperations[] = array("name"=>"match",
                                    "tag"=>$lcs_of_ids[$i_lcs_of_ids]['tag'],
                                    "id"=>$lcs_of_ids[$i_lcs_of_ids]['id'],
                                    "index"=>$i_lcs_of_ids,
                                    "simplexml_active"=>$source_elements[$source_element_id_start]['simplexml'],
                                    "simplexml_sequent"=>$target_elements[$target_element_id_start]['simplexml'],
                                    "text_active"=>$source_elements[$source_element_id_start]['text'],
                                    "text_sequent"=>$target_elements[$target_element_id_start]['text']
                                   );
        }

        $source_element_id_end = array_key_last($source_elements) + 1;
        $target_element_id_end = array_key_last($target_elements) + 1;
        if($i_lcs_of_ids+1 < $count_lcs_of_ids) 
        {
          $source_element_id_end = $lcs_of_ids[$i_lcs_of_ids+1]['source_index'];
          $target_element_id_end = $lcs_of_ids[$i_lcs_of_ids+1]['target_index'];
        }
        
        $source_elements_between_ids = $this->filter_elements_by_start_end($source_elements,$source_element_id_start + 1,$source_element_id_end - 1);
        $target_elements_between_ids = $this->filter_elements_by_start_end($target_elements,$target_element_id_start + 1,$target_element_id_end - 1);
        
        $lcs_no_id = $this->catch_lcs_from_xhtml_children_filtered($source_elements_between_ids,$target_elements_between_ids,false);

        $source_matches = array();
        $target_matches = array();
        foreach($lcs_no_id as $element_match)
        {
          $source_matches[$element_match['source_index']] = true;
          $target_matches[$element_match['target_index']] = true;
        }
        
        $i_source_element_between_id = $source_element_id_start + 1;
        $i_target_element_between_id = $target_element_id_start + 1;
        while(  ($i_source_element_between_id <= $source_element_id_end - 1)
              ||($i_target_element_between_id <= $target_element_id_end - 1))
        {
          $has_matched = false;
          while($i_source_element_between_id <= $source_element_id_end - 1)
          {
            if(!array_key_exists($i_source_element_between_id,$source_matches))
            {
              
              $diffoperations[] = array("name"=>"remove","tag"=>$source_elements[$i_source_element_between_id]['tag']);
              $i_source_element_between_id++;
            }
            else
            { 
              $has_matched = true;
              $i_source_element_between_id++;
              break;
            }
          }
       
          while($i_target_element_between_id <= $target_element_id_end - 1)
          {
            if(!array_key_exists($i_target_element_between_id,$target_matches))
            {
              $diffoperations[] = array("name"=>"insert",
                                        "tag"=>$target_elements[$i_target_element_between_id]['tag'],
                                        "id"=>$target_elements[$i_target_element_between_id]['id'],
                                        "target_index"=>$i_target_element_between_id,
                                        "simplexml_sequent"=>$target_elements[$i_target_element_between_id]['simplexml'],
                                        "text_sequent"=>$target_elements[$i_target_element_between_id]['text']);
              $i_target_element_between_id++;
            }
            else
            {
              $has_matched = true;
              $i_target_element_between_id++;
              break;
            }
          }

          if($has_matched == true)
          {
            $diffoperations[] = array("name"=>"match",
                                      "tag"=>$target_elements[$i_target_element_between_id-1]['tag'],
                                      "id"=>$target_elements[$i_target_element_between_id-1]['id'],
                                      "target_index"=>($i_target_element_between_id-1),
                                      "simplexml_active"=>$source_elements[$i_source_element_between_id-1]['simplexml'],
                                      "simplexml_sequent"=>$target_elements[$i_target_element_between_id-1]['simplexml'],
                                      "text_active"=>$source_elements[$i_source_element_between_id-1]['text'],
                                      "text_sequent"=>$target_elements[$i_target_element_between_id-1]['text']
                                    );
          }
        }
      }

      //EVALUATE
      if(!isset($diffoperations))    return false;
      if(!is_array($diffoperations)) return false;
      if(is_array($diffoperations))  return $diffoperations;
      return false;
    }

    function filter_elements_by_start_end(&$elements,$index_element_start,$index_element_end)
    {
      //DEMAND
      if(!is_int($index_element_start))             return false;
      if(!is_int($index_element_end))               return false;
      if($index_element_start > $index_element_end) return array();

      //SOLVE
      $elements_between_ids = array();
      for($i = $index_element_start;$i <= $index_element_end;$i++)
      {
        $element = $elements[$i];
        $elements_between_ids[] = array("tag"=>$element['tag'],"id"=>$element['id'],"index"=>$i);
      }
     
      //EVALUATE
      if(empty($elements_between_ids))  return false;
      if(!empty($elements_between_ids)) return $elements_between_ids;
      return false;
    }

    function catch_lcs_from_xhtml_children_filtered(array $source_elements_filtered,array $target_elements_filtered,$consider_ids = true)
    {
      //DEMAND
      if(empty($source_elements_filtered)) return array();
      if(empty($target_elements_filtered)) return array();
      if(!isset($consider_ids))            return false;
      if(!is_bool($consider_ids))          return false;

      //SOLVE
      $common_matrix = null;
        
      $matrix_source_count = count($source_elements_filtered);
      $matrix_target_count = count($target_elements_filtered);

      //part: matching
      for($i_matrix_source = 0; $i_matrix_source < $matrix_source_count; $i_matrix_source++)
      {
        for($i_matrix_target = 0; $i_matrix_target < $matrix_target_count; $i_matrix_target++)
        {
          $match = 0;

          if(  ($source_elements_filtered[$i_matrix_source]['tag'] == $target_elements_filtered[$i_matrix_target]['tag'])
             &&(  (  ($source_elements_filtered[$i_matrix_source]['id']  == $target_elements_filtered[$i_matrix_target]['id'])
                   &&($consider_ids == true)
                  )
                ||($consider_ids == false)
               )
            ) $match = 1;

          $values = array(0);
          if(  ($i_matrix_source == 0)
             &&($i_matrix_target == 0))$values   = array($match);
          if($i_matrix_source > 0)     $values[] = $common_matrix[$i_matrix_source-1][$i_matrix_target  ];
          if($i_matrix_target > 0)     $values[] = $common_matrix[$i_matrix_source  ][$i_matrix_target-1];
          if(  ($i_matrix_source > 0)
             &&($i_matrix_target > 0)) $values[] = $common_matrix[$i_matrix_source-1][$i_matrix_target-1] + $match;

          $common_matrix[$i_matrix_source][$i_matrix_target] = max($values);
        }
      }

      //part: backtracing
      $i_matrix_source = $matrix_source_count - 1;
      $i_matrix_target = $matrix_target_count - 1;
      $lcs = array();
      
      while(($i_matrix_source > -1)||($i_matrix_target > -1)) 
      {
        $value = $common_matrix[$i_matrix_source][$i_matrix_target];
        $values = array(0);
        if($i_matrix_source > 0)     $values[] = $common_matrix[$i_matrix_source-1][$i_matrix_target  ];
        if($i_matrix_target > 0)     $values[] = $common_matrix[$i_matrix_source  ][$i_matrix_target-1];
        if(  ($i_matrix_source > 0)
           &&($i_matrix_target > 0)) $values[] = $common_matrix[$i_matrix_source-1][$i_matrix_target-1];

        $max = max($values);

        $match = $value - $max;
        if($match == 1)
        {
          $element = array();
          $element['tag']          = $source_elements_filtered[$i_matrix_source]['tag'];
          $element['id']           = $source_elements_filtered[$i_matrix_source]['id'];
          $element['source_index'] = $source_elements_filtered[$i_matrix_source]['index'];
          $element['target_index'] = $target_elements_filtered[$i_matrix_target]['index'];
          array_unshift($lcs, $element);
        } 
        
        $changed = false;

        if(($changed == false)&&($i_matrix_source > 0)&&($i_matrix_target > 0))
          if($common_matrix[$i_matrix_source-1][$i_matrix_target-1] == $max)
          {
            $i_matrix_source--;
            $i_matrix_target--;
            $changed = true;
          }

        if(($changed == false)&&($i_matrix_source > 0))
          if($common_matrix[$i_matrix_source-1][$i_matrix_target] == $max)
          {
            $i_matrix_source--;
            $changed = true;
          }

        if(($changed == false)&&($i_matrix_target > 0))
          if($common_matrix[$i_matrix_source][$i_matrix_target-1] == $max)
          {
            $i_matrix_target--;
            $changed = true;
          }

        if(($changed == false))
          {
            if($i_matrix_source > -1) $i_matrix_source--;
            if($i_matrix_target > -1) $i_matrix_target--;
            $changed = true;
          }
      }

      //EVALUATE
      if(!isset($lcs)) return false;
      if(is_array($lcs)) return $lcs;
      return false;
    }

    function filter_elements_by_id($elements)
    {
      //DEMAND
      if(empty($elements)) return array();
      //SOLVE
      $valid_elements = false;
      $elements_filtered = array();
      foreach($elements as $element_index=>$element)
      {
        if(!array_key_exists('tag', $element))                          break;
        else if(($element['tag']!=null)&&(!is_string($element['tag']))) break;
        else if(!array_key_exists('id', $element))                      break;
        else if(($element['id']!=null)&&(!is_scalar($element['id'])))   break;

        if($element === end( $elements )) $valid_elements = true;

        if((is_string($element['id']))&&($element['id']!=null)) 
          $elements_filtered[] = array("tag"=>$element['tag'],"id"=>$element['id'],"index"=>$element_index);
      }
    
      //EVALUATE
      if($valid_elements == false)        return false;
      if(!isset($elements_filtered))      return false;
      if(!is_array($elements_filtered))   return false;
      if(  ($valid_elements == true)
         &&(is_array($elements_filtered)))return $elements_filtered;
      return false;
    }
  }

  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    echo $version."<br/>";
    $path = pathinfo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $dir = $path['dirname'];

    $testcases = ['document1'=>1,'diffid'=>2,'subtree'=>3,'tagandtext'=>4,'dices'=>5,'scriptscript'=>6];
    $testcase = null;
    $output = null;
    if(isset($_REQUEST['testcase']))$testcase = $_REQUEST['testcase'];
    //if(array_key_exists($testcase,$testcases)) $testcase = $testcases[$testcase];
    if(isset($_REQUEST['output']))$output = $_REQUEST['output'];

    $xhtml_differ = new dontcode_xhtml_differ();
    switch($testcase)
    {
      case $testcases['document1']:
        $xhtml_doc1 = '<html>';
        $xhtml_doc1.= '<head>';
        $xhtml_doc1.= '<title>version '.$version.'</title>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/dontcode_diffrequesthandler.js" defer></script>';
        $xhtml_doc1.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/dontcode_eventhandler.js" defer></script>';
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

        $xhtml_doc2 = '<html>';
        $xhtml_doc2.= '<head>';
        $xhtml_doc2.= '<title>version '.$version.'</title>';
        $xhtml_doc2.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/dontcode_diffrequesthandler.js" defer></script>';
        $xhtml_doc2.= '<script language="javascript" type="text/javascript" src="https://'.$dir.'/dontcode_eventhandler.js" defer></script>';
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

        if(empty($output))      
        {
          $shiftxml = xhtml_differ->determine_differences($xhtml_doc1,$xhtml_doc2);
        }
        if(($output==1)&&(isset($xhtml_doc1)))      echo $xhtml_doc1;
        else if(($output==2)&&(isset($xhtml_doc2))) echo $xhtml_doc2;
        else                                        echo 'no output';
      break;
      case $testcases['diffid']:
        $active_elements = array(array("tag"=>"h1","id"=>"label1"),
                                 array("tag"=>"p" ,"id"=>null),
                                 array("tag"=>"p","id"=>"statement1"),
                                 array("tag"=>"h1","id"=>"label2"),
                                 array("tag"=>"p","id"=>"statement3"),
                                 array("tag"=>"p","id"=>null),
                                 array("tag"=>"table","id"=>null),
                                 array("tag"=>"p","id"=>"statement4"),
                                 array("tag"=>"h1","id"=>"label3"),
                                 array("tag"=>"p","id"=>"link")
                                );
        $sequent_elements = array(array("tag"=>"h1","id"=>"label1"),
                                  array("tag"=>"p" ,"id"=>null),
                                  array("tag"=>"p","id"=>"statement1"),
                                  array("tag"=>"p","id"=>"statement2"),
                                  array("tag"=>"h1","id"=>"label2"),
                                  array("tag"=>"p","id"=>"statement3"),
                                  array("tag"=>"p","id"=>null),
                                  array("tag"=>"li","id"=>null),
                                  array("tag"=>"div","id"=>null),
                                  array("tag"=>"p","id"=>"statement4"),
                                  array("tag"=>"h1","id"=>"label3"),
                                  array("tag"=>"a","id"=>"link")
                                 );
        $diffoperations = $xhtml_differ->catch_diffoperations_from_xhtml_children($active_elements,$sequent_elements);
        echo "<pre>";
        print_r($diffoperations);
        echo "</pre>";
      break;
      case $testcases['subtree']:
        $xhtml_doc_active = '<html>';
        $xhtml_doc_active.= '<head>';
        $xhtml_doc_active.= '<title>Active Document</title>';
        $xhtml_doc_active.= '</head>';
        $xhtml_doc_active.= '<body title="Unittest Active Subtree">';
        $xhtml_doc_active.= '<p title="Intro in ShiftXML">Diese Website testet ShiftXML</p>';
        $xhtml_doc_active.= '<h2>Funktionsumfang</h2>';
        $xhtml_doc_active.= '<ol><li>Inserts</li><li title="command">Replace</li><li title="remove">Removes</li></ol>';
        $xhtml_doc_active.= '<p>Zu was ist ShiftXML geeignet?</p>';
        $xhtml_doc_active.= '<div id="box1">';
        $xhtml_doc_active.=   '<h2>Alternative XUpdate</h2>';
        $xhtml_doc_active.=   '<p>XUpdate ist nicht wirklich eine Alternative, da es mehrdeutig ist und keine eindeutigen Spezifikationen vorweist.</p>';
        $xhtml_doc_active.= '</div>';
        $xhtml_doc_active.= '<img alt="Bild"/>';
        $xhtml_doc_active.= '</body>';
        $xhtml_doc_active.= '</html>';

        $xhtml_doc_sequent = '<HTML>';
        $xhtml_doc_sequent.= '<head>';
        $xhtml_doc_sequent.= '<title>Active Document</title>';
        $xhtml_doc_sequent.= '</head>';
 
        $xhtml_doc_sequent.= '<body title="Unittest Sequent Subtree">';
        $xhtml_doc_sequent.= '<h1>Intro in ShiftXML</h1>';
        $xhtml_doc_sequent.= '<p>Diese Website testet ShiftXML</p>';
        $xhtml_doc_sequent.= '<h2>Funktionsumfang</h2>';
        $xhtml_doc_sequent.= '<ol><li title="command1">Inserts</li><li title="command2">Replace</li><li>Removes</li></ol>';
        $xhtml_doc_sequent.= '<p>ShiftXML ist sowohl zum Modifizieren für generelle XML-Dokumente als auch XHTML-Dokumente geeignet.</p>';
        $xhtml_doc_sequent.= '<div title="no real option" id="box1">';
        $xhtml_doc_sequent.=   '<h2>Alternative XUpdate</h2>';
        $xhtml_doc_sequent.=   '<p>XUpdate ist nicht wirklich eine Alternative, da es mehrdeutig ist und keine eindeutigen Spezifikationen vorweist.</p>';
        $xhtml_doc_sequent.= '</div>';
        $xhtml_doc_sequent.= '</body>';
        $xhtml_doc_sequent.= '</HTML>';

        $shiftxml = $xhtml_differ->catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent);
      
        echo "<pre>";
        print_r(htmlentities(str_replace(['><'], [">\r\n<"], $shiftxml)));
        echo "</pre>";
        break;
      case $testcases['tagandtext']:
        $xhtml_doc_active = '<html>';
        $xhtml_doc_active.= '<head>';
        $xhtml_doc_active.= '<title>Active Document</title>';
        $xhtml_doc_active.= '</head>';
        $xhtml_doc_active.= '<body title="Unittest Active Subtree">';
        $xhtml_doc_active.= 'This is a';
        $xhtml_doc_active.= '<b>very important</b>';
        $xhtml_doc_active.= ' sentence which leads to this';
        $xhtml_doc_active.= '<a src="dontcode.de">link</a>';
        $xhtml_doc_active.= '</body>';
        $xhtml_doc_active.= '</html>';

        $xhtml_doc_sequent = '<HTML>';
        $xhtml_doc_sequent.= '<head>';
        $xhtml_doc_sequent.= '<title>Sequent Document</title>';
        $xhtml_doc_sequent.= '</head>';
        $xhtml_doc_sequent.= '<body title="Unittest Sequent Subtree">';
        $xhtml_doc_sequent.= 'This might be a';
        $xhtml_doc_sequent.= '<b>not so very important</b>';
        //$xhtml_doc_sequent.= ' sentence which leads to this';
        $xhtml_doc_sequent.= '<a src="witze.de">link</a>';
        $xhtml_doc_sequent.= 'leave a comment!';
        $xhtml_doc_sequent.= '</body>';
        $xhtml_doc_sequent.= '</HTML>';

        $shiftxml = $xhtml_differ->catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent);
      
        echo "<pre>";
        print_r(htmlentities(str_replace(['><'], [">\r\n<"], $shiftxml)));
        echo "</pre>";
        break;
      case $testcases['dices']:
        /*$xhtml_doc_active = '<?xml version="1.0"?>';*/
        $xhtml_doc_active = '<html>';
        $xhtml_doc_active.= '<head>';
        $xhtml_doc_active.= '<title>Roll the dices!</title>';
        $xhtml_doc_active.= '<meta name="tab-id" content="1"/>';
        $xhtml_doc_active.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_diffrequesthandler.js" defer=""> </script>';
        $xhtml_doc_active.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_eventhandler.js" defer=""> </script>';
        $xhtml_doc_active.= '</head>';
        $xhtml_doc_active.= '<body>';
        $xhtml_doc_active.= '<header>';
        $xhtml_doc_active.= '<marquee>Roll the dices!</marquee>';
        $xhtml_doc_active.= '</header>';
        $xhtml_doc_active.= '<input type="button" value="Roll now!" onclick="include_shiftxml.register(\'https:/\'+\'/fix.dontcode.de/stuff/dices.php?tab_id=1\');"/>';
        $xhtml_doc_active.= '<div></div>';
        $xhtml_doc_active.= '<div></div>';
        $xhtml_doc_active.= '<div></div>';
        $xhtml_doc_active.= '<div></div>';
        $xhtml_doc_active.= '<div></div>';
        //$xhtml_doc_active.= '<div>&#x2680;</div>';
        //$xhtml_doc_active.= '<div>&#x2681;</div>';
        //$xhtml_doc_active.= '<div>&#x2682;</div>';
        //$xhtml_doc_active.= '<div>&#x2683;</div>';
        //$xhtml_doc_active.= '<div>&#x2684;</div>';
        $xhtml_doc_active.= '</body></html>';

        /*$xhtml_doc_sequent = '<?xml version="1.0"?>';*/
        $xhtml_doc_sequent = '<html>';
        $xhtml_doc_sequent.= '<head>';
        $xhtml_doc_sequent.= '<title>Roll the dices!</title>';
        $xhtml_doc_sequent.= '<meta name="tab-id" content="1"/>';
        $xhtml_doc_sequent.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_diffrequesthandler.js" defer=""> </script>';
        $xhtml_doc_sequent.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_eventhandler.js" defer=""> </script>';
        $xhtml_doc_sequent.= '</head>';
        $xhtml_doc_sequent.= '<body>';
        $xhtml_doc_sequent.= '<header>';
        $xhtml_doc_sequent.= '<marquee>Roll the dices!</marquee>';
        $xhtml_doc_sequent.= '</header>';
        $xhtml_doc_sequent.= '<input type="button" value="Roll now!" onclick="include_shiftxml.register(\'https:/\'+\'/fix.dontcode.de/stuff/dices.php?tab_id=1\');"/>';
        $xhtml_doc_sequent.= '<div></div>';
        $xhtml_doc_sequent.= '<div></div>';
        $xhtml_doc_sequent.= '<div></div>';
        //$xhtml_doc_sequent.= '<div>&#x2680;</div>';
        //$xhtml_doc_sequent.= '<div>&#x2681;</div>';
        //$xhtml_doc_sequent.= '<div>&#x2682;</div>';
        $xhtml_doc_sequent.= '</body></html>';

        $shiftxml = $xhtml_differ->catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent);
      
        echo "<pre>";
        print_r(htmlentities(str_replace(['><'], [">\r\n<"], $shiftxml)));
        echo "</pre>";
        break;
      case $testcases['scriptscript']:
        /*$xhtml_doc_active = '<?xml version="1.0"?>';*/
        $xhtml_doc_active = '<html>';
        $xhtml_doc_active.= '<head>';
        $xhtml_doc_active.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_includer.js" defer=""> </script>';
        $xhtml_doc_active.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_eventhandler.js" defer=""> </script>';
        $xhtml_doc_active.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_doesntexist.js" defer=""> </script>';
        $xhtml_doc_active.= '</head>';
        $xhtml_doc_active.= '<body>';
        $xhtml_doc_active.= '</body></html>';

        /*$xhtml_doc_sequent = '<?xml version="1.0"?>';*/
        $xhtml_doc_sequent = '<html>';
        $xhtml_doc_sequent.= '<head>';
        $xhtml_doc_sequent.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_includer.js" defer=""> </script>';
        $xhtml_doc_sequent.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_eventhandler.js" defer=""> </script>';
        $xhtml_doc_sequent.= '<script type="text/javascript" src="https://fix.dontcode.de/lib_dontcode/dontcode_doesntexist.js" defer=""> </script>';
        $xhtml_doc_sequent.= '</head>';
        $xhtml_doc_sequent.= '<body>';
        $xhtml_doc_sequent.= '</body></html>';

        $shiftxml = $xhtml_differ->catch_shiftxml_out_of_xhtml_documents($xhtml_doc_active,$xhtml_doc_sequent);
      
        echo "<pre>";
        print_r(htmlentities(str_replace(['><'], [">\r\n<"], $shiftxml)));
        echo "</pre>";
        break;
    }
    $output = null;
  }

?>