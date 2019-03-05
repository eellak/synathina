<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_userphotos
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
header('Content-Type: text/html; charset=utf-8');


?>
<form action="index.php?option=com_support&view=supports" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
            <th width="5%">
				<?php echo JText::_('COM_SUPPORT_PUBLISHED'); ?>
			</th>
			<th width="90%">
				<?php echo JText::_('COM_SUPPORT_NAME') ;?>
			</th>
			<th width="2%">
				<?php echo JText::_('COM_SUPPORT_ID'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
            	
				<?php $counter=0; ?>
				
				<?php foreach ($this->items as $i => $row) : ?>
                	
 					<?php if ($row->parent_id == 0) : ?>
                    	
                        <?php $counter++; ?>
                    	<?php $current_id = $row->id; ?>
                        
                        <tr>
                            <td>
                                <?php echo JHtml::_('grid.id', $counter, $row->id); ?>
                            </td>
                            <td align="center">
                                <?php echo JHtml::_('jgrid.published', $row->published, $counter, 'supports.', true, 'cb'); ?>
                            </td>
                            <td>
                            	<a href="<?php echo JRoute::_('index.php?option=com_support&task=support.edit&id='.$row->id); ?>">
                                	<?php echo $row->name; ?>
                                </a>
                            </td>
                            <td align="center">
                                <?php echo $row->id; ?>
                            </td>
                        </tr>
                        
                        <?php foreach ($this->items as $sub) : ?>
                        	<?php if ($sub->parent_id == $current_id) : ?>
                            
                            	<?php $counter++; ?>
   
                                 <tr>
                                    <td>
                                        <?php echo JHtml::_('grid.id', $counter, $sub->id); ?>
                                    </td>
                                    <td align="center">
                                        <?php echo JHtml::_('jgrid.published', $sub->published, $counter, 'supports.', true, 'cb'); ?>
                                    </td>
                                    <td>
                                        <span class="gi">— </span>
                                        <a href="<?php echo JRoute::_('index.php?option=com_support&task=support.edit&id='.$sub->id); ?>">
                                            <?php echo $sub->name; ?>
                                        </a>
                                    </td>
                                    <td align="center">
                                        <?php echo $sub->id; ?>
                                    </td>
                                </tr>
                            
                            <?php endif; ?>	
                        	
                        <?php endforeach; ?>
                        
                    <?php endif; ?>
                    
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
    <input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>