<?php

$string['pluginname'] = 'Request manager';
$string['plugin_home'] = 'Home page request manager';

$string['editingteacher'] = 'Teacher';
$string['teacher'] = 'Assistant';
$string['access_denied'] = 'Access denied';
$string['start_page_title'] = 'Prepare your request';
$string['heading'] = 'Request manager';
$string['new_courses'] = 'New courses';
$string['clone_courses'] = 'Clone courses';
$string['delete_courses'] = 'Delete courses';
$string['manage_courses'] = 'Manage requests';
$string['resume_courses'] = 'Resume';
$string['no_courses_to_delete']='No courses to delete';

$string['clone_page_title'] = 'Clone existing courses:';
$string['clone_next'] = 'Confirm and continue';
$string['no_courses_to_clone']='No courses to clone';

$string['resume_page_title'] = 'Resume';
$string['resume_tablehead_title'] = 'Title';
$string['resume_tablehead_cat'] = 'Category';
$string['resume_tablehead_teacher'] = 'Assistant';
$string['resume_tablehead_editingteacher'] = 'Teacher';
$string['resume_tablehead_note'] = 'Note';
$string['resume_comments'] = 'Comments';
$string['resume_comment_placeholder'] = 'Add comment to your submission..';
$string['resume_next'] = 'Submit request';
$string['resume_tablehead_actions'] = 'Action';
$string['resume_page_success'] = 'Request inserted successfully.';
$string['resume_page_error'] = 'There was an error.';



$string['manage_page_title'] = 'Manage requests';
$string['manage_tablehead_shortname'] = 'Shortname';
$string['manage_tablehead_actions'] = 'Actions';
$string['manage_tablehead_type'] = 'Type';
$string['manage_noentry'] = 'There are no pending requests';
$string['manage_btn_confirm'] = 'Confirm and delete';
$string['manage_request_user'] = 'Submitted by';
$string['manage_save_course'] = 'Create course';
$string['manage_cancel_course'] = 'Cancel course';
$string['manage_search_requests'] = 'Search requests';
$string['manage_sel_user'] = 'Filter by User';
$string['manage_filter_nouserselected'] = 'All users';
$string['manage_filter_nostateselected'] = 'All requests';
$string['manage_reject_req'] = 'Reject Request';
$string['manage_save_req'] = 'Save Request';
$string['manage_cancel_req'] = 'Cancel Request';
$string['manage_enroll_user'] = 'Enroll Teacher/Assistant';
$string['manage_enroll_button'] = 'Enroll';
$string['manage_unenroll_button'] = 'Unenroll';

$string['delete_page_title'] = 'Delete existing courses';
$string['delete_next'] = 'Delete selection';
$string['delete_modal_cancel'] = 'Cancel';
$string['delete_modal_confirm'] = 'Confirm';
$string['delete_page_success'] = 'Request sent correctly.';
$string['delete_page_error'] = 'Request insert error.';

$string['delete_modal_body'] = 'Delete these existing courses. This peration is definitive and can\'t be undone';

$string['filter_nocatselected'] = 'No categories selected';
$string['filter_bycat'] = 'Filter by category';
$string['filter_bycat_allcat'] = 'Select All Categories';


/**
 * Settings
 * **/
// web services
$string['usi_host_name'] = 'USI web service address';
$string['usi_host_description'] = 'The address of the USI web service';
$string['usi_private_key_name'] = 'USI private key';
$string['usi_private_key_description'] = 'The private key to access the USI web service';
$string['supsi_host_name'] = 'SUPSI  web service address';
$string['supsi_host_description'] = 'The address of the SUPSI web service';
$string['supsi_private_key_name'] = 'SUPSI private key';
$string['supsi_private_key_description'] = 'The private key to access the SUPSI web service';

$string['subject_mail_name'] = 'Mail subject';
$string['subject_mail_description'] = 'The subject of the mail';
$string['message_mail_name'] = 'Mail message';
$string['message_mail_description'] = 'The message to send by mail';
$string['new_request_subject'] = 'Moodle: New request in Request manager moodle';
$string['new_request_message'] = 'There are new requests in Request manager';
$string['notify_manager_by_mail_name'] = 'Notify to manager by mail';
$string['notify_manager_by_mail_description'] = 'Select if you want to notify by mail request to the manager.';
$string['notify_user_by_mail_name'] = 'Notify to the user by mail';
$string['notify_user_by_mail_description'] = 'Select if you want to notify by mail the updates to the user.';
// mail subject of the notification of a clone executed
$string['subject_clone_mail_name'] = 'Subject clone executed';
$string['subject_clone_mail_description'] = 'Subject of the mail sent to confirm the clone executed';
$string['subject_clone_mail_default'] = 'The request to clone a course is done';

// mail message of the notification of a clone executed
$string['message_clone_mail_name'] = 'Message clone executed';
$string['message_clone_mail_description'] = 'Message to send for a clone executed (##TITLE## will be replaced by the title of the just cloned course)';
$string['message_clone_mail_default'] = 'The request to clone the course: ##TITLE##';

// mail subject of the notification of a new course inserted
$string['subject_new_course_mail_name'] = 'Subject creation executed';
$string['subject_new_course_mail_description'] = 'Subject of the mail sent to confirm the creation of a course)';
$string['subject_new_course_mail_default'] = 'The course requested was created';

// mail message of the notification of a new course inserted
$string['message_new_course_mail_name'] = 'Message cretion executed';
$string['message_new_course_mail_description'] = 'Message to send for a creation executed (##TITLE## will be replaced by the title of the just created course)';
$string['message_new_course_mail_default'] = 'The course: "##TITLE##" was created.';

// mail subject of the notification of a delete executed
$string['subject_delete_mail_name'] = 'Subject deletion executed';
$string['subject_delete_mail_description'] = 'Subject of the mail sent to confirm the deletion of a course)';
$string['subject_delete_mail_default'] = 'The course requested was deleted';

// mail message of the notification of a delete executed
$string['message_delete_mail_name'] = 'Message deletion executed';
$string['message_delete_mail_description'] = 'Message to send for a deletion executed (##TITLE## will be replaced by the title of the just deleted course)';
$string['message_delete_mail_default'] = 'The course: "##TITLE##" was deleted.';

// mail subject of the notification of a rejected request
$string['subject_reject_mail_name'] = 'Subject request rejected';
$string['subject_reject_mail_description'] = 'Subject of the mail sent to notify the reject of a request)';
$string['subject_reject_mail_default'] = 'The request was rejected';

// mail message of the notification of a rejected request
$string['message_reject_mail_name'] = 'Message request rejected';
$string['message_reject_mail_description'] = 'Message to sent to notify a request rejected (##TITLE## will be replaced by the title of the just rejected course request, ##COMMENT## will be replaced by the custom comment of the manager)';
$string['message_reject_mail_default'] = 'The request for the course: "##TITLE##" was rejected. <br> ##COMMENT##';


// Nuovo

$string['search_string'] = 'Search string';
$string['reset_filter'] = 'Reset filter';
$string['Select courses'] = 'Select courses from USI/SUPSI database';
$string['other_courses'] = 'Other courses';
$string['course_creation'] = 'Create courses';
$string['new_course_creation'] = 'Create new courses';
$string['add'] = 'Add';
$string['role'] = 'Role';
$string['next'] = 'Next';
$string['title'] = 'Title';
$string['code'] = 'Code';
$string['create_new_course_from_null'] = 'Create new course that does not exist in the database USI/SUPSI';
$string['add_course'] = 'Add a new course';
$string['add_another_course'] = 'Add another new course';
$string['selected_courses'] = 'Selected courses to create';
$string['delete'] = 'Delete';