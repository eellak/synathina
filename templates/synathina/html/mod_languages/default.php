<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_languages
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);
if ($params->get('dropdown', 1))
{
	JHtml::_('formbehavior.chosen', 'select');
}
?>
<ul class="i18n-actions">
<?php foreach ($list as $language) : ?>
		<li class="<?php echo $language->active ? 'selected' : ''; ?>"><a href="<?php echo $language->link; ?>"><?php echo $language->sef; ?></a></li>
<?php endforeach; ?>
</ul>