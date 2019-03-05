<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
$document  = JFactory::getDocument();
?>

<div class="l-register">
	<div class="module module--synathina">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register">
				<h3 class="popup-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h3>
<?php
	$str = preg_replace('/^\h*\v+/m', '', $this->document->getBuffer('message'));
	if(!empty($str)){
?>
				<div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php echo $this->document->getBuffer('message');?>
				</div>				
<?php
	}
?>
				<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate form-horizontal well form">
					<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
						<p><?php echo JText::_($fieldset->label); ?></p>
						<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
							<div class="form-inline l-fg6 ">
								<div class="form-group">
									<?php echo str_replace('class="required','class="required is-block ',$field->label); ?>
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
					<div class="control-group">
						<div class="controls">
							<button type="submit" class="pull-left btn btn--coral btn--bold btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
						</div>
					</div>
					<?php echo JHtml::_('form.token'); ?>				
				</form>
				<br />
				<br />
			</div>
		</div>
	</div>
</div>