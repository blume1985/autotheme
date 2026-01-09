<?php

  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.
  
  include_once($_SERVER['DOCUMENT_ROOT']."/lib_autotheme/autotheme_mvc_controller.php");

  //init
  $name                = "template";
  $model_parts         = array();
  $view_xhtml_template = null;
  $controller_rules    = array();

  //model parts
  class model_template
  {
  }
  $model = new model_template();

  //view
  //view template
  $view_xhtml_template = <<<EOF
  <html>
    <head>
    </head>
    <body>
      <header>Manager for articles</header>
      <main>
        <div id="add_article_group">
          <input type="text" value="new article group"/><input type="button" value="add articlegroup/>
        </div>
        <div id="articlegroups">
          <select name="articlegroup" id="articlegroup">
            <option value=""> </option>
          </select>
        </div>
        <h1 id="articlegroup_selected"></h1>
        <a
      </main>
    </body>
  </html>
  EOF;

  //view css
  $view_css = <<<EOF
  EOF;

  //controller rules
  /*
  $controller_rules[1]['info']                                                  = "Just for you";
  $controller_rules[1]['xpath']                                                 = "/html/body/element";
  $controller_rules[1]['request'][1]['model']                                   = 'get_method';
  $controller_rules[1]['request'][1]['modeldata2dom']['@attribute']             = "variable"; 
  $controller_rules[1]['user-action'][1]['event']                               = '@event';
  $controller_rules[1]['user-action'][1]['domdata2model']['variable']           = '@attribute';
  $controller_rules[1]['user-action'][1]['model']                               = 'set_method';
  */

  $template = new autotheme_mvc_controller($name,$model,$view_xhtml_template,$view_css,$controller_rules);
  $template->run();

?>