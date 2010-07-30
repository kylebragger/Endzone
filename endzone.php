<?php

/**
* Endzone - Timezone happiness for CodeIgniter
*
* Kyle Bragger
* Started July 30, 2010
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
        
        $this->_ci->load->helper('date');
    }
    
    /**
    * gmt_to_local()
    *
    * Takes a GMT timestamp and converts into a timestamp in the current timezone
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
