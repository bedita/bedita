<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Events module controller
 *
 */
class EventsController extends ModulesController {

	public $helpers 	= array('BeTree', 'BeToolbar', 'Paginator');
	public $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeSecurity');
	public $uses = array('BEObject','Event','Category','Area','Tree', 'DateItem');

	// default calendar: 7 days
	protected $calendarDays = 7;
	protected $moduleName = 'events';
	protected $categorizableModels = array('Event');
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['event']["id"];
		$filter["count_annotation"] = array("Comment","EditorNote");
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter["object_type_id"]);
	 }

    public function calendar() {
        if(!empty($this->params["url"]["Date_Day"])) {
            $startDay = $this->params["url"]["Date_Year"] . "-" . 
                $this->params["url"]["Date_Month"] . "-" . 
                str_pad($this->params["url"]["Date_Day"], 2, "0", STR_PAD_LEFT);
        } else {
            $startDay = date("Y-m-d");
        }
        $startTime = $startDay . " 00:00:00";
        
        $this->set("startTime", $startTime);
        // end day: today + caelndarDays + 1
        $nextCalendarDay = date("Y-m-d", strtotime($startTime) + ($this->calendarDays * DAY));
        $this->set("nextCalendarDay", $nextCalendarDay);
        $prevCalendarDay = date("Y-m-d", strtotime($startTime) - ($this->calendarDays * DAY));
        $this->set("prevCalendarDay", $prevCalendarDay);

        $calendarData = $this->DateItem->loadDateItemsCalendar($startDay, $nextCalendarDay);
        
        $events = array();
        $this->Event->containLevel("minimum");
        if (!empty($calendarData["objIds"])) {
            $events = $this->Event->find("all", array(
                    "conditions" => array(
                            "Event.id IN (" . implode(",", $calendarData["objIds"]) . ")",
                            "BEObject.object_type_id" => Configure::read("objectTypes.event.id")),
        
            ));
        }
        
        $eventsOrdered = array();
        foreach ($events as &$evt) {
            $eventsOrdered[$evt["id"]] = $evt;
        }
        
        $dateItemsCalendar = array();
        $allDates = array();
        foreach ($calendarData["calendar"] as $day => $items) {
            foreach ($items as $di) {
                if (!empty($eventsOrdered[$di["DateItem"]["object_id"]])) {
                    $di["DateItem"]["Event"] = $eventsOrdered[$di["DateItem"]["object_id"]];
                    array_push($allDates, $eventsOrdered[$di["DateItem"]["object_id"]]);
                    $dateItemsCalendar[] = $di;
                }
            }
        }
        $this->set("dateItems", $dateItemsCalendar);
        $this->set("allDates", $allDates);
    }

	public function view($id = null) {
		$this->viewObject($this->Event, $id);
		// check date items
		if (!empty($this->viewVars["object"]["DateItem"])) {
    		foreach ($this->viewVars["object"]["DateItem"] as &$di) {
    		    if (!empty($di["start_date"]) && !empty($di["end_date"])) {
    		        if($di["start_date"] > $di["end_date"]) {
    		            $sDate = strftime(Configure::read("dateTimePattern"), strtotime($di["start_date"]));
    		            $eDate = strftime(Configure::read("dateTimePattern"), strtotime($di["end_date"]));
    		            $this->userWarnMessage(__("Calendar start date after end date", true) . 
    		                    ": " . $sDate . " -" . $eDate);
    		        }
    		    }
    		}
		}
	}

	public function save() {
 		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		$this->saveObject($this->Event);
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("Event saved", true)." - ".$this->data["title"]);
		$this->eventInfo("event [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Event);
	}
}

