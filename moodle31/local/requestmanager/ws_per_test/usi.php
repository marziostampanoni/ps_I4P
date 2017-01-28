<?php

$key='usi_key_sha_256';
if($_POST['private_key']==$key) {
    require_once('WsUsi.php');
    $ws = new WsUsi();
    $json = json_encode($ws->getCorsi($_POST['netid'], $_POST['string']));
    if (!json_last_error()) echo $json;
    else echo "JSON ERROR! " . json_last_error();
}else{echo 'Access denied';}
exit;