<?php

/*

                     DON'T BE A DICK PUBLIC LICENSE

                       Version 1, December 2009

    Copyright (C) 2009 Philip Sturgeon <email@philsturgeon.co.uk>

    Everyone is permitted to copy and distribute verbatim or modified
    copies of this license document, and changing it is allowed as long
    as the name is changed.

                     DON'T BE A DICK PUBLIC LICENSE
       TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

     1. Do whatever you like with the original work, just don't be a dick.

        Being a dick includes - but is not limited to - the following instances:

    1a. Outright copyright infringement - Don't just copy this and change the name.
    1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
    1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

     2. If you become rich through modifications, related works/services, or supporting the original work,
    share the love. Only a dick would make loads off this work and not buy the original works 
    creator(s) a pint.

     3. Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes 
    you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.

*/

/**
* Endzone - Timezone happiness for CodeIgniter
*
* Note that you must set the time_reference config to 'gmt' for maximum win.
*
* @author Kyle Bragger <kyle@forrst.com>
* @package CodeIgniter
* @subpackage Endzone
* @since July 30, 2010
*
*/

class Endzone {
    /* the current CI timezone */
    public $current_zone = 'UTC';
    
    /* CI object */
    private $_ci;
    
    public function __construct()
    {
        $this->_ci =& get_instance();
        
        if ($this->_ci->config->item('time_reference') != 'gmt') die('Endzone: Please set the CodeIgniter time_reference config to gmt');
        
        $this->_ci->load->helper('date');
    }
    
    /**
    * gmt_to_local()
    *
    * Takes a GMT timestamp and converts into a timestamp in the current timezone
    *
    * @param int $time a timestamp; defaults to now()
    *
    */
    public function gmt_to_local($time = null)
    {
        if ($time === null) $time = now();
        
        $time += timezones($this->current_zone) * 3600;
        if ($this->in_dst()) $time += 3600;
        
        return $time;
    }
    
    /**
    * in_dst()
    *
    * Is the current timezone currently in DST?
    *
    * Thoughts:
    * - $time = now() -> is that correct? should the time comparison be done locally?
    *
    * Tip o' the hat: http://www.toosweettobesour.com/2009/03/10/calculating-daylight-savings-time-boundary-in-php/
    *
    */
    public function in_dst()
    {
        if (!$this->dst_supported()) return false;
        
        $time  = now();
        $begin = strtotime('Second Sunday March 0');
        $end   = strtotime('First Sunday November 0');
        
        return ($time >= $begin && $time < $end) ? true : false;
    }
    
    /**
    * dst_supported()
    *
    * Does the current timezone support DST?
    *
    * Tip o' the hat: http://stackoverflow.com/questions/1586552/kittens/1586628#1586628
    *
    */
    public function dst_supported()
    {
        $tzId = $this->_ci_to_php_zone();
        $tz = new DateTimeZone($tzId);
        $trans = $tz->getTransitions();
        return ((count($trans) && $trans[count($trans) - 1]['ts'] > time()));
    }
    
    /**
    * _ci_to_php_zone()
    *
    * Converts a CI timezone ($zone) to a PHP timezone identifier
    *
    * Thoughts:
    * - timezone_name_from_abbr() needs false for param 3 always? should that be the value of dst_supported()?
    *
    */
    private function _ci_to_php_zone($zone = null)
    {
        if ($zone === null) $zone = $this->current_zone;
        
        $offset = timezones($zone);
        return timezone_name_from_abbr('', $offset * 3600, false);
    }
}
