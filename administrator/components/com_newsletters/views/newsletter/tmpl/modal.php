<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsletters
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

$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "newsletter.cancel" || document.formvalidator.isValid(document.getElementById("newsletter-form")))
		{
			' . $this->form->getField('misc')->save() . '

			if (window.opener && (task == "newsletter.save" || task == "newsletter.cancel"))
			{
				window.opener.document.closeEditWindow = self;
				window.opener.setTimeout("window.document.closeEditWindow.close()", 1000);
			}

			Joomla.submitform(task, document.getElementById("newsletter-form"));
		}
	};
');

?>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<div class="container-popup">

<div class="pull-right">
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('newsletter.apply');"><?php echo JText::_('JTOOLBAR_APPLY') ?></button>
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('newsletter.save');"><?php echo JText::_('JTOOLBAR_SAVE') ?></button>
	<button class="btn" type="button" onclick="Joomla.submitbutton('newsletter.cancel');"><?php echo JText::_('JCANCEL') ?></button>
</div>

<div class="clearfix"> </div>
<hr class="hr-condensed" />

<form action="<?php echo JRoute::_('index.php?option=com_newsletters&layout=modal&tmpl=component&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="newsletter-form" class="form-validate">

	<?php //echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? 'New newsletter' : 'Edit newsletter'); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('name'); ?>
						<?php echo $this->form->renderField('alias'); ?>
						<?php echo $this->form->renderField('paint_number'); ?>
						<?php echo $this->form->renderField('painter_id'); ?>
						<?php echo $this->form->renderField('exhibition_id'); ?>
						<?php echo $this->form->renderField('annexe_id'); ?>
						<?php echo $this->form->renderField('short_title'); ?>
						<?php echo $this->form->renderField('year'); ?>
						<?php echo $this->form->renderField('style'); ?>
						<?php echo $this->form->renderField('dimensions'); ?>
						<?php echo $this->form->renderField('donation'); ?>
						<?php echo $this->form->renderField('image'); ?>
						<?php echo $this->form->renderField('video'); ?>
						<?php echo $this->form->renderField('permanent'); ?>
						<?php echo $this->form->renderField('highlight'); ?>
						<?php echo $this->form->renderField('type'); ?>
						<?php echo $this->form->renderField('week'); ?>
					</div>
					<div class="span6">
						&nbsp;
					</div>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>

				
				<?php
				if($this->item->image!=''){
					echo '<style>';
  						echo '#draggable { 
  							width: 100%; 
  							position: absolute; 
  							border: 1px solid #e1a600; 
  							height: 40%; 
  							padding: 0; 
  							left:0%!important;
  							top: '.$this->item->week_position.'%;
  							background-color: rgba(0,0,0,0.5);
  							color: #FFFFFF;
  						}';
  					echo '</style>';
					echo '<div style="position: relative" id="dragStart">';
						echo '<img src="../'.$this->item->image.'" class="img-responsive" />';
						echo '<div id="draggable" class="ui-widget-content">';
						  echo '<p>Drag me around for week module</p>';
						echo '</div>';
					echo '</div>';
					?>
					<script> 
							jQuery(function($) { 
								$( "#draggable" ).draggable({
									stop: function( event, ui ) {
										var dragStartOffset=$( "#dragStart" ).offset().top;
										var draggableOffset=$( "#draggable" ).offset().top;
										var imagHeight=$( "#dragStart img" ).height();

										var difference=draggableOffset-dragStartOffset;

										if(difference<0){
											difference=0;
										}
										
										difference=(1.0*difference*100)/imagHeight;
										$('#jform_week_position').val(difference);
									}
								}); 
							}); 
					</script>
				<?php 
				}
				?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9">
				<?php echo $this->form->renderField('desc'); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9">
				<?php echo $this->form->renderField('series'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if ($assoc) : ?>
			<div class="hidden"><?php echo $this->loadTemplate('associations'); ?></div>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[week_position]" id="jform_week_position" value="<?php echo $this->item->week_position; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
