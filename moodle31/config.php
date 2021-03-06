<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();
//$CFG->debug = 6143;
//$CFG->debugdisplay = 1;
$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle31';
$CFG->dbuser    = 'moodle';
$CFG->dbpass    = 'moodle';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '8889',
  'dbsocket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
);

$CFG->wwwroot   = 'http://localhost:8888/moodle31';
// $CFG->wwwroot   = 'http://192.168.0.10:8888/moodle31';
$CFG->dataroot  = '/Applications/MAMP/data/moodle31';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

@error_reporting(E_ALL & ~E_NOTICE);
@ini_set('display_errors', '1');
$CFG->debug = (E_ALL & ~E_NOTICE);
$CFG->debugdisplay = 1;

$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = '/Applications/MAMP/data/moodle31/phpunit_prova';


// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
