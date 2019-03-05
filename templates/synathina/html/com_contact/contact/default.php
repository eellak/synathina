<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams('com_media');

jimport('joomla.html.html.bootstrap');
?>
<div class="l-contact">
   <div class="module module--synathina">
      <div class="module-skewed">
         <!-- Content Module -->
         <div class="l-contact__wrapper module__forms">
            <h3 class="popup-title"><?php echo $this->contact->name; ?></h3>
            <div class="c-article">
               <?php echo $this->contact->misc; ?>
            </div>
            <div class="contact-map" id="contact-map">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4446.984357332299!2d23.722076872730188!3d37.988964516607474!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14a1bd2e657ae2f5%3A0x2b00728c10575d83!2sMunicipality+of+Athens!5e0!3m2!1sen!2sgr!4v1464707231275" width="100%" height="262" frameborder="0" style="border:0" allowfullscreen></iframe>
						</div>
            <span class="is-block is-italic"><?php echo JText::_('COM_CONTACT_INQUIRIES');?></span>
						<?php  echo $this->loadTemplate('form');  ?>
         </div>

       </div>
   </div>
</div>

<?php
//meta tags
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuname = $menu->getActive()->title;
$article_image='http://www.synathina.gr/images/template/synathina_big.jpg';
$document = JFactory::getDocument();
$document->setMetaData( 'twitter:card', 'summary_large_image' );
$document->setMetaData( 'twitter:site', '@synathina' );
$document->setMetaData( 'twitter:title', 'συνΑθηνά' );
$document->setMetaData( 'twitter:description', $menuname );
$document->setMetaData( 'twitter:image', $article_image );
?>