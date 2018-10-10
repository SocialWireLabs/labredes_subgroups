<?php
/*
 * © Copyright by Laboratorio de Redes 2011—2012
 */

function lbr_subgroups_entity_user_menu_setup($hook, $type, $return, $params) {
    if (elgg_in_context('widgets')) {
        return $return;
    }
    
    $entity = $params['entity'];
    
    if (elgg_instanceof($entity, 'user')) {
        $group = elgg_get_page_owner_entity();
        if (elgg_instanceof($group, 'group', 'lbr_subgroup')) {
            elgg_load_library('labredes:subgroups');
            $operator = elgg_get_logged_in_user_entity();
            $user = $entity;
            if (lbr_subgroups_is_group_admin($group, $operator)) {
                if ($group->isMember($user)) {
                    $mem_url = 'action/lbr_subgroups/remove?group_guid=' . $group->getGUID() . '&user_guid=' . $user->getGUID();
                    $mem_text = elgg_echo('lbr_subgroups:remove_user');
                } else {
                    $mem_url = 'action/lbr_subgroups/add?group_guid=' . $group->getGUID() . '&user_guid=' . $user->getGUID();
                    $mem_text = elgg_echo('lbr_subgroups:add_user');
                }
            } else if (lbr_subgroups_is_allowed_to_leave($group, $user)) {
                $mem_url = 'action/groups/leave?group_guid=' . $group->getGUID() . '&user_guid=' . $user->getGUID();
                $mem_text = elgg_echo('groups:leave');
            } else if (lbr_subgroups_is_allowed_to_leave($group, $user)) {
                $mem_url = 'action/groups/join?group_guid=' . $group->getGUID() . '&user_guid=' . $user->getGUID();
                $mem_text = elgg_echo('lbr_subgroups:join');    
            }
            
            if (isset($mem_text)) {
                $options = array(
                			'name' => 'belong',
                			'text' => $mem_text,
                			'href' => $mem_url,
                			'priority' => 300,
                			'is_action' => true
                );
                $return[] = ElggMenuItem::factory($options);
            }
            
            return $return;
        }
    }
}

function lbr_subgroups_entity_subgroup_menu_setup($hook, $type, $return, $params) {       
    if (elgg_in_context('widgets')) {
        return $return;
    }

    $entity = $params['entity'];       
    
    $handler = elgg_extract('handler', $params, false);
    if ($handler != 'lbr_subgroups') {
        return $return;
    }

    foreach ($return as $index => $item) {
        if (in_array($item->getName(), array('access', 'likes'))) {
            unset($return[$index]);
        }
    }

    // membership type
    $membership = $entity->membership;
    if ($membership == ACCESS_PUBLIC) {
        $mem = elgg_echo('lbr_subgroups:access:public');
    } else {
        $mem = elgg_echo('lbr_subgroups:access:private');
    }
    $options = array(
		'name' => 'membership',
		'text' => $mem,
		'href' => false,
		'priority' => 100,
    );
    $return[] = ElggMenuItem::factory($options);

    // number of members

    //$num_members = get_group_members($entity->guid, 10, 0, 0, true);
    $num_members = $entity->getMembers(array(
		'relationship_guid'=>$entity_guid,
		'limit' => 10,
		'offset'=>0, 
		'count'=>true
    ));

    $members_string = elgg_echo('groups:member');
    $options = array(
		'name' => 'members',
		'text' => $num_members . ' ' . $members_string,
		'href' => elgg_get_site_url() . 'lbr_subgroups/' . $entity->guid . '/' . elgg_get_friendly_title($entity->name),
		'priority' => 200,
    );
    $return[] = ElggMenuItem::factory($options);    
    
    elgg_load_library('labredes:subgroups');
    
    if(lbr_subgroups_is_allowed_to_join($entity, elgg_get_logged_in_user_entity())) {
        $mem_url = 'action/groups/join?group_guid=' . $entity->getGUID() . '&user_guid=' . elgg_get_logged_in_user_guid();
        $mem_text = elgg_echo('lbr_subgroups:join');      
    } else if (lbr_subgroups_is_allowed_to_leave($entity, elgg_get_logged_in_user_entity())) {
        $mem_text = elgg_echo('groups:leave');
        $mem_url = 'action/groups/leave?group_guid=' . $entity->getGUID() . '&user_guid=' . elgg_get_logged_in_user_guid();
    }
    
    if (isset($mem_text)) {
        $options = array(
    			'name' => 'belong',
    			'text' => $mem_text,
    			'href' => $mem_url,
    			'priority' => 300,
    			'is_action' => true
        );
        $return[] = ElggMenuItem::factory($options);
    }

    return $return;
}

function lbr_subgroups_ph($page) {    
    $subgroups_dir = elgg_get_plugins_path() . 'labredes_subgroups/pages/lbr_subgroups';    
    
    if (is_numeric($page[0])) {
        $group_guid = $page[0];        
    } else if (isset($page[1])) {
        $group_guid = $page[1];
    }
    
    set_input('group_guid', $group_guid);
    $group = get_entity($group_guid);
    if (!$group || !elgg_instanceof($group, 'group')) {               
        forward('groups/all');
    }
    
    elgg_push_breadcrumb(elgg_echo('groups'), "groups/all");
    if (elgg_instanceof($group, 'group', 'lbr_subgroup')) {
        $gparent = $group->getContainerEntity();
        $gparent_guid = $gparent->getGUID();
        $gparent_name = elgg_get_friendly_title($gparent->name);
        elgg_push_breadcrumb($gparent->name, "groups/profile/$gparent_guid/$gparent_name");       
    }
        
    $group_name = elgg_get_friendly_title($group->name);          

    if (isset($page[1])) {        
        switch ($page[0]) {
            case 'index':
                elgg_push_breadcrumb($group->name, "groups/profile/$group_guid/$group_name");
                elgg_push_breadcrumb(elgg_echo('lbr_subgroups:subgroups'));                
                include "$subgroups_dir/index.php";   
                return;
            case 'add':
                elgg_push_breadcrumb($group->name, "groups/profile/$group_guid/$group_name");
                elgg_push_breadcrumb(elgg_echo('lbr_subgroups:subgroups'));
                include "$subgroups_dir/add.php";                            
                return;
            case 'edit':
                elgg_push_breadcrumb(elgg_echo('lbr_subgroups:subgroups'), "lbr_subgroups/index/$gparent_guid/$gparent_name");
                elgg_push_breadcrumb($group->name);
                include "$subgroups_dir/edit.php";
                return;
        }
    }

    elgg_push_breadcrumb(elgg_echo('lbr_subgroups:subgroups'), "lbr_subgroups/index/$gparent_guid/$gparent_name");
    elgg_push_breadcrumb($group->name);
    include("$subgroups_dir/view.php");    
}

function _lbr_subgroups_write_acl_parent_plugin_hook($hook, $entity_type, $returnvalue, $params) {
    if ($entity_type == 'user') {
        $user_id = $params['user_id'];
        $site_id = $params['site_id'];
        $page_owner = elgg_get_page_owner_entity();
        
        if ($page_owner instanceof LabredesSubgroup) {
            $parent_group = $page_owner->getContainerEntity();
            
            $returnvalue[$parent_group->group_acl] = elgg_echo('groups:group') . ': ' . $parent_group->name;
        }
    }
    
    return $returnvalue;
}

function _lbr_subgroups_write_acl_plugin_hook($hook, $entity_type, $returnvalue, $params) {
    if ($entity_type == 'user') {
        $user_id = $params['user_id'];
        $site_id = $params['site_id'];

        /* Find all groups that either:
         * a) are operated by us. Then, for each of them, add write_acl for their subgroups
         * b) are owned by us. Then, for each of them , add write_acl for their subgroups
         *
         */
        if (elgg_is_active_plugin('group_tools')) {
            $operated = elgg_get_entities_from_relationship(array(
              'types' => 'group',
              'subtypes' => array(ELGG_ENTITIES_NO_VALUE),
              'limit' => null,
              'site_guids' => $site_id,
              'relationship' => 'group_admin',
              'relationship_guid' => $user_id,
              'inverse_relationship' => false,
            ));
        } else {
            $operated = false;
        }

        if ($operated == false) {
            $operated = array();
        }

        $owned = elgg_get_entities(array(
            'types' => 'group',
            'subtypes' => array(ELGG_ENTITIES_NO_VALUE),
            'limit' => null,
            'site_guids' => $site_id,
            'owner_guids' => $user_id,
                ));
        if ($owned == false) {
            $owned = array();
        }

        $master_groups = array_merge($operated, $owned);

        $subgroups = array();
        $container_guids = array();
        foreach ($master_groups as $parent_group) {
            $container_guids[] = $parent_group->getGUID();
        }

        if (!empty($container_guids)) {
            $sub = elgg_get_entities(array(
                'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
                'limit' => null,
                'site_guids' => $site_id,
                'container_guids' => $container_guids,
                    ));

            if ($sub && is_array($sub)) {
                $subgroups = array_merge($subgroups, $sub);
            }
        }

        $write = array_merge($subgroups, $owned);
        foreach ($write as $group) {
            $returnvalue[$group->group_acl] = elgg_echo('groups:group') . ': ' . $group->name;
        }

        return $returnvalue;
    }
}

function _lbr_subgroups_groupicon_hook($hook, $entity_type, $returnvalue, $params)
{
    $group = $params['entity'];

    if ($hook == 'entity:icon:url' && elgg_instanceof($group, 'group', 'lbr_subgroup')) {           
        $parent = $group->getContainerEntity();
        assert($parent instanceof ElggGroup);

        $params['entity'] = $parent;
        //return groups_icon_url_override($hook, 'group', $returnvalue, $params);
	return groups_set_icon_url($hook, 'group', $returnvalue, $params);
    }
}

function _lbr_subgroups_leave_from_parent($event, $object_type, $object) {
    $group = $object['group'];
    $user = $object['user'];

    if ($group->getSubtype() != 'lbr_subgroup') {
        /* Get the list of subgroups this user is member of */
        $subgroups = elgg_get_entities_from_relationship(array(
        	'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
        	'container_guids' => $group->getGUID(),
            'limit' => null,
            'relationship' => 'member',
            'relationship_guid' => $user->getGUID(),
        ));

        if ($subgroups && is_array($subgroups)) {
            foreach($subgroups as $subgroup) {
                $subgroup->leave($user);
            }
        }
    }
}

function _lbr_subgroups_allow_edit_check($hook, $entity_type, $returnvalue, $params) {
    $entity = $params['entity'];
    $user = $params['user'];
    
    if ($entity instanceof ElggEntity && $user instanceof ElggUser) { // Basic sanity check
        // Grant write access is entity belongs to a subgroup the user is member of
        $container = get_entity($entity->container_guid);
        if ($container instanceof ElggGroup && $container->getSubtype() == 'lbr_subgroup' &&
            $container->isMember($user)) {
                 return true;                 
        }
    }
    
    return null;
}

function lbr_subgroups_owner_block_menu($hook, $type, $return, $params) {    
	if (elgg_instanceof($params['entity'], 'group') && !elgg_instanceof($params['entity'], 'group', 'lbr_subgroup')) {
	    /* Add a link to subgroups functionality iif:
	    * a) The user is a group operator
	    * b) There're subgroups already created
	    */
	    
	    $group = $params['entity'];	      
	    if (elgg_is_logged_in()) {
	        $user = elgg_get_logged_in_user_entity();
	        if ($group->isMember($user)) {
	            elgg_load_library('labredes:subgroups');
	            $show = false;
	            $group_guid = $group->getGUID();
	            
	            if (elgg_is_admin_logged_in()) {
	                $show = true;
	            } else if (lbr_subgroups_is_group_admin($group, $user)) {
	                $show = true;
	            } else if (0 < elgg_get_entities(array(
	            		'type_subtype_pairs' => array('group' => 'lbr_subgroup'),
	            		'container_guids' => $group->getGUID(),
	                    'count' => true,	                    
	            ))){
	                $show = true;
	            }
	            
	            if ($show) {
	                $name = elgg_get_friendly_title($group->name);
	                $url = "lbr_subgroups/index/{$group_guid}/{$name}";
	                $item = new ElggMenuItem('subgroups', elgg_echo('lbr_subgroups:subgroups'), $url);
	                $item->setSection('admin');
                    $return[] = $item;
	            }	            
	        }
	    }
	}

	return $return;
}

function lbr_subgroups_update_class() {
    update_subtype('group', 'lbr_subgroup', 'LabredesSubgroup');
}

function _lbr_subgroups_init() {    
    add_subtype('group', 'lbr_subgroup', 'LabredesSubgroup');
    
    run_function_once('lbr_subgroups_update_class');
    
    elgg_register_page_handler('lbr_subgroups', 'lbr_subgroups_ph');    

    $action_path = elgg_get_plugins_path() . 'labredes_subgroups/actions';
    elgg_register_action('lbr_subgroups/create', "${action_path}/create.php", 'logged_in');
    elgg_register_action('lbr_subgroups/remove', "${action_path}/remove.php", 'logged_in');
    elgg_register_action('lbr_subgroups/add', "${action_path}/add.php", 'logged_in');
    elgg_register_action('lbr_subgroups/edit', "${action_path}/edit.php", 'logged_in');
    elgg_register_action('lbr_subgroups/delete', "${action_path}/delete.php", 'logged_in');

    /* Let the container group owner, and operators, write access to subgroups */
    elgg_register_plugin_hook_handler('access:collections:write', 'all', '_lbr_subgroups_write_acl_plugin_hook');
    
    /* Add the parent group to the write acl */
    elgg_register_plugin_hook_handler('access:collections:write', 'all', '_lbr_subgroups_write_acl_parent_plugin_hook');

    /* Catch users abandoning the parent group and remove them from subgroups */
    elgg_register_event_handler('leave', 'group', '_lbr_subgroups_leave_from_parent');

    /* Let every subgroup user edit any subgroup content */
    elgg_register_plugin_hook_handler('permissions_check', 'all', '_lbr_subgroups_allow_edit_check');
	    
    /* Now override icons */
    elgg_register_plugin_hook_handler('entity:icon:url', 'group', '_lbr_subgroups_groupicon_hook');   
   
    /* Avoid full group edit for subgroups */
    elgg_extend_view('groups/edit', 'lbr_subgroups/edit', 1);
    
    /* Fix the breadcrumb in the group profile for subgroups */
    elgg_extend_view('groups/profile/layout', 'lbr_subgroups/fix_breadcrumb', 1);
    
    /* Extend owner_block group menu */
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'lbr_subgroups_owner_block_menu');
    // subgroup entity menu
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'lbr_subgroups_entity_subgroup_menu_setup');
    // entity menu for entities in submenus
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'lbr_subgroups_entity_user_menu_setup');

    elgg_register_library('labredes:subgroups', elgg_get_plugins_path() . 'labredes_subgroups/lib/lbr_subgroups.php');
}

elgg_register_event_handler('init', 'system', '_lbr_subgroups_init');
