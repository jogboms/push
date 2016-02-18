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

Class Time 
{
	public static $offset;
	public static $timezone;
	public static 
	// $_append = array('s','m','hr','d','month','yr','just now','today','yesterday',' ago','in '),
	// $_append = array('s','m','hr','d','month','yr',' ','today','yesterday','','in ',' '),
	$_append = array('s','m','hr','d','month','yr','just now','today','yesterday','','in ',' '),
	$_division = array(1,60,3600,86400,2419200,29030400);

	const FULL = 1, FIRST = 2, SECOND = 3;
	
	/**
	*
	* @usage
	*	Time::d(); // date now
	*	Time::m(); // month now
	*	Time::y(); // year now
	*	Time::d('+ 11'); // today + 11 days
	*	Time::l('+ 14 days'); // day(thursday) + 14 days
	*	Time::m('- 11 months'); // 11 months before now
	*	...etc
	*/
	public static function __callStatic($modifier, $args)
	{
		return date($modifier, (!$args ?time() :(is_int($args[0]) ?($args[0]) :strtotime($args[0]))));
	}

	public static function ago($date = null, $type = self::FULL) 
	{
		if(is_null($date)) $date = time();
		self::timezone();

		$date = is_numeric($date) ? $date : strtotime($date);

		$span = time()-$date;
		$i = 0;
		if($span>=60) {
			for($i=(count(self::$_division)-1);$i>0;$i--)
				if(($time = floor($span/self::$_division[$i])) >= 1) break;
		}
		else $time = $span;

		$html = '<span class="agoTime">';
		$html .= self::beautify($i,$date+self::$offset,$time,$type);
		$html .= '</span>';
		return $html;
	}
	// TODO
	public static function togo($date)
	{

	}
	public static function beautify($index,$old_date,$new_date,$type)
	{
		// return $index;
		if($new_date < 10 && $index == 0) 
			// $r = $new_date.self::$_append[$index]; // less than 10secs [just now]
			$r = self::$_append[6]; // less than 10secs
		else if($new_date > 6  && $index == 2 && date('d', $old_date) == date('d'))
			$r = self::$_append[7]; // hours > 6 and still within today
		else if(($new_date > 6 || $new_date == 1) && $index == 2)
			$r = self::$_append[8]; // hours > 6 and not within today (yesterday)
		else $r = $new_date.self::$_append[$index].self::$_append[9]; // hours > 6

		$ext = date('a',$old_date) == 'am' ? 'a' : 'p';
		if($type == self::FIRST) return $r;
		else if($type == self::SECOND)
			return date('g:i',$old_date).$ext;
		return ($index<2)?$r:$r.self::$_append[11].'[ '.date('g:i',$old_date).$ext.' ]';
	}

	static function timezone()
	{
		list(,$a,$b,,$c,$d) = date('P');

		$offset = self::$offset = (int)($a.$b)*3600 + (int)($c.$d)*60;

		// TODO 
		// array_map(function($frame, $offset){
		// 	foreach($frame as $city){
		// 		if($city['offset'] == $offset){
		// 			alert('found it - '.$city['timezone_id'].' with offset - '.$offset);
		// 		}
		// 	}
		// }, timezone_abbreviations_list());

		foreach(timezone_abbreviations_list() as $abbr){
			foreach($abbr as $city){
				if($city['offset'] == self::$offset){
					self::$timezone = $city['timezone_id'];
					break;
				}
			}
			if(!empty(self::$timezone)) break;
		}
		date_default_timezone_set(self::$timezone);
		// 0.02 and 2097152b
	}

	static function date($date = null, $modifier = null)
	{
		if(is_null($date)) $date = time();
		$raw = is_numeric($date) ? $date : strtotime($date);
		if(!is_null($modifier)) return date($modifier, $raw);
		return date('D jS F Y \a\t g:ia', $raw);
	}
	static function mk($type, $value)
	{
		return self::date(mktime($value,$value,$value,$value,$value,$value), $type);
	}

	static function calendar($month = null, $year = null, $day = null)
	{
		$days = array(
			array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'),
			array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')
		);
		$month = $month ?: self::m();
		$year = $year ?: self::Y();
		$day = $day ?: self::d();

		$total = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$y = array_flip($days[0]);
		$pos = $y[strtolower(self::D(mktime(0,0,0,$month,1,$year)))];
		$render =  '<table class="table calendar"><h6>'.self::mk('F',$month).', '.$year.'</h6><thead><tr>';
		foreach($days[0] as $d)
			$render .= '<th>'.strtoupper($d).'</th>';

		$render .= '</tr></thead><tbody><tr>';
		$count = 0;
		for($i = 1; $i <= $total; $i++){
			if($pos != 0 && $i == 1)
				for($k = 0; $k < $pos; $k++){
					$count = $k+1;
					$render .= '<td></td>';
				}
				
			$render .= '<td class="';
			$render .= (($i==$day && $month == self::m())?'active':'').'"><a href="'.URI::calendar($year,$month,$i).'">'.$i.'</a></td>';

			if(($u = $pos%7) < 7 && $i == $total)
				for($k = $u+1; $k < 7; $k++)
					$render .= '<td></td>';

			if(($count+$i)%7 == 0) $render .= '</tr>';

			$pos++;
		}
		return $render .= '</tbody></table>';
	}
}
