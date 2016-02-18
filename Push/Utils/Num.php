<?php
/**
 * PUSH MVC Framework.
 * @package PUSH MVC Framework
 * @version See PUSH.json
 * @author See PUSH.json
 * @copyright See PUSH.json
 * @license See PUSH.json
 * PUSH MVC Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See PUSH.json for copyright notices and details.
 */

namespace Push\Utils;

Class Num 
{

	public static function count($num, $precision=1)
	{
		$a = array('','k','M','Bn','Tr','Z');
		for($i = 0; $i <= 15; $i += 3){
			if($num/pow(10, $i+1) > 100) continue;
			return round($num/pow(10, $i), $precision).$a[$i/3].($i>1?'+':'');
		}
		return round($num, $precision);
	}

	/**
	 * todo
	 */
	public function s($num, $precision=1, $count = 0, $prefix = '')
	{
		$a = array('','k', 'M', 'B', 'T', 'Z');
		return round((($num/1000 > 100) ? s($num/1000, $precision, ++$count) : (($num>=1000) ? $num/1000 : $num)), $precision).$a[++$count];
	}
}