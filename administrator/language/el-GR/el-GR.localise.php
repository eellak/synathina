<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * el-GR localise class
 *
 * @package		Joomla.Site
 * @since		1.6
 */
abstract class el_GRLocalise {
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param	int $count  The number of items.
	 * @return	array  An array of potential suffixes.
	 * @since	1.6
	 */
	public static function getPluralSuffixes($count) {
		if ($count == 0) {
			$return =  array('0');
		}
		elseif($count == 1) {
			$return =  array('1');
		}
		else {
			$return = array('MORE');
		}
		return $return;
	}
	/**
	 * Returns the ignored search words
	 *
	 * @return	array  An array of ignored search words.
	 * @since	1.6
	 */
	public static function getIgnoredSearchWords() {
		$search_ignore = array();
		$search_ignore[] = "και";
		$search_ignore[] = "με";
		$search_ignore[] = "σε";
		return $search_ignore;
	}
	/**
	 * Returns the lower length limit of search words
	 *
	 * @return	integer  The lower length limit of search words.
	 * @since	1.6
	 */
	public static function getLowerLimitSearchWord() {
		return 3;
	}
	/**
	 * Returns the upper length limit of search words
	 *
	 * @return	integer  The upper length limit of search words.
	 * @since	1.6
	 */
	public static function getUpperLimitSearchWord() {
		return 20;
	}
	/**
	 * Returns the number of chars to display when searching
	 *
	 * @return	integer  The number of chars to display when searching.
	 * @since	1.6
	 */
	public static function getSearchDisplayedCharactersNumber() {
		return 200;
	}

	public static function transliterate($string)
	{
		$str = JString::strtolower($string);
 
		//Specific language transliteration.
		//This one is for latin 1, latin supplement , extended A, Cyrillic, Greek
 
		$glyph_array = array(
			'afth'	=>	'αυθ',
			'afk'	=>	'αυκ',
			'afks'	=>	'αυξ',
			'afp'	=>	'αυπ',
			'afs'	=>	'αυσ',
			'aft'	=>	'αυτ',
			'aff'	=>	'αυφ',
			'afx'	=>	'αυχ',
			'afps'	=>	'αυψ',
			'efth'	=>	'ευθ',
			'efk'	=>	'ευκ',
			'efks'	=>	'ευξ',
			'efp'	=>	'ευπ',
			'efs'	=>	'ευσ',
			'eft'	=>	'ευτ',
			'eff'	=>	'ευφ',
			'efx'	=>	'ευχ',
			'efps'	=>	'ευψ',
			'ifth'	=>	'ηυθ',
			'ifk'	=>	'ηυκ',
			'ifks'	=>	'ηυξ',
			'ifp'	=>	'ηυπ',
			'ifs'	=>	'ηυσ',
			'ift'	=>	'ηυτ',
			'iff'	=>	'ηυφ',
			'ifx'	=>	'ηυχ',
			'ifps'	=>	'ηυψ',
			'-b'	=>	'-μπ',
			'-d'	=>	'-ντ',
			'-g'	=>	'-γκ',
			' b'	=>	' μπ',
			' d'	=>	' ντ',
			' g'	=>	' γκ',
			'av'	=>	'αυ',
			'ev'	=>	'ευ',
			'iv'	=>	'ηυ',
			'ou'	=>	'ου',
			'a'		=>	'a,à,á,â,ã,ä,å,ā,ă,ą,ḁ,α,ά',
			'ae'	=>	'æ',
			'b'		=>	'б,^μπ',
			'c'		=>	'c,ç,ć,ĉ,ċ,č,ћ,ц',
			'ch'	=>	'ч',
			'd'		=>	'ď,đ,Ð,д,ђ,δ,ð,^ντ',
			'dz'	=>	'џ',
			'e'		=>	'e,è,é,ê,ë,ē,ĕ,ė,ę,ě,э,ε,έ',
			'f'		=>	'ƒ,ф,φ',
			'g'		=>	'ğ,ĝ,ğ,ġ,ģ,г,γ,^γκ',
			'h'		=>	'ĥ,ħ,Ħ,х',
			'i'		=>	'i,ì,í,î,ï,ı,ĩ,ī,ĭ,į,и,й,ъ,ы,ь,η,ή,ι,ί,ϊ,ΐ',
			'ij'	=>	'ĳ',
			'j'		=>	'ĵ,j',
			'ja'	=>	'я',
			'ju'	=>	'яю',
			'k'		=>	'ķ,ĸ,κ',
			'ks'	=>	'ξ',
			'l'		=>	'ĺ,ļ,ľ,ŀ,ł,л,λ',
			'lj'	=>	'љ',
			'm'		=>	'μ,м',
			'n'		=>	'ñ,ņ,ň,ŉ,ŋ,н,ν',
			'nj'	=>	'њ',
			'o'		=>	'ò,ó,ô,õ,ø,ō,ŏ,ő,ο,ό,ω,ώ',
			'oe'	=>	'œ,ö',
			'p'		=>	'п,π',
			'ps'	=>	'ψ',
			'r'		=>	'ŕ,ŗ,ř,р,ρ',
			's'		=>	'ş,ś,ŝ,ş,š,с,σ,ς',
			'ss'	=>	'ß,ſ',
			'sh'	=>	'ш',
			'shch'	=>	'щ',
			't'		=>	'ţ,ť,ŧ,τ,т',
			'th'	=>	'θ',
			'u'		=>	'u,ù,ú,û,ü,ũ,ū,ŭ,ů,ű,ų,у',
			'v'		=>	'в,β',
			'w'		=>	'ŵ',
			'x'		=>	'χ',
			'y'		=>	'ý,þ,ÿ,ŷ,υ,ύ,ϋ,ΰ',
			'z'		=>	'ź,ż,ž,з,ж,ζ'
		);
 
		foreach($glyph_array as $letter => $glyphs) {
			preg_match_all('/(\^[^,]+(,|$))/', $glyphs, $matches);
			if (count($matches[0])) {
				foreach ($matches[0] as $m) {
                    if (strpos($m, ',')) {
                        $glyphs = str_replace($m, '', $glyphs);
                    }
                    elseif(strpos($glyphs, ',')) {
                        $glyphs = str_replace(','.$m, '', $glyphs);
                    }
                    else {
                        $glyphs = '';
                    }
        			$str = preg_replace('/'.$m.'/', $letter, $str);
                }
			}
			$glyphs = explode(',', $glyphs);
			$str = str_replace($glyphs, $letter, $str);
		}
 
		return $str;
	}
}

jimport('joomla.utilities.date');
$datefile = JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'utilities' . DIRECTORY_SEPARATOR . 'date.php';

$fh = fopen($datefile, 'r');
$theData = fread($fh, filesize($datefile));
fclose($fh);

$theData = str_replace('<?php', '', $theData);

$splitter1 = 'public function format(';
$splitter2 = 'return $return;';

$parts1 = explode($splitter1, $theData);
$parts2 = explode($splitter2, $parts1[1]);

$parts2[0] .= 'if(preg_match("/d|j/", $format)){';
$parts2[0] .= '$orig_months = array("άριος", "άρτιος", "ίλιος", "άιος", "ύνιος", "ύλιος", "ύγουστος", "έμβριος", "ώβριος");';
$parts2[0] .= '$new_months = array("αρίου", "αρτίου", "ιλίου", "αΐου", "υνίου", "υλίου", "υγούστου", "εμβρίου", "ωβρίου");';
$parts2[0] .= 'for ($i = 0; $i < count($orig_months); $i++) $return = str_replace($orig_months[$i], $new_months[$i], $return);';
$parts2[0] .= '}';

$fullcode = implode($splitter2, $parts2);
$fullcode = $parts1[0].$splitter1.$fullcode;
$fullcode = str_replace('class JDate extends DateTime', 'class el_GRDate extends DateTime', $fullcode);

eval($fullcode);
