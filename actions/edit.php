<?php

/*
 * © Copyright by Laboratorio de Redes 2011—2012
 */

$group_guid = get_input('group_guid', false);
$user_quota = get_input('user_quota', 0);
$brief_description = get_input('briefdescription', '');

$group = get_entity($group_guid);

if (elgg_instanceof($group, 'group', 'lbr_subgroup')) {
    $new_name = get_input('name', $group->name);
    $membership = get_input('membership', $group->membership);
    $user = elgg_get_logged_in_user_entity();

    elgg_load_library('labredes:subgroups');
    if (lbr_subgroups_is_group_admin($group, $user)) {
	if ($user_quota > 0 && $user_quota >= $group->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true))) {
            $group->name = $new_name;
            $group->membership = $membership;
            $group->user_quota = $user_quota;
            $group->briefdescription = $brief_description;
            $group->save();

            system_message(elgg_echo('lbr_subgroups:updated'));
            forward($group->getURL());
        }
    }
}

register_error(elgg_echo('lbr_subgroups:nonupdated'));
forward(REFERER);
