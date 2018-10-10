<?php
/*
 * © Copyright by Laboratorio de Redes 2011—2012
 */

$group = $vars['group'];

?>
<div>
    <label>
        <?php echo elgg_echo('name'); ?>
    </label>
    <?php
    echo elgg_view('input/text', array(
        'name' => 'name',
        'value' => $group->name,
    ));
    ?>
</div>
<div>
    <label>
        <?php echo elgg_echo('groups:briefdescription'); ?>
    </label>
    <?php
    echo elgg_view('input/text', array(
        'name' => 'briefdescription',
        'value' => $group->briefdescription,
    ));
    ?>
</div>
<div>
    <label>
        <?php echo elgg_echo('lbr_subgroups:user_quota'); ?>
    </label>
    <?php
    echo elgg_view('input/number', array(
 	    'min' => max(array(1, $group->getMembers(array('limit'=>0, 'offset'=> 0, 'count'=> true)))),
        'step' => 1,
        'name' => 'user_quota',
        'value' => $group->user_quota,
            )
    );
    ?>
</div>
<div>
    <label><?php echo elgg_echo('groups:membership'); ?></label><br />
    <?php
    echo elgg_view('input/access', array(
        'name' => 'membership',
        'value' => $group->membership,
        'options_values' => array(
            ACCESS_PRIVATE => elgg_echo('lbr_subgroups:access:private'),
            ACCESS_PUBLIC => elgg_echo('lbr_subgroups:access:public'),)));
    ?>
</div>

<?php
echo elgg_view('input/hidden', array(
    'name' => 'group_guid',
    'value' => $group->getGUID(),
));

echo elgg_view('input/submit', array(
    'name' => 'submit',
    'value' => elgg_echo('save'),
));
