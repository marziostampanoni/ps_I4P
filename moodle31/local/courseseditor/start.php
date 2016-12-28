<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('mainchoiceform.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->set_url($CFG->wwwroot.'/addcourse.php');

echo $OUTPUT->header();
echo('<h2>Effettua la tua richiesta</h2><br>');
$form = new mainchoiceform(); //puoi passare l'action del form come parametro in costruzione.

$form->display();
?>


<?php
echo $OUTPUT->footer();
?>
