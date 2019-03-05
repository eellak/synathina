<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

jimport('joomla.filesystem.folder');

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

/**
 * Helper for mod_articles_latest
 *
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 * @since       1.6
 */
abstract class ModOverlayVideoHelper
{
    /**
     * Retrieve a list of article
     *
     * @param   \Joomla\Registry\Registry  &$params  module parameters
     *
     * @return  mixed
     *
     * @since   1.6
     */
    public static function getList(&$module, &$params, &$attribs) {

      $video_code = $params->get('video_code', '');

      if($video_code == '') {

        $db = JFactory::getDbo();

        $lang = JFactory::getLanguage();

        $lgArray = explode("-", $lang->getTag());

        $lang_code = 'en';

        $limit = ' LIMIT 1';

        if (sizeof($lgArray) > 1) {
          $lang_code = $lgArray[0];
        }

        $query = " SELECT c.* "

          . " FROM #__content AS c "

          . " WHERE c.id = " . $params->get('article_id', '') . ""

          //." AND p.language IN ('*', '".$lang->getTag()."') "

          . $limit;

        $db->setQuery($query);

        $items = $db->loadObjectList();

        foreach ($items as &$item) {

          $item->module_path = $module_path;

          // setting for route link

          $item->slug = $item->id . ':' . $item->alias;

          $item->catslug = $item->catid . ':' . $item->category_alias;

          // item link

          $item->link = '';

          if ($item->urls) {

            $item->urls = json_decode($item->urls);

            $item->link = $item->urls->urla;

            if (!$item->link) {
              $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            }

          }
          else {

            $item->link = JRoute::_('index.php?option=com_users&view=login');

          }

          if ($item->images) {

            $item->images = json_decode($item->images);

            if(is_null($item->images->image_intro)) {

              $module_path = JURI::base() . 'modules/' . $module->module;

              $item->intro_img = $module_path.'/tmpl/images/no-image.png';

            } else {
              $item->intro_img = $item->images->image_intro;
            }

          }
        }
        $item->overtitle = $params->get('header_text', '');
        $item->subtitle = $params->get('subtitle_text', '');
        $item->text = $params->get('intro_text', '');
      } else {
        $items = [];
        $item = new stdClass();
        $item->video_code = $video_code;
        array_push($items, $item);
      }

      return $items;
    }
}