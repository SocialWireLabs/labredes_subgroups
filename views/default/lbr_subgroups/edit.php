<?php
/*
 * © Copyright by Laboratorio de Redes 2012
 */

$group = $vars['entity'];

if (elgg_instanceof($group, 'group', 'lbr_subgroup'))
    forward(elgg_get_site_url () . 'lbr_subgroups/edit/' . $group->getGUID());