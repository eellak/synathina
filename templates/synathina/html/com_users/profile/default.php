<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$config = JFactory::getConfig();

$document  = JFactory::getDocument();
$renderer  = $document->loadRenderer('message');
$app = JFactory::getApplication();
//onclick="document.getElementById('email2').value=document.getElementById('email1').value; document.getElementById('password2').value=document.getElementById('password1').value;"
$renderer->render('message');

$breadcumbs_modules=JModuleHelper::getModules('breadcumbs');
$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');

//local db
$db = JFactory::getDbo();

//get team state
$query = "SELECT published FROM #__teams WHERE user_id='".$this->data->id."' LIMIT 1 ";
$db->setQuery($query);
$teams_activated = $db->loadResult();
if($isroot==1){
}else{
?>

<div class="l-register show-profile">
<?php
		foreach ($breadcumbs_modules as $breadcumbs_module){
			echo JModuleHelper::renderModule($breadcumbs_module);
		}
?>
	<div class="module module--synathina">
		<div class="module-skewed">
			<!-- Content Module -->
			<div class="register">
				<h3 class="popup-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h3>
				<?php if (JFactory::getUser()->id == $this->data->id) : ?>
				<ul class="btn-toolbar">
					<li class="btn-group" style="list-style:none;list-style-type:none; display:inline; padding-right:12px">
						<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id);?>">
							<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
					</li>
<?php
	if($teams_activated==1){
?>
					<li class="btn-group" style="list-style:none;list-style-type:none; display:inline; padding-right:12px">
						<a class="btn" href="<?php echo JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143');?>">
							<span class="icon-user"></span> <?php echo JText::_('COM_USERS_SHOW_ACTIONS'); ?></a>
					</li>
					<li class="btn-group" style="list-style:none;list-style-type:none; display:inline; padding-right:12px">
						<a class="btn" href="<?php echo JRoute::_('index.php?option=com_actions&view=form&Itemid=139&user_id=' . (int) $this->data->id);?>">
							<span class="icon-user"></span> <?php echo JText::_('COM_USERS_NEW_ACTION'); ?></a>
					</li>
					<li class="btn-group" style="list-style:none;list-style-type:none; display:inline; padding-right:12px">
						<a class="btn" href="<?php echo JRoute::_('index.php?option=com_opencalls&view=opencalls&Itemid=170&user_id=' . (int) $this->data->id);?>">
							<span class="icon-user"></span> <?php echo JText::_('COM_OPENCALLS'); ?></a>
					</li>
<?php
	}
?>
				</ul>
				<?php endif; ?>
				<?php echo $this->loadTemplate('core'); ?>
				<br />
			</div>
		</div>
	</div>
</div>
<?php
}
?>