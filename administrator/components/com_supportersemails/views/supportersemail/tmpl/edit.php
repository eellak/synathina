<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$assoc = JLanguageAssociations::isEnabled();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "stat.cancel" || document.formvalidator.isValid(document.getElementById("stat-form")))
		{
			Joomla.submitform(task, document.getElementById("stat-form"));
		}
	};
');
jimport('joomla.filesystem.folder');


?>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>

</script>
<form action="<?php echo JRoute::_('index.php?option=com_stats&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="stat-form" class="form-validate">

	<?php //echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? 'New stegi hour' : 'Edit stegi hour'); ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('team_id'); ?>
						<?php echo $this->form->renderField('description'); ?>

						<?php echo $this->form->renderField('date_start'); ?>
						<?php echo $this->form->renderField('date_end'); ?>
					</div>
					<div class="span6"><hr />
						<?php //echo $this->form->renderField('privacy'); ?>
						<?php echo $this->form->renderField('language'); ?>
						<?php echo $this->form->renderField('published'); ?>					
					</div>
					<div class="span6">
						&nbsp;
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>



		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="catid" value="" />
	<input type="hidden" name="asset_id" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
