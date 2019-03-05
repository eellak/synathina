<?php
function synathina_email($type, $parameters, $emails, $attachment)
{
	$db = JFactory::getDBO();
	$mailer = JFactory::getMailer();
	$config = JFactory::getConfig();

	//get email from db
	$query = "SELECT * FROM #__emails WHERE type='".$type."' LIMIT 1";
	$db->setQuery($query);
	$custom_emails = $db->loadObjectList();

	foreach ($custom_emails as $custom_email) {
		$sender = array(
			$custom_email->from_email,
			$custom_email->to_email,
			$custom_email->from_name
		);

		$mailer->setSender($sender);
		$recipients = array();
		if ($custom_email->to_email != '') {
			$email_array = explode(',', $custom_email->to_email);
			for ($c = 0; $c < count($email_array); $c++) {
				$recipients[] = $email_array[$c];
			}
		} elseif (!empty($emails)) {
			for ($c = 0; $c < count($emails); $c++) {
				$recipients[] = $emails[$c];
			}
		} else {
			$recipients = array($config->get( 'mailfrom' ));
		}

		$mailer->setSubject($custom_email->subject);
		if ($config->get('dev_mode')) {
			$mailer->setSubject('DEV MODE: ' . $custom_email->subject);
		}

		$body = '<body style="margin:0px auto; padding:0px; background-color:#FFFFFF; color:#5d5d5d; font-family:Arial; outline:none; font-size:12px;" bgcolor="#FFFFFF">
							<div style="background-color:#FFFFFF;margin:0px auto; font-family:Arial;color:#5d5d5d;">
								<div style="margin:0px auto; width:640px; text-align:left; background-color:#ebebeb; font-family:Arial; padding:20px;color:#5d5d5d;">
								<div style="text-align:right;"><img src="'.$config->get( 'live_site' ).'/images/template/synathina_logo.jpg" alt="συνΑθηνά" /></div>
								<div style="font-size: 18px;font-weight:bold; color:#05c0de;padding-bottom: 10px;">'.$custom_email->email_title.'</div>';
		if ($config->get('dev_mode')) {
			$body .= '<div>'.implode(',', $recipients).'</div>';
		}
		$s_array = array();
		for ($i = 0; $i < count($parameters); $i++) {
			$s_array[] = '%s'.($i + 1);
		}
		if (!empty($s_array)) {
			$body.= str_replace($s_array, $parameters, $custom_email->body);
		} else {
			$body.= $custom_email->body;
		}
		$body .= '	<div style="font-size: 10px;">
						<p>Παρακαλούμε μην απαντήσετε σε αυτό το αυτοματοποιημένο email. Για να επικοινωνήσετε με την ομάδα του συνΑθηνά, μπορείτε να στείλετε email στο synathina@athens.gr ή στο synathinaplatform@gmail.com.</p>
					</div>';
		$body .= '</div></div></body>';

		if ($config->get('dev_mode')) {
			$recipients = array($config->get('dev_email'));
		}

		$mailer->addRecipient($recipients);

		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($body);
		if ($attachment != '') {
			$mailer->addAttachment(JPATH_BASE . '/' . $attachment);
		}
		$send = $mailer->Send();
		return true;
	}
}

function get_first_num_of_words($string, $num_of_words)
{
	$string = preg_replace('/\s+/', ' ', trim($string));
	$words = explode(" ",  $string);
	if ($num_of_words > count($words)) {
		$num_of_words = count($words);
	}
	$new_string = "";
	for ($i = 0; $i < $num_of_words; $i++) {
		$new_string .= $words[$i] . " ";
	}

	return trim($new_string);
}
?>