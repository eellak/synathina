<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stegihours
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
//$listOrder = $this->escape($this->state->get('list.ordering'));
$listOrder='';
$listDirn  = $this->escape($this->state->get('list.direction'));
$archived  = $this->state->get('filter.published') == 2 ? true : false;
$trashed   = $this->state->get('filter.published') == -2 ? true : false;
$canOrder  = $user->authorise('core.edit.state', 'com_stegihours.stegihour');
$saveOrder='';
//$saveOrder = $listOrder == 'a.ordering';
$db = JFactory::getDBO();
//if ($saveOrder)
//{
	//$saveOrderingUrl = 'index.php?option=com_stegihours&task=stegihours.saveOrderAjax&tmpl=component';
	//JHtml::_('sortablelist.sortable', 'stegihourList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
//}

$sortFields = $this->getSortFields();
$assoc		= JLanguageAssociations::isEnabled();

/*JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, "");
	};
');*/
?>

<form stegihour="<?php echo JRoute::_('index.php?option=com_stegihours'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('com_stegihours_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('com_stegihours_SEARCH_IN_NAME'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="stegihourList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<span class="icon-menu-2"></span>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" style="min-width:55px;" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'Team', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th width="20%" class="nowrap hidden-phone">
							Description
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'Start date', 'a.date_start', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'End date', 'a.date_end', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					//$ordering   = $listOrder == 'a.ordering';
					$canCreate  = $user->authorise('core.create',     'com_stegihours.category.' . @$item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_stegihours.category.' . @$item->catid);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					//$canEditOwn = $user->authorise('core.edit.own',   'com_stegihours.category.' . @$item->catid) && $item->created_by == $userId;
					$canEditOwn=true;
					$canChange  = $user->authorise('core.edit.state', 'com_stegihours.category.' . @$item->catid) && $canCheckin;

					$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_stegihours&task=edit&type=other&id=' . @$item->catid);
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5"
									value="<?php //echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center" style="vertical-align:top;" align="top">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'stegihours.', $canChange, 'cb', '', ''); ?>
								<?php
								// Create dropdown items
								$stegihour = $archived ? 'unarchive' : 'archive';
								//JHtml::_('stegihoursdropdown.' . $stegihour, 'cb' . $i, 'stegihours');

								$stegihour = $trashed ? 'untrash' : 'trash';
								//JHtml::_('stegihoursdropdown.' . $stegihour, 'cb' . $i, 'stegihours');

								// Render dropdown list
								//echo JHtml::_('stegihoursdropdown.render', $this->escape($item->name));
								?>
							</div>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php if ($canEdit || $canEditOwn) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_stegihours&task=stegihour.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
							</div>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php echo $item->description; ?>
							</div>
						</td>
						<td align="center" class="small hidden-phone">
							<?php echo $item->date_start; ?>
						</td>
						<td align="center" class="small hidden-phone">
							<?php echo $item->date_end; ?>
						</td>
						<td align="center" class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		<?php endif;?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
