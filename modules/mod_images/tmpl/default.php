<?php
/**
 * @module		com_di
 * @script		default.php
 * @author-name Tomas Kartasovas
 * @copyright	Copyright (C) 2013 dizi.lt
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<?php if( $list ) : ?>
	<?php if( $params->get( 'fluid' ) ) : ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo JUri::base(); ?>media/com_di/js/jquery-gp-gallery/src/jquery-gp-gallery.css" />
		<script src="<?php echo JUri::base(); ?>media/com_di/js/jquery-gp-gallery/src/jquery-gp-gallery.js"></script>
	<?php endif; ?>
	
	<?php if( $params->get( 'fluid' ) || $params->get( 'load_fancybox' ) ) : ?>
		<script>
			jQuery( function(){
				if( typeof( jQuery.fancybox ) == 'function' || typeof( $.fancybox ) == 'function' ){
					jQuery( '.fancy' ).fancybox( {
						transitionIn: 'elastic',
						transitionOut: 'elastic',
						titlePosition: 'inside'
					} );
				}
				
				<?php if( $params->get( 'fluid' ) ) : ?>
					jQuery( '.gallery > .pictures' ).gpGallery( 'a', {
						is_first_big: false,
						row_min_height: <?php echo $list[ 0 ]->info->thumb[ 1 ]; ?>,
						gutter: 6
					} );
				<?php endif; ?>
			} );
		</script>
	<?php endif; ?>
	
	<?php if( $params->get( 'load_fancybox' ) ) : ?>
		<!--  fancybox  -->
		<link rel="stylesheet" href="<?php echo JUri::root(); ?>media/com_di/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" type="text/css" />
		<script src="<?php echo JUri::root(); ?>media/com_di/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js" type="text/javascript"></script>
	<?php endif; ?>
	
	<div class="gallery">
		<?php if( $module->showtitle ) : ?>
			<h3><?php echo $module->title; ?></h3>
		<?php endif; ?>
		
		<div class="pictures">
			<?php foreach( $list as $item ) : ?>
					<a class="fancy" rel="images" title="<?php echo $item->title; ?>" href="<?php echo $item->src->zoomed; ?>">
						<img width="<?php echo $item->info->thumb[ 0 ]; ?>" height="<?php echo $item->info->thumb[ 1 ]; ?>" src="<?php echo $item->src->thumb; ?>" alt="<?php echo $item->title; ?>" title="<?php echo $item->title; ?>" />
					</a>
			<?php endforeach; ?>
		</div>
		<div style="clear: both;"></div>
	</div>
<?php endif; ?>