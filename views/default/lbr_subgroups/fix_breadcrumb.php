<?php
/*
 * © Copyright by Laboratorio de Redes 2012
 */

$group = $vars['entity'];
$parent_group = $group->getContainerEntity();

$tail_breadcrumb = elgg_pop_breadcrumb();
elgg_push_breadcrumb($parent_group->name, $parent_group->getURL());
elgg_push_breadcrumb($tail_breadcrumb['title'], $tail_breadcrumb['link']);
