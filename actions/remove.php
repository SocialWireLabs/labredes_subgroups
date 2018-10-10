<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$group_guid = (int)get_input('group_guid', false);
$user_guid = (int)get_input('user_guid', false);

$operator = elgg_get_logged_in_user_entity();

if ($group_guid && $user_guid && $operator instanceof ElggUser) {
    $group = get_entity($group_guid);
    $user = get_entity($user_guid);

    if ($user instanceof ElggUser && elgg_instanceof($group, 'group', 'lbr_subgroup')) {
        $container_group = $group->getContainerEntity();    
        assert($container_group instanceof ElggGroup);
        /* Sanity checks:
         * a) user already a member
         * b) the group has open membership
         */
        elgg_load_library('labredes:subgroups');
        if ($group->isMember($user) && lbr_subgroups_is_group_admin($group, $operator)) {
            $group->leave($user);
            system_message(elgg_echo('lbr_subgroups:removed', array($user->name, $group->name)));            

            forward(REFERER);
        }
    }
}

register_error(elgg_echo('lbr_subgroups:cantremove', array($user->name, $group->name)));

if ($group instanceof ElggGroup) {
    forward($group->getURL());
} else {
    forward();
}

?>
