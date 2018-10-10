<?php
/*
 * © Copyright by Laboratorio de Redes 2011
*/

$group = $vars['group'];
?>
<div>
	<label>
	<?php echo elgg_view('output/text', array('value' => elgg_echo('lbr_subgroups:add_quantity'))); ?>
	</label><br />	
<?php echo elgg_view('input/number', array(
    'min' => 1,
    'step' => 1,
    'value' => 5,
    'name' => 'quantity',
)); ?>
</div>
<div>
	<label><?php echo elgg_echo('lbr_subgroups:user_quota'); ?> </label><br />	
<?php echo elgg_view('input/number', array(
        'min' => 1,
        'step' => 1,
    	'name' => 'user_quota',
        'value' => 2,    	
        )
    );?>
</div>
<div>
<label><?php echo elgg_echo('groups:membership'); ?></label><br />
<?php echo elgg_view('input/access', array(
	'name' => 'membership',
	'value' => $membership,
	'options_values' => array(
	    ACCESS_PRIVATE => elgg_echo('lbr_subgroups:access:private'),
	    ACCESS_PUBLIC => elgg_echo('lbr_subgroups:access:public'), ) ));?>
</div>

<?php echo elgg_view('input/hidden', array(
	'name' => 'group_guid',
	'value'=> $group->getGUID(),
));

echo elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('lbr_subgroups:create_submit'),
));
