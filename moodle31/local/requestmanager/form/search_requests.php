<?php


class FormSearchRequests extends moodleform
{

    protected function definition()
    {
        global $USER;
        $group = array();
        $form = $this->_form;
        if(local_requestmanager\CEUtil::isManager($USER->id) || is_siteadmin($USER->id)) {
            $users = array();
            $users[0] = get_string('manage_filter_nouserselected', 'local_requestmanager');
            foreach ($this->_customdata['users'] as $id) {
                $user = get_complete_user_data('id', $id);
                $users[$id] = "$user->firstname $user->lastname ($user->email)";
            }
            $group[] = &$form->createElement('select', 'user', '', $users);
        }
        $stat = array();
        $stat[0] = get_string('manage_filter_nostateselected','local_requestmanager');
        $stat[STATO_RICHIESTA_FATTO]=\local_requestmanager\CEUtil::statoRichiesta(STATO_RICHIESTA_FATTO);
        $stat[STATO_RICHIESTA_DA_GESTIRE]=\local_requestmanager\CEUtil::statoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
        $stat[STATO_RICHIESTA_SOSPESO]=\local_requestmanager\CEUtil::statoRichiesta(STATO_RICHIESTA_SOSPESO);
        $group[] = &$form->createElement('select', 'state', '', $stat);

        $group[] = &$form->createElement('submit', 'next', get_string('manage_search_requests', 'local_requestmanager'));
        $form->addGroup($group, 'userFilter', '', array(' '), false);
        $form->closeHeaderBefore('userFilter');

    }
}
