<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace calendartype_thai;
use core_calendar\type_base;

/**
 * Handles calendar functions for the Thai calendar.
 *
 * @package calendartype_thai
 * @author Ekachai Singthong {@link ekachai.singthong@gmail.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structure extends type_base {    

    /**
     * Returns the name of the calendar.
     *
     * This is the non-translated name, usually just
     * the name of the folder.
     *
     * @return string the calendar name
     */
    public function get_name() {
        return 'thai';
    }

    /**
     * Returns a list of all the possible days for all months.
     *
     * This is used to generate the select box for the days
     * in the date selector elements. Some months contain more days
     * than others so this function should return all possible days as
     * we can not predict what month will be chosen (the user
     * may have JS turned off and we need to support this situation in
     * Moodle).
     *
     * @return array the days
     */
    public function get_days() {
        $days = array();

        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     * Returns a list of all the names of the months.
     *
     * @return array the month names
     */
    public function get_months() {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = get_string('month' . $i, 'calendartype_thai');
        }

        return $months;
    }

    /**
     * Returns the minimum year for the calendar.
     *
     * @return int The minimum year
     */
    public function get_min_year() {
        return ((int)date('Y') - 100)+543;
    }

    /**
     * Returns the maximum year for the calendar.
     *
     * @return int The maximum year
     */
    public function get_max_year() {
        return ((int)date('Y') + 100)+543;
    }

    /**
     * Returns an array of years.
     *
     * @param int $minyear
     * @param int $maxyear
     * @return array the years
     */
    public function get_years($minyear = null, $maxyear = null) {
        if (is_null($minyear)) {
            $minyear = $this->get_min_year();
        }

        if (is_null($maxyear)) {
            $maxyear = $this->get_max_year();
        }

        $years = array();
        for ($i = $minyear; $i <= $maxyear; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    /**
     * Returns a multidimensional array with information for day, month, year
     * and the order they are displayed when selecting a date.
     * The order in the array will be the order displayed when selecting a date.
     * Override this function to change the date selector order.
     *
     * @param int $minyear The year to start with
     * @param int $maxyear The year to finish with
     * @return array Full date information
     */
    public function get_date_order($minyear = null, $maxyear = null) {
        $dateinfo = array();
        $dateinfo['day'] = $this->get_days();
        $dateinfo['month'] = $this->get_months();
        $dateinfo['year'] = $this->get_years($minyear, $maxyear);

        return $dateinfo;
    }

    /**
     * Returns the number of days in a week.
     *
     * @return int the number of days
     */
    public function get_num_weekdays() {
        return 7;
    }

    /**
     * Returns an indexed list of all the names of the weekdays.
     *
     * The list starts with the index 0. Each index, representing a
     * day, must be an array that contains the indexes 'shortname'
     * and 'fullname'.
     *
     * @return array array of days
     */
    public function get_weekdays() {
        return array(
            0 => array(
                'shortname' => get_string('wday0', 'calendartype_thai'),
                'fullname' => get_string('weekday0', 'calendartype_thai')
            ),
            1 => array(
                'shortname' => get_string('wday1', 'calendartype_thai'),
                'fullname' => get_string('weekday1', 'calendartype_thai')
            ),
            2 => array(
                'shortname' => get_string('wday2', 'calendartype_thai'),
                'fullname' => get_string('weekday2', 'calendartype_thai')
            ),
            3 => array(
                'shortname' => get_string('wday3', 'calendartype_thai'),
                'fullname' => get_string('weekday3', 'calendartype_thai')
            ),
            4 => array(
                'shortname' => get_string('wday4', 'calendartype_thai'),
                'fullname' => get_string('weekday4', 'calendartype_thai')
            ),
            5 => array(
                'shortname' => get_string('wday5', 'calendartype_thai'),
                'fullname' => get_string('weekday5', 'calendartype_thai')
            ),
            6 => array(
                'shortname' => get_string('wday6', 'calendartype_thai'),
                'fullname' => get_string('weekday6', 'calendartype_thai')
            ),
        );
    }

    /**
     * Returns the index of the starting week day.
     *
     * This may vary, for example some may consider Monday as the start of the week,
     * where as others may consider Sunday the start.
     *
     * @return int
     */
	public function get_starting_weekday() {
        global $CFG;

        if (isset($CFG->calendar_startwday)) {
            $firstday = $CFG->calendar_startwday;
        } else {
            $firstday = get_string('firstdayofweek', 'langconfig');
        }

        if (!is_numeric($firstday)) {
            $startingweekday = CALENDAR_DEFAULT_STARTING_WEEKDAY;
        } else {
            $startingweekday = intval($firstday) % 7;
        }

        return get_user_preferences('calendar_startwday', $startingweekday);
    }

    /**
     * Returns the index of the weekday for a specific calendar date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    public function get_weekday($year, $month, $day) {
        $gdate = $this->convert_to_gregorian($year, $month, $day);
        return intval(date('w', mktime(12, 0, 0, $gdate['month'], $gdate['day'], $gdate['year'])));
    }

    /**
     * Returns the number of days in a given month.
     *
     * @param int $year
     * @param int $month
     * @return int the number of days
     */
	public function get_num_days_in_month($year, $month) {
        return intval(date('t', mktime(0, 0, 0, $month, 1, $year)));
    }

    /**
     * Get the previous month.
     *
     * If the current month is Farvardin, it will get the last month of the previous year.
     *
     * @param int $year
     * @param int $month
     * @return array previous month and year
     */
	public function get_prev_month($year, $month) {
        if ($month == 1) {
            return array(12, $year - 1);
        } else {
            return array($month - 1, $year);
        }
    }

    /**
     * Get the next month.
     *
     * If the current month is Esfand, it will get the first month of the following year.
     *
     * @param int $year
     * @param int $month
     * @return array the following month and year
     */
    public function get_next_month($year, $month) {
        if ($month == 12) {
            return array(1, $year + 1);
        } else {
            return array($month + 1, $year);
        }
    }

    /**
     * Returns a formatted string that represents a date in user time.
     *
     * Returns a formatted string that represents a date in user time
     * <b>WARNING: note that the format is for strftime(), not date().</b>
     * Because of a bug in most Windows time libraries, we can't use
     * the nicer %e, so we have to use %d which has leading zeroes.
     * A lot of the fuss in the function is just getting rid of these leading
     * zeroes as efficiently as possible.
     *
     * If parameter fixday = true (default), then take off leading
     * zero from %d, else maintain it.
     *
     * @param int $time the timestamp in UTC, as obtained from the database
     * @param string $format strftime format
     * @param int|float|string $timezone the timezone to use
     *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @param bool $fixday if true then the leading zero from %d is removed,
     *        if false then the leading zero is maintained
     * @param bool $fixhour if true then the leading zero from %I is removed,
     *        if false then the leading zero is maintained
     * @return string the formatted date/time
     */
	public function timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour) {
        global $CFG;

        $amstring = get_string('am', 'calendartype_thai');
        $pmstring = get_string('pm', 'calendartype_thai');
        $AMstring = get_string('am_caps', 'calendartype_thai');
        $PMstring = get_string('pm_caps', 'calendartype_thai');

        if (empty($format)) {
            $format = get_string('strftimedaydatetime', 'langconfig');
        }

        if (!empty($CFG->nofixday)) {  // Config.php can force %d not to be fixed.
            $fixday = false;
        }

        $tdate = $this->timestamp_to_date_array($time, $timezone);
        //this is not sufficient code, change it. but it works correctly.
        $format = str_replace( array(
            '%a',
            '%A',
            '%b',
            '%B',
            '%d',
            '%m',
            '%y',
            '%Y',
            '%p',
            '%P'
        ), array($tdate['weekday'],
            $tdate['weekday'],
            $tdate['month'],
            $tdate['month'],
            (($tdate['mday'] < 10 && !$fixday) ? '0' : '') . $tdate['mday'],
            ($tdate['mon'] < 10 ? '0' : '') . $tdate['mon'],
            $tdate['year'] % 100,
            $tdate['year'],
            ($tdate['hours'] < 12 ? $AMstring : $PMstring),
            ($tdate['hours'] < 12 ? $amstring : $pmstring)
            ), $format);

        $gregoriancalendar = \core_calendar\type_factory::get_calendar_instance('gregorian');
        return $gregoriancalendar->timestamp_to_date_string($time, $format, $timezone, $fixday, $fixhour);
    }

    /**
     * Given a $time timestamp in GMT (seconds since epoch), returns an array that
     * represents the date in user time.
     *
     * @param int $time Timestamp in GMT
     * @param float|int|string $timezone offset's time with timezone, if float and not 99, then no
     *        dst offset is applied {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * @return array an array that represents the date in user time
     */
    public function timestamp_to_date_array($time, $timezone = 99) {
        $gregoriancalendar = \core_calendar\type_factory::get_calendar_instance('gregorian');

        $date = $gregoriancalendar->timestamp_to_date_array($time, $timezone);
        $tdate = $this->convert_from_gregorian($date['year'], $date['mon'], $date['mday']);

        $date['month'] = get_string("month{$tdate['month']}", 'calendartype_thai');
        $date['weekday'] = get_string("weekday{$date['wday']}", 'calendartype_thai');
        $date['year'] = $tdate['year'];
        $date['mon'] = $tdate['month'];
        $date['mday'] = $tdate['day'];

        return $date;
    }
    

    /**
     * Provided with a day, month, year, hour and minute in Gregorian
     * convert it into the equivalent Thai date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    public function convert_from_gregorian($year, $month, $day, $hour = 0, $minute = 0) {       

        $date = array();
        $date['year'] = $year+543 ;
        $date['month'] = $month;
        $date['day'] = $day;
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }

    /**
     * Provided with a day, month, year, hour and minute in thai
     * convert it into the equivalent Gregorian date.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @return array the converted date
     */
    public function convert_to_gregorian($year, $month, $day, $hour = 0, $minute = 0) {        

        $date = array();
        $date['year'] = $year-543;
        $date['month'] = $month;
        $date['day'] = $day;
        $date['hour'] = $hour;
        $date['minute'] = $minute;

        return $date;
    }

    /**
     * This return locale for windows os.
     *
     * @return string locale
     */
    public function locale_win_charset() {
        return 'utf-8';
    }
}
