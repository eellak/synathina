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
?>
<div style="width:100%;height:500px;">
	<?php if (!empty($this->items)) : ?>
		<?php foreach ($this->items as $i => $row) : ?>
			<?php echo $row->email; ?><br/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
