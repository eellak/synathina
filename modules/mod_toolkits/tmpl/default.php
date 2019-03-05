<?php
defined('_JEXEC') or die;
//connect to db
$db = JFactory::getDBO();

//get toolkits
$query = "SELECT  c.id, c.title FROM #__content AS c WHERE c.state=1 AND c.catid='".$params->get('cat')."' ORDER BY c.ordering ASC ";
$db->setQuery($query);
$articles = $db->loadObjectList();

//JUri::base()
//echo ;
?>
<h2><?php echo $module->title; ?></h2>
<div id="toolkit_tabs">
  <ul class="inline-list inline-list--separated inline-list--headlines">
<?php
	$i=1;
	foreach($articles as $article){
		//echo '<li><a href="#tabs-'.$i.'">'.$article->title.'</a></li>';
		echo '<li><a href="'.JURI::getInstance()->toString().'#tabstoolkit-'.$i.'">'.$article->title.'</a></li>';
		$i++;
	}
?>	
  </ul>
	<div class="documents-list">
		<div class="documents-list-row">
<?php
	$i=1;
	foreach($articles as $article){
		$query = "SELECT  a.url,a.display_name,a.filename FROM #__attachments AS a WHERE a.parent_id='".$article->id."' AND a.parent_type='com_content' AND a.parent_entity='article' AND a.state=1 ORDER BY a.id ASC ";
		$db->setQuery($query);
		$toolkits = $db->loadObjectList();
		echo '<div id="tabstoolkit-'.$i.'">';
		foreach($toolkits as $toolkit){
			echo '<a href="'.$toolkit->url.'" target="_blank" class="documents-list-item">
							<img src="images/template/pdf.png" alt="" style="text-align:left" /><br />'.($toolkit->display_name!=''?$toolkit->display_name:$toolkit->filename).'
						</a>';			
		}
		echo '</div>';
		$i++;
	}
?>		
		</div>
	</div>
</div>
