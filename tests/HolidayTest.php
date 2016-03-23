<?php

require_once \dirname( __DIR__ ) . '/vendor/autoload.php';

use Ulpi\Holidays\HolidayName;
use Ulpi\Holidays\HolidayType;
use Ulpi\Holidays\Holiday;


/**
 * @author         Ulpi Kans <radarier+gh@gmail.com>
 * @copyright  (c) 2016, Ulpi Kans
 * @since          2016-03-22
 * @version        0.1.0alpha (AK47)
 */
class HolidayTest extends PHPUnit_Framework_TestCase
{

   /**
    * @type Holiday
    */
   private $holidayStatic1;

   /**
    * @type Holiday
    */
   private $holidayStatic2;

   /**
    * @type Holiday
    */
   private $holidayDynamic1;

   /**
    * @type Holiday
    */
   private $holidayDynamic2;

   /**
    * @type Holiday
    */
   private $holidayDynamicBase;

   private $globalCallbacks;


   protected function setUp()
   {

      $this->holidayStatic1 = Holiday::Create( HolidayType::STATIC )
         ->setMonth( 12, false )
         ->setDay  ( 25, false )
         ->addNameTranslations(
            'en', 'Christmas Day', 'fr', 'Noël',                 'it', 'Natale',
            'es', 'Navidad',       'pt', 'Natal do Senhor',      'cz', '1. svátek vánoční',
            'jp', '1.クリスマス' )
         ->addNames(
            HolidayName::Create( 'de', '1. Weihnachtstag' )
               ->setRegions( [ 4, 5, 6, 7, 8, 9, 10, 11, 12, 14 ] )->addRegionsArray( \range( 16, 31 ) ),
            HolidayName::Create( 'de', 'Erster Weihnachtstag' )->setRegions( [ 0, 1, 2, 45, 46 ] ),
            HolidayName::Create( 'de', '1. Weihnachtsfeiertag'     )->setRegions( [ 3, 13 ] ),
            HolidayName::Create( 'de', 'Erster Weihnachtsfeiertag' )
               ->setRegions( [ 15 ] )->addRegionsArray( \range( 32, 44 ) ) );

      $this->holidayStatic2 = Holiday::Create( HolidayType::STATIC )
         ->setMonth( 12, false )->setDay( 27, false ) // 12-27
         // is only valid if 12-26 was on a sunday
         ->setConditionCallback( function( $year ) { return \Ulpi\Holidays\is_sunday( $year, 12, 26 ); }, false )
         ->addNameTranslations(
            'de', 'Verschobener Feiertag - 2. Weihnachtstag',  'fr', 'Vacances Motion - Lendemain de Noël',
            'it', 'Spostato vacanze - Santo Stefano',          'es', 'Movido vacaciones - Sant Esteve',
            'pt', 'Feriado moveu - 2. Dia de Natal',           'cz', 'Přesunuta dovolená - 2. svátek vánoční',
            'jp', '振（り）替（え）休日 - 2.クリスマスの日' )
         ->addName( HolidayName::Create( 'en', 'Moved holiday - Boxing Day' ) );

      $this->holidayDynamic1 = Holiday::Create( HolidayType::DYNAMIC )
         ->setDynamicCallback(
            function( $year )
              {
                 return ( new DateTime( $year . '-11-23' ) )->modify( 'last wednesday' );
              },
            false
         )
         ->addNameTranslations(
            'en', 'Repentance Day',       'fr', 'jour repentance',         'it', 'pentimento Giorno',
            'es', 'Día arrepentimiento',  'pt', 'Dia arrependimento',      'cz', 'Den pokání',
            'jp', '悔い改めの日' )
         ->addName(
            HolidayName::Create( 'de', 'Buß- und Bettag' )->setRegions( [ 12 ] )->addRegionsArray( \range( 16, 31 ) )
         );

      // The 2nd monday in january : seijin no hi- 成人の日
      $this->holidayDynamic2 = Holiday::Create( HolidayType::DYNAMIC )
         ->setDescription( 'seijin no hi' )
         ->setDynamicCallback(
            function( $year )
            {
               return ( new DateTime() )->modify( 'second monday of january ' . $year );
            },
            false
         )
         ->setValidFromYear( 2000, false )
         ->addNameTranslations( 'de', 'Tag der Mündigkeitserklärung' )
         ->addName( HolidayName::Create( 'jp', '成人の日' ) );

      // Easter Monday (Easter sunday + 1 day)…
      $this->holidayDynamicBase = Holiday::Create( HolidayType::DYNAMIC_BASE )
         ->setBaseCallbackName( 'easter_sunday', false )
         ->setDifferenceDays  ( 1, false )
         ->addNameTranslations(
            'de', 'Ostermontag',        'fr', 'Lundi de Pâques',         'it', 'Pasquetta',
            'es', 'Lunes de Pascua',    'pt', 'Feira de Páscoa',         'cz', 'Velikonoční pondělí',
            'jp', '復活の月曜日' )
         ->addName( HolidayName::Create( 'en', 'Easter Monday' ) );

      $this->globalCallbacks =  [
         'easter_sunday' => function( $year )
            {
               return ( new \DateTime() )->setTimestamp( \easter_date( $year ) );
            }
      ];

      parent::setUp();

   }


   // <editor-fold desc="// ======== All GETTER tests ====================">

   // <editor-fold desc="// getDescription()">

   public function testGetDescription_S1()
   {

      $this->assertNull( $this->holidayStatic1->getDescription() );

   }

   public function testGetDescription_S2()
   {

      $this->assertNull( $this->holidayStatic2->getDescription() );

   }

   public function testGetDescription_D1()
   {

      $this->assertNull( $this->holidayDynamic1->getDescription() );

   }

   public function testGetDescription_D2()
   {

      $this->assertSame( 'seijin no hi', $this->holidayDynamic2->getDescription() );

   }

   public function testGetDescription_DB()
   {

      $this->assertNull( $this->holidayDynamicBase->getDescription() );

   }

   // </editor-fold>

   // <editor-fold desc="// getBaseCallbackName()">

   public function testGetBaseCallbackName_S1()
   {

      $this->assertNull( $this->holidayStatic1->getBaseCallbackName() );
   }

   public function testGetBaseCallbackName_S2()
   {

      $this->assertNull( $this->holidayStatic2->getBaseCallbackName() );
   }

   public function testGetBaseCallbackName_D1()
   {

      $this->assertNull( $this->holidayDynamic1->getBaseCallbackName() );
   }

   public function testGetBaseCallbackName_D2()
   {

      $this->assertNull( $this->holidayDynamic2->getBaseCallbackName() );
   }

   public function testGetBaseCallbackName_DB()
   {

      $this->assertEquals( 'easter_sunday', $this->holidayDynamicBase->getBaseCallbackName() );
   }

   // </editor-fold>

   // <editor-fold desc="// getConditionCallback()">

   public function testGetConditionCallback_S1()
   {
      $this->assertNull( $this->holidayStatic1->getConditionCallback() );
   }

   public function testGetConditionCallback_S2()
   {
      $this->assertTrue( \is_callable( $this->holidayStatic2->getConditionCallback() ) );
   }

   public function testGetConditionCallback_D1()
   {
      $this->assertNull( $this->holidayDynamic1->getConditionCallback());
   }

   public function testGetConditionCallback_D2()
   {
      $this->assertNull( $this->holidayDynamic2->getConditionCallback() );
   }

   public function testGetConditionCallback_DB()
   {
      $this->assertNull( $this->holidayDynamicBase->getConditionCallback() );
   }

   // </editor-fold>

   // <editor-fold desc="// getDay()">

   public function testGetDay_S1()
   {
      $this->assertSame( 25, $this->holidayStatic1->getDay() );
   }

   public function testGetDay_S2()
   {
      $this->assertSame( 27, $this->holidayStatic2->getDay() );
   }

   public function testGetDay_D1()
   {
      $this->assertSame( 0, $this->holidayDynamic1->getDay() );
   }

   public function testGetDay_D2()
   {
      $this->assertSame( 0, $this->holidayDynamic2->getDay() );
   }

   public function testGetDay_DB()
   {
      $this->assertSame( 0, $this->holidayDynamicBase->getDay() );
   }

   // </editor-fold>

   // <editor-fold desc="// getDifferenceDays()">

   public function testGetDifferenceDays_S1()
   {
      $this->assertSame( 0, $this->holidayStatic1->getDifferenceDays() );
   }

   public function testGetDifferenceDays_S2()
   {
      $this->assertSame( 0, $this->holidayStatic2->getDifferenceDays() );
   }

   public function testGetDifferenceDays_D1()
   {
      $this->assertSame( 0, $this->holidayDynamic1->getDifferenceDays() );
   }

   public function testGetDifferenceDays_D2()
   {
      $this->assertSame( 0, $this->holidayDynamic2->getDifferenceDays() );
   }

   public function testGetDifferenceDays_DB()
   {
      $this->assertSame( 1, $this->holidayDynamicBase->getDifferenceDays() );
   }

   // </editor-fold>

   // <editor-fold desc="// getDynamicCallback()">

   public function testGetDynamicCallback_S1()
   {
      $this->assertNull( $this->holidayStatic1->getDynamicCallback() );
   }

   public function testGetDynamicCallback_S2()
   {
      $this->assertNull( $this->holidayStatic2->getDynamicCallback() );
   }

   public function testGetDynamicCallback_D1()
   {
      $this->assertTrue( \is_callable( $this->holidayDynamic1->getDynamicCallback() ) );
   }

   public function testGetDynamicCallback_D2()
   {
      $this->assertTrue( \is_callable( $this->holidayDynamic2->getDynamicCallback() ) );
   }

   public function testGetDynamicCallback_DB()
   {
      $this->assertNull( $this->holidayDynamicBase->getDynamicCallback() );
   }

   // </editor-fold>

   // <editor-fold desc="// getMonth()">

   public function testGetMonth_S1()
   {
      $this->assertSame( 12, $this->holidayStatic1->getMonth() );
   }

   public function testGetMonth_S2()
   {
      $this->assertSame( 12, $this->holidayStatic2->getMonth() );
   }

   public function testGetMonth_D1()
   {
      $this->assertSame( 0, $this->holidayDynamic1->getMonth() );
   }

   public function testGetMonth_D2()
   {
      $this->assertSame( 0, $this->holidayDynamic2->getMonth() );
   }

   public function testGetMonth_DB()
   {
      $this->assertSame( 0, $this->holidayDynamicBase->getMonth() );
   }

   // </editor-fold>

   // <editor-fold desc="// getNameTranslated()">

   public function testGetNameTranslated_S1()
   {
      $this->assertSame( '1.クリスマス', $this->holidayStatic1->getNameTranslated( 'jp' ) );
   }

   public function testGetNameTranslated_S2()
   {
      $this->assertSame( 'Verschobener Feiertag - 2. Weihnachtstag', $this->holidayStatic2->getNameTranslated( 'de' ) );
   }

   public function testGetNameTranslated_D1()
   {
      $this->assertSame( 'Repentance Day', $this->holidayDynamic1->getNameTranslated( 'en' ) );
   }

   public function testGetNameTranslated_D2()
   {
      $this->assertFalse( $this->holidayDynamic2->getNameTranslated( 'en' ) );
   }

   public function testGetNameTranslated_DB()
   {
      $this->assertSame( 'Velikonoční pondělí', $this->holidayDynamicBase->getNameTranslated( 'cz' ) );
   }

   // </editor-fold>

   // <editor-fold desc="// getType()">

   public function testGetType_S1()
   {
      $this->assertSame( HolidayType::STATIC, $this->holidayStatic1->getType() );
   }

   public function testGetType_S2()
   {
      $this->assertSame( HolidayType::STATIC, $this->holidayStatic2->getType() );
   }

   public function testGetType_D1()
   {
      $this->assertSame( HolidayType::DYNAMIC, $this->holidayDynamic1->getType() );
   }

   public function testGetType_D2()
   {
      $this->assertSame( HolidayType::DYNAMIC, $this->holidayDynamic2->getType() );
   }

   public function testGetType_DB()
   {
      $this->assertSame( HolidayType::DYNAMIC_BASE, $this->holidayDynamicBase->getType() );
   }

   // </editor-fold>

   // <editor-fold desc="// getValidFromYear()">

   public function testGetValidFromYear_S1()
   {
      $this->assertSame( 0, $this->holidayStatic1->getValidFromYear() );
   }

   public function testGetValidFromYear_S2()
   {
      $this->assertSame( 0, $this->holidayStatic2->getValidFromYear() );
   }

   public function testGetValidFromYear_D1()
   {
      $this->assertSame( 0, $this->holidayDynamic1->getValidFromYear() );
   }

   public function testGetValidFromYear_D2()
   {
      $this->assertSame( 2000, $this->holidayDynamic2->getValidFromYear() );
   }

   public function testGetValidFromYear_DB()
   {
      $this->assertSame( 0, $this->holidayDynamicBase->getValidFromYear() );
   }

   // </editor-fold>

   // <editor-fold desc="// getValidToYear()">

   public function testGetValidToYear_S1()
   {
      $this->assertSame( 0, $this->holidayStatic1->getValidToYear() );
   }

   public function testGetValidToYear_S2()
   {
      $this->assertSame( 0, $this->holidayStatic2->getValidToYear() );
   }

   public function testGetValidToYear_D1()
   {
      $this->assertSame( 0, $this->holidayDynamic1->getValidToYear() );
   }

   public function testGetValidToYear_D2()
   {
      $this->assertSame( 0, $this->holidayDynamic2->getValidToYear() );
   }

   public function testGetValidToYear_DB()
   {
      $this->assertSame( 0, $this->holidayDynamicBase->getValidToYear() );
   }

   // </editor-fold>

   // <editor-fold desc="// getDate()">

   public function testGetDate_S1()
   {
      $this->assertSame( '2016-12-25', $this->holidayStatic1->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_S2_1()
   {
      $this->assertSame( '2021-12-27', $this->holidayStatic2->getDate( 2021, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_S2_2()
   {
      $this->assertFalse( $this->holidayStatic2->getDate( 2016, $this->globalCallbacks ) );
   }

   public function testGetDate_D1_1()
   {
      $this->assertSame( '2016-11-16', $this->holidayDynamic1->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_D1_2()
   {
      $this->assertSame( '2017-11-22', $this->holidayDynamic1->getDate( 2017, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_D1_3()
   {
      $this->assertSame( '2018-11-21', $this->holidayDynamic1->getDate( 2018, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_D2_1()
   {
      $this->assertSame( '2016-01-11', $this->holidayDynamic2->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   public function testGetDate_D2_2()
   {
      $this->assertFalse( $this->holidayDynamic2->getDate( 1999, $this->globalCallbacks ) );
   }

   public function testGetDate_DB()
   {
      $this->assertSame( '2016-03-28', $this->holidayDynamicBase->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' ) );
   }

   // </editor-fold>

   // </editor-fold>


   // <editor-fold desc="// ======== All is* tests =======================">

   // <editor-fold desc="// isValid()">

   public function testIsValid_S1()
   {
      $this->assertTrue( $this->holidayStatic1->isValid( $this->globalCallbacks ) );
   }

   public function testIsValid_S2()
   {
      $this->assertTrue( $this->holidayStatic2->isValid( $this->globalCallbacks ) );
   }

   public function testIsValid_D1()
   {
      $this->assertTrue( $this->holidayDynamic1->isValid( $this->globalCallbacks ) );
   }

   public function testIsValid_D2()
   {
      $this->assertTrue( $this->holidayDynamic2->isValid( $this->globalCallbacks ) );
   }

   public function testIsValid_DB()
   {
      $this->assertTrue( $this->holidayDynamicBase->isValid( $this->globalCallbacks ) );
   }

   // </editor-fold>

   // <editor-fold desc="// isDynamic()">

   public function testIsDynamic_S1()
   {
      $this->assertFalse( $this->holidayStatic1->isDynamic() );
   }

   public function testIsDynamic_S2()
   {
      $this->assertFalse( $this->holidayStatic2->isDynamic() );
   }

   public function testIsDynamic_D1()
   {
      $this->assertTrue( $this->holidayDynamic1->isDynamic() );
   }

   public function testIsDynamic_D2()
   {
      $this->assertTrue( $this->holidayDynamic2->isDynamic() );
   }

   public function testIsDynamic_DB()
   {
      $this->assertTrue( $this->holidayDynamicBase->isDynamic() );
   }

   // </editor-fold>

   // <editor-fold desc="// isRegion( … )">

   public function testIsRegion_S1_1()
   {
      $this->assertFalse( $this->holidayStatic1->isRegion( -1, $refNameObj ) );
   }

   public function testIsRegion_S1_2()
   {
      $this->assertTrue( $this->holidayStatic1->isRegion( 2, $refNameObj ) );
   }

   public function testIsRegion_S1_3()
   {
      $this->assertFalse( $this->holidayStatic1->isRegion( 99, $refNameObj ) );
   }

   public function testIsRegion_S2_1()
   {
      $this->assertTrue( $this->holidayStatic2->isRegion( -1, $refNameObj ) );
      $this->assertEquals( 'Moved holiday - Boxing Day', $refNameObj->getName() );
   }

   public function testIsRegion_S2_2()
   {
      $this->assertTrue( $this->holidayStatic2->isRegion( 2, $refNameObj ) );
      $this->assertEquals( 'Moved holiday - Boxing Day', $refNameObj->getName() );
   }

   public function testIsRegion_D1_1()
   {
      $this->assertFalse( $this->holidayDynamic1->isRegion( 8, $refNameObj ) );
   }

   public function testIsRegion_D1_2()
   {
      $this->assertTrue( $this->holidayDynamic1->isRegion( 12, $refNameObj ) );
   }

   public function testIsRegion_D1_3()
   {
      $this->assertTrue( $this->holidayDynamic1->isRegion( 16, $refNameObj ) );
   }

   public function testIsRegion_D1_4()
   {
      $this->assertTrue( $this->holidayDynamic1->isRegion( 31, $refNameObj ) );
   }

   public function testIsRegion_D1_5()
   {
      $this->assertFalse( $this->holidayDynamic1->isRegion( 32, $refNameObj ) );
   }

   public function testIsRegion_D2_1()
   {
      $this->assertTrue( $this->holidayDynamic2->isRegion( 32, $refNameObj ) );
   }

   public function testIsRegion_D2_2()
   {
      $this->assertTrue( $this->holidayDynamic2->isRegion( -1, $refNameObj ) );
   }

   public function testIsRegion_DB_1()
   {
      $this->assertTrue( $this->holidayDynamicBase->isRegion( 100, $refNameObj ) );
   }

   public function testIsRegion_DB_2()
   {
      $this->assertTrue( $this->holidayDynamicBase->isRegion( -1, $refNameObj ) );
   }

   // </editor-fold>

   // <editor-fold desc="// isStatic()">

   public function testIsStatic_S1()
   {
      $this->assertTrue( $this->holidayStatic1->isStatic() );
   }

   public function testIsStatic_S2()
   {
      $this->assertTrue( $this->holidayStatic2->isStatic() );
   }

   public function testIsStatic_D1()
   {
      $this->assertFalse( $this->holidayDynamic1->isStatic() );
   }

   public function testIsStatic_D2()
   {
      $this->assertFalse( $this->holidayDynamic2->isStatic() );
   }

   public function testIsStatic_DB()
   {
      $this->assertFalse( $this->holidayDynamicBase->isStatic() );
   }

   // </editor-fold>

   // </editor-fold>


   // <editor-fold desc="// ======== All add* tests ======================">

   // <editor-fold desc="// addName( … )">

   public function testAddName_S1()
   {
      $this->holidayStatic1->addName( HolidayName::Create( 'de', 'Heiliges Foo' )->setRegions( [ 48 ] ) );
      $this->assertEquals( 'Heiliges Foo', $this->holidayStatic1->getName( 48 ) );
      $this->holidayStatic1->setNames( [
         HolidayName::Create( 'de', '1. Weihnachtstag' )
                    ->setRegions( [ 4, 5, 6, 7, 8, 9, 10, 11, 12, 14 ] )->addRegionsArray( \range( 16, 31 ) ),
         HolidayName::Create( 'de', 'Erster Weihnachtstag' )->setRegions( [ 0, 1, 2, 45, 46 ] ),
         HolidayName::Create( 'de', '1. Weihnachtsfeiertag'     )->setRegions( [ 3, 13 ] ),
         HolidayName::Create( 'de', 'Erster Weihnachtsfeiertag' )
                    ->setRegions( [ 15 ] )->addRegionsArray( \range( 32, 44 ) ) ] );
   }

   public function testAddName_D1()
   {
      $this->holidayDynamic1->addName( HolidayName::Create( 'en', '成人の日' )->setRegions( [ 4 ] ) );
      $this->assertEquals( '成人の日', $this->holidayDynamic1->getName( 4 ) );
      $this->holidayDynamic1->setNames( [
         HolidayName::Create( 'de', 'Buß- und Bettag' )->setRegions( [ 12 ] )->addRegionsArray( \range( 16, 31 ) ) ] );
   }

   // </editor-fold>

   public function testAddNames()
   {
      $this->holidayStatic1->addNames(
         HolidayName::Create( 'de', 'Foo' )->setRegions( [ 47 ] ),
         HolidayName::Create( 'de', 'Bar' )->setRegions( [ 48, 49 ] ),
         HolidayName::Create( 'de', 'Baz' )->setRegions( [ 50 ] )
      );
      $this->assertSame( 'Foo', $this->holidayStatic1->getName( 47 ) );
      $this->assertSame( 'Bar', $this->holidayStatic1->getName( 48 ) );
      $this->assertSame( 'Bar', $this->holidayStatic1->getName( 49 ) );
      $this->assertSame( 'Baz', $this->holidayStatic1->getName( 50 ) );
      $this->holidayStatic1->setNames( [
         HolidayName::Create( 'de', '1. Weihnachtstag' )
                    ->setRegions( [ 4, 5, 6, 7, 8, 9, 10, 11, 12, 14 ] )->addRegionsArray( \range( 16, 31 ) ),
         HolidayName::Create( 'de', 'Erster Weihnachtstag' )->setRegions( [ 0, 1, 2, 45, 46 ] ),
         HolidayName::Create( 'de', '1. Weihnachtsfeiertag'     )->setRegions( [ 3, 13 ] ),
         HolidayName::Create( 'de', 'Erster Weihnachtsfeiertag' )
                    ->setRegions( [ 15 ] )->addRegionsArray( \range( 32, 44 ) ) ] );
   }

   // </editor-fold>



   public function testSetBaseCallbackName_S1()
   {
      $this->assertSame( 'testTEST', $this->holidayStatic1->setBaseCallbackName( 'testTEST' )->getBaseCallbackName() );
      $this->holidayStatic1->setBaseCallbackName( '' );
   }

   public function testSetBaseCallbackName_S2()
   {
      $this->assertSame( '$testTEST', $this->holidayStatic2->setBaseCallbackName( '$testTEST' )->getBaseCallbackName() );
      $this->holidayStatic2->setBaseCallbackName( '' );
   }

   public function testSetBaseCallbackName_D1()
   {
      $this->assertSame( '#testTEST', $this->holidayDynamic1->setBaseCallbackName( '#testTEST' )->getBaseCallbackName() );
      $this->holidayDynamic1->setBaseCallbackName( '' );
   }

   public function testSetBaseCallbackName_D2()
   {
      $this->assertSame( ':testøÆTEST', $this->holidayDynamic2->setBaseCallbackName( ':testøÆTEST' )->getBaseCallbackName() );
      $this->holidayDynamic2->setBaseCallbackName( '' );
   }

   public function testSetBaseCallbackName_DB()
   {
      $this->assertSame( 'äöüÄÖÜß', $this->holidayDynamicBase->setBaseCallbackName( 'äöüÄÖÜß' )->getBaseCallbackName() );
      $this->holidayDynamic2->setBaseCallbackName( '' );
   }

   public function testSetDay_S1_1()
   {
      $this->assertSame( 11, $this->holidayStatic1->setDay( 11 )->getDay() );
      $this->holidayStatic1->setDay( 25 );
   }

   public function testSetDay_S1_2()
   {
      $this->assertSame( 0, $this->holidayStatic1->setDay( -1 )->getDay() );
      $this->holidayStatic1->setDay( 25 );
   }

   public function testSetDay_S1_3()
   {
      $this->assertSame( 31, $this->holidayStatic1->setDay( 32 )->getDay() );
      $this->holidayStatic1->setDay( 25 );
   }

   public function testSetMonth_S1_1()
   {
      $this->assertSame( 11, $this->holidayStatic1->setMonth( 11 )->getMonth() );
      $this->holidayStatic1->setMonth( 12 );
   }

   public function testSetMonth_S1_2()
   {
      $this->assertSame( 0, $this->holidayStatic1->setMonth( -1 )->getMonth() );
      $this->holidayStatic1->setMonth( 12 );
   }

   public function testSetMonth_S1_3()
   {
      $this->assertSame( 3, $this->holidayStatic1->setMonth( 15 )->getMonth() );
      $this->holidayStatic1->setMonth( 12 );
   }

   public function testSetValidFromYear()
   {
      $this->assertFalse( $this->holidayStatic1->setValidFromYear( 2017 )->getDate( 2016, [] ) );
      $this->assertSame( '2016-12-25', $this->holidayStatic1->setValidFromYear( 0 )->getDate( 2016, [] )->format( 'Y-m-d' ) );
   }

   public function testSetConditionCallback()
   {
      $this->holidayStatic1->setConditionCallback( function( $year ) { return $year > 2016; } );
      $this->assertFalse( $this->holidayStatic1->getDate( 2016, [] ) );
      $this->holidayStatic1->setConditionCallback( null );
      $this->assertSame( '2016-12-25', $this->holidayStatic1->setValidFromYear( 0 )->getDate( 2016, [] )->format( 'Y-m-d' ) );
   }

   public function testSetDescription_S1()
   {
      $this->assertSame( 'Foo', $this->holidayStatic1->setDescription( 'Foo' )->getDescription() );
   }

   public function testSetDescription_S2()
   {
      $this->assertSame( '<b>Foo</b>', $this->holidayStatic2->setDescription( '<b>Foo</b>' )->getDescription() );
   }

   public function testSetDescription_D1()
   {
      $this->assertSame( 'Føø Bær', $this->holidayDynamic1->setDescription( 'Føø Bær' )->getDescription() );
   }

   public function testSetDescription_D2()
   {
      $this->assertSame( '»«¢„“”µ·…–', $this->holidayDynamic2->setDescription( '»«¢„“”µ·…–' )->getDescription() );
   }

   public function testSetDescription_DB()
   {
      $this->assertSame( 'Foo Bar Baz', $this->holidayDynamicBase->setDescription( 'Foo Bar Baz' )->getDescription() );
   }

   public function testSetDifferenceDays_DB()
   {
      $this->assertSame(
         '2016-03-29',
         $this->holidayDynamicBase->setDifferenceDays( 2 )->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' )
      );
      $this->holidayDynamicBase->setDifferenceDays( 1 );
   }

   public function testSetDifferenceDays_S1()
   {
      $this->assertSame(
         '2016-12-25',
         $this->holidayStatic1->setDifferenceDays( 2 )->getDate( 2016, $this->globalCallbacks )->format( 'Y-m-d' )
      );
      $this->holidayStatic1->setDifferenceDays( 0 );
   }

   public function testSetDynamicCallback()
   {
      $this->holidayDynamic1->setDynamicCallback(
         function( $year )
         {
            return ( new DateTime( $year . '-11-23' ) )->modify( 'last tuesday' );
         }
      );
      $this->assertSame( '2016-11-22', $this->holidayDynamic1->getDate( 2016, [] )->format( 'Y-m-d' ) );
      $this->holidayDynamic1->setDynamicCallback(
         function( $year )
         {
            return ( new DateTime( $year . '-11-23' ) )->modify( 'last wednesday' );
         }
      );
   }

   public function testSetNameTranslation()
   {
      $this->holidayStatic1->setNameTranslation( 'xy', 'Holy Foo' );
      $this->assertSame( 'Erster Weihnachtstag', $this->holidayStatic1->getName( 2 ) );
      $this->assertSame( 'Holy Foo', $this->holidayStatic1->getName( 2, 'xy' ) );
   }

   public function testSetNameTranslations()
   {
      $this->holidayStatic1->setNameTranslations( 'xy', 'Holy Foo', 'ab', 'cde' );
      $this->assertSame( 'Erster Weihnachtstag', $this->holidayStatic1->getName( 2 ) );
      $this->assertSame( 'Holy Foo', $this->holidayStatic1->getName( 2, 'xy' ) );
      $this->assertSame( 'cde', $this->holidayStatic1->getName( 2, 'ab' ) );
   }


   // TODO

   /* * /
   public function testSetType()
   {
      # $this->holidayStatic1->setType( HolidayType::DYNAMIC );

      #$this->assertTrue( $this->holidayStatic1->isStatic() );
      #$this->assertTrue( $this->holidayStatic2->isStatic() );
      #$this->assertFalse( $this->holidayDynamic1->isStatic() );
      #$this->assertFalse( $this->holidayDynamic2->isStatic() );
      #$this->assertFalse( $this->holidayDynamicBase->isStatic() );
   }


   // TODO

   /* * /
   public function testSetValidToYear()
   {
      # $this->holidayStatic1->setValidToYear( 1999 );
      #$this->assertTrue( $this->holidayStatic1->isStatic() );
      #$this->assertTrue( $this->holidayStatic2->isStatic() );
      #$this->assertFalse( $this->holidayDynamic1->isStatic() );
      #$this->assertFalse( $this->holidayDynamic2->isStatic() );
      #$this->assertFalse( $this->holidayDynamicBase->isStatic() );
   }/**/



}
