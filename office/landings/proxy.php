<?php
   require_once '../../app/init.php'; 
   if (Auth::guest()) exit;
   header('Content-Type: application/json');
   ini_set("allow_url_fopen", 1);
   $url = "https://wp.boostapp.co.il/boostapp/index.php?".http_build_query($_GET)."&cookie=".$_COOKIE['247SOFT_session'];
   $arrContextOptions=array(
//      "ssl"=>array(
//          "verify_peer"=>false,
//          "verify_peer_name"=>false
//      ),
   );

   
//   $contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
   $contents = file_get_contents($url);
   echo $contents = json_decode(json_encode($contents));
