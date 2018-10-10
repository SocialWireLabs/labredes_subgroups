<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

/* Restrict access to subgroups to:
 * a) admin of the parent group
 * b) admins of the parent group
 * c) members of the group
 */
function lbr_subgroups_gatekeeper($forward = true) {
    $allowed = true;

    if ($group = elgg_get_page_owner_entity()) {
        if (elgg_instanceof($group, 'group', 'lbr_subgroup')) {
            $allowed = false;

            if (elgg_is_admin_logged_in()) {
                $allowed = true;
            } else if (elgg_is_logged_in()) {
                $user = elgg_get_logged_in_user_entity();
                if ($group->isMember($user)) {
                    $allowed = true;
                } else if (lbr_subgroups_is_group_admin($group, $user)) {
                    return true;
                }
            }
        }
    }

    if ($forward && $allowed == false) {
        register_error(elgg_echo('membershiprequired'));
        forward(REFERER);
        exit;
    }

    return $allowed;
}

function lbr_subgroups_is_group_admin(ElggGroup $group, ElggUser $user) {
    if ($group->getSubtype() == 'lbr_subgroup') {
        $group = get_entity($group->container_guid);
        assert($group instanceof ElggGroup);
    }

    if ($user->getGUID() == $group->owner_guid || elgg_is_admin_logged_in()) {
        return true;
    } else if (elgg_is_active_plugin('group_tools') && check_entity_relationship($user->getGUID(), 'group_admin', $group->getGUID())) {
        return true;
    }

    return false;
}

function lbr_subgroups_is_allowed_to_join(LabredesSubgroup $subgroup, ElggUser $user) {
    $parent_group = get_entity($subgroup->container_guid);
    assert($parent_group instanceof ElggGroup);

    /* Sanity checks:
     * a) user is not already a member
     * b) quota is not to be exceeded
     * c) user is not a member of any other subgroup
     * d) user is a member of the parent group
     * e) the group has open membership
     */
    if ($subgroup->isPublicMembership()) {
        /* If the user is not a member of any group and is a member of parent and there are vacancies, it can join */
        if ($subgroup->isMember($user) == false &&
                $subgroup->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true)) < $subgroup->user_quota &&
                $parent_group->isMember($user) && lbr_subgroups_count_membership($parent_group, $user) == 0) {
            return true;
        }
    } else {
        $parent_group = get_entity($subgroup->container_guid);
        /* Also admit the user if he/she has been previously invited */
        if ($subgroup->isMember($user) == false &&
                $subgroup->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true)) < $subgroup->user_quota &&
                $parent_group->isMember($user) && lbr_subgroups_count_membership($parent_group, $user) == 0)
        /* Admit the user if he/she has been invited */
            if (check_entity_relationship($subgroup->getGUID(), 'invited', $user->getGUID())) {
                return true;
            }
        /* Admit the user if the performer is an operator */
        if ($subgroup->canEdit()) {
            return true;
        }
    }

    return false;
}

function lbr_subgroups_is_allowed_to_leave(ElggGroup $subgroup, ElggUser $user) {
    assert($subgroup->getSubtype() == 'lbr_subgroup');

    /* Sanity checks:
     * a) user is a member and either
     * i) the group has open membership
     * ii) or alternatively, the user is a group admin
     */
    if ($subgroup->isMember($user) && (
            $subgroup->isPublicMembership() || lbr_subgroups_is_group_admin($subgroup, $user))) {
        return true;
    }

    return false;
}

function lbr_subgroups_count_membership(ElggGroup $container_group, ElggUser $user) {
    return elgg_get_entities_from_relationship(array(
        'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
        'count' => true,
        'container_guids' => $container_group->getGUID(),
        'relationship' => 'member',
        'inverse_relationship' => false,
        'relationship_guid' => $user->getGUID(),
            ));
}
