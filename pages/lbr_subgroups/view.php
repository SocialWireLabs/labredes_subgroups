<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$group_guid = get_input('group_guid', false);
if (!is_numeric($group_guid)) {
    forward();
}
elgg_set_page_owner_guid($group_guid);

$group = get_entity($group_guid);

elgg_load_library('labredes:subgroups');
lbr_subgroups_gatekeeper();

if (lbr_subgroups_is_group_admin($group, elgg_get_logged_in_user_entity())) {
    elgg_register_title_button('lbr_subgroups', 'edit');
}

$title = sprintf(elgg_echo('lbr_subgroups:subgroup_info_title'), $group->name);

$content = elgg_view_entity($group, array('full_view' => true));

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
));

echo elgg_view_page($title, $body);
