<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teamsexports
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
							 ->setTitle("Teams export ". date('Y-m-d'))
							 ->setSubject("Teams export". date('Y-m-d'))
							 ->setDescription("Teams export")
							 ->setKeywords("synathina teams")
							 ->setCategory("");

$styleArray = ['font' => ['bold' => true]];

$db = JFactory::getDBO();

if (isset($_POST['export'])) {
	$inner_join = '';
	$where = '';
	$group_by = '';
	$areas = [];

	for ($i = 1; $i < 8; $i++) {
		if (isset($_POST['area_' . $i])) {
			$areas[] = $i;
		}
	}

	if ($areas) {
		//return teams with activities in specific areas

		//remote db
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->connect();

		$query = "SELECT team_id
					FROM #__actions
					WHERE action_id > 0 AND area IN (".implode(',', $areas).") AND origin = 1 AND published = 1
					GROUP BY team_id";
		$db_remote->setQuery($query);
		$team_ids = $db_remote->loadColumn();

		$query = "SELECT t.*,u.email AS uemail
							FROM #__teams AS t
							INNER JOIN #__users AS u ON u.id=t.user_id
							WHERE u.block=0 AND u.activation='' AND t.published=1 AND t.id IN (".implode(',', $team_ids).")
							ORDER BY t.created DESC ";
		$db->setQuery($query);
		$teams = $db->loadObjectList();
	} else {
		//return all teams
		$query = "SELECT t.*,u.email AS uemail
							FROM #__teams AS t
							INNER JOIN #__users AS u ON u.id=t.user_id
							WHERE u.block=0 AND u.activation='' AND t.published=1 ORDER BY t.created DESC ";
		$db->setQuery($query);
		$teams = $db->loadObjectList();
	}
}

//export to excel
if (isset($_POST['export'])) {
	$get_activities = "SELECT * FROM cemyx_team_activities WHERE published = 1";
	$db->setQuery($get_activities);
	$activities = $db->loadObjectList();
	$activities_data = [];
	foreach ($activities as $activity) {
		$activities_data[$activity->id] = $activity->name;
	}

	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A1', 'ΟΝΟΜΑ ΟΜΑΔΑΣ')
	            ->setCellValue('B1', 'USER EMAIL')
	            ->setCellValue('C1', 'ΗΜΕΡΟΜΗΝΙΑ ΕΓΓΡΑΦΗΣ')
	            ->setCellValue('D1', 'ΝΟΜΙΚΗ ΜΟΡΦΗ')
							->setCellValue('E1', 'ΘΕΜΑΤΙΚΕΣ')
							->setCellValue('F1', 'WEBSITE')
							->setCellValue('G1', 'FACEBOOK PAGE')
							->setCellValue('H1', 'ΥΠΕΥΘΥΝΟΣ ΕΠΙΚΟΙΝΩΝΙΑΣ 1')
							->setCellValue('I1', 'EMAIL 1')
							->setCellValue('J1', 'ΤΗΛΕΦΩΝΟ 1')
							->setCellValue('K1', 'ΥΠΕΥΘΥΝΟΣ ΕΠΙΚΟΙΝΩΝΙΑΣ 2')
							->setCellValue('L1', 'EMAIL 2')
							->setCellValue('M1', 'ΤΗΛΕΦΩΝΟ 2')
							->setCellValue('N1', 'ΥΠΕΥΘΥΝΟΣ ΕΠΙΚΟΙΝΩΝΙΑΣ 3')
							->setCellValue('O1', 'EMAIL 3')
							->setCellValue('P1', 'ΤΗΛΕΦΩΝΟ 3');

	$alpha_array=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
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

		$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$i, htmlspecialchars_decode($team->name))
								->setCellValue('B'.$i, $team->uemail)
								->setCellValue('C'.$i, $team->created)
								->setCellValue('D'.$i, ($team->legal_form==1?'ΝΑΙ':'ΟΧΙ'))
								->setCellValue('E'.$i, implode(', ', $team_activity_names))
								->setCellValue('F'.$i, $team->web_link)
								->setCellValue('G'.$i, $team->fb_link)
								->setCellValue('H'.$i, $team->contact_1_name)
								->setCellValue('I'.$i, $team->contact_1_email)
								->setCellValue('J'.$i, $team->contact_1_phone)
								->setCellValue('K'.$i, $team->contact_2_name)
								->setCellValue('L'.$i, $team->contact_2_email)
								->setCellValue('M'.$i, $team->contact_2_phone)
								->setCellValue('N'.$i, $team->contact_3_name)
								->setCellValue('O'.$i, $team->contact_3_email)
								->setCellValue('P'.$i, $team->contact_3_phone);
		$i++;
	}
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Teams export '.date('Y-m-d'));

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="teams_export_'.date('Y-m-d').'.xlsx"');
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
}

?>
<form action="<?php echo JRoute::_('index.php?option=com_teamsexports'); ?>" method="post" name="adminForm" id="adminForm">
	<h3>Φίλτρα</h3>
	<h4>Δημοτικά διαμερίσματα</h4>
<?php for ($i=1; $i < 8; $i++) { ?>
	<span style="padding: 5px;"><label style="display: inline;" for="area_<?php echo $i; ?>"><?php echo $i; ?>ο</label> <input type="checkbox" id="area_<?php echo $i; ?>" name="area_<?php echo $i; ?>" /></span>
<?php } ?>
	<br /><br />
		<input type="submit" name="export" value="Export" />
</form>
