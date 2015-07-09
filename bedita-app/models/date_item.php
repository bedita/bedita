<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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

/**
 * Date item object
 */
class DateItem extends BEAppModel 
{
    public $recursive = 0;

    public $validate = array();

    function beforeValidate() {
        $this->checkDate('start_date');
        $this->checkDate('end_date');
        $data = &$this->data[$this->name];

        // if new row and start_date and end_date are null empty $data array to not save in db
        if (empty($data['id']) && $data['start_date'] === null && $data['end_date'] === null) {
            $data = array();
        } else {
            if(!empty($data['start_date']) && !empty($data['timeStart'])) {
                $data['start_date'] .= " " . $data['timeStart'];
            }
            if (!empty($data['end_date']) && !empty($data['timeEnd'])) {
                $data['end_date'] .= " " . $data['timeEnd'];
            }
            if (!empty($data['days'])) {
                $data['params'] = serialize(array("days" => $data['days']));
            }
        }
        return true;
	}

	
    function afterFind($results) {
        if (!empty($results[0]["DateItem"])) {
            foreach ($results as &$r) {
                if (!empty($r["DateItem"]["params"])) {
                    $params = @unserialize($r["DateItem"]["params"]);
                    if (!empty($params["days"])) {
                        $r["DateItem"]["days"] = $params["days"];
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Load calendar date items from a "start day" to an "end day"
     * Returns array contatining object_id (events) with matching date items, 
     * and "calendar" array with DateItems for each day
     * 
     * @param string $today - in the form YYYY-MM-DD, i.e. 2014-02-28
     * @param string $nextCalendarDay, 
     * @return array, containing 
     *     "objIds" => array of object id matched, 
     *     "calendar" => associative array having "date" as key and date items as values
     */
    public function loadDateItemsCalendar($today, $nextCalendarDay) {

        $lastDay = $today;
        $todayTime = $today . " 00:00:00";
        $nextCalendarTime = $nextCalendarDay . " 00:00:00";

        $query = "select * from date_items as DateItem where " .
                " (start_date >= '$todayTime' AND start_date < '$nextCalendarTime') ".
                " OR (start_date < '$nextCalendarTime' AND end_date > '$todayTime' AND end_date IS NOT NULL)" .
                " order by start_date";
        $dateItems = $this->query($query);
        $objIds = array();

        $calendar = array();
        $multiDay = array();
        foreach ($dateItems as &$di) {
            if (!empty($di["DateItem"]["start_date"])) {
                $isMultiDay = false;
                $objIds[] = $di["DateItem"]["object_id"];
                $startDay = substr($di["DateItem"]["start_date"], 0, 10);
                $calDay = null;
                if (!empty($di["DateItem"]["end_date"])) {
                    $endDay = substr($di["DateItem"]["end_date"], 0, 10);
                    if ($startDay !== $endDay) {
                        $startTime = substr($di["DateItem"]["start_date"], 10);
                        $calDay = ($startDay >= $today) ? $startDay : $today;
                        $di["DateItem"]["start_date"] = $calDay . $startTime;
                        unset($di["DateItem"]["end_date"]);
                        $di["DateItem"]["firstDay"] = $calDay;
                        $di["DateItem"]["lastDay"] = $endDay;
                        $di["DateItem"]["startTime"] = $startTime;
                        $isMultiDay = true;
                        $multiDay[] = $di;
                    } else {
                        $calDay = $startDay;
                    }
                } else {
                    $calDay = $startDay;
                }
                if (!$isMultiDay && ($calDay > $lastDay)) {
                    $lastDay = $calDay;
                }
                if (empty($calendar[$calDay])) {
                    $calendar[$calDay]= array();
                }
                if (!$isMultiDay) {
                    $calendar[$calDay][] = $di;
                }
            }
        }

        foreach ($multiDay as $multi) {
            $first = $multi["DateItem"]["firstDay"];
            $last = $multi["DateItem"]["lastDay"];
            $sTime = $multi["DateItem"]["startTime"];
            foreach ($calendar as $day => &$item) {
                $dc = date_create($day);
                $nDay = intval(date("N", date_timestamp_get($dc)));
                if (empty($multi["DateItem"]["days"]) ||
                in_array($nDay, $multi["DateItem"]["days"])) {
                    if ($day >= $first && $day <= $last) {
                        $multi["DateItem"]["start_date"] = $day . $sTime;
                        if ($sTime == " 00:00:00") {
                            array_push($item, $multi);
                        } else {
                            $newItem = array();
                            $found = false;
                            foreach ($item as $di) {
                                if (!$found &&
                                ( ($multi["DateItem"]["start_date"] < $di["DateItem"]["start_date"])
                                        || ( isset($di["DateItem"]["startTime"]) &&
                                                $di["DateItem"]["startTime"] == " 00:00:00" ) )) {
                                    $newItem[] = $multi;
                                    $found = true;
                                }
                                $newItem[] = $di;
                            }
                            if (!$found) {
                                $newItem[] = $multi;
                            }
                            $calendar[$day] = $newItem;
                        }
                    }
                }
            }
        }
        return array("objIds" => $objIds, "calendar" => $calendar);
    }

    /**
     * Return an array of column types to transform (cast)
     * Used to build consistent REST APIs
     *
     * Add to table fields type 'days' as 'integerArray' to convert ["0", "1", ...] in [0, 1, ...]
     *
     * Possible options are:
     * - 'castable' an array of fields that the rest api would be cast to
     *
     * @see AppModel::apiTransformer()
     * @param array $options
     * @return array
     */
    public function apiTransformer(array $options = array()) {
        $transformer = parent::apiTransformer($options);
        $transformer['days'] = 'integerArray';
        return $transformer;
    }
}

