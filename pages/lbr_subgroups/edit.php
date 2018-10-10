<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$group_guid = get_input('group_guid', false);
if (!is_numeric($group_guid)) {
    forward(REFERER);
}
$group = get_entity($group_guid);
if (!(elgg_instanceof($group, 'group', 'lbr_subgroup'))) {
    forward(REFERER);
}

elgg_load_library('labredes:subgroups');

elgg_set_page_owner_guid($group_guid);
if (!lbr_subgroups_is_group_admin($group, elgg_get_logged_in_user_entity())) {
    forward(REFERER);
}

$content = elgg_view_form('lbr_subgroups/edit', null, array('group' => $group));

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => elgg_echo('lbr_subgroups:edit'),
	'filter' => '',
));

echo elgg_view_page(elgg_echo('lbr_subgroups:edit'), $body);