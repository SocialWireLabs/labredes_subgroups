<?php
/*
 * © Copyright by Laboratorio de Redes 2011
 */

$guid = get_input('guid');
$entity = get_entity($guid);

if (($entity) && ($entity->canEdit())) {
	if ($entity->delete()) {
		system_message(elgg_echo('group:deleted'));
	} else {
		register_error(elgg_echo('entity:notdeleted'));
	}
} else {
	register_error(elgg_echo('entity:notdeleted'));
}

forward(REFERER);
