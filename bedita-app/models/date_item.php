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
    /**
     * Recursive find depth.
     *
     * @var int
     */
    public $recursive = 0;

    /**
     * Validation rules.
     *
     * @var array
     */
    public $validate = array();

    /**
     * Returns the column type of a column in the model.
     *
     * @param string $column The name of the model column.
     * @return string Column type.
     * @see Model::getColumnType()
     */
    public function getColumnType($column) {
        static $columns = array();  // Use static cache to avoid duplicate calls to parent.

        if (empty($columns[$column])) {
            $columns[$column] = parent::getColumnType($column);
        }
        return strtolower($columns[$column]);
    }

    /**
     * Filter out valid parameter keys.
     *
     * @param array $params Array of potential parameters.
     * @return array Filtered array of parameters.
     */
    protected function filterParams(array $params) {
        static $validKeys = array('days', 'scale_factor', 'bias');

        if (!empty($params['days']) && is_array($params['days'])) {
            $params['days'] = array_unique(array_values($params['days']));
        }
        if (!empty($params['scale_factor']) && is_array($params['scale_factor'])) {
            $params['scale_factor'] = array_values($params['scale_factor']);
            if (count($params['scale_factor']) == 1 || $params['scale_factor'][0] == $params['scale_factor'][1]) {
                $params['scale_factor'] = $params['scale_factor'][0];
            }
        }
        if (!empty($params['bias']) && is_array($params['bias'])) {
            $params['bias'] = array_values($params['bias']);
            if (count($params['bias']) == 1 || $params['bias'][0] == $params['bias'][1]) {
                $params['bias'] = $params['bias'][0];
            }
        }

        return array_filter(array_intersect_key($params, array_flip($validKeys)));
    }

    /**
     * Convert a string to timestamp.
     *
     * This function is safe with years after 9999 or before -9999.
     *
     * @param string $date Date to be converted.
     * @return int|null Timestamp.
     */
    public function stringToTimestamp($date) {
        $year = null;
        $date = preg_replace_callback('/(?<=^|-|\/)\s*(?P<year>-?\d{5,})/', function ($match) use (&$year) {
            $year = $match['year'];
            return '1972';
        }, (string) $date);  // Replace any 5+-digit year with 1972 (must be a leap year).
        $date = strtotime($date) ?: null;

        if (is_null($year) || is_null($date)) {
            // Invalid date, or 4-digit years.
            return $date;
        }

        if (!class_exists('DateTime')) {
            // Missing class `DateTime`, required for handling of 5+-digit years.
            return null;
        }

        /** Fix timestamp for 5+-digit years. */
        $datetime = new DateTime();
        $datetime->setTimestamp($date);
        $datetime->setDate($year, $datetime->format('m'), $datetime->format('d'));
        $date = $datetime->getTimestamp();

        return $date;
    }

    /**
     * Prepares one or more dates in the correct format for the underlying database structure (either `DATETIME` or `BIGINT`).
     *
     * @param string $column The name of the model column.
     * @param mixed $date Date (or array of dates) to be prepared.
     * @return mixed Prepared date(s).
     */
    public function prepareDate($column, $date) {
        if (is_array($date)) {
            foreach ($date as &$d) {
                $d = $this->prepareDate($column, $d);
            }
            reset($date);
            return $date;
        }

        if (!is_int($date)) {
            if (empty($date)) {
                return null;
            }

            $date = preg_replace('/^-|([\/-])-/', '$1', $date, -1, $negative);

            $pattern = str_replace('%', '', Configure::read('datePattern'));
            if ($dateTime = date_create_from_format($pattern . ' G:i', $date)) {
                $date = date_format($dateTime, 'Y-m-d H:i:00');
            } elseif ($dateTime = date_create_from_format($pattern, $date)) {
                $date = date_format($dateTime, 'Y-m-d 00:00:00');
            }

            if ($negative) {
                $date = '-' . $date;
            }
        }

        $type = $this->getColumnType($column);
        if ($type == 'datetime' && is_int($date)) {
            $date = date('Y-m-d H:i:s', $date);
        } elseif ($type == 'integer' && !is_int($date)) {
            $date = $this->stringToTimestamp($date);
        }
        return $date;
    }

    /**
     * Helper function to recursively prepare conditions.
     *
     * @param array $data Data to be prepared.
     * @return array Prepared data.
     */
    private function prepareConditions(array $data) {
        foreach ($data as $key => &$value) {
            if (strpos($key, 'start_date') !== false && !empty($value)) {
                $value = $this->prepareDate('start_date', $value);
                continue;
            }
            if (strpos($key, 'end_date') !== false && !empty($value)) {
                $value = $this->prepareDate('end_date', $value);
                continue;
            }
            if (is_array($value)) {
                $value = $this->prepareConditions($value);
            }
        }
        reset($data);
        return $data;
    }

    /**
     * Runs actual validation rules. If supplied values aren't valid, they are silently removed.
     *
     * @return bool Returns `true`.
     */
    public function beforeValidate() {
        $data = &$this->data[$this->name];
        if (empty($data['id']) && empty($data['start_date']) && empty($data['end_date']) && empty($data['duration'])) {
            // Skip save if no (valid) data is present.
            $data = array();
            return true;
        }

        /**
         * Prepare start/end dates.
         */
        if (!empty($data['start_date']) && !empty($data['timeStart'])) {
            $data['start_date'] .= ' ' . $data['timeStart'];
        }
        $data['start_date'] = $this->prepareDate('start_date', $data['start_date']);
        if (!empty($data['end_date']) && !empty($data['timeEnd'])) {
            $data['end_date'] .= ' ' . $data['timeEnd'];
        }
        $data['end_date'] = $this->prepareDate('end_date', $data['end_date']);

        /**
         * Prepare params.
         */
        $params = $this->filterParams($data);
        if (!empty($params)) {
            $data['params'] = serialize($params);
        }

        return true;
    }

    /**
     * Before find condition formatting.
     *
     * @param array $queryData Array with query data.
     * @return array Array with modified query data.
     */
    public function beforeFind(array $queryData) {
        if (!empty($queryData['conditions'])) {
            $queryData['conditions'] = $this->prepareConditions($queryData['conditions']);
        }
        return $queryData;
    }

    /**
     * After find data formatting.
     *
     * @param mixed $results Found results.
     * @return array Formatted results.
     */
    public function afterFind($results) {
        if (empty($results[0]['DateItem'])) {
            return $results;
        }
        foreach ($results as &$r) {
            if (isset($r['DateItem']['start_date'])) {
                $r['DateItem']['start_date'] = is_numeric($r['DateItem']['start_date']) ? date('Y-m-d H:i:s', $r['DateItem']['start_date']) : $r['DateItem']['start_date'];
            }
            if (isset($r['DateItem']['end_date'])) {
                $r['DateItem']['end_date'] = is_numeric($r['DateItem']['end_date']) ? date('Y-m-d H:i:s', $r['DateItem']['end_date']) : $r['DateItem']['end_date'];
            }

            if (empty($r['DateItem']['params'])) {
                continue;
            }
            $params = @unserialize($r['DateItem']['params']) ?: array();
            unset($r['DateItem']['params']);

            $r['DateItem'] = array_merge($r['DateItem'], $this->filterParams($params));
        }
        reset($results);
        return $results;
    }

    /**
     * Add a DateItem to the calendar with respect of time order.
     *
     * @param array $calendar Calendar.
     * @param array $dateItem DateItem to be added.
     */
    protected static function addToCalendar(array &$calendar, array $dateItem) {
        $timestamp = strtotime($dateItem['DateItem']['start_date']);
        $day = date('Y-m-d', $timestamp);

        if (empty($calendar[$day])) {
            $calendar[$day] = array();
        }

        $move = null;
        $tot = count($calendar[$day]);
        for ($i = 0; $i < $tot; $i++) {
            if (!$move && strtotime($calendar[$day][$i]['DateItem']['start_date']) < $timestamp) {
                continue;
            }
            $mv = $calendar[$day][$i];
            $calendar[$day][$i] = $move ?: $dateItem;
            $move = $mv;
        }
        array_push($calendar[$day], $move ?: $dateItem);
        reset($calendar[$day]);
    }

    /**
     * Load calendar date items in the time window specified.
     * Returns array containing a list of object IDs with matching date items, and a list of DateItems for each day.
     *
     * @param string $startDay Start date, using the format `YYYY-MM-DD`, i.e. `2014-02-28`.
     * @param string $endDay End date (not included), using the format `YYYY-MM-DD`, i.e. `2014-03-14`.
     * @return array, containing
     *     "objIds" => array of object id matched,
     *     "calendar" => associative array having "date" as key and date items as values
     */
    public function loadDateItemsCalendar($startDay, $endDay) {
        $winStart = strtotime($startDay);
        $winEnd = strtotime($endDay);

        /**
         * Find all events in range.
         */
        $dateItems = $this->find('all', array(
            'conditions' => array(
                'OR' => array(
                    'AND' => array(
                        'start_date >=' => $startDay . ' 00:00:00',
                        'start_date <' => $endDay . ' 00:00:00',
                    ),
                    'OR' => array(
                        '1 = 0',
                        'AND' => array(
                            'end_date NOT' => null,
                            'start_date <' => $endDay . ' 00:00:00',
                            'end_date >' => $startDay . ' 00:00:00',
                        ),
                    ),
                ),
            ),
            'order' => array('start_date'),
        ));

        $objIds = array();
        $calendar = array();
        foreach ($dateItems as $di) {
            if (empty($di['DateItem']['start_date'])) {
                continue;
            }
            $objIds[] = $di['DateItem']['object_id'];

            /**
             * Start date.
             */
            $start = strtotime($di['DateItem']['start_date']);
            $s = max($start, $winStart);
            $sd = date('Y-m-d', $start);
            $st = date('H:i:s', $start);
            $di['DateItem']['start_date'] = date('Y-m-d H:i:s', $s);

            /**
             * End date.
             */
            $end = !empty($di['DateItem']['end_date']) ? strtotime($di['DateItem']['end_date']) : null;
            $e = !empty($end) ? min($end, $winEnd) : null;
            $ed = !empty($end) ? date('Y-m-d', $end) : null;
            $et = !empty($end) ? date('H:i:s', $end) : null;
            $di['DateItem']['end_date'] = !empty($end) ? date('Y-m-d H:i:s', $e) : null;

            /**
             * Single-day date item.
             */
            if (empty($end) || $sd == $ed) {
                self::addToCalendar($calendar, $di);
                continue;
            }

            /**
             * Recurring event.
             */
            $time = $s;
            $day = date('Y-m-d', $time);
            $lastDay = ($e === $winEnd) ?  date('Y-m-d', $e - DAY) : date('Y-m-d', $e);
            while ($day <= $lastDay) {
                if (!empty($di['DateItem']['days']) && !in_array(date('N', $time), $di['DateItem']['days'])) {
                    $time = strtotime('+1 day', $time);
                    $day = date('Y-m-d', $time);
                    continue;
                }

                $newItem = $di;
                $newItem['DateItem']['start_date'] = $day . ' ' . $st;
                $newItem['DateItem']['end_date'] = $day . ' ' . $et;

                self::addToCalendar($calendar, $newItem);

                $time = strtotime('+1 day', $time);
                $day = date('Y-m-d', $time);

            }
        }
        ksort($calendar);
        return compact('objIds', 'calendar');
    }

    /**
     * Return an array of column types to transform (cast)
     * Used to build consistent REST APIs
     *
     * - Force 'start_date' and 'end_date' to be formatted as dates also when those fields are defined as BIGINT for handle historical dates
     * - Add to table fields type 'days' as 'integerArray' to convert ["0", "1", ...] in [0, 1, ...]
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
        $transformer['start_date'] = 'datetime';
        $transformer['end_date'] = 'datetime';
        $transformer['days'] = 'integerArray';
        return $transformer;
    }
}
