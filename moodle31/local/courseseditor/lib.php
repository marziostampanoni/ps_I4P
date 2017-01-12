<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 12.01.17
 * Time: 19:07
 */


function local_courseseditor_extend_navigation(global_navigation $navigation) {
    global $PAGE;
    $node = $PAGE->navigation->add(get_string('pluginname','local_courseseditor'), new moodle_url('/local/courseseditor/start.php'), navigation_node::TYPE_CONTAINER);;

    $node1 = $node->add(get_string('new_courses','local_courseseditor'), new moodle_url('/local/courseseditor/new_courses.php'));
    //$node1->make_active();
    $node2 = $node->add(get_string('clone_courses','local_courseseditor'), new moodle_url('/local/courseseditor/clone_courses.php'));
    //$node2->make_active();
    $node3 = $node->add(get_string('delete_courses','local_courseseditor'), new moodle_url('/local/courseseditor/delete_courses.php'));
    //$node3->make_active();
}

function local_courseseditor_extend_settings_navigation($settingsnav, $context) {

}