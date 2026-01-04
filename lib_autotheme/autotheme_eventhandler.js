//This Source Code Form is subject to the terms of the Mozilla Public
//License, v. 2.0. If a copy of the MPL was not distributed with this
//file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

class dontcode_log
{
  name = null;

  constructor(name)
  {
    this.name = name;
  }

  handle(returnvalue,loglevel,linenumber)
  {
    //console.log('['+this.name+']@LINE '+linenumber+' value:'+returnvalue);
    return returnvalue;
  }
}

log = new dontcode_log('dontcode_eventhandler');

class dontcode_eventhandler
{
  rules = [];
  diffrequesthandler = null;

  constructor(diffrequesthandler)
  {
    //DEMAND
    if(typeof diffrequesthandler != "object")                        return log.handle(false,'warn',new Error().lineNumber);
    if(!(diffrequesthandler instanceof dontcode_diffrequesthandler)) return log.handle(false,'warn',new Error().lineNumber);
    if(typeof this.diffrequesthandler == "undefined")                return log.handle(false,'warn',new Error().lineNumber);

    //SOLVE
    var has_init = null;
    if(typeof diffrequesthandler.register === "function")
    {
      this.diffrequesthandler = diffrequesthandler;
      has_init = true;
    }
    else
    {
      has_init = false;
    }
    
    //EVALUATE
    if(typeof has_init != "boolean") return log.handle(false,'warn',new Error().lineNumber);
    if(has_init == false)            return log.handle(false,'info',new Error().lineNumber);
    if(has_init == true)             return log.handle(true ,'info',new Error().lineNumber);
    return false;
  }

  is_xpath_valid(xpath)
  {
    //DEMAND
    if(typeof xpath != "string") return false;
    
    //SOLVE
    var is_valid = null;
    try 
    {
      document.evaluate(xpath, document, null, XPathResult.ANY_TYPE, null);
      is_valid = true;
    } 
    catch (e) 
    {
      is_valid = false;
      console.error('Provided XPath expression is not valid', e);
    }

    //EVALUATE
    if(typeof is_valid != "boolean") return false;
    if(typeof is_valid == "boolean") return is_valid;
    return false;
  }

  confirm_all_attributes_are_xpath(xpath,attributes)
  {
    //DEMAND
    //console.log("typeof attributes:"+(typeof attributes));
    if(typeof attributes != "object")                        return log.handle(false,'warn',new Error().lineNumber);
    if(!((attributes == null)||(Array.isArray(attributes)))) return log.handle(false,'warn',new Error().lineNumber);
    
    //SOLVE
    var attributes_are_all_xpath    = null;
    if(attributes == null)
    {
      attributes_are_all_xpath = true;
    }
    else
    {
      for(var attribute_index in attributes)
      {
        console.log(attributes);
        if(this.is_xpath_valid(xpath+'/'+attributes[attribute_index][0]))
          attributes_are_all_xpath = true;
        else
        {
          attributes_are_all_xpath = false;
          break;
        }
      }
    }

    //EVELUATE
    if(typeof attributes_are_all_xpath != "boolean") return log.handle(false,'warning',new Error().lineNumber);
    if(attributes_are_all_xpath == false)            return log.handle(false,'warning',new Error().lineNumber);
    if(attributes_are_all_xpath == true)             return log.handle(true ,'debug',new Error().lineNumber);
    return log.handle(false,'critical',new Error().lineNumber);
  }

  init_release_rule(number,xpath,number_user_action,pass_attributes,xpath_suffix_event,pass_variables)
  {
    //DEMAND
    //console.log('number->type:'+(typeof number)+':'+number);
    if(typeof number             != "number")                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof xpath              != "string")                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof number_user_action != "number")                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof pass_attributes    != "object")                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof xpath_suffix_event != "string")                         return log.handle(false,'warning',new Error().lineNumber);
    if(!this.is_xpath_valid(xpath))                                   return log.handle(false,'warning',new Error().lineNumber);
    //if(typeof this.rule[number] != "undefined")                     return log.handle(false,'warning',new Error().lineNumber);
    if(!this.confirm_all_attributes_are_xpath(xpath,pass_attributes)) return log.handle(false,'warning',new Error().lineNumber);
    if(xpath_suffix_event.length < 1)                                 return log.handle(false,'warning',new Error().lineNumber);
    
    //SOLVE
    var has_init = null;
    //try
    //{
      if(typeof this.rules[number] == "undefined")
      {
        this.rules[number] = [];
      }
      this.rules[number][number_user_action] = pass_attributes;

      var event_xpath = xpath;
      if(xpath_suffix_event.substring(0,1)=='@')
      {
        var event_name = xpath_suffix_event.substring(1,xpath_suffix_event.length);
      }
      else
      {
        var xpath_suffix = xpath_suffix_event.substring(0, xpath_suffix_event.indexOf("/@")+1);
        var event_name   = xpath_suffix_event.substring(xpath_suffix_event.indexOf("/@")+2,xpath_suffix_event.length);
        event_xpath  = xpath + '/' + xpath_suffix;
      }

      
      //console.log('event_xpath:'+event_xpath+' event_name:'+event_name);
      var iterator = document.evaluate(event_xpath, document, null, XPathResult.ANY_TYPE, null);

      var element = iterator.iterateNext();
      var elements = [];
      while (element) 
      {   
        elements.push(element);
        element = iterator.iterateNext();
      }
      for(var element_index in elements)
      {
        //console.log('[element] '+elements[element_index]);
        element = elements[element_index];
        this.new_event(event_name,element,element_index,number,number_user_action);
        //element.addEventListener(event_name,function() {alert(element_index);that.run(element,event,number,number_user_action);});
      }
      has_init = true;
      
    //}
    //catch (e) 
    //{
    //  has_init = false;
      //console.log('[error] not exists: event_xpath = '+event_xpath);
      //console.error('Provided XPath expression is not valid', e);
    //}
    
    //VALID
    if(typeof has_init != "boolean")                 return log.handle(false,'warning',new Error().lineNumber);
    if(has_init == false)                            return log.handle(false,'warning',new Error().lineNumber);
    if(has_init == true)                             return log.handle(true, 'debug'  ,new Error().lineNumber);
    return log.handle(false,'critical',new Error().lineNumber);
  }

  new_event(event_name,element,element_index,number,number_user_action)
  {
    var that = this;
    element.addEventListener(event_name,function(event) {that.run(element,event,number,number_user_action);});
  }

  run(element,event,rule_number,rule_number_user_action)
  {
    //DEMAND
    //console.log('[run] element:'+element);
    if(typeof element     != "object")                                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof rule_number != "number")                                         return log.handle(false,'warning',new Error().lineNumber);
    if(typeof rule_number_user_action != "number")                             return log.handle(false,'warning',new Error().lineNumber);
    if(typeof this.diffrequesthandler.register != "function")                  return log.handle(false,'warning',new Error().lineNumber);
    if(typeof this.rules[rule_number][rule_number_user_action] == "undefined") return log.handle(false,'warning',new Error().lineNumber);

    //SOLVE
    var has_run = null;
    //try
    //{
      var attributes = this.rules[rule_number][rule_number_user_action];
      //console.log('attributes: '+attributes);
      var values = [];
      for(var attribute_index in attributes)
      {
        var attribute_subpath = attributes[attribute_index][0];
        var variable_name = attributes[attribute_index][1];
        console.log('attribute_subpath: '+ attribute_subpath);
        var xpath;
        var xpath_suffix;
        var attribute_name;
        if(attribute_subpath.substring(0,1)=='@') //only attribute, e.g. @test
        {
          console.log('[only attribute] '+attribute_subpath);
          xpath_suffix = "";
          attribute_name = attribute_subpath.substring(1,attribute_subpath.length);
        }//if
        else //more then attribute, e.g. div/@test
        {
          xpath_suffix = attribute_subpath.substring(0, attribute_subpath.indexOf("/@")+1);
          attribute_name = attribute_subpath.substring(attribute_subpath.indexOf("/@")+2,attribute_subpath.length);
        }//else
        

        console.log('xpath_suffix.substring(0,1)'+xpath_suffix.substring(0,1));
        if(xpath_suffix.substring(0,1)!='/')
        {
          console.log('[true] element= '+element); 
          var xpath_prefix = "";
          var parent = element;
          while(parent.parentElement)
          {
            console.log('parent: '+parent.nodeName);
            var index = Array.prototype.indexOf.call(parent.parentElement.children, parent);
            xpath_prefix = '*[' + (index+1) + ']'+xpath_prefix;
            parent = parent.parentElement;
            if(typeof parent != "undefined") 
              xpath_prefix = '/'+xpath_prefix;
          }//while
          xpath = '/*[1]' + xpath_prefix + xpath_suffix;
        }//if
        else
        {
          console.log('[false] xpath_suffix='+xpath_suffix); 
          xpath = xpath_suffix;
        }//else

        console.log('xpath = '+xpath);

        var iterator = document.evaluate(xpath, document, null, XPathResult.ANY_TYPE, null);

        var element = iterator.iterateNext();
        while (element) 
        {   
          console.log('[element for attribute] '+element);
          if(typeof values[variable_name] == "undefined")
          {
            console.log('[attribute] '+attribute_name+':'+element.getAttribute(attribute_name));
            if(element.getAttribute(attribute_name) != null)
            {
              values[variable_name] = element.getAttribute(attribute_name);
            }
            else
            {
              values[variable_name] = "";
            }
          }
          element = iterator.iterateNext();
        }

      }//for
      this.diffrequesthandler.register(values,rule_number,rule_number_user_action);
      has_run = true;
    //}
    //catch (e)
    //{
    //  has_run = false;
    //}
    
    //VALID
    if(typeof has_run != "boolean")                            return log.handle(false,'warning',new Error().lineNumber);
    if(has_run == false)                                       return log.handle(false,'warning',new Error().lineNumber);
    if(has_run == true)                                        return log.handle(true ,'info',new Error().lineNumber);
    return log.handle(false,'warning',new Error().lineNumber);
  }
}

if(diffrequesthandler)
{
  if(typeof diffrequesthandler == "object")
  {
    if(diffrequesthandler instanceof dontcode_diffrequesthandler)
    {
      eventhandler = new dontcode_eventhandler(diffrequesthandler);
      for(number in rules)
      {
        console.log(number+':'+rules[number][0]+","+rules[number][1]+","+rules[number][2]+","+rules[number][3]+","+rules[number][4]);
        eventhandler.init_release_rule(rules[number][0],rules[number][1],rules[number][2],rules[number][3],rules[number][4]);
      }
    }
  }
}
