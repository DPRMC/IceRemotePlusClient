<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
use DPRMC\IceRemotePlusClient\RemotePlusClient;
use PHPUnit\Framework\TestCase;

class RemotePlusClientVariedItemsTest extends TestCase {


    /**
     * @test
     * @group vary
     * The AAMT item does not take a date. It returns the Current Annualized Payment Amount is the annual interest
     * payable for fixed income securities, shown as a percentage of par value, or the annualized indicated annual
     * dividend for equities. So the expected value of the AAMT item will change from day to day.
     */
    public function aamtShouldReturnValidData() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $item               = 'AAMT';
        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusip( $cusip )
                                              ->addItem( $item )
                                              ->run();
        $securityResponse   = $remotePlusResponse->getByIdentifier( $cusip );
        $this->assertNotEmpty( $securityResponse->getItem( $item ) );
    }


    /**
     * The AAMT item does not take a date.
     * ACCDT: The final day on which interest accrues at the current rate for the corresponding payment date.
     * @test
     * @group vary
     */
    public function accdtShouldReturnValidData() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $item               = 'ACCDT';
        $expectedValue      = '20181226'; // Something like this, but it will change depending when the test is run.
        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusip( $cusip )
                                              ->addItem( $item )
                                              ->run();
        $securityResponse   = $remotePlusResponse->getByIdentifier( $cusip );
        $this->assertNotEmpty( $securityResponse->getItem( $item ) );
    }


    /**
     * @test
     * @group vary
     */
    public function abidShouldReturnValidData() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $date               = '2018-12-31';
        $item               = 'ABID';
        $expectedValue      = 90.54675;
        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusip( $cusip )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->run();
        $securityResponse   = $remotePlusResponse->getByIdentifier( $cusip );
        $this->assertEquals( $expectedValue, $securityResponse->getItem( $item ) );

    }



    /**
     * @test
     * @group multi
     */
    public function multipleItemsShouldReturnMultiplePrices() {
        $user   = $_ENV[ 'ICE_TEST_USER' ];
        $pass   = $_ENV[ 'ICE_TEST_PASS' ];
        $cusips = [ '17307GNX2',
                    '07325KAG3',
                    '22541QFF4',
                    '933095AF8',
                    '86358EUD6',
                    '07384YTS5' ];
        $date   = '2019-01-23';
        $items  = [ 'IEBID', 'IEMID', 'IEASK' ];

        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusips( $cusips )
                                              ->addDate( $date )
                                              ->addItems( $items )
                                              ->run();

        $midPrices = $remotePlusResponse->getAllValuesForItem( 'IEMID' );
        $this->assertCount( 6, $midPrices );

        $this->expectException( \Exception::class );
        $securityResponse = $remotePlusResponse->getByIdentifier( '22541QFF4' );
        $price            = $securityResponse->getItem( 'IEMID' );
    }


}