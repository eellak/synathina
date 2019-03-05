<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_actions
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
		if (task == "action.cancel" || document.formvalidator.isValid(document.getElementById("action-form")))
		{
			Joomla.submitform(task, document.getElementById("action-form"));
		}
	};
');
jimport('joomla.filesystem.folder');

?>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>

</script>
<form action="<?php echo JRoute::_('index.php?option=com_actions&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="action-form" class="form-validate">

	<?php //echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? 'New action' : 'Edit action'); ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('user_id'); ?>
						<?php echo $this->form->renderField('name'); ?>
						<?php echo $this->form->renderField('alias'); ?>
						<?php echo $this->form->renderField('create_actions'); ?>
						<?php echo $this->form->renderField('support_actions'); ?>
						<?php echo $this->form->renderField('action_or_org'); ?>
						<?php echo $this->form->renderField('activities'); ?>
						<?php echo $this->form->renderField('legal_form'); ?>
						<?php echo $this->form->renderField('profit'); ?>
						<?php echo $this->form->renderField('profit_id'); ?>
						<?php echo $this->form->renderField('profit_custom'); ?>
						
						<?php echo $this->form->renderField('org_donation'); ?>
						<?php echo $this->form->renderField('donation_other_1'); ?>
						<?php echo $this->form->renderField('donation_other_2'); ?>
						<?php //echo $this->form->renderField('email'); ?>
						
						<?php echo $this->form->renderField('logo'); ?>
						<?php 
							//echo $this->form->renderField('gallery');
							$directory = 'images/action_photos/'.$this->item->id.'/';
							$path = JPATH_SITE . '/' . $directory;
							$exclude = array('index.html');
							$images = JFolder::files($path, '.', null, null, $exclude );

							foreach($images as $image)
							{
									echo '<img style="width:80px; margin-right:5px;" src="' . JUri::root() . $directory . '/' . $image . '" alt="" />';
							}	
							echo $this->form->renderField('gallery_upload');							
							echo $this->form->renderField('files_upload');							
						?>
						<?php echo $this->form->renderField('newsletter'); ?>
					</div>
					<div class="span6">
						<?php echo $this->form->renderField('description'); ?>
						<?php echo $this->form->renderField('web_link'); ?>
						<?php echo $this->form->renderField('fb_link'); ?>
						<?php echo $this->form->renderField('tw_link'); ?>
						<?php echo $this->form->renderField('go_link'); ?>
						<?php echo $this->form->renderField('yt_link'); ?>
						<?php echo $this->form->renderField('li_link'); ?>
						<?php echo $this->form->renderField('in_link'); ?>
						<?php echo $this->form->renderField('pn_link'); ?>
					</div>
					<div class="span6"><hr />
						<?php echo $this->form->renderField('contact_1_name'); ?>
						<?php echo $this->form->renderField('contact_1_email'); ?>
						<?php echo $this->form->renderField('contact_1_phone'); ?>
						<?php echo $this->form->renderField('contact_2_name'); ?>
						<?php echo $this->form->renderField('contact_2_email'); ?>
						<?php echo $this->form->renderField('contact_2_phone'); ?>
						<?php echo $this->form->renderField('contact_3_name'); ?>
						<?php echo $this->form->renderField('contact_3_email'); ?>
						<?php echo $this->form->renderField('contact_3_phone'); ?>
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
