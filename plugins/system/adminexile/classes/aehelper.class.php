<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

class AdminExileHelper {
    static function gmp() {
        $ret = false;
        if(extension_loaded('gmp')) {
            $ret = self::testGMP();
        } else {
            if(function_exists('dl')) {
                $filename = (PHP_SHLIB_SUFFIX === 'dll')?'php_gmp.dll':'gmp.so';
                try {
                    $dl = @dl($filename);
                } catch(Exception $e) {
                    error_log($e->getMessage());
                }
                if($dl) $ret = self::testGMP();
            }
        }
        return $ret;
    }
    private function testGMP() {        
        $gmpfuncs = array(
            'gmp_init','gmp_setbit','gmp_strval','gmp_testbit',
            'gmp_cmp','gmp_intval','gmp_add','gmp_neg','gmp_mul',
            'gmp_sub','gmp_pow','gmp_scan0','gmp_and','gmp_or');
        return array_intersect($gmpfuncs,get_extension_funcs('gmp')) == $gmpfuncs;
    }
    static function ipArray($string) {        
        if(substr($string,0,1) == '[') {
            $return = json_decode($string);
        } else {
            $return = explode("\n", trim(str_replace(array("\r", "\t", " "), array('', '', ''), $string)));
        }
        return $return;
    }
}