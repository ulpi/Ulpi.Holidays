<?php

require_once \dirname( __DIR__ ) . '/vendor/autoload.php';

use Ulpi\Holidays\HolidayName;

/**
 * @author         Ulpi Kans <radarier+gh@gmail.com>
 * @copyright  (c) 2016, Ulpi Kans
 * @since          2016-03-21
 * @version        0.1.0alpha (AK47)
 */
class HolidayNameTest extends PHPUnit_Framework_TestCase
{

   /**
    * @type HolidayName
    */
   private $name1;

   /**
    * @type HolidayName
    */
   private $name2;


   protected function setUp()
   {
      $this->name1 = HolidayName::Create( 'de', 'Karfreitag' );
      $this->name2 = HolidayName::Create( 'jp', 'グッドフライデー' )->setRegions( [ 1, 5 ] );
      parent::setUp();
   }

   public function testGetName()
   {
      $this->assertSame( 'Karfreitag', $this->name1->getName() );
      $this->assertSame( 'グッドフライデー', $this->name2->getName() );
   }

   public function testGetDefaultLanguage()
   {
      $this->assertSame( 'de', $this->name1->getDefaultLanguage() );
      $this->assertSame( 'jp', $this->name2->getDefaultLanguage() );
   }

   public function testGetRegions()
   {
      //$this->name1->addRegionsArray( [ 4, 12 ] );
      $this->assertEquals( [ 1, 5 ], $this->name2->getRegions() );
      $this->assertEquals( [ -1 ], $this->name1->getRegions() );
   }

   public function testAddRegionsArray()
   {
      $this->name2->addRegionsArray( [ 4, 5, 12 ] );
      $this->assertEquals( [ 1, 5, 4, 12 ], $this->name2->getRegions() );
      $this->name2->setRegions( [ 1, 5 ] );
   }

   public function testAddRegion()
   {
      $this->name2->addRegion( 4 )->addRegion( 5 );
      $this->assertEquals( [ 1, 5, 4 ], $this->name2->getRegions() );
      $this->name2->setRegions( [ 1, 5 ] );
   }

   public function testAddRegions()
   {
      $this->name2->addRegions( 4, 5, 12 );
      $this->assertEquals( [ 1, 5, 4, 12 ], $this->name2->getRegions() );
      $this->name2->setRegions( [ 1, 5 ] );
   }

   public function testMatchesAllRegions()
   {
      $this->assertTrue( $this->name1->matchesAllRegions() );
      $this->assertFalse( $this->name2->matchesAllRegions() );
   }


}
