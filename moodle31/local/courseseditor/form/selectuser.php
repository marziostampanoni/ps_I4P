<?php


class FormSelectUser extends moodleform
{

    protected function definition()
    {
        $group = array();
        $form = $this->_form;
        $mails = array();
        $mails[0] = get_string('manage_filter_nouserselected','local_courseseditor');
        foreach ($this->_customdata['users'] as $id => $user) {
            $user = get_complete_user_data('id', $id);
            $mails[$id] = $user->email;
        }
        $group[] = &$form->createElement('select', 'user', '', $mails);
        $group[] = &$form->createElement('submit', 'next', get_string('manage_filter_byuser', 'local_courseseditor'));
        $form->addGroup($group, 'userFilter', '', array(' '), false);
        $form->closeHeaderBefore('userFilter');

    }
}
