<?php
/*
 * © Copyright by Laboratorio de Redes 2011—2012
 */


$group = $vars['entity'];
$parent_group = $group->getContainerEntity();

$icon = elgg_view_entity_icon($parent_group, 'tiny');

$metadata = elgg_view_menu('entity', array(
	'entity' => $group,
	'handler' => 'lbr_subgroups',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
    $metadata = '';
}

if ($vars['full_view']) {
    echo elgg_view('lbr_subgroups/full', $vars);
} else {
    // brief view

    $params = array(
		'entity' => $group,
		'metadata' => $metadata,
		'subtitle' => sprintf(elgg_echo('lbr_subgroups:available_space'), $group->user_quota - $group->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true))),
    );
    $params = $params + $vars;
    $list_body = elgg_view('group/elements/summary', $params);

    echo elgg_view_image_block($icon, $list_body, $vars);
}

?>
