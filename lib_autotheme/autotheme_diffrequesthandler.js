// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at https://mozilla.org/MPL/2.0/.

class autotheme_diffrequesthandler
{
  constructor()
  {
    this.observer = new fileloader(this);
  }

  register(values,rule_number,rule_number_user_action)
  {
    var url = window.location.href;
    console.log(url);
    var tab_id = document.querySelector('meta[name="tab-id"]');
    if(tab_id)
    {
      //console.log('this.observer.request:'+url+'?tab_id='+tab_id.content);
      var url_request = url+'?tab_id='+tab_id.content;
      url_request += '&_autotheme_rule_number='+rule_number;
      url_request += '&_autotheme_rule_number_user_action='+rule_number_user_action;
      for(var value_key in values)
      {
        url_request += '&'+value_key+'='+values[value_key];
        //console.log('values: '+value_key+'='+values[value_key]);
      }
     
      this.observer.request(url_request);
      console.log('[url_request]'+url_request);
    }
    else {
      console.log('[failure] /html/head/meta/@name=tab-id does not exists!');
    }
  }

  notify(text)
  {
    //console.log('walkover:'+text);
    var parser = new DOMParser();
    var doc_shiftxml = parser.parseFromString(text,"text/xml");
    var doc_browser = document;

    this.walkover_documents(doc_shiftxml,doc_browser);
  }

  walkover_documents(node_shiftxml,node_browser)
  {
    console.log('shiftxml:' + node_shiftxml.nodeName+' browser:'+node_browser.nodeName);
    var j = 0;
    for (var i = 0; i < node_shiftxml.childNodes.length; i++) 
    {
      var child_shiftxml = node_shiftxml.childNodes[i];
      
      var child_browser = null;
      if(j < node_browser.childNodes.length)
      {
        child_browser = node_browser.childNodes[j];
        console.log('shiftxml.child:' + child_shiftxml.nodeName + ' child_browser:' + child_browser.nodeName);
      }

      if(child_shiftxml.nodeType == Node.ELEMENT_NODE)
      {
        if(child_shiftxml.nodeName.toLowerCase() == 'shiftxml:diffoperations')
        {
          console.log('i:'+i+'shiftxml.length:' + child_shiftxml.childNodes.length);
          if(child_shiftxml.childNodes.length==1)
          {
            console.log('i:'+i+'shiftxml:' + child_shiftxml.nodeName);
            this.walkover_documents(child_shiftxml,node_browser);
          }
        }
        else if(child_shiftxml.nodeName.toLowerCase() == 'shiftxml:insert')
        {
          if(child_shiftxml.childNodes.length==1)
          {
            var subchild_shiftxml = child_shiftxml.childNodes[0];
            if(child_browser != null)
            {
              node_browser.insertBefore(subchild_shiftxml,child_browser);
            }
            else
            {
              node_browser.append(subchild_shiftxml);
            }
            console.log('[insert] i:'+i+'shiftxml:' + subchild_shiftxml.nodeName);
            //j--;
          }
        }
        else if(child_shiftxml.nodeName.toLowerCase() == 'shiftxml:replace')
        {
          if((child_shiftxml.childNodes.length==1)&&(child_browser != null))
          {
            var subchild_shiftxml = child_shiftxml.childNodes[0];
            if(subchild_shiftxml.nodeName == child_browser.nodeName)
            {
              node_browser.replaceChild(subchild_shiftxml,child_browser);
              console.log('[replace] i:'+i+'shiftxml:' + subchild_shiftxml.nodeName + ' browser:' + child_browser.nodeName + ' length:' + child_shiftxml.childNodes.length);
            }
          }
        }
        else if(child_shiftxml.nodeName.toLowerCase() == 'shiftxml:remove')
        {
          if((child_shiftxml.childNodes.length==1)&&(child_browser != null))
          {
            var subchild_shiftxml = child_shiftxml.childNodes[0];
            if(  (subchild_shiftxml.nodeName.toLowerCase() == child_browser.nodeName.toLowerCase())
               &&(subchild_shiftxml.nodeName != '#text'))
            {
              node_browser.removeChild(child_browser);
              j--;
              console.log('[remove] i:'+i+'shiftxml:' + subchild_shiftxml.nodeName + ' browser:' + child_browser.nodeName + ' length:' + child_shiftxml.childNodes.length);
            }
          }
        }
        else if(child_browser.nodeName.toLowerCase()=='head')
        {

        }
        else if(child_browser != null)
        { 
          if(child_shiftxml.nodeName.toLowerCase() == child_browser.nodeName.toLowerCase())
          {
            console.log('i:'+i+'shiftxml:' + child_shiftxml.nodeName + ' browser:' + child_browser.nodeName);
            if(child_shiftxml.hasAttributes())
            {
              for(var i_attributes=0;i_attributes < child_shiftxml.attributes.length;i_attributes++)
              {
                var attribute = child_shiftxml.attributes[i_attributes].name;
                var attribute_command = attribute.substring(0, attribute.indexOf(":"));
                var attribute_name = attribute.substring(attribute.indexOf(":")+1);
                if(attribute_command == 'insert')
                {
                  if(!child_browser.hasAttribute(attribute_name))
                  {
                    var attribute_value = child_shiftxml.attributes[i_attributes].value;
                    child_browser.setAttribute(attribute_name,attribute_value);
                    console.log('[attribute insert] i:'+i+'shiftxml:' + child_shiftxml.nodeName + ' name:' + attribute_name + ' value:' + attribute_value);
                  }
                }
                else if(attribute_command == 'replace')
                {
                  if(child_browser.hasAttribute(attribute_name))
                  {
                    var attribute_value = child_shiftxml.attributes[i_attributes].value;
                    console.log('[attribute replace] i:'+i+'shiftxml:' + child_shiftxml.nodeName + ' name:' + attribute_name + ' value:' + attribute_value);
                    child_browser.setAttribute(attribute_name,attribute_value);
                  }
                }
                else if(attribute_command == 'remove')
                {
                  if(child_browser.hasAttribute(attribute_name))
                  {
                    child_browser.removeAttribute(attribute_name);
                    console.log('[attribute remove] i:'+i+'shiftxml:' + child_shiftxml.nodeName + ' name:' + attribute_name);
                  }
                }
              }
            }
            this.walkover_documents(child_shiftxml,child_browser);
          }
        }
        else
        {
          console.log('[no match] i:'+i+'shiftxml:' + child_shiftxml.nodeName + ' browser:' + child_browser.nodeName);
        }
      }
      j++;
    }
  }

  test()
  {
    alert('v18.101');
  }
}//xupdate_includer = class extends xhtml_includer,fileloader

class fileloader
{
  constructor()
  {
    var args = Array.prototype.slice.call(arguments);
    var observer = args[0];
    if(observer != null) this.observer = observer;
  }
  request(url)
  {
    console.log('fileloader.request:'+url);
    var xmlhttp;
    var that = this;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
        console.log(xmlhttp.responseText);
        that.observer.notify(xmlhttp.responseText);
      }
    }
    xmlhttp.open("GET", url, true );
    xmlhttp.send();   
  }

}//const server = class

diffrequesthandler = new autotheme_diffrequesthandler();