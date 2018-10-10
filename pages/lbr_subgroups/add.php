<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

group_gatekeeper();

$group_guid = get_input('group_guid', false);
if (!is_numeric($group_guid)) {
    forward();
}
$group = get_entity($group_guid);
if (!($group instanceof ElggGroup)) {
    forward();
}

elgg_set_page_owner_guid($group_guid);

elgg_pop_breadcrumb();
$group_fname = elgg_get_friendly_title($group->name);
elgg_push_breadcrumb(elgg_echo('lbr_subgroups:subgroups'), "lbr_subgroups/index/$group_guid/$group_fname");

$content = elgg_view_form('lbr_subgroups/create', null, array('group' => $group));

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => elgg_echo('lbr_subgroups:add'),
	'filter' => '',
));

echo elgg_view_page(elgg_echo('lbr_subgroups:add'), $body);