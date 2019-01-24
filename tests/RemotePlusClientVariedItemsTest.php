<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
use DPRMC\IceRemotePlusClient\RemotePlusClient;
use PHPUnit\Framework\TestCase;

class RemotePlusClientVariedItemsTest extends TestCase {


    /**
     * @test
     * @group vary
     */
    public function aamtShouldReturnValidData() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $date               = '2018-12-31';
        $item               = 'AAMT';
        $expectedValue      = 4.36625;
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
     * @group vary
     */
    public function accdtShouldReturnValidData() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $date               = '2018-12-31';
        $item               = 'ACCDT';
        $expectedValue      = '20181226';
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