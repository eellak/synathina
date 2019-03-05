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
				<h3 class="popup-title">Ευχαριστούμε για την εγγραφή σας</h3>
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
				<p>Θα λάβετε σύντομα ένα email με οδηγίες ενεργοποίησης του λογαριασμού σας.</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</div>
		</div>
	</div>
</div>	

