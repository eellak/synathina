<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
class SimpleCIDR {
        protected static $instances = array();
        public $network;
        public function __construct($network=false) {
            if($network) $this->setNetwork($network);
        }
        public static function getInstance($network=false) {
            $instanceid = $network?$network:'';
            if(empty(self::$instances[$instanceid])) {
                self::$instances[$instanceid] = new SimpleCIDR($instanceid);
            }
            return self::$instances[$instanceid];
        }
        public function setNetwork($network=false) {
            if($network) $this->network = $network;
        }	
        public function contains($ip) {
	  list ($subnet, $bits) = explode('/', $this->network);
	  $ip = ip2long($ip);
	  $subnet = ip2long($subnet);
	  $mask = -1 << (32 - $bits);
	  $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
	  return ($ip & $mask) == $subnet;	  
        }
}