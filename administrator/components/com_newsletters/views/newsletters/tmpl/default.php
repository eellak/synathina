<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsletters
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
							 ->setTitle("Newsletters export ". date('Y-m-d'))
							 ->setSubject("Newsletters export". date('Y-m-d'))
							 ->setDescription("Newsletters export")
							 ->setKeywords("synathina Newsletters")
							 ->setCategory("");
							 
$styleArray = array(
    'font'  => array(
        'bold'  => true
		));							 

$db = JFactory::getDBO();

$query = "SELECT email 
					FROM #__newsletters
					WHERE status=1 ORDER BY id DESC ";
$db->setQuery($query);
$newsletters1 = $db->loadObjectList();	

$query = "SELECT t.contact_1_email AS email 
					FROM #__teams AS t
					INNER JOIN #__users AS u ON u.id=t.user_id
					WHERE u.block=0 AND u.activation='' AND t.published=1 AND t.newsletter=1 ORDER BY t.created DESC ";					
$db->setQuery($query);
$newsletters2 = $db->loadObjectList();	

$newsletters = (object) array_merge((array) $newsletters1, (array) $newsletters2);

// Add some data
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
$i=1;
foreach($newsletters as $newsletter){
	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$i, $newsletter->email);	
	$i++;
}




// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Newsletters export '.date('Y-m-d'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="newsletters_export_'.date('Y-m-d').'.xlsx"');
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
