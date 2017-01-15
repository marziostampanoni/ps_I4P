<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:05
 */


require_once('WsUsi.php');
$ws = new WsUsi();
$json=json_encode($ws->getCorsi($_GET['netid'],$_GET['string']));
if(!json_last_error()) echo $json;
else echo "JSON ERROR! ".json_last_error();
exit;