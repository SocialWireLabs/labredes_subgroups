<?php
/*
 * © Copyright by Laboratorio de Redes 2011—2012
 */

function create_subgroup(ElggGroup $group, $membership, $user_quota) {
    $n_subgroups = elgg_get_entities(array(
        'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
        'count' => true,
        'container_guid' => $group->getGUID(),
    ));
    
    $new_group = new LabredesSubgroup();    
    $new_group->container_guid = $group->getGUID();    
    $new_group->name = $group->name . ' / ' . ($n_subgroups + 1);
    $new_group->membership = $membership;
    $new_group->access_id = $group->group_acl;
    $new_group->user_quota = $user_quota;

    $new_group->save();
}

$group_guid = get_input('group_guid', false);
$quantity = (int)get_input('quantity', 0);
$membership = (int)get_input('membership', ACCESS_PRIVATE);
$user_quota = (int)get_input('user_quota', 2);

/* Sanity check */
if (is_numeric($group_guid) && is_numeric($quantity) && $quantity > 0) {
    $group = get_entity($group_guid);
    if ($group instanceof ElggGroup) {
        $user = elgg_get_logged_in_user_entity();
        if ($user->getGUID() == $group->owner_guid || check_entity_relationship($user->getGUID(), 'group_admin', $group_guid)) {
            /* Everything is ok. We can create the subgroups */
            for ($i = 0; $i < $quantity; $i++) {
                create_subgroup($group, $membership, $user_quota);
            }

            system_message(elgg_echo('lbr_subgroups:create_ok'));
	    // This is new in 1.10
	    $wwwroot = elgg_get_config('wwwroot');
            forward($wwwroot . 'lbr_subgroups/index/' . $group_guid);
        }
    }
}

register_error(elgg_echo('lbr_subgroups:create_failure'));
