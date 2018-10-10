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

        elgg_load_library('labredes:subgroups');
        if (lbr_subgroups_is_group_admin($group, $operator) && $container_group->isMember($user) &&
        elgg_get_entities_from_relationship(array(
		        'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
		        'count' => true,
		        'relationship' => 'member',
		        'relationship_guid' => $user->getGUID(),
		        'container_guid' => $container_group->getGUID(),
		    )) == 0 /* Not a member of any other subgroup */
        ) {               
            $group->join($user);
            system_message(elgg_echo('lbr_groups:added', array($user, $group)));
            //add_to_river('river/relationship/member/create','join', $user->guid, $group->guid);
            
            forward(REFERER);
        }
    }
}

register_error(sprintf(elgg_echo('lbr_groups:notadded'), $user, $group));
if ($group instanceof ElggGroup) {
    forward($group->getURL());
} else {
    forward();
}

?>
