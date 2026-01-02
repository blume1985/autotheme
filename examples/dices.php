<!-- This Source Code Form is subject to the terms of the Mozilla Public
   - License, v. 2.0. If a copy of the MPL was not distributed with this
   - file, You can obtain one at https://mozilla.org/MPL/2.0/. -->

<?php

  $version = "25.44";

  include_once($_SERVER['DOCUMENT_ROOT']."/lib_dontcode/dontcode_requesthandler.php");
  $dontcode_requesthandler = new dontcode_requesthandler(true);

  ?><html><?
    ?><head><?
      ?><title><?
        ?>Roll the dices! <?=$version;
      ?></title><?
      ?><style>
        div.dice { 
          font-size:200px;float:left;
        }

          @-webkit-keyframes rotating {
            from{
                -webkit-transform: rotate(0deg);
            }
            to{
                -webkit-transform: rotate(360deg);
            }
        }
        
        .rotating {
            -webkit-animation: rotating 2s linear;
        }

        .elementToFadeInAndOut:active 
        {
          opacity: 1;
          animation: fade 1s linear;
        }

        @keyframes fade 
        {
          0%,100% { opacity: 0 }
          50% { opacity: 1 }
        }

      </style><?
      ?><script type="text/javascript" defer="">
        var rules = [];
        rules[1] = [1,"/html/body/input",null,"@click"];
      </script><?
    ?></head><?
    ?><body><?

      ?><header><?
        ?><marquee style="font-size:100px;font-family:Tahoma;">Roll the dices!</marquee><?
      ?></header><?

      ?><input type="button" class="elementToFadeInAndOut" style="height:150px;width:150px;font-size:20px;font-family:Courier;background-color:#fff;border:3px #aaa solid;" value="Roll now!"><?
      ?></input><br/><br/><?

      $r = rand(1,6);
      $z = 9856;
      for($i=0;$i<$r;$i++)
      {
        $id = rand(1,100000000);
        ?><div id="dice_<? echo $id; ?>" class="dice rotating"><?
          echo "&#".($z+$i).";"
        ?></div><?
      }

      ?><div id="clear" style="clear:left;"></div><?

    ?></body><?
  ?></html><?

  $dontcode_requesthandler->stop_request();

?>
