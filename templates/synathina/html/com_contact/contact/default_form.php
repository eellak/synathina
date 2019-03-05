<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

$captchaEnabled = false;

foreach (JPluginHelper::getPlugin('captcha') as $plugin)
{
	if (JFactory::getApplication()->get('captcha', '0') === $plugin->name)
	{
		$captchaEnabled = true;
		break;
	}
}
?>
<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate form-horizontal well form form-inline">
<div class="form-group is-block">
		<label id="jform_contact_name-lbl" for="jform_contact_name" class="is-block required"><?php echo JText::_('COM_CONTACT_FULLNAME');?>*:</label>
		<input type="text" name="jform[contact_name]" id="jform_contact_name" value="" class="input--large required" required="" aria-required="true">
 </div>
 <div class="form-group is-block">
		<label id="jform_contact_email-lbl" for="jform_contact_email" class="is-block required">E-mail*:</label>
		<input type="email" name="jform[contact_email]" class="input--large validate-email required" id="jform_contact_email" value="" autocomplete="email" required="" aria-required="true">		
 </div>
 <div class="form-group is-block">
		<label id="jform_contact_emailmsg-lbl" for="jform_contact_emailmsg" class="is-block required"><?php echo JText::_('COM_CONTACT_CONTACT_MESSAGE_SUBJECT_LABEL');?>*:</label>
		<input type="text" name="jform[contact_subject]" id="jform_contact_emailmsg" value="" class="input--large required" required="" aria-required="true">
 </div> 
 <div class="form-group is-block">
		<label id="jform_contact_message-lbl" for="jform_contact_message" class="hasTooltip is-block required"><?php echo JText::_('COM_CONTACT_CONTACT_ENTER_COMMENTS');?>*:</label>
		<textarea name="jform[contact_message]" id="jform_contact_message" rows="10" class="required" required="" aria-required="true"></textarea>
 </div>
 <div class="form-group form-group--tail is-block clearfix">
		<span class="pull-left"><em>*<?php echo JText::_('COM_CONTACT_REQUIRED');?></em></span>
		<button class="pull-right btn btn--coral btn--bold validate" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
 </div>    
	<input type="hidden" name="option" value="com_contact" />
	<input type="hidden" name="task" value="contact.submit" />
	<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
	<?php echo JHtml::_('form.token'); ?>	 
</form>