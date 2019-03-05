<?php
defined('_JEXEC') or die;
//connect to db
$db = JFactory::getDBO();

//language
$doc = JFactory::getDocument();
$lang_code_array=explode('-',$doc->language);
$lang_code=$lang_code_array[0];

//functions
function sortBy($field, &$array, $direction = 'desc')
{
	usort($array, create_function('$a, $b', '
		$a = $a["' . $field . '"];
		$b = $b["' . $field . '"];

		if ($a == $b)
		{
			return 0;
		}

		return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
	'));

	return true;
}

//NEW SLIDER
//1st slider
function count_actions($year)
{
	$where = "a.team_id>0 AND aa.published=1 AND a.published=1 AND aa.action_id>0 AND aa.action_date_start>='".$year."-01-01 00:00:00'";
	if ($year == date('Y')) {
		$where .= " AND aa.action_date_start<='".date('Y-m-d H:i:s')."' ";
	} else {
		$where .= " AND aa.action_date_start<='".$year."-31-21 23:59:59' ";
	}

	//remote db
	$activityClass = new RemotedbActivity();
	$activities_count = $activityClass->getActivitiesCount($where);

	return $activities_count;
}

//get action by year
$slider_1_2013=185+23;
$slider_1_2014=317+70;
$slider_1_2015=451+169;
$slider_1_2016=638+459;
for($y=2017; $y<=date('Y'); $y++){
	${'slider_1_'.$y}=count_actions($y);
}
//total
$slider_1_all=0;
for($y=2013; $y<=date('Y'); $y++){
	$slider_1_all+=${'slider_1_'.$y};
}
//total/percent per year
$slider_1_total_2013=$slider_1_2013;
$slider_1_percent_total_2013 = (100/$slider_1_all)*$slider_1_total_2013;
$slider_1_percent_sub_2013 = (100/$slider_1_total_2013)*($slider_1_total_2013-$slider_1_2013);
for($y=2014; $y<=date('Y'); $y++){
	${'slider_1_total_'.$y}=${'slider_1_total_'.($y-1)}+${'slider_1_'.$y};
	${'slider_1_percent_total_'.$y} = (100/$slider_1_all)*${'slider_1_total_'.$y};
	${'slider_1_percent_sub_'.$y} = ${'slider_1_percent_total_'.($y-1)};
}

//get 2nd slider
function teams_count($year)
{
	$db = JFactory::getDBO();
	$query = "SELECT COUNT(u.id) FROM #__users AS u
				INNER JOIN #__teams AS t
				ON t.user_id=u.id
				WHERE u.block=0 AND u.activation='' AND t.published=1 AND t.hidden=0 AND t.created>='".$year."-01-01 00:00:00' AND t.created<='".$year."-31-21 23:59:59' ";
	$db->setQuery($query);
	$db->execute();

	return $db->loadResult();
}

$slider_2_2013 = 42;
$slider_2_2014 = 119;
$slider_2_2015 = 194;
$slider_2_2016 = 281;

$slider_2_unique_2013 = 42;
$slider_2_unique_2014 = 77;
$slider_2_unique_2015 = 75;
$slider_2_unique_2016 = 87;
for($i=2017; $i<=date('Y'); $i++){
	${'slider_2_unique_'.$i} = teams_count($i);
	${'slider_2_'.$i} = ${'slider_2_unique_'.$i}+${'slider_2_'.($i-1)};
}
//total teams
$slider_2_all=0;
for($y=2013; $y<=date('Y'); $y++){
	$slider_2_all+=${'slider_2_unique_'.$y};
}


//get 3rd slider
//get action by year
$slider_3_2013=185+23;
$slider_3_2014=317+70;
$slider_3_2015=451+169;
$slider_3_2016=638+459;
for($y=2017; $y<=date('Y'); $y++){
	${'slider_3_'.$y}=count_actions($y);
}
//total
$current_year_3 = date('Y');
if(date('n')<3){
	$current_year_3 = date('Y')-1;
}
//test
//$current_year_3=2017;
$slider_3_all=${'slider_3_'.$current_year_3};
$query = "SELECT * FROM #__team_activities
					WHERE published=1";
$db->setQuery($query);
$activities_object = $db->loadObjectList();
foreach($activities_object as $activity){
	if($lang_code=='en'){
		$activity_name=$activity->name_en;
	}else{
		$activity_name=$activity->name;
	}
	$activities_name[$activity->id]=$activity_name;
}
if($current_year_3==2016){
	//$activities_actions[$activity->id] = array('percent'=>$percent,'activity_id'=>$activity->id,'activity_color'=>$activity->color,'activity_name'=>$activity_name);
	$activities_actions[1]=array('percent'=>44.6,'activity_id'=>1,'activity_color'=>'6da9a4','activity_name'=>$activities_name[1]);
	$activities_actions[2]=array('percent'=>15.3,'activity_id'=>2,'activity_color'=>'be5d33','activity_name'=>$activities_name[2]);
	$activities_actions[4]=array('percent'=>2.1,'activity_id'=>4,'activity_color'=>'ab6ad1','activity_name'=>$activities_name[4]);
	$activities_actions[5]=array('percent'=>6.5,'activity_id'=>5,'activity_color'=>'ff9933','activity_name'=>$activities_name[5]);
	$activities_actions[6]=array('percent'=>3.1,'activity_id'=>6,'activity_color'=>'41c241','activity_name'=>$activities_name[6]);
	$activities_actions[7]=array('percent'=>2,'activity_id'=>7,'activity_color'=>'6851ff','activity_name'=>$activities_name[7]);
	$activities_actions[8]=array('percent'=>11.9,'activity_id'=>8,'activity_color'=>'1d9ee5','activity_name'=>$activities_name[8]);
	$activities_actions[9]=array('percent'=>21.7,'activity_id'=>9,'activity_color'=>'c29950','activity_name'=>$activities_name[9]);
	$activities_actions[10]=array('percent'=>3.6,'activity_id'=>10,'activity_color'=>'d1b700','activity_name'=>$activities_name[10]);
	$activities_actions[11]=array('percent'=>15.7,'activity_id'=>11,'activity_color'=>'dfb0dc','activity_name'=>$activities_name[11]);
}else{
	$activities_actions=array();
	$total_action_count = 0;
	foreach($activities_object as $activity) {
		//remote db
		$where = "a.team_id>0 AND aa.published=1 AND a.published=1 AND aa.action_id>0 AND find_in_set('" . $activity->id . "',aa.activities) AND aa.action_date_start>='" . $current_year_3 . "-01-01 00:00:00' AND aa.action_date_start<='" . date('Y-m-d H:i:s') . "'";
		$activityClass = new RemotedbActivity();
		$activities_count = $activityClass->getActivitiesCount($where);
	    $total_action_count += $activities_count;
	    $activity->action_count = $activities_count;
    }

	foreach($activities_object as $activity) {
		//echo $actions_count.'<br />';
		if($activity->action_count>0){
			$percent=round((100/$total_action_count)*($activity->action_count),1);
		}else{
			$percent=0;
		}
		//echo '(100/'.$slider_3_all.')*('.$actions_count.'+round(851/100*'.$test_array[$activity->id].'))<br />';
		//testttttt
		//$percent=$test_array[$activity->id];
		if($lang_code=='en'){
			$activity_name=$activity->name_en;
		}else{
			$activity_name=$activity->name;
		}
		$activities_actions[$activity->id] = array('percent'=>$percent,'activity_id'=>$activity->id,'activity_color'=>$activity->color,'activity_name'=>$activity_name);
	}
}
sortBy('percent',$activities_actions);







//language
$doc = JFactory::getDocument();
$lang_code_array=explode('-',$doc->language);
$lang_code=$lang_code_array[0];


?>
   <div class="module module--synathina">

         <div class="gallery gallery--charts">
						<div class="chart chart--1">
							 <div class="chart-content">
									<div class="chart-title">
										 <div class="module-cells module-cells--halfs text-right container">
												<h3><?php echo JText::_('MOD_SLIDER_1_TITLE');?>:</h3>
												<span class="label chart-overview-value"><?php echo $slider_1_all; ?> <?php echo JText::_('MOD_SLIDER_1_ACTIVITIES');?></span>
										 </div>
									</div>
									<div class="chart-com">
										 <!--<div class="chart-com-column"></div>-->
<?php
	$c=1;
	$f=1;
	for($i=2013; $i<=date('Y'); $i++){
		echo '					 <div class="chart-com-column" rel="js-chart-column">
												<div class="bar bar--vertical">
													 <div id="graph-'.$f.'" class="bar-value bar--orange" data-type="1" data-value="'.${'slider_1_percent_total_'.$i}.'" data-pair-percent="'.$c.'" style="height: '.${'slider_1_percent_total_'.$i}.'%"></div>
														<div id="graph-'.($f+1).'" class="bar-value pattern pattern--dots" data-type="2" data-value="'.${'slider_1_percent_sub_'.$i}.'" style="height: '.${'slider_1_percent_sub_'.$i}.'%;"></div>
												</div>
												<div class="chart-com-label">
													 <span class="value-x is-block">'.$i.'</span>
													 <span class="value-sum is-block"><span data-pair-sum="'.$c.'">'.($i>2013?'+':'').${'slider_1_'.$i}.'</span></span>
												</div>
										 </div>';
		$c++;
		$f++;
		$f++;
	}
?>

									</div>
									<div class="slider_index_container">
									<div class="slider_index">
										<div class="slider_index_row">
											<div class="slider_index_col red-box">
											</div>
											<div class="slider_index_col">
											<?php echo JText::_('MOD_SLIDER_1_ACTIVITIES_1');?>
											</div>
										</div>
										<div class="slider_index_row">
											<div class="slider_index_col trans-box">
											</div>
											<div class="slider_index_col">
											<?php echo JText::_('MOD_SLIDER_1_ACTIVITIES_2');?>
											</div>
										</div>
									</div>
									</div>
							 </div>
						</div>
            <div class="chart chart--2">
							<div class="container-fluid chart-2" style="background-color: #FFF;">
								<div class="row chart-title">
									<h3><?php echo JText::_('MOD_SLIDER_2_TITLE');?></h3>
									<span class="actions"><?php echo $slider_2_all; ?> <?php echo JText::_('MOD_SLIDER_2_TEAMS');?> </span>
								</div>
								<graph id="teams" class="teams_slider_2" width="" height="" >
<?php
	$c=1;
	$f=0;
	$colors_array=array('fd5e5b','ca69ba','ffdc3e','68d3bf');
	for($i=2013; $i<=date('Y'); $i++){
		echo '<point name="'.$i.'" valuex="'.$c.'" valuey="'.${'slider_2_'.$i}.'" color1="#666"  color2="#'.$colors_array[$f].'" notex="" notey="'.${'slider_2_'.$i}.' '.JText::_('MOD_SLIDER_2_TEAMS1').'" ></point>';
		$c++;
		$f++;
		if($f==4){
			$f=0;
		}
	}
?>
								</graph>
								<svg id="output" width="100" height="100" style="overflow:visible">
								</svg>
								<script>
									/**
									 * innerHTML property for SVGElement
									 * Copyright(c) 2010, Jeff Schiller
									 *
									 * Licensed under the Apache License, Version 2
									 *
									 * Works in a SVG document in Chrome 6+, Safari 5+, Firefox 4+ and IE9+.
									 * Works in a HTML5 document in Chrome 7+, Firefox 4+ and IE9+.
									 * Does not work in Opera since it doesn't support the SVGElement interface yet.
									 *
									 * I haven't decided on the best name for this property - thus the duplication.
									 */

									(function() {
									var serializeXML = function(node, output) {
										var nodeType = node.nodeType;
										if (nodeType == 3) { // TEXT nodes.
											// Replace special XML characters with their entities.
											output.push(node.textContent.replace(/&/, '&amp;').replace(/</, '&lt;').replace('>', '&gt;'));
										} else if (nodeType == 1) { // ELEMENT nodes.
											// Serialize Element nodes.
											output.push('<', node.tagName);
											if (node.hasAttributes()) {
												var attrMap = node.attributes;
												for (var i = 0, len = attrMap.length; i < len; ++i) {
													var attrNode = attrMap.item(i);
													output.push(' ', attrNode.name, '=\'', attrNode.value, '\'');
												}
											}
											if (node.hasChildNodes()) {
												output.push('>');
												var childNodes = node.childNodes;
												for (var i = 0, len = childNodes.length; i < len; ++i) {
													serializeXML(childNodes.item(i), output);
												}
												output.push('</', node.tagName, '>');
											} else {
												output.push('/>');
											}
										} else if (nodeType == 8) {
											// TODO(codedread): Replace special characters with XML entities?
											output.push('<!--', node.nodeValue, '-->');
										} else {
											// TODO: Handle CDATA nodes.
											// TODO: Handle ENTITY nodes.
											// TODO: Handle DOCUMENT nodes.
											throw 'Error serializing XML. Unhandled node of type: ' + nodeType;
										}
									}
									// The innerHTML DOM property for SVGElement.
									Object.defineProperty(SVGElement.prototype, 'innerHTML', {
										get: function() {
											var output = [];
											var childNode = this.firstChild;
											while (childNode) {
												serializeXML(childNode, output);
												childNode = childNode.nextSibling;
											}
											return output.join('');
										},
										set: function(markupText) {
											// Wipe out the current contents of the element.
											while (this.firstChild) {
												this.removeChild(this.firstChild);
											}

											try {
												// Parse the markup into valid nodes.
												var dXML = new DOMParser();
												dXML.async = false;
												// Wrap the markup into a SVG node to ensure parsing works.
												sXML = '<svg xmlns=\'http://www.w3.org/2000/svg\'>' + markupText + '</svg>';
												var svgDocElement = dXML.parseFromString(sXML, 'text/xml').documentElement;

												// Now take each node, import it and append to this element.
												var childNode = svgDocElement.firstChild;
												while(childNode) {
													this.appendChild(this.ownerDocument.importNode(childNode, true));
													childNode = childNode.nextSibling;
												}
											} catch(e) {
												throw new Error('Error parsing XML string');
											};
										}
									});

									// The innerSVG DOM property for SVGElement.
									Object.defineProperty(SVGElement.prototype, 'innerSVG', {
										get: function() {
											return this.innerHTML;
										},
										set: function(markupText) {
											this.innerHTML = markupText;
										}
									});

									})();
									var element = document.getElementById("teams");
									var browser_width = window.innerWidth;
									if(browser_width<360){
										element.setAttribute("width", "200");
										element.setAttribute("height", "350");
										offsetx=24;
									}else if(browser_width<420){
										element.setAttribute("width", "240");
										element.setAttribute("height", "350");
										offsetx=28;
									}else if(browser_width<520){
										element.setAttribute("width", "320");
										element.setAttribute("height", "350");
										offsetx=24;
									}else if(browser_width<768){
										element.setAttribute("width", "420");
										element.setAttribute("height", "400");
										offsetx=34;
									}else{
										element.setAttribute("width", "600");
										element.setAttribute("height", "400");
										offsetx=40;
									}
									offsetx2=0;
									gr=document.getElementById('teams');
									ou=document.getElementById('output');

									offsety=0;
									h=gr.getAttribute('height')*1;
									w=gr.getAttribute('width')*1;

									ou.style.width=w+'px';
									ou.style.height=(h+120)+'px';

									i=0;
									dots="";
									grid="";
									legend="";
									texts="";
									lines="";

									ystep=w/(gr.children.length+2);
									g1='<line x1="'+offsetx2+'" y1="'+h+'" x2="'+(offsetx2+w)+'" y2="'+h+'"  style="stroke:rgb(0,0,0);stroke-width:2" />';
									g2='<line x1="'+offsetx2+'" y1="'+'0'+'" x2="'+offsetx2+'" y2="'+h+'"  style="stroke:rgb(0,0,0);stroke-width:2" />';

									g3='';

									i=1;
									while(i*50<h){
										g3+='<line x1="'+offsetx2+'" y1="'+(h-i*50)+'" x2="'+(offsetx2+w)+'" y2="'+(h-i*50)+'"  style="stroke:rgb(220,220,220);stroke-width:1" />';
										i++;
									}

									i=1
									while(i*ystep<w){
										g3+='<line x1="'+(offsetx2+i*ystep)+'" y1="'+'0'+'" x2="'+(offsetx2+i*ystep)+'" y2="'+h+'"  style="stroke:rgb(220,220,220);stroke-width:1" />';
										i++;
									}


									i=0;


									grid+=g1+g2+g3;
									while(i<gr.children.length){
										//place grey dots
										y1=offsety+h-gr.children[i].getAttribute('valuey');
										x1=offsetx+(gr.children[i].getAttribute('valuex')*ystep);
										c1=gr.children[i].getAttribute('color1');
										c2=gr.children[i].getAttribute('color2');

										t1=gr.children[i].getAttribute('name')
										t2=gr.children[i].getAttribute('notey')
										t3=gr.children[i].getAttribute('notex')


										l1='';
										if(i>0){
											l1='<line x1="'+px+'" y1="'+py+'" x2="'+x1+'" y2="'+y1+'" style="stroke:rgb(0,0,0);stroke-width:1" />'
										}

											l2='<line x1="'+x1+'" y1="'+(h+25)+'" x2="'+x1+'" y2="'+(h+45)+'" style="stroke:rgb(0,0,0);stroke-width:1" />'

										px=x1;
										py=y1;
										dot1="<circle cx=\""+x1+"\" cy=\""+y1+"\" r=\"7\" fill=\""+c1+"\" />";
										dot2="<circle cx=\""+x1+"\" cy=\""+(h+offsety)+"\" r=\"7\" fill=\""+c2+"\" />";
										lines+=l1+l2;
										dots+=dot1+dot2;

										text1= '<text x="'+x1+'" y="'+(h+80)+'" fill="#666" text-anchor="middle" class="slider_2_middle_text" >'+t1+'</text>';
										text2='<text x="'+(x1-10)+'" y="'+(y1-10)+'" fill="#666" text-anchor="end" class="slider_2_end_text" >'+t2+'</text>';
										text3= '<text x="'+x1+'" y="'+(h+100)+'" fill="#666" text-anchor="start" style="font-size:12px">'+t3+'</text>';

										texts+=text1+text2+text3;

										i++;
									}
									ou.innerHTML+=grid;
									ou.innerHTML+=lines;
									ou.innerHTML+=dots;
									ou.innerHTML+=texts;
								</script>
							</div>
            </div>
            <div class="chart chart--3">
               <!--<img src="images/template/slide-3.jpg" alt="" />-->
							<div class="container-fluid chart-3">
								<div class="row chart-title">
									<h3><?php echo JText::_('MOD_SLIDER_3_TITLE').' '.$current_year_3;?>:</h3>
									<span class="actions"><?php echo $slider_3_all; ?> <?php echo JText::_('MOD_SLIDER_1_ACTIVITIES');?> </span>
								</div>
<?php
	for($i=0; $i<count($activities_actions); $i++){
		echo '<div class="row">
						<div class="d-title" style="color: #'.$activities_actions[$i]['activity_color'].';">
								<span>'.$activities_actions[$i]['percent'].'%</span>
								 '.$activities_actions[$i]['activity_name'].'
							</div>
							<div class="graph-cont">
									<div class="graph">
											<div style="background-color: #'.$activities_actions[$i]['activity_color'].';width:'.$activities_actions[$i]['percent'].'%"></div>
									</div>
							</div>
					</div>';
	}
?>
							</div>
            </div>
         </div>
   </div>