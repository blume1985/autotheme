<?php

  // This Source Code Form is subject to the terms of the Mozilla Public
  // License, v. 2.0. If a copy of the MPL was not distributed with this
  // file, You can obtain one at https://mozilla.org/MPL/2.0/.
  
  //UNIT-TEST
  if($_SERVER['SCRIPT_FILENAME']==__FILE__)
  {
    $testcases = ['document1'=>1];

    $path = pathinfo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $dir = $path['dirname'];

    $testcase = null;
    echo '<html>';
    echo '<head>';
    echo '<title>diffrequesthandler</title>';
    echo '<script language="javascript" type="text/javascript" src="https://'.$dir.'/autotheme_diffrequesthandler.js" defer></script>';
    ?><style><?
    ?>h1   {color: blue;font-weight:bold;font-size:20px;}<?
    ?>p    {color: red;}<?
    ?></style><?
    echo '</head>';
    if(isset($_REQUEST['testcase']))$testcase = $_REQUEST['testcase'];
    switch($testcase)
    {
      case $testcases['document1']:
        echo '<body id="document">';
        echo '<p title="Intro in ShiftXML">Diese Website testet ShiftXML</p>';
        echo '<h1>Funktionsumfang</h1>';
        echo '<p>ShiftXML beherrscht Inserts, Replacments und Removes sowohl von Elementen als auch Attributen.</p>';
        echo '<p>ShiftXML ist sowohl zum Modifizieren für generelle XML-Dokumente als auch XHTML-Dokumente geeignet.</p>';
        echo '<h1>Alternative XUpdate</h1>';
        echo '<p>XUpdate ist nicht wirklich eine Alternative, da es mehrdeutig ist und keine eindeutigen Spezifikationen vorweist.</p>';
        echo '</body>';
        echo '</html>';
      break;
      default:
        foreach($testcases as $testcase_name=>$testcase_value)
        {
          //$url = $_SERVER['SERVER_NAME'].$variable = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?"));
          $path = pathinfo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
          $url = $path['dirname'].'/'.strstr($path['basename'], '?', true);
          echo "<p>\n";
          echo '<a href="https://'.$url.'?testcase='.$testcase_value.'">';
          echo "Go to testcase:".$testcase_name."\n";
          echo "</a>\n";
          echo "</p>\n";
        }
      break;
    }
    if(isset($testcase))
    {
      ?><input type="submit" value="Ersetzung vornehmen" onclick="diffrequesthandler.register();"/><?
      
    }
    echo '</body>';
    echo '</html>';
  }

?>