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
 * A country depending holiday collection factory
 *
 * @since v0.1.0alpha (AK47)
 */
class HolidayCollectionFactory
{


   // <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Gets the holydays
    * @param  string $countryName The country name in lower case (e.g.: 'germany', 'uk', etc.)
    * @return \Ulpi\Holidays\HolidayCollection|FALSE
    */
   public static function Create( string $countryName )
   {

      $file = __DIR__ . '/Countries/' . $countryName . '.php';

      if ( ! \file_exists( $file ) )
      {
         return false;
      }

      return include $file;

   }

   // </editor-fold>


}

