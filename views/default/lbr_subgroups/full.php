<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$group = $vars['entity'];

elgg_load_library('labredes:subgroups');

lbr_subgroups_gatekeeper();

?>
<div>
<?php 

echo elgg_list_entities_from_relationship(array(
    'types' => 'user',
    'relationship' => 'member',
    'relationship_guid' => $group->getGUID(),
    'inverse_relationship' => true,
    'full_view' => false,
    'limit' => 10,    
));
?>
</div>
<?php
/* If there is capacity, show a user picker to add new users to the group */
if ($group->user_quota > $group->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true))) {
    $parent_group = $group->getContainerEntity();    
    assert($parent_group instanceof ElggGroup);

    $members = $parent_group->getMembers(array('limit'=>null));
    if ($members && is_array($members)) {        
		foreach($members as $member) {		    
		    if ($group->isMember($member) != false) {
		        continue;
		    } else if (elgg_get_entities_from_relationship(array(
		        'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
		        'count' => true,
		        'relationship' => 'member',
		        'relationship_guid' => $member->getGUID(),
		        'container_guid' => $parent_group->getGUID(),
		    )) > 0) { /* If the user is a member of any other subgroup... */
		        continue;
		    }
		    $free_members[] = $member;
		}
    }
    if (is_array($free_members)) {
        echo elgg_view_title(elgg_echo('lbr_subgroups:available_space', array($group->user_quota - $group->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true)))));
        echo elgg_view_entity_list($free_members, array(
            'full_view' => false,
            'count' => count($free_members),
            'limit' => 10,
        ));
    }
}
?>
