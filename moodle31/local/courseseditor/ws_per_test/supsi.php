<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:04
 */

$ws = new WsSupsi();

if($_GET['netid'] && $_GET['netid']!='') echo json_encode($ws->getCorsiPerId($_GET['netid']));

if($_GET['string'] && $_GET['string']!='') echo json_encode($ws->getCorsiPerStringa($_GET['string']));

exit;