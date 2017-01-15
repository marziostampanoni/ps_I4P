<?php
require_once('../../config.php');
require_once('form/nuovo.php');
require_once('form/cercacorsiws.php');
require_once('form/selectcorsi.php');
require_once('class/SupsiWebServices.php');
require_once('class/UsiWebServices.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2>Crea un nuovo corso</h2>');


if($_GET['user_type']=='usi') $ws = new UsiWebServices();
else $ws = new SupsiWebServices();

$filter_cerca=null;
$filter_netID=null;
$form = new FormCercaCorsiWs();
$form->display();

if ($fromform = $form->get_data()) {
    if($fromform->string!='') $filter_cerca = $fromform->string;
    if($fromform->onlythis==1) $filter_netID = $USER->username;
    if($fromform->submitsupsi) $ws = new SupsiWebServices();
    if($fromform->submitusi) $ws = new UsiWebServices();
}

$result = $ws->getCorsi($filter_netID,$filter_cerca);

$form = new FormSelectCorsi(NULL,array('corsi'=>$result));

if ($fromform = $form->get_data()) {

    $selezionati=array();
    foreach ($fromform as $name=>$item){

        if(substr($name,0,4)=='name'){
            $selezionati[]=substr($name,5);

            echo " <br> ".substr($name,5);
        }
    }
    foreach ($selezionati as $num){
        $param="data-$num";
        echo " <br> ".var_dump($fromform->$param);
    }

}

echo '<h4>'. get_string('Seleziona corsi','local_courseseditor').'</h4>';

$form->display();

$form = new FormNuovo(NULL,array('corsi'=>$result));

if ($fromform = $form->get_data()) {
    var_dump($fromform);

}

echo '<h4>'. get_string('Aggiungi corso','local_courseseditor').'</h4>';
echo "<p>Crea un corso che non esiste nei database ufficiale dei corsi USI/SUPSI</p>";
$form->display();

echo $OUTPUT->footer();
?>
