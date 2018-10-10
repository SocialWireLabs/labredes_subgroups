<?php

/*
 * © Copyright by Laboratorio de Redes 2012
 */

class LabredesSubgroup extends ElggGroup {

    protected function initializeAttributes() {
        parent::initializeAttributes();

        $this->attributes['subtype'] = 'lbr_subgroup';
    }

    public function __construct($guid = null) {
        if ($guid && !is_object($guid)) {
              // Loading entities via __construct(GUID) is deprecated, so we give it the entity row and the
              // attribute loader will finish the job. This is necessary due to not using a custom
              // subtype (see above).
              $guid = get_entity_as_row($guid);
        }
        parent::__construct($guid);
    }

    /* Admins are reported as members for the sake of group_gakekeeper
     * functionality.
     */

    public function isMember($user = 0) {
        if (!($user instanceof ElggUser)) {
            $user = elgg_get_logged_in_user_entity();
        }
        if (!($user instanceof ElggUser)) {
            return false;
        }

        elgg_load_library('labredes:subgroups');
        if (lbr_subgroups_is_group_admin($this, $user)) {
            return true;
        }

        return parent::isMember($user);
    }

    /**
     * Join an elgg user to this group.
     *
     * @param ElggUser $user User
     *
     * @return bool
     */
    public function join(ElggUser $user) {
        elgg_load_library('labredes:subgroups');

        if (lbr_subgroups_is_allowed_to_join($this, $user)) {
            return parent::join($user);
        } else {
            register_error(elgg_echo('groups:cantjoin'));
            forward($this->getURL());
        }
    }

    /**
     * Remove a user from the group.
     *
     * @param ElggUser $user User
     *
     * @return bool
     */
    public function leave(ElggUser $user) {
        elgg_load_library('labredes:subgroups');

        if (lbr_subgroups_is_allowed_to_leave($this, $user)) {
            return parent::leave($user);
        } else {
            register_error(elgg_echo('groups:cantleave'));
            forward($this->getURL());
        }
    }

}
