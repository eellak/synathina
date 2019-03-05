<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

$input = $app->input;

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'team.cancel' || document.formvalidator.isValid(document.id('team-form'))) {
			<?php //echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('team-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_d&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="team-form" class="form-validate">


    <div class="span10 form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TEAMS_DETAILS_TAB', true)); ?>

        <div class="row-fluid">
            <div class="span10">
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('main_info'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('main_info'); ?>
								</div>
						</div>						
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('name'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('name'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('team_or_org'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('team_or_org'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('team_type'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('team_type'); ?>
								</div>
						</div>						
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('org_donation'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('org_donation'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('email'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('email'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('description'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('description'); ?>
								</div>
						</div>						
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('logo'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('logo'); ?>
								</div>
						</div>	
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('social'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('social'); ?>
								</div>
						</div>						
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('web_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('web_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('fb_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('fb_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('tw_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('tw_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('go_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('go_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('in_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('in_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('pn_link'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('pn_link'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('various_info'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('various_info'); ?>
								</div>
						</div>						
<?php
for($i=1; $i<4; $i++){
?>
						
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('contact_'.$i.'_name'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('contact_'.$i.'_name'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('contact_'.$i.'_phone'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('contact_'.$i.'_phone'); ?>
								</div>
						</div>
						<div class="control-group">
								<div class="control-label">
										<?php echo $this->form->getLabel('contact_'.$i.'_email'); ?>
								</div>
								<div class="controls">
										<?php echo $this->form->getInput('contact_'.$i.'_email'); ?>
								</div>
						</div>
<?php
}
?>
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('newsletter'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('newsletter'); ?>
                </div>
            </div>	
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('privacy'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('privacy'); ?>
                </div>
            </div>					
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('published'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('published'); ?>
                </div>
            </div>						
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('language'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('language'); ?>
                </div>
            </div>								
            </div>
						
        </div>

        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publish', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>

        <div class="row-fluid">
            <div class="span6">
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('published'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('published'); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php echo JHtml::_('bootstrap.endTab'); ?>


        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
				 <input type="hidden" name="title" value="" />
				<input type="hidden" name="option" value="com_teams" />
        <input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>

</form>
