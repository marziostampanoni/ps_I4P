<?php
require_once('../../config.php');
require_once('form/nuovo.php');
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
echo('<h2>Crea un nuovo corso</h2><br><div>');


if($_GET['user_type']=='usi') $ws = new UsiWebServices();
else $ws = new SupsiWebServices();

$result=$ws->getCorsiPerNetID($USER->username);

$form = new FormNuovo(NULL,array('corsi'=>$result));

if ($fromform = $form->get_data()) {
    var_dump($fromform);
}

$form->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>
