<?php
/**
 * @package 	mod_bt_contentshowcase - BT ContentShowcase Module
 * @version		1.0
 * @created		June 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
if($modal){
	JHTML::_('behavior.modal');
}

?>

<?php if(count($list)>0) : ?>

  <?php foreach ($list as $listItem) { ?>

    <?php if(isset($listItem->video_code)) { ?>

            <p><iframe src="https://www.youtube.com/embed/<?php echo $listItem->video_code; ?>" width="853" height="480" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>

    <?php } else { ?>

      <style>
          .video-player {
              line-height: normal;
          }
          .iframe-scaler > div {
              background-color: white;
              box-shadow: none;
              text-align: left;
              width: 100%;
              max-width: 853px;
              padding: 60px 0;
              position: absolute;
              top:0;
          }

          .iframe-scaler span.popup-overtitle {
              font-size: 14px;
          }

          .iframe-scaler h2.popup-title {
              color: #44a7ef;
              margin: 5px 0;
              font-size: 21px;
          }

          .iframe-scaler p.popup-subtitle {
              font-size: 13px;
              font-weight: bold;
              padding:0 60px;
              margin-bottom: 40px;
          }

          .iframe-scaler p.main-content {
              display: inline-block;
              font-size: 15px;
              max-width: 48%;
              position: relative;
              padding: 0;
              float: left;
          }

          .iframe-scaler p.main-content.img {
              padding: 0 30px 0 60px;
          }

          .iframe-scaler p.main-content.text {
              padding-right:    60px;
          }

          .iframe-scaler p.main-content.img img {
              max-width: 100%;
          }

          .iframe-scaler span, p, h2 {
              padding: 0 60px;
          }

          .iframe-scaler > div button {
              display: block;
              margin-top: 30px;
              padding: 15px 0;
              background-color: #44a7ef;
              color: #fff;
              font-size: 17px;
              font-weight: bold;
              border: none;
              width: 100%;
          }
      </style>

            <div>
                <form action="<?php echo $listItem->link ?>" method="get">
                    <span class="popup-overtitle"><?php echo $listItem->overtitle; ?></span>
                    <h2 class="popup-title"><?php echo $listItem->title; ?></h2>
                    <p class="popup-subtitle"><?php echo $listItem->subtitle; ?></p>
                    <p class="main-content img"><img src="<?php echo $listItem->intro_img; ?>"/></p>
                    <p class="main-content text">
                      <?php echo $listItem->text; ?>
                        <button>ΜΑΘΕ ΠΕΡΙΣΣΟΤΕΡΑ</button>
                    </p>
                </form>
            </div>
    <?php } ?>

  <?php } ?>

<?php endif; ?>