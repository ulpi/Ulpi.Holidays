<?php
/**
 * @author         Ulpi Kans <radarier+gh@gmail.com>
 * @copyright  (c) 2016, Ulpi Kans
 * @package        Ulpi\Holidays
 * @since          2016-03-21
 * @version        0.1.0alpha (AK47)
 */


declare( strict_types = 1 );


namespace Ulpi\Holidays;


/**
 * Defines all countries with known holydays.
 *
 * @package Ulpi\Holidays
 */
interface HolidayCountry
{


   /**
    * Germany
    */
   const GERMANY = 'germany';

   /**
    * Austria
    */
   const AUSTRIA = 'austria';

   /**
    * United kingdom
    */
   const UNITED_KINGDOM = 'uk';

   /**
    * Defines all known countries.
    *
    * @type array
    */
   const KNOWN_COUNTRIES = [ self::GERMANY, self::AUSTRIA ];

}

