<?php
/**
 * Attachments component
 *
 * @package Attachments
 * @subpackage Attachments_Component
 *
 * @copyright Copyright (C) 2007-2015 Jonathan M. Cameron, All Rights Reserved
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://joomlacode.org/gf/project/attachments/frs/
 * @author Jonathan M. Cameron
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Load the Attachments helper
require_once(JPATH_SITE.'/components/com_attachments/helper.php'); /* ??? Needed? */
require_once(JPATH_SITE.'/components/com_attachments/javascript.php');

$user = JFactory::getUser();
$logged_in = $user->get('username') <> '';

$app = JFactory::getApplication();
$uri = JFactory::getURI();

// Set a few variables for convenience
$attachments = $this->list;
$parent_id = $this->parent_id;
$parent_type = $this->parent_type;
$parent_entity = $this->parent_entity;

$base_url = $this->base_url;

$format = JRequest::getWord('format', '');

$html = '';

if ( $format != 'raw' ) {

	// If any attachments are modifiable, add necessary Javascript for iframe
	if ( $this->some_attachments_modifiable ) {
		AttachmentsJavascript::setupModalJavascript();
		}

	// Construct the empty div for the attachments
	if ( $parent_id === null ) {
		// If there is no parent_id, the parent is being created, use the username instead
		$pid = $user->get('username');
		}
	else {
		$pid = $parent_id;
		}
	$div_id = 'attachmentsList' . '_' . $parent_type . '_' . $parent_entity	 . '_' . (string)$pid;
	//$html .= "\n<div class=\"$this->style\" id=\"$div_id\">\n";
	}

//$html .= "<table>\n";
//$html .= "<caption>{$this->title}</caption>\n";
$html.="<h3 class=\"text-center\">{$this->title}</h3>";
$html.='<ul class="inline-list">';

//$html .= "<tbody>\n";

// Construct the lines for the attachments
$row_num = 0;
for ($i=0, $n=count($attachments); $i < $n; $i++) {
	$attachment = $attachments[$i];
	if ( $attachment->uri_type == 'file' ) {
		// Handle file attachments
		if ( $this->secure ) {
			$url = JRoute::_("index.php?option=com_attachments&task=download&id=" . (int)$attachment->id);
			}
		else {
			$url = $base_url . $attachment->url;
			if (strtoupper(substr(PHP_OS,0,3) == 'WIN')) {
				$url = utf8_encode($url);
				}
			}
	}	
	$attachment = $attachments[$i];
	$filename = $attachment->filename;
	$html.="<li><a href=\"$url\" download=\"$attachment->filename\" target=\"_blank\"><i class=\"doc-icon doc-icon--pdf\"></i><div>$filename</div></a></li>";


	}
$html.='</ul>';
// Close the HTML
//$html .= "</tbody></table>\n";

if ( $format != 'raw' ) {
	//$html .= "</div>\n";
	}

echo $html;
