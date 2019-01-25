<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
use DPRMC\IceRemotePlusClient\Exceptions\ItemDoesNotExistInSecurityResponse;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailable;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseHoliday;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseNotExpected;
use DPRMC\IceRemotePlusClient\Exceptions\RemotePlusError;
use DPRMC\IceRemotePlusClient\RemotePlusClient;
use PHPUnit\Framework\TestCase;

class RemotePlusClientErrorsTest extends TestCase {


    /**
     * @test
     * @group error
     */
    public function gettingItemWithErrorShouldThrowException() {

        $this->expectException( RemotePlusError::class );
        $user  = $_ENV[ 'ICE_TEST_USER' ];
        $pass  = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip = '22541QFF4'; // Does not have a price for 2018-12-31
        $date  = '2018-12-31';
        $item  = 'THISITEMDOESNOTEXIST';

        RemotePlusClient::instantiate( $user, $pass )
                        ->addIdentifier( $cusip )
                        ->addDate( $date )
                        ->addItem( $item )
                        ->run();
    }


    /**
     * @test
     * @group error
     */
    public function getItemOnNonExistentItemShouldThrowException() {
        $this->expectException( ItemDoesNotExistInSecurityResponse::class );
        $user      = $_ENV[ 'ICE_TEST_USER' ];
        $pass      = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip     = '22541QFF4'; // Does not have a price for 2018-12-31
        $date      = '2018-12-31';
        $item      = 'IEBID';
        $response  = RemotePlusClient::instantiate( $user, $pass )
                                     ->addIdentifier( $cusip )
                                     ->addDate( $date )
                                     ->addItem( $item )
                                     ->run();
        $responses = $response->getResponses();
        $responses[ $cusip ]->getItem( 'IEASK' );
    }


    /**
     * @test
     * @group error
     */
    public function getItemWithNAValueShouldThrowException() {
        $this->expectException( ItemValueNotAvailable::class );
        $user      = $_ENV[ 'ICE_TEST_USER' ];
        $pass      = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip     = '22541QFF4'; // Does not have a price for 2018-12-31
        $date      = '2018-12-31';
        $item      = 'IEBID';
        $response  = RemotePlusClient::instantiate( $user, $pass )
                                     ->addIdentifier( $cusip )
                                     ->addDate( $date )
                                     ->addItem( $item )
                                     ->run();
        $responses = $response->getResponses();
        $responses[ $cusip ]->getItem( 'IEBID' );
    }

    /**
     * @test
     * @group error
     */
    public function getItemWithNHValueShouldThrowException() {
        //$this->expectException( ItemValueNotAvailableBecauseHoliday::class ); // I should get this, but they are sending !NA now.
        $this->expectException( ItemValueNotAvailable::class );
        $user     = $_ENV[ 'ICE_TEST_USER' ];
        $pass     = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip    = '17307GNX2';
        $date     = '2019-01-01';
        $item     = 'IEBID';
        $response = RemotePlusClient::instantiate( $user, $pass )
                                    ->addIdentifier( $cusip )
                                    ->addDate( $date )
                                    ->addItem( $item )
                                    ->run();

        $responses = $response->getResponses();
        $responses[ $cusip ]->getItem( 'IEBID' );
    }


    /**
     * @test
     * @group error
     */
    public function requestForOldDataShouldThrowException() {
        $this->expectException( RemotePlusError::class );
        $user      = $_ENV[ 'ICE_TEST_USER' ];
        $pass      = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip     = '17307GNX2';
        $date      = '2016-01-01';
        $item      = 'IEBID';
        $response  = RemotePlusClient::instantiate( $user, $pass )
                                     ->addIdentifier( $cusip )
                                     ->addDate( $date )
                                     ->addItem( $item )
                                     ->run();
        $responses = $response->getResponses();
        $responses[ $cusip ]->getItem( 'IEBID' );
    }


    /**
     * @test
     * @group error
     */
    public function requestForEquityPriceOnHolidayShouldThrowException() {
        $this->expectException( ItemValueNotAvailableBecauseHoliday::class );
        $user       = $_ENV[ 'ICE_TEST_USER' ];
        $pass       = $_ENV[ 'ICE_TEST_PASS' ];
        $identifier = 'IBM';
        $date       = '2019-01-01';
        $item       = 'PRC';
        $response   = RemotePlusClient::instantiate( $user, $pass )
                                      ->addIdentifier( $identifier )
                                      ->addDate( $date )
                                      ->addItem( $item )
                                      ->run();
        $responses  = $response->getResponses();
        $responses[ $identifier ]->getItem( $item );
    }

    /**
     * @test
     * @group err1
     */
    public function requestForMdatOnIbmShouldThrowException() {
        $this->expectException( ItemValueNotAvailableBecauseNotExpected::class );
        $user       = $_ENV[ 'ICE_TEST_USER' ];
        $pass       = $_ENV[ 'ICE_TEST_PASS' ];
        $identifier = 'IBM';
        $date       = '2019-01-01';
        $item       = 'MDAT';
        $response   = RemotePlusClient::instantiate( $user, $pass )
                                      ->addIdentifier( $identifier )
                                      ->addDate( $date )
                                      ->addItem( $item )
                                      ->run();
        $responses  = $response->getResponses();
        $prc        = $responses[ $identifier ]->getItem( $item );
    }

}