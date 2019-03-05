<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_supportersexports
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');

require_once 'php-class/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Synathina platform")
							 ->setLastModifiedBy("administrator")
							 ->setTitle("Supporters export ". date('Y-m-d'))
							 ->setSubject("Supporters export". date('Y-m-d'))
							 ->setDescription("Supporters export")
							 ->setKeywords("synathina supporters")
							 ->setCategory("");
							 
$styleArray = array(
    'font'  => array(
        'bold'  => true
		));							 

$db = JFactory::getDBO();

$query = "SELECT u.email AS uemail, u.name AS uname, t.name, t.created, t.activities, t.org_donation, t.hidden 
						FROM #__users AS u 
						JOIN #__teams AS t ON t.user_id=u.id 
						WHERE t.published=1 AND t.support_actions=1 ORDER BY t.created DESC ";

$db->setQuery($query);
$teams = $db->loadObjectList();	
$data = array();

$get_activities = "SELECT * FROM cemyx_team_activities WHERE published = 1";
$db->setQuery($get_activities);
$activities = $db->loadObjectList();
$activities_data = [];
foreach ($activities as $activity) {
	$activities_data[$activity->id] = $activity->name;
}

$get_donations = "SELECT * FROM cemyx_team_donation_types WHERE published = 1";
$db->setQuery($get_donations);
$donations = $db->loadObjectList();
$donations_data = [];
foreach ($donations as $donation) {
  $donations_data[$donation->id] = $donation->name;
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ΟΝΟΜΑ ΟΜΑΔΑΣ')
            ->setCellValue('B1', 'USER NAME')
            ->setCellValue('C1', 'USER EMAIL')
            ->setCellValue('D1', 'ΚΑΤΗΓΟΡΙΕΣ ΥΠΟΣΤΗΡΙΞΗΣ')
            ->setCellValue('E1', 'ΘΕΜΑΤΙΚΕΣ')
            ->setCellValue('F1', 'ΑΝΩΝΥΜΟΣ ΥΠΟΣΤΗΡΙΚΤΗΣ');

$alpha_array=array('A','B','C','D','E','F','G');
for($a=0; $a<count($alpha_array); $a++){
	$objPHPExcel->getActiveSheet()->getStyle($alpha_array[$a].'1')->applyFromArray($styleArray);	
	//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($alpha_array[$a])->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($alpha_array[$a])->setWidth(50);
}

$i=2;
foreach($teams as $team){
	$team_activities = explode(',', $team->activities);
	$team_activity_names = [];
	foreach ($team_activities as $activity_id) {
		if (isset($activities_data[$activity_id])) {
      $team_activity_names[] = $activities_data[$activity_id];
    }
	}

  $team_donations = explode(',', $team->org_donation);
  $team_donation_names = [];
  foreach ($team_donations as $donation_id) {
    if (isset($donations_data[$donation_id])) {
      $team_donation_names[] = $donations_data[$donation_id];
    }
  }

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$i, htmlspecialchars_decode($team->name))
							->setCellValue('B'.$i, $team->uname)
							->setCellValue('C'.$i, $team->uemail)
							->setCellValue('D'.$i, implode(', ', $team_donation_names))
							->setCellValue('E'.$i, implode(', ', $team_activity_names))
							->setCellValue('F'.$i, $team->hidden? 'ΝΑΙ' : 'ΟΧΙ');
	$i++;
}




// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Supporters export '.date('Y-m-d'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="supporters_export_'.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>
