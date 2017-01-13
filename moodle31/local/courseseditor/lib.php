<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 12.01.17
 * Time: 19:07
 */


function local_courseseditor_extend_navigation(global_navigation $navigation) {
    global $USER;
    var_dump($USER);
    $node = $navigation->add(get_string('pluginname','local_courseseditor'), new moodle_url('/local/courseseditor/start.php'), navigation_node::TYPE_CONTAINER);;
    $node->add(get_string('new_courses','local_courseseditor'), new moodle_url('/local/courseseditor/nuovo.php'));
    $node->add(get_string('clone_courses','local_courseseditor'), new moodle_url('/local/courseseditor/clona.php'));
    $node->add(get_string('delete_courses','local_courseseditor'), new moodle_url('/local/courseseditor/cancella.php'));
    $node->add(get_string('manage_courses','local_courseseditor'), new moodle_url('/local/courseseditor/manage.php'));

}

function local_courseseditor_extend_settings_navigation($settingsnav, $context) {

}