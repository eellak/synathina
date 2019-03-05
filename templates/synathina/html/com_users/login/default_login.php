<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//JHtml::_('behavior.keepalive');

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
				<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well form">
						<?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
							<?php if (!$field->hidden) : ?>
								<div class="form-inline l-fg6 ">
									<div class="form-group">
										 <?php echo str_replace('class="required','class="required is-block ',$field->label); ?>
										 <?php echo $field->input; ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>

						<?php if ($this->tfa): ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getField('secretkey')->label; ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getField('secretkey')->input; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div  class="control-group">
							<div class="control-label"><label><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME') ?></label></div>
							<div class="controls"><input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"/></div>
						</div>
						<?php endif; ?>

						<div class="control-group">
							<div class="controls">
								<button type="submit" class="pull-left btn btn--coral btn--bold btn  btn btn-primary validate">
									<?php echo JText::_('JLOGIN'); ?>
								</button>
							</div>
						</div>

						<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
						<?php echo JHtml::_('form.token'); ?>
				</form>
				<ul class="login-reminders">
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
						<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
					</li>
					<!--<li>
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
						<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
					</li>-->
					<?php
					$usersConfig = JComponentHelper::getParams('com_users');
					if ($usersConfig->get('allowUserRegistration')) : ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
							<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
					</li>
					<?php endif; ?>
				</ul>				
			</div>
		</div>
	</div>
</div>