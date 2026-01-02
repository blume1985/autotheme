<?

  //if(!isset($version))
  //{
    $version = "1.0.0.ready";
  //}//if

  class dontcode_mvc_view 
  {
    private $name = null;

    private $dom = null;
    private $tab_id = null;

    public function __construct($name,$xhtml_template)
    {
      //DEMAND
      if(!is_string($name))                     die("no contract@".__FILE__.'@'.__LINE__);
      if(!is_null($this->dom))                  die("no contract@".__FILE__.'@'.__LINE__);

      //SOLVE
      $this->name = $name;
      $has_loaded = $this->load_xhtml_template_by_variable($xhtml_template);
      if($has_loaded)
      {
        $has_head                                   = $this->has_head_otherwise_insert();
        //$has_meta                                   = $this->has_meta_tabid_otherwise_insert();
        $has_css_link                               = $this->has_css_link_otherwise_insert();
        //$has_script_diffrequesthandler              = $this->has_script_diffrequesthandler_otherwise_insert();
        //$has_script_eventhandler                    = $this->has_script_eventhandler_otherwise_insert();
        $has_script_container_for_rules_action_user = $this->has_script_container_for_rules_action_user_otherwise_insert();
      }

      //EVALUATE
      if(!is_string($this->name))                            die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_loaded))                                 die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_head))                                   die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_css_link))                               die("no contract@".__FILE__.'@'.__LINE__);
      //if(empty($has_script_diffrequesthandler))            die("no contract@".__FILE__.'@'.__LINE__);
      //if(empty($has_script_eventhandler))                  die("no contract@".__FILE__.'@'.__LINE__);
      if(empty($has_script_container_for_rules_action_user)) die("no contract@".__FILE__.'@'.__LINE__);
    }

    public function load_xhtml_template_by_variable($xhtml_template)
    {
      //DEMAND
      if(empty($xhtml_template)) return false;

      //SOLVE
      $has_loaded = null;
      try 
      {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->loadXML($xhtml_template,LIBXML_NOXMLDECL|LIBXML_NOBLANKS);
        $this->dom->encoding = "UTF-8";
        $has_loaded = true;
      } catch (\Throwable $th) 
      {
        $has_loaded = false;
      }

      //EVALUATE
      if(is_null($has_loaded)) return false;
      if($has_loaded == false) return false;
      if($has_loaded == true)  return true;
      return false;
    }

    public function has_head_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $xhtml_dom_root_name = strtolower($this->dom->documentElement->tagName);
      $has_head = null;
      if($xhtml_dom_root_name == 'html')
      {
        $xhtml_xpath = new DOMXpath($this->dom);
        $element_head = $xhtml_xpath->query('/html/head');
        if(count($element_head)==0)
        {
          $domElement_head = $this->dom->createElement('head');
          $this->dom->documentElement->appendChild($domElement_head);
          $element_head = $xhtml_xpath->query('/html/head');
          $has_head = true;
        }//if
        else if(count($element_head)==1) 
        {
          $has_head = true;
        }//else if
        else
        {                             
          $has_head = false;
        }//else
      }//if
      else 
      {
        $has_head = false;
      }//else 

      //EVALUATE
      if($has_head === null)  return false;
      if($has_head === false) return false;
      if($has_head !== true)  return false;
      if($has_head === true)  return true;
      return false;
    }

    public function has_meta_tabid_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;
      if($this->set_tab_id() != true)          return false;
      if(empty($this->tab_id))                 return false;

      //SOLVE
      $has_meta = null;
      $tab_id = 1;
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_child = $xhtml_xpath->query('/html/head/meta[@name="tab-id"]');
      if(count($element_child)==0)
      {
        $element_head  = $xhtml_xpath->query('/html/head');
        if(count($element_head)!=1)     $has_meta = false;
        else 
        {
          $domElement_child = $this->dom->createElement('meta');
          $domAttribute1    = $this->dom->createAttribute('name');
          $domAttribute1->value = 'tab-id';
          $domAttribute2    = $this->dom->createAttribute('content');
          $domAttribute2->value = $tab_id;
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $element_head[0]->appendChild($domElement_child);
                                        $has_meta = true;
        }
      }
      else if(count($element_child)==1) $has_meta = true;
      else                              $has_meta = false;
      
      //EVALUATE
      if($has_meta === null)  return false;
      if($has_meta === false) return false;
      if($has_meta !== true)  return false;
      if($has_meta === true)  return true;
      return false;
    }

    public function set_tab_id()
    {
      //DEMAND
      if(!empty($this->tab_id)) return false;

      //SOLVE
      //@change for REQUEST[tab_id] and !REQUEST[tab_id]
      $this->tab_id = 1;

      //EVALUATE
      return true;
    }
    
    public function has_css_link_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $has_meta = null;
      $url = sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME']
      );
      $link = $url."/lib_dontcode/dontcode.css.php?_dontcode_name=".$this->name;
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_child = $xhtml_xpath->query('/html/head/meta[@href="'.$link.'"]');
      
      if(count($element_child)==0)
      {
        $element_head  = $xhtml_xpath->query('/html/head');
        if(count($element_head)!=1)     $has_meta = false;
        else 
        {
          $domElement_child = $this->dom->createElement('link');
          $domAttribute1 = $this->dom->createAttribute('rel');
          $domAttribute1->value = 'stylesheet';
          $domAttribute2 = $this->dom->createAttribute('href');
          $domAttribute2->value = $link;
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $element_head[0]->appendChild($domElement_child);
                                        $has_meta = true;
        }
      }
      else if(count($element_child)==1) $has_meta = true;
      else                              $has_meta = false;
      
      //EVALUATE
      if(is_null($has_meta)) return false;
      if($has_meta != true)  return false;
      if($has_meta == true)  return true;
      return false;
    }

    public function has_script_diffrequesthandler_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $has_script_diffrequesthandler = null;
      $script_path = "https://".$_SERVER['HTTP_HOST']."/lib_dontcode/dontcode_diffrequesthandler.js";
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_child = $xhtml_xpath->query('/html/head/script[@src="'.$script_path.'"]');
      
      if(count($element_child)==0)
      {
        $element_head  = $xhtml_xpath->query('/html/head');
        if(count($element_head)!=1)     $has_script_diffrequesthandler = false;
        else 
        {
          $domElement_child = $this->dom->createElement('script',' ');
          $domAttribute1 = $this->dom->createAttribute('type');
          $domAttribute1->value = 'text/javascript';
          $domAttribute2 = $this->dom->createAttribute('src');
          $domAttribute2->value = $script_path;
          $domAttribute3 = $this->dom->createAttribute('defer');
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $domElement_child->appendChild($domAttribute3);
          $element_head[0]->appendChild($domElement_child);
                                        $has_script_diffrequesthandler = true;
        }
      }
      else if(count($element_child)==1) $has_script_diffrequesthandler = true;
      else                              $has_script_diffrequesthandler = false;
      
      //EVALUATE
      if(is_null($has_script_diffrequesthandler)) return false;
      if($has_script_diffrequesthandler != true)  return false;
      if($has_script_diffrequesthandler == true)  return true;
      return false;
    }

    public function has_script_eventhandler_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $has_script_eventhandler = null;
      $script_path = "https://".$_SERVER['HTTP_HOST']."/lib_dontcode/dontcode_eventhandler.js";
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_child = $xhtml_xpath->query('/html/head/script[@src="'.$script_path.'"]');
      
      if(count($element_child)==0)
      {
        $element_head  = $xhtml_xpath->query('/html/head');
        if(count($element_head)!=1)     $has_script_eventhandler = false;
        else 
        {
          $domElement_child = $this->dom->createElement('script',' ');
          $domAttribute1 = $this->dom->createAttribute('type');
          $domAttribute1->value = 'text/javascript';
          $domAttribute2 = $this->dom->createAttribute('src');
          $domAttribute2->value = $script_path;
          $domAttribute3 = $this->dom->createAttribute('defer');
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $domElement_child->appendChild($domAttribute3);
          $element_head[0]->appendChild($domElement_child);
                                        $has_script_eventhandler = true;
        }
      }
      else if(count($element_child)==1) $has_script_eventhandler = true;
      else                              $has_script_eventhandler = false;
      
      //EVALUATE
      if(is_null($has_script_eventhandler)) return false;
      if($has_script_eventhandler != true)  return false;
      if($has_script_eventhandler == true)  return true;
      return false;
    }

    public function has_script_container_for_rules_action_user_otherwise_insert()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $has_script_rules = null;
      $script_id = "_dontcode_script_rules";
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_child = $xhtml_xpath->query('/html/head/script[@id="'.$script_id.'"]');

      if(count($element_child)==0)
      {
        $element_head  = $xhtml_xpath->query('/html/head');
        if(count($element_head)!=1)     $has_script_rules = false;
        else 
        {
          $domElement_child = $this->dom->createElement('script',' ');
          $domAttribute1 = $this->dom->createAttribute('type');
          $domAttribute1->value = 'text/javascript';
          $domAttribute2 = $this->dom->createAttribute('id');
          $domAttribute2->value = $script_id;
          $domAttribute3 = $this->dom->createAttribute('defer');
          $domElement_child->appendChild($domAttribute1);
          $domElement_child->appendChild($domAttribute2);
          $domElement_child->appendChild($domAttribute3);
          $element_head[0]->appendChild($domElement_child);
                                        $has_script_rules = true;
        }
      }
      else if(count($element_child)==1) $has_script_rules = true;
      else                              $has_script_rules = false;

      //EVALUATE
      if($has_script_rules === null)  return false;
      if($has_script_rules === false) return false;
      if($has_script_rules !== true)  return false;
      if($has_script_rules === true)  return true;
      return false;
    }

    public function integrate_data_into_xpath_given_elements_of_dom($xpath_template,$data,array $modeldata2dom)
    {//description: xpath could give multiple elements -> we need this function to handle this!
      //DEMAND
      if(empty($this->dom))                                          return false;
      if(!($this->dom instanceof DOMDocument))                       return false;
      if((!is_array($data))&&(!is_scalar($data)&&(!is_null($data)))) return false;
      if(empty($xpath_template))                                     return false;
      if(!is_string($xpath_template))                                return false;

      //SOLVE
      $xhtml_xpath = new DOMXpath($this->dom);
      $template_elements = $xhtml_xpath->query($xpath_template);

      $found_element_by_xpath = null;
      if(count($template_elements)>0)
      {
        foreach($template_elements as $template_element)
        {
          $this->integrate_data_into_template_element($template_element,$data,$modeldata2dom);
        }
        $found_element_by_xpath = true;
      }
      else if(count($template_elements)==0)
      {
        $found_element_by_xpath = false;
      }
      //EVALUATE
      if($found_element_by_xpath === null)  return false;
      if($found_element_by_xpath === false) return false;
      if($found_element_by_xpath !== true)  return false;
      if($found_element_by_xpath === true)  return true;
      return false;
    }
    private function integrate_data_into_template_element($template_element,$datasets,$modeldata2dom)
    {
      //DEMAND
      if(empty($this->dom))                                                  return false;
      if(!($this->dom instanceof DOMDocument))                               return false;
      if((!is_array($datasets))&&(!is_scalar($datasets)&&(!is_null($data)))) return false;
      if(!is_array($modeldata2dom))                                          return false;

      //SOLVE
      $has_integrated = null;
      $template_main = null;
      $templates_nested = array();
      $first_scalar = false;

      if($datasets !== null)
      {
        if(is_scalar($datasets)) $data = array($datasets);
        if(is_array($datasets))
        {
          $xpath = new DOMXpath($this->dom);
          
          foreach($datasets as $dataset_index=>$dataset)
          {
            $template_duplicate = $template_element->cloneNode(true);
            
            foreach($dataset as $data_variable=>$data_value)
            {
              if(preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $data_variable))
              {
                $relative_xpaths = array_keys($modeldata2dom, $data_variable);
                
                foreach($relative_xpaths as $relative_xpath)
                {
                  $this->insert_textnode_if_text_requested($xpath,$relative_xpath,$template_duplicate);
                  
                  $elements_insert_of_template_duplicate = $xpath->query('./'.$relative_xpath, $template_duplicate);
                  
                  foreach($elements_insert_of_template_duplicate as $element_insert_of_template_duplicate)
                  {
                    $this->include_data_into_xml_element($element_insert_of_template_duplicate,$data_variable,$data_value);
                  }//foreach
                }//foreach
              }//if
            }//foreach

            $template_element->parentNode->append($template_duplicate);
          }//foreach($data as $data_variable=>$data_values)
        }//if(is_array($data))
      }//if($data !== null)
      
      $template_element->parentNode->removeChild($template_element);
      
      $has_integrated = true;

      //EVALUATE
      if($has_integrated === null)  return false;
      if($has_integrated === false) return false;
      if($has_integrated !== true)  return false;
      if($has_integrated === true)  return true;
      return false;
    }//function

    private function insert_textnode_if_text_requested($xpath,$relative_xpath,$template_duplicate)
    {
      //DEMAND
      if(!(str_ends_with($relative_xpath, 'text()'))) return false;
              
      //SOLVE
      $has_insert_textnode = null;
      $relative_xpath_without_text = './'.substr_replace($relative_xpath ,"",-7);

      if(strlen($relative_xpath_without_text) == 2)
      {
        $relative_xpath_without_text = ".";
      }//if

      $elements_of_template_duplicate = $xpath->query($relative_xpath_without_text, $template_duplicate);
      
      foreach($elements_of_template_duplicate as $element_of_template_duplicate)
      {
        if(empty($element_of_template_duplicate->nodeValue))
        {
          $element_of_template_duplicate->nodeValue = " ";
          $has_insert_textnode = true;
        }//if
      }//foreach

      //EVALUATE
      if($has_insert_textnode === null) return false;
      if($has_insert_textnode !== true) return false;
      if($has_insert_textnode === true) return false;
      return false;
    }

    private function include_data_into_xml_element($element,$data_variable,$data_values/*,$comesfromarray */)
    {
      //DEMAND
      if(!(  ($element instanceof DOMElement)
           ||($element instanceof DOMAttr)
           ||($element instanceof DOMText)))                     return false;
      if(!(preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $data_variable))) return false;
      if(!(is_scalar($data_values)&&!is_array($data_values)))         return false;
      
      //SOLVE
      $has_included = null;

      if(is_scalar($data_values)) 
      {
        if  ($element->nodeType == XML_TEXT_NODE)   
        {
          if(($element->nodeValue === "")||($element->nodeValue === " "))       
          {
            $element->nodeValue = $data_values;
            $has_included = true;
          }//if
          else if(!empty($element->nodeValue)) 
          {
            $element->nodeValue = $element->nodeValue . ',' . $data_values;
            $has_included = true;
          }//else if
        }
        else if($element->nodeType == XML_ATTRIBUTE_NODE)
        {
          if(($element->value === "")||($element->value === " "))       
          {
            $element->value = $data_values;
            $has_included = true;
          }
          else
          {
            $element->value = ",".$data_values;
            $has_included = true;
          }
        }
        else if($element->nodeType == XML_ELEMENT_NODE)
        {
          try {
            $element->nodeValue = $data_values;
            $has_included = true;
          } catch (\Throwable $th) {
            $has_included = false;
          }
        }
      }

      //EVALUATE
      if($has_included === null)  return false;
      if($has_included === false) return false;
      if($has_included !== true)  return false;
      if($has_included === true)  return true;
      return false;
    }

    public function deposit_rule_release_2dom($rule_number,$rule_xpath,$rule_user_action_number,$rules_pass,$rule_event)
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $has_script_rules = null;
      $script_id = "_dontcode_script_rules";
      $xhtml_xpath = new DOMXpath($this->dom);
      $element_rules = $xhtml_xpath->query('/html/head/script[@id="'.$script_id.'"]');
      $script_valid = null;
      if(count($element_rules)==1)
      {
        $node = $element_rules[0];
       
        $nodeValue = $node->textContent;
        if(strlen(trim($nodeValue)) == 0)
        {
          $node->nodeValue = "var rules = [];\n";
        }
        $nodeValue = 'rules['.$rule_number.'] = ';
        $nodeValue.= '['.$rule_number.',';
        $nodeValue.= '"'.$rule_xpath.'",';
        $nodeValue.= ''.$rule_user_action_number.',';
        if(empty($rules_pass))
        {
          $nodeValue.= 'null,';
        }//if
        else 
        {
          $nodeValue.= '[';
          foreach($rules_pass as $key=>$rule_pass)
          {
            $nodeValue.= '["'.$rule_pass.'",';
            $nodeValue.= '"'.$key.'"]';
            if(!($key === array_key_last($rules_pass)))
              $nodeValue.= ',';
          }
          $nodeValue.= '],';
        }
        $nodeValue.= '"'.$rule_event.'"';
        $nodeValue.= '];';
        $nodeValue.= "\n";

        //@delete: $domElement_text = $this->dom->createTextNode($nodeValue);
        //@delete: $node->appendChild($domElement_text);
        //@delete: $node->append($nodeValue);
        $node->nodeValue = $node->nodeValue . $nodeValue;

        $script_valid = true;
      }
      else 
      {
        $script_valid = false;
      }

      //EVALUATE
      if($script_valid === null)  return false;
      if($script_valid === false) return false;
      if($script_valid !== true)  return false;
      if($script_valid === true)  return true;
      return false;
    }

    public function show()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $show = $this->get();
      echo $show;
      $has_shown = (bool)$show;
      
      //EVALUATE
      if(empty($has_shown))  return false;
      if($has_shown != true) return false;
      if($has_shown == true) return true;
      return false;
    }

    public function get()
    {
      //DEMAND
      if(empty($this->dom))                    return false;
      if(!($this->dom instanceof DOMDocument)) return false;

      //SOLVE
      $xhtml_document = null;
      try 
      {
        $xhtml_document = $this->dom->saveXML($this->dom->documentElement);
        $xhtml_document = str_replace("&amp;#","&#",$xhtml_document);
      } catch (\Throwable $th) 
      {
        $xhtml_document = false;
      }
      
      //EVALUATE
      if($xhtml_document === null)    return false;
      if($xhtml_document === false)   return false;
      if(!is_string($xhtml_document)) return false;
      if(is_string($xhtml_document))  return $xhtml_document;
      return false;
    }
  }

?>