<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stegihours
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('StegihoursHelper', JPATH_ADMINISTRATOR . '/components/com_stegihours/helpers/stegihours.php');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_stegihours
 */
abstract class JHtmlStegihour
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $stegihourid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 */
	public static function association($stegihourid)
	{
		// Defaults
		$html = '';

		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_stegihours', '#__stegi', 'com_stegihours.stegihour', $stegihourid, 'id', '', ''))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated contact items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('c.id, c.name as title')
				->select('l.sef as lang_sef')
				->from('#__stegi as c')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);
			
			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (runtimeException $e)
			{
				throw new Exception($e->getMessage(), 500);

				return false;
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$url = JRoute::_('index.php?option=com_stegihours&task=stegihour.edit&id=' . (int) $item->id);
					$tooltipParts = array(
						JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
								$item->language_title,
								array('title' => $item->language_title),
								true
						),
						$item->title,
						'(' . $item->category_title . ')'
					);

					$item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}

}
