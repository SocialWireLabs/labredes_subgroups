<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$group_guid = get_input('group_guid', false);
if (!is_numeric($group_guid)) {
    forward();
}
$group = get_entity($group_guid);
if (!($group instanceof ElggGroup)) {
    forward();
}

elgg_set_page_owner_guid($group_guid);

elgg_load_library('labredes:subgroups');

lbr_subgroups_gatekeeper();

$offset = get_input('offset', 0);

$user = elgg_get_logged_in_user_entity();
if (lbr_subgroups_is_group_admin($group, $user)) {
    elgg_register_title_button();
}

$content = elgg_list_entities(array(
	'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
	'container_guid' => $group_guid,
    'limit' => 10,
    'offset' => $offset,
    'full_view' => false,
    'list_type_toggle' => false,    
));

if (!$content) {
    $content = elgg_echo("lbr_subgroups:none");
}

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => elgg_echo('lbr_subgroups:subgroups'),
	'filter' => '',
));

echo elgg_view_page(elgg_echo('lbr_subgroups:subgroups'), $body);
