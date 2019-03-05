<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
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
				<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout'); ?>" method="post" class="form-horizontal well form">
					<div class="control-group">
						<div class="controls">
							<button type="submit" class="pull-left btn btn--coral btn btn-primary"><span class="icon-arrow-left icon-white"></span> <?php echo JText::_('JLOGOUT'); ?></button>
						</div>
					</div>
					<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return'))); ?>" />
					<?php echo JHtml::_('form.token'); ?>		
				</form>
				<br />
				<br />
			</div>
		</div>
	</div>
</div>
