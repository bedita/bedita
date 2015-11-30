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
        $this->set("listTags",$this->Category->getTags(array("cloud" => false)));

	}

    public function calendar() {

        $calendarRange = Configure::read('eventCalendarRange');

        // get range from toolbar
        if (!empty($this->params["form"]["toolbarStartDate"]) && !empty($this->params["form"]["toolbarEndDate"])) {
            $startDay = $this->params["form"]["toolbarStartDate"];
            $endDay = $this->params["form"]["toolbarEndDate"];
        } else {

            // set date_items range
            if (!empty($this->params["form"]["start_date"])) {
                $startDay = $this->Event->getDefaultDateFormat($this->params["form"]["start_date"], true);
            } else {
                $startDay = date('Y-m-d');
            }

            if (!empty($this->params["form"]["start_date"]) && !empty($this->params["form"]["end_date"])) {
                $endDay = $this->Event->getDefaultDateFormat($this->params["form"]["end_date"], true);
            } else {
                $d = strtotime(date($startDay));
                $endDay = date('Y-m-d', strtotime($calendarRange, $d));
            }

        }



        $this->set("startDay", $startDay);
        $this->set("endDay", $endDay);


        // from here on untoched should be reviewd - xho
        $startTime = $startDay . " 00:00:00";
        $this->set("startTime", $startTime);

        $calendarData = $this->DateItem->loadDateItemsCalendar($startDay, $endDay);
        
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
        if (empty($this->viewVars['object']['DateItem'])) {
            return;
        }
        foreach ($this->viewVars['object']['DateItem'] as $di) {
            if (empty($di['start_date']) || empty($di['end_date'])) {
                continue;
            }
            if ($di['start_date'] > $di['end_date']) {
                $sDate = strftime(Configure::read("dateTimePattern"), strtotime($di["start_date"]));
                $eDate = strftime(Configure::read("dateTimePattern"), strtotime($di["end_date"]));
                $this->userWarnMessage(sprintf(__('Calendar start date after end date: %s - %s', true), $sDate, $eDate));
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

