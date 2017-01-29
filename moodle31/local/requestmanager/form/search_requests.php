<?php


class FormSearchRequests extends moodleform
{

    protected function definition()
    {
        $group = array();
        $form = $this->_form;
        $mails = array();
        $mails[0] = get_string('manage_filter_nouserselected','local_requestmanager');
        foreach ($this->_customdata['users'] as $id => $user) {
            $user = get_complete_user_data('id', $id);
            $mails[$id] = $user->email;
        }
        $group[] = &$form->createElement('select', 'user', '', $mails);

        $stat = array();
        $stat[STATO_RICHIESTA_FATTO]=\local_requestmanager\CEUtil::
        $mails[0] = get_string('manage_filter_nouserselected','local_requestmanager');
        foreach ($this->_customdata['users'] as $id => $user) {
            $user = get_complete_user_data('id', $id);
            $mails[$id] = $user->email;
        }
        $group[] = &$form->createElement('select', 'user', '', $mails);
        $group[] = &$form->createElement('submit', 'next', get_string('manage_rearch_requests', 'local_requestmanager'));
        $form->addGroup($group, 'userFilter', '', array(' '), false);
        $form->closeHeaderBefore('userFilter');

    }
}
