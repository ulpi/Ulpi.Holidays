<?php
/**
 * @author         Ulpi Kans <radarier+gh@gmail.com>
 * @copyright  (c) 2016, Ulpi Kans
 * @package        Ulpi\Holidays
 * @since          2016-03-20
 * @version        0.1.0alpha (AK47)
 */


declare( strict_types = 1 );


namespace Ulpi\Holidays;


/**
 * The Ulpi\Holidays base exception
 *
 * @since v0.1.0alpha (AK47)
 */
class Exception extends \Exception
{


   // <editor-fold desc="= = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Exception constructor.
    *
    * @param string          $message
    * @param int             $code
    * @param \Exception|null $previous
    */
   public function __construct( string $message, int $code = \E_USER_ERROR, \Exception $previous = null )
   {

      parent::__construct( 'Ulpi.Holidays error: ' . $message, $code, $previous );

   }

   // </editor-fold>


}

