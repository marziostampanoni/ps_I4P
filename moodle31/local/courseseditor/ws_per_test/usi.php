<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:05
 */


require_once('WsUsi.php');
$ws = new WsUsi();

if($_GET['netid'] && $_GET['netid']!='') $json= json_encode($ws->getCorsiPerId($_GET['netid']));

elseif($_GET['string'] && $_GET['string']!='') $json= json_encode($ws->getCorsiPerStringa($_GET['string']));
else {
    $json=json_encode($ws->getCorsi());
}
if(!json_last_error()) echo $json;
else echo "JSON ERROR! ".json_last_error();
exit;