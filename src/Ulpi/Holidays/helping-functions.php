<?php
/**
 * @author         Ulpi Kans <radarier+gh@gmail.com>
 * @copyright  (c) 2016, Ulpi Kans
 * @since          2016-03-21
 * @version        0.1.0alpha (AK47)
 */

namespace Ulpi\Holidays;


/**
 * Return if the date is at weekend (saturday or sunday)
 *
 * @param  int $year
 * @param  int $month
 * @param  int $day
 * @return bool
 */
function is_weekend( int $year, int $month, int $day ) : bool
{

   $wDay = \intval( \strftime( '%w', \mktime( 0, 0, 0, $month, $day, $year ) ) );

   return 0 === $wDay || 6 === $wDay;

}

function is_sunday( int $year, int $month, int $day ) : bool
{

   return 0 === \intval( \strftime( '%w', \mktime( 0, 0, 0, $month, $day, $year ) ) );

}

function is_saturday( int $year, int $month, int $day ) : bool
{

   return 6 === \intval( \strftime( '%w', \mktime( 0, 0, 0, $month, $day, $year ) ) );

}

