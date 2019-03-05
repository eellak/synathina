<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_support
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<form action="<?php echo JRoute::_('index.php?option=com_support&layout=edit&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm">
	<div class="form-horizontal">
        <fieldset class="adminform">
        	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
        	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details',  JText::_('COM_SUPPORT_TYPE_DETAILS')); ?>
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="support.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>