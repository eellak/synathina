<?php

/**
 * @package plugin AdminExile
 * @copyright (C) 2010-2013 Michael Richey
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
require_once(JPATH_PLUGINS.'/system/adminexile/classes/aehelper.class.php');
class plgSystemAdminExile extends JPlugin {

    private $_app;
    private $_ip;
    private $_net;
    private $_key;
    private $_log;
    private $_pass;
    private $_gmp;

    public function __construct(&$subject, $config = array()) {
        parent::__construct($subject, $config);
        $this->_app = JFactory::getApplication();
        if ($this->_app->isAdmin() && JFactory::getUser()->guest) {
            $this->_ip = $this->_getIP();
            $this->_log = $this->_ipcheck($this->_ip);
            $this->_pass = false;
        }
    }

    public function onAfterInitialise() {
        if ($this->_app->isAdmin()) {
            $this->_net = false;
            JFactory::getLanguage()->load('plg_system_adminexile', JPATH_ADMINISTRATOR);
            if (JFactory::getUser()->id) {
                // check for block removals
                if ($this->_app->input->get->get('adminexile_removeblock', false)) {
                    $ip = $this->_app->input->get->get('ip', 0);
                    $firstattempt = $this->_app->input->get->get('firstattempt', 0, 'RAW');
                    if ($this->_clearBlocks(array('ip' => $ip, 'firstattempt' => $firstattempt))) {
                        header('Content-Type: application/json');
                        die(json_encode(array('success' => true)));
                    }
                }
                $this->_pass = true;
                return true; // user is already logged in
            } else {
                if ($this->params->get('maillink', 0)) {
                    if (@$email = $this->_app->input->get->get('email', false)) {
                        if ($this->params->get('maillink', true) && count($this->params->get('maillinkgroup', array()))) {
                            $this->_maillink($email, $this->params->get('maillinkgroup', array()));
                            $this->_redirect();
                            return true;
                        }
                    }
                }
            }

            $this->_key = $this->params->get('key', 'adminexile');
            if ($this->_app->getUserState("plg_sys_adminexile.$this->_key", false)) {
                $this->_pass = true;
                if (isset($_GET[$this->_key]))
                    $this->_app->redirect(JURI::root() . '/administrator'); // hide the key as soon as possible
                return true; // user provided a key and should be shown the login form
            }

            if ($this->params->get('bruteforce', 0) && $this->_log && $this->_log->penalty != 0)
                return true;

            if($this->params->get('tmpwhitelist',0)) {
                if($this->_getTmpWhitelist($this->_ip)) {
                    $this->_pass = true;
                    $this->_authorized();
                    return true;
                }
            }
            if ($this->params->get('ipsecurity', 0)) {
                $this->_gmp = AdminExileHelper::gmp();
                $ip = $this->_blackwhite($this->_ip);
                if ($ip) {
                    if ($ip === true) {
                        $this->_pass = true;
                        $this->_authorized();
                        return true;
                    } else {
                        $this->_net = $ip;
                        return true;
                    }
                }
            }

            if ($this->_keyauth()) {
                $this->_pass = true;
                $this->_authorized();
                return true;
            }
            return true;
        } else {
            $this->_frontrestrict();
        }
    }
                
    public function onUserLogout($user, $options = array()) {
        if ($this->_app->isAdmin() && JFactory::getUser()->id == $user['id']) {
            if($this->params->get('tmpwhitelist',0)) {
                $this->_setTmpWhitelist();
                $tmpwhitelist = true;
            } else {
                $tmpwhitelist = false;
            }
            if (!$this->params->get('ipsecurity', 0) && !$this->_blackwhite($this->_ip) && !$tmpwhitelist) {
                JFactory::getSession()->destroy();
                $this->_pass = true;
                $this->_redirect(true);
                return true;
            }
        }
    }

    public function onUserLoginFailure() {
        if ($this->_app->isSite())
            return true;
        if($this->params->get('tmpwhitelist',0)) $this->_clearTmpWhitelist ($this->_getIP());
        $this->_logit(true);
        if ($this->_log && $this->_log->penalty) 
            $this->_redirect();
        return true;
    }

    public function onBeforeRender() {
        if ($this->_app->isAdmin() && !$this->_pass) {
            $this->_logit();
            $this->_redirect();
            return true;
        }
    }

    private function _getIP() {
        $ip = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : (
            getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : (
                getenv('HTTP_X_FORWARDED') ? getenv('HTTP_X_FORWARDED') : (
                    getenv('HTTP_FORWARDED_FOR') ? getenv('HTTP_FORWARDED_FOR') : (
                        getenv('HTTP_FORWARDED') ? getenv('HTTP_FORWARDED') : (
                            getenv('REMOTE_ADDR')
                        )
                    )
                )
            )
        );
        return $ip;
    }

    private function _frontrestrict() {
        if (
            !$this->params->get('frontrestrict', 0) || // frontrestrict is disabled
            !JFactory::getUser()->guest || // user is already logged in
//            $this->_app->input->get('option') != 'com_users' || // can't get option on routed pages pre-dispatch
            $this->_app->input->get('task') != 'user.login' // this isn't a login attempt
        )
            return true;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__users')->where('username=' . $db->quote($this->_app->input->post->get('username', '')));
        $db->setQuery($query);
        $user = JFactory::getUser($db->loadResult());
        $restrictgroup = $this->params->get('restrictgroup', array());
        foreach ($restrictgroup as $group) {
            if (in_array($group, $user->groups)) {
                // this will give a nice non-descript error
                $this->_app->input->set('password', ''); // this doesn't work because jinput sucks balls
                $_POST['password'] = ''; // jrequest rocked!
                return true;
            }
        }
        return true;
    }

    private function _keyauth() {
        if (!isset($_GET[$this->_key]))
            return false;
        if ($this->params->get('twofactor', false)) {
            return ($this->params->get('keyvalue', false) === $_GET[$this->_key]);
        } else {
            return true;
        }
        return false;
    }

    private function _maillink($username = false, $groups = array()) {
        if (!is_bool($this->_blackwhite($this->_ip))) {
            $this->_logit();
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__users')->where('username=' . $db->quote($username));
        $db->setQuery($query);
        $userid = $db->loadResult();
        if (is_numeric($userid)) {
            $this->_pass = true;
            $user = JFactory::getUser($userid);
            $authorized = false;
            foreach ($user->groups as $group)
                if (in_array($group, $groups))
                    $authorized = true;

            if ($authorized) {

                // building the /administrator URL
                $url = parse_url(JURI::root());
                $url['path'] = explode('/', preg_replace(array('/(^\/)/', '/(\/$)/'), '', $url['path']));
                $url['path'][] = 'administrator';
                $url['path'] = '/'.implode('/', $url['path']);
                $key = urlencode($this->params->get('key', 'adminexile'));
                if ($this->params->get('twofactor', false)) {
                    $url['query'] = http_build_query(array($key => urlencode($this->params->get('keyvalue', false))));
                } else {
                    $url['query'] = $key;
                }
                $secureurl = rwhttp_build_url($url);
                $subject = JText::sprintf('PLG_SYS_ADMINEXILE_EMAIL_SUBJECT', $this->_app->getCfg('sitename'));
                $body = JText::sprintf('PLG_SYS_ADMINEXILE_EMAIL_BODY', $secureurl, $user->email, $user->username, $this->_ip);
                $this->_sendMail($user->email, $subject, $body);
            }
        }
    }

    private function _authorized() {
        if ($this->params->get('bruteforce', 0)) {
            $this->_clearBlocks(array('ip' => $this->_ip));
        }
        $this->_app->setUserState("plg_sys_adminexile.$this->_key", true);
    }

    private function _blackwhite($ip) {
        $whitelistnet = array();
        $blacklistnet = array();
        $whitelist = array();
        $blacklist = array();
        foreach (array('whitelist', 'blacklist') as $list) {
            $listnet = $list . 'net';
            $listdefault = JText::_('PLG_SYS_ADMINEXILE_DEFAULT_'.strtoupper($list));
            $listvalue = $this->params->get($list, $listdefault);
            ${$list} = AdminExileHelper::ipArray($listvalue);
            foreach (${$list} as $key => $item) {
                $item = trim($item);
                if (preg_match('/\//', $item)) {
                    unset(${$list}[$key]);
                    array_push(${$listnet}, $item);
                } else {
                    ${$list}[$key] = $item;
                }
            }
        }
        if (in_array($ip, $whitelist))
            return true;
        if (in_array($ip, $blacklist))
            return $ip;
        if (count(array_merge($whitelistnet, $blacklistnet))) {
            $requires = array('IPv6Net'=>'IPv6Net','simplecidr'=>'SimpleCIDR');
            $require = $this->_gmp ? 'IPv6Net' : 'simplecidr';
            if(!class_exists($requires[$require])) require_once(JPATH_PLUGINS.'/system/adminexile/classes/'.$require.'.class.php');
            foreach ($whitelistnet as $net) {
                $ipnet = $this->_bwnet($net);
                if ($ipnet && $ipnet->contains($ip))
                    return true;
            }
            foreach ($blacklistnet as $net) {
                $ipnet = $this->_bwnet($net);
                if ($ipnet && $ipnet->contains($ip))
                    return $net;
            }
        }
        return false;
    }

    private function _bwnet($net) {
        try {
            $ret = $this->_gmp?(new IPv6Net($net)):SimpleCIDR::getInstance($net);
        } catch (Exception $e) {
            error_log("AdminExile cannot process ".$net." due to:".$e->getMessage());
            $ret = false;
        }
        return $ret;
    }

    private function _ipcheck($ip) {
        $this->_clearBlocks(); // clear expired blocks
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__plg_system_adminexile')->where('ip = ' . $db->quote($ip));
        $db->setQuery($query);
        $result = $db->loadObject();
        $ret = $result ? $result : false;
        return $ret;
    }

    private function _clearBlocks($ip = false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__plg_system_adminexile');
        if ($ip) {
            foreach ($ip as $var => $value)
                $ip[$var] = $var . '=' . $db->quote($value);
            $query->where($ip);
        } else {
            $query->where('penalty > 0 AND TIME_TO_SEC(TIMEDIFF(ADDDATE(`lastattempt`,INTERVAL `penalty` MINUTE),NOW())) <= 0');
        }
        $db->setQuery($query);
        $db->query();
        return true;
    }
    
    private function _getTmpWhitelist($ip) {
        $this->_clearTmpWhitelist();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('ip')->from('#__plg_system_adminexile_tmpwhitelist')->where('ip = '.$db->quote($ip). 'AND expire >= NOW()');
        $db->setQuery($query);
        $result = $db->loadResult();
        return ($result==$ip);
    }
    
    private function _setTmpWhitelist() {
        $this->_clearTmpWhitelist();
        $ip = $this->_getIP();
        $tmpperiod = $this->params->get('tmpperiod',300);
        $db = JFactory::getDbo();
        $query = 'REPLACE INTO #__plg_system_adminexile_tmpwhitelist (`ip`,`expire`) VALUES ('.$db->quote($ip).',DATE_ADD(NOW(), INTERVAL '.$tmpperiod.' SECOND))';
        $db->setQuery($query);
        $db->execute();
        return true;  
    }
    
    private function _clearTmpWhitelist($ip = null) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__plg_system_adminexile_tmpwhitelist')->where(($ip?'ip = '.$db->quote($ip):'expire <= NOW()'));
        $db->setQuery($query);
        $db->query();
        return true;        
    }

    private function _logit($loganyway = false) {
        if (!$this->params->get('bruteforce', 0) && !$this->_net)
            return;
        if ($this->_pass && !$loganyway)
            return;
        if ($this->params->get('ipsecurity', 0) && $this->_blackwhite($this->_ip) === true)
            return; // never act upon whitelist
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if ($this->_log) {
            $query->update('#__plg_system_adminexile')->where('ip = ' . $db->quote($this->_ip));
            $this->_log->attempts++;
            switch ($this->_net) {
                case false:
                    if ($this->_log->attempts >= $this->params->get('bfmax', 3)) {
                        $this->_log->penalty = $this->_log->penalty * $this->params->get('bfpenaltymultiplier', 1);
                        if ($this->_log->penalty == 0)
                            $this->_log->penalty = $this->params->get('bfpenalty', 5);
                    }
                    $query->set('lastattempt = NOW()')->set('attempts = ' . $this->_log->attempts)->set('penalty = ' . $this->_log->penalty);
                    break;
                default:
                    $query->set('lastattempt = NOW()')->set('attempts = ' . $this->_log->attempts)->set('penalty = 0');
                    break;
            }
        } else {
            $this->_log = new stdClass;
            $this->_log->ip = $this->_ip;
            $this->_log->firstattempt = date("Y-m-d H:i:s O");
            $this->_log->lastattempt = $this->_log->firstattempt;
            $this->_log->attempts = 1;
            $this->_log->penalty = ($this->params->get('bfmax', 3) == 1 && !$this->_net) ? $this->params->get('bfpenalty', 5) : 0;
            $query->insert('#__plg_system_adminexile')->columns('ip,lastattempt,attempts,penalty')->values($db->quote($this->_ip) . ',NOW(),1,' . $this->_log->penalty);
        }
        $db->setQuery($query);
        $db->execute();
        $max = $this->_log->penalty ? $this->params->get('bfmax', 3) : 3;
        $once = $this->_log->penalty ? $this->params->get('bfemailonce', 1) : $this->params->get('blemailonce', 1);
        if (
                ($this->_log->attempts == $max) || // this takes care of the first attempt
                ($this->_log->attempts % $max == 0 && !$once) // this matches the first attempt - and it also matches multiples, sends email if bfemailonce is turned off.
        )
            $this->_notify();
    }

    private function _notify() {
        if ($this->_log->penalty > 0) { // these are bruteforce attempts
            if (!$this->params->get('bfemail', 1))
                return true;
            $mailrecipient = $this->params->get('bfemailuser', false);
            if ($this->_log->penalty && !$mailrecipient)
                return true;
            $mailsubject = 'PLG_SYS_ADMINEXILE_BFEMAIL_SUBJECT';
            $mailbody = 'PLG_SYS_ADMINEXILE_BFEMAIL_BODY';
        } else { // these are blacklist attempts
            if (!$this->params->get('blemail', 1) && $this->_log->penalty == 0)
                return true;
            $mailrecipient = $this->params->get('blemailuser', false);
            if (!$this->_log->penalty && !$mailrecipient)
                return true;
            $mailsubject = 'PLG_SYS_ADMINEXILE_BLEMAIL_SUBJECT';
            $mailbody = 'PLG_SYS_ADMINEXILE_BLEMAIL_BODY';
        }
        $recipient = JFactory::getUser($mailrecipient)->email;
        $subject = JText::sprintf($mailsubject, $this->_app->getCfg('sitename'));
        $body = JText::sprintf($mailbody, $this->_log->ip, $this->_log->attempts, $this->_log->firstattempt, $this->_log->lastattempt, $this->_log->penalty);
        $this->_sendMail($recipient, $subject, $body);
    }
    
    private function _sendMail($recipient,$subject,$body) {
        // prepare and send the email
        $config = JFactory::getConfig();
        $mailer = JFactory::getMailer();
        $mailer->setSender(array($config->get('config.mailfrom'), $config->get('config.fromname')));
        $mailer->addRecipient($recipient);
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $send = & $mailer->Send();
        if ($send !== true && property_exists($send,'message')) {
            error_log($send->message);
            $this->_killMessage($send->message);
        }        
    }

    private function _redirect($clear = false) {
        if ($this->params->get('ipsecurity', 0) && $this->_blackwhite($this->_ip) === true)
            return; // never act upon whitelist
        if ($clear)
            $this->_clearBlocks(array('ip' => $this->_ip));
        // this is for users who have successfully passed adminexile key/value but tripped the Brute Force detection
        if ($this->_app->getUserState("plg_sys_adminexile.$this->_key", false)) {
            $this->_app->setUserState("plg_sys_adminexile.$this->_key", false);
        }
        
        $this->_stealth(); // prevent joomla session cookie
        
        $redirecturl = $this->params->get('redirect', JURI::root());
        switch ($redirecturl) {
            case '{HOME}':
                $redirecturl = JURI::root();
                break;
            case '{404}':
                header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
                header("Status: 404 Not Found");
                if (!$this->params->get('fourofour', false)) {
                    die($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
                } else {
                    $find = array('{url}', '{serversignature}');
                    $replace = array($_SERVER['REQUEST_URI'], $_SERVER["SERVER_SIGNATURE"]);
                    die(str_replace($find, $replace, $this->params->get('fourofour')));
                }
                break;
            default:
                break;
        }
        if (!in_array($redirecturl,array('{404}','{joomla404}'))) {
            header("Location: " . $redirecturl);
            exit();
        }
        return true;
    }
    
    private function _stealth() {
        // this is a stealth feature - prevent /administrator session cookie from being set
        $hasheaders = false;
        foreach (headers_list() as $header) {
            if ($hasheaders)
                continue;
            if (preg_match('/Set-Cookie/', $header))
                $hasheaders = true;
        }
        if ($hasheaders) {
            if (version_compare(PHP_VERSION, 5.3, '>=')) {
                header_remove('Set-Cookie');
            } else {
                header('Set-Cookie:');
            }
        }        
    }

    private function _killMessage($error) {
        if(!class_exists('ReflectionClass')) return;
        $appReflection = new ReflectionClass(get_class($this->_app));
        $_messageQueue = $appReflection->getProperty('_messageQueue');
        $_messageQueue->setAccessible(true);
        $messages = $_messageQueue->getValue($this->_app);
        foreach ($messages as $key => $message)
            if ($message['message'] == $error)
                unset($messages[$key]);
        $_messageQueue->setValue($this->_app, $messages);
    }

}

// replace with values set by parse_url defines;
//$defines = array(
//    'HTTP_URL_REPLACE'=>1,          // Replace every part of the first URL when there's one of the second URL
//    'HTTP_URL_JOIN_PATH'=>2,        // Join relative paths
//    'HTTP_URL_JOIN_QUERY'=>4,       // Join query strings
//    'HTTP_URL_STRIP_USER'=>8,       // Strip any user authentication information
//    'HTTP_URL_STRIP_PASS'=>16,      // Strip any password authentication information
//    'HTTP_URL_STRIP_AUTH'=>32,      // Strip any authentication information
//    'HTTP_URL_STRIP_PORT'=>64,      // Strip explicit port numbers
//    'HTTP_URL_STRIP_PATH'=>128,     // Strip complete path
//    'HTTP_URL_STRIP_QUERY'=>256,    // Strip query string
//    'HTTP_URL_STRIP_FRAGMENT'=>512, // Strip any fragments (#identifier)
//    'HTTP_URL_STRIP_ALL'=>1024     // Strip anything but scheme and host
//);
$defines = array(
    'HTTP_URL_REPLACE'=>0,          // Replace every part of the first URL when there's one of the second URL
    'HTTP_URL_JOIN_PATH'=>1,        // Join relative paths
    'HTTP_URL_JOIN_QUERY'=>2,       // Join query strings
    'HTTP_URL_STRIP_USER'=>4,       // Strip any user authentication information
    'HTTP_URL_STRIP_PASS'=>8,      // Strip any password authentication information
    'HTTP_URL_STRIP_AUTH'=>16,      // Strip any authentication information
    'HTTP_URL_STRIP_PORT'=>32,      // Strip explicit port numbers
    'HTTP_URL_STRIP_PATH'=>64,     // Strip complete path
    'HTTP_URL_STRIP_QUERY'=>128,    // Strip query string
    'HTTP_URL_STRIP_FRAGMENT'=>256, // Strip any fragments (#identifier)
    'HTTP_URL_STRIP_ALL'=>492     // Strip anything but scheme and host
);
foreach($defines as $constant=>$value) if(!defined($constant)) define($constant,$value);
// Build an URL
// The parts of the second URL will be merged into the first according to the flags argument. 
// 
// @param  mixed      (Part(s) of) an URL in form of a string or associative array like parse_url() returns
// @param  mixed      Same as the first argument
// @param  int        A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
// @param  array      If set, it will be filled with the parts of the composed url like parse_url() would return 

function rwhttp_build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = false) {
    $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

    // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
    if ($flags & HTTP_URL_STRIP_ALL) {
        $flags |= HTTP_URL_STRIP_USER;
        $flags |= HTTP_URL_STRIP_PASS;
        $flags |= HTTP_URL_STRIP_PORT;
        $flags |= HTTP_URL_STRIP_PATH;
        $flags |= HTTP_URL_STRIP_QUERY;
        $flags |= HTTP_URL_STRIP_FRAGMENT;
    }
    // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
    else if ($flags & HTTP_URL_STRIP_AUTH) {
        $flags |= HTTP_URL_STRIP_USER;
        $flags |= HTTP_URL_STRIP_PASS;
    }

    // Parse the original URL
    $parse_url = is_array($url) ? $url : parse_url($url);

    // Scheme and Host are always replaced
    if (isset($parts['scheme']))
        $parse_url['scheme'] = $parts['scheme'];
    if (isset($parts['host']))
        $parse_url['host'] = $parts['host'];

    // (If applicable) Replace the original URL with it's new parts
    if ($flags & HTTP_URL_REPLACE) {
        foreach ($keys as $key) {
            if (isset($parts[$key]))
                $parse_url[$key] = $parts[$key];
        }
    }
    else {
        // Join the original URL path with the new path
        if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
            if (isset($parse_url['path']))
                $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
            else
                $parse_url['path'] = $parts['path'];
        }

        // Join the original query string with the new query string
        if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
            if (isset($parse_url['query']))
                $parse_url['query'] .= '&' . $parts['query'];
            else
                $parse_url['query'] = $parts['query'];
        }
    }

    // Strips all the applicable sections of the URL
    // Note: Scheme and Host are never stripped
    foreach ($keys as $key) {
        if ($flags & (int) constant('HTTP_URL_STRIP_' . strtoupper($key)))
            unset($parse_url[$key]);
    }

    $new_url = $parse_url;

    $ret =
            ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            . ((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') . '@' : '')
            . ((isset($parse_url['host'])) ? $parse_url['host'] : '')
            . ((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            . ((isset($parse_url['path'])) ? $parse_url['path'] : '')
            . ((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            . ((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
    ;
    return str_replace('//administrator','/administrator',$ret);
}