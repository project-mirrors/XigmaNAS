<?php
/*
	grid_properties.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2023 XigmaNAS® <info@xigmanas.com>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS®, either expressed or implied.
*/

declare(strict_types = 1);

namespace system\cron;

use common\properties as myp;

use function gettext;

class grid_properties extends myp\container_row {
	protected $x_scheduler;
	public function init_scheduler(): myp\property_text {
		$title = gettext('Schedule Time');
		$property = $this->x_scheduler = new myp\property_text($this);
		$property->
			set_name('scheduler')->
			set_title($title);
		return $property;
	}
	final public function get_scheduler(): myp\property_text {
		return $this->x_scheduler ?? $this->init_scheduler();
	}
	protected $x_command;
	public function init_command(): myp\property_text {
		$title = gettext('Command');
		$property = $this->x_command = new myp\property_text($this);
		$property->
			set_name('command')->
			set_title($title);
		return $property;
	}
	final public function get_command(): myp\property_text {
		return $this->x_command ?? $this->init_command();
	}
	protected $x_who;
	public function init_who(): myp\property_list {
		$title = gettext('Who');
		$property = $this->x_who = new myp\property_list($this);
		$property->
			set_name('who')->
			set_title($title);
		return $property;
	}
	final public function get_who(): myp\property_list {
		return $this->x_who ?? $this->init_who();
	}
	protected $x_desc;
	public function init_description(): myp\property_description {
		$property = parent::init_description();
		$property->
			set_id('desc')->
			set_name('desc');
		return $property;
	}
	protected $x_minutes;
	public function init_minutes(): myp\property_list_multi {
		$title = gettext('Minutes');
		$property = $this->x_minutes = new myp\property_list_multi($this);
		$property->
			set_name('minute')->
			set_title($title);
		return $property;
	}
	final public function get_minutes(): myp\property_list_multi {
		return $this->x_minutes ?? $this->init_minutes();
	}
	protected $x_hours;
	public function init_hours(): myp\property_list_multi {
		$title = gettext('Hours');
		$property = $this->x_hours = new myp\property_list_multi($this);
		$property->
			set_name('hour')->
			set_title($title);
		return $property;
	}
	final public function get_hours(): myp\property_list_multi {
		return $this->x_hours ?? $this->init_hours();
	}
	protected $x_days;
	public function init_days(): myp\property_list_multi {
		$title = gettext('Days');
		$property = $this->x_days = new myp\property_list_multi($this);
		$property->
			set_name('day')->
			set_title($title);
		return $property;
	}
	final public function get_days(): myp\property_list_multi {
		return $this->x_days ?? $this->init_days();
	}
	protected $x_months;
	public function init_months(): myp\property_list_multi {
		$title = gettext('Months');
		$property = $this->x_months = new myp\property_list_multi($this);
		$property->
			set_name('month')->
			set_title($title);
		return $property;
	}
	final public function get_months(): myp\property_list_multi {
		return $this->x_months ?? $this->init_months();
	}
	protected $x_weekdays;
	public function init_weekdays(): myp\property_list_multi {
		$title = gettext('Weekdays');
		$property = $this->x_weekdays = new myp\property_list_multi($this);
		$property->
			set_name('weekday')->
			set_title($title);
		return $property;
	}
	final public function get_weekdays(): myp\property_list_multi {
		return $this->x_weekdays ?? $this->init_weekdays();
	}
	protected $x_all_minutes;
	public function init_all_minutes(): myp\property_int {
		$title = gettext('All Minutes');
		$property = $this->x_all_minutes = new myp\property_int($this);
		$property->
			set_name('all_mins')->
			set_title($title);
		return $property;
	}
	final public function get_all_minutes(): myp\property_int {
		return $this->x_all_minutes ?? $this->init_all_minutes();
	}
	protected $x_all_hours;
	public function init_all_hours(): myp\property_int {
		$title = gettext('All Hours');
		$property = $this->x_all_hours = new myp\property_int($this);
		$property->
			set_name('all_hours')->
			set_title($title);
		return $property;
	}
	final public function get_all_hours(): myp\property_int {
		return $this->x_all_hours ?? $this->init_all_hours();
	}
	protected $x_all_days;
	public function init_all_days(): myp\property_int {
		$title = gettext('All Days');
		$property = $this->x_all_days = new myp\property_int($this);
		$property->
			set_name('all_days')->
			set_title($title);
		return $property;
	}
	final public function get_all_days(): myp\property_int {
		return $this->x_all_days ?? $this->init_all_days();
	}
	protected $x_all_months;
	public function init_all_months(): myp\property_int {
		$title = gettext('All Months');
		$property = $this->x_all_months = new myp\property_int($this);
		$property->
			set_name('all_months')->
			set_title($title);
		return $property;
	}
	final public function get_all_months(): myp\property_int {
		return $this->x_all_months ?? $this->init_all_months();
	}
	protected $x_all_weekdays;
	public function init_all_weekdays(): myp\property_int {
		$title = gettext('All Weekdays');
		$property = $this->x_all_weekdays = new myp\property_int($this);
		$property->
			set_name('all_weekdays')->
			set_title($title);
		return $property;
	}
	final public function get_all_weekdays(): myp\property_int {
		return $this->x_all_weekdays ?? $this->init_all_weekdays();
	}
}
