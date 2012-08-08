<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

if (!class_exists("TimeHelper")) {
	App::import("Helper", "Time");
}

/**
 * Date, Time Helper that extend TimeHelper
 * use strftime and its format  instead of date
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class BeTimeHelper extends TimeHelper {

	/**
	 * default date pattern
	 * @var string (strftime format) 
	 */
	private $datePattern;

	/**
	 * default time pattern
	 * @var string (strftime format)
	 */
	private $dateTimePattern;

	public function __construct() {
		$conf = Configure::getInstance();
		$this->datePattern = $conf->datePattern;
		$this->dateTimePattern = $conf->dateTimePattern;
	}

	/**
	 * format date
	 * 
	 * @param string $date
	 * @param string $format
	 * @param boolean $invalid
	 * @param int $userOffset
	 * @return mixed string|boolean
	 */
	public function date($date, $format = null, $invalid = false, $userOffset = null) {
		if (empty($format)) {
			$format = $this->datePattern;
		}
		return $this->format($format, $date, $invalid, $userOffset);
	}

	/**
	 * format date
	 * 
	 * @param string $date
	 * @param string $format
	 * @param boolean $invalid
	 * @param int $userOffset
	 * @return mixed string|boolean
	 */
	public function dateTime($date, $format = null, $invalid = false, $userOffset = null) {
		if (empty($format)) {
			$format = $this->dateTimePattern;
		}
		return $this->format($format, $date, $invalid, $userOffset);
	}

	/**
	 * get day from date
	 * 
	 * @param string $date
	 * @return mixed string|boolean
	 */
	public function day($date) {
		return $this->format("%d", $date);
	}

	/**
	 * get day name from date
	 * 
	 * @param string $date
	 * @return mixed string|boolean
	 */
	public function dayName($date) {
		return $this->format("%A", $date);
	}

	/**
	 * get month from date
	 * 
	 * @param string $date
	 * @return mixed string|boolean
	 */
	public function month($date) {
		return $this->format("%m", $date);
	}

	/**
	 * get month name from date
	 * 
	 * @param string $date
	 * @return mixed string|boolean
	 */
	public function monthName($date) {
		return $this->format("%B", $date);
	}

	/**
	 * get year from date
	 * 
	 * @param string $date
	 * @return mixed string|boolean
	 */
	public function year($date) {
		return $this->format("%Y", $date);
	}

	/**
	 * format date
	 * 
	 * @param string $format
	 * @param string $date
	 * @param boolean $invalid
	 * @param int $userOffset
	 * @return mixed string|boolean
	 * 
	 * @see TimeHelper::format()
	 */
	public function format($format = '%d-%m-%Y', $date, $invalid = false, $userOffset = null) {
		$date = $this->fromString($date, $userOffset);
		if ($date === false && $invalid !== false) {
			return $invalid;
		}
		return strftime($format, $date);
	}

	/**
	 * calculate the difference between two dates
	 *
	 * @param string $dateStart
	 * @param string $dateEnd
	 * @param string $period (units, default minutes)
	 * @param bool $complete true return also $period
	 * @return mixed int|string
	 */
	public function dateDiff($dateStart, $dateEnd, $period="minutes", $complete=false) {
		$secondsRatio = array(
			"seconds" => 1,
			"minutes" => 60,
			"hours" => 3600,
			"days" => 86400,
			"years" => 31536000
		);
		$dateStart = $this->fromString($dateStart);
		$dateEnd = $this->fromString($dateEnd);
		$diff = $dateEnd - $dateStart;
		$diffPeriod = round($diff/$secondsRatio[$period], 2);
		return ($complete)? $diffPeriod . " " . __($period, true) : $diffPeriod;
	}

}
?>