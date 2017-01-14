<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('resume_page_title', 'local_courseseditor') . '</h2><br><div>');
$form = new FormClona(); //puoi passare l'action del form come parametro in costruzione.ai
if ($fromform = $form->get_data()) {
    foreach ($fromform as $name => $post) {
        $prefix = substr($name, 0, 4);
        $id = substr($name, strpos($name, "-") + 1);
        if ($prefix == 'name') {
            var_dump($id . ' ' . 'checked');
            var_dump(json_decode($post));
        }

    }


}
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

