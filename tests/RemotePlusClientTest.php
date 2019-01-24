<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
use DPRMC\IceRemotePlusClient\RemotePlusClient;
use PHPUnit\Framework\TestCase;

class RemotePlusClientTest extends TestCase {


    /**
     * @test
     */
    public function iebidShouldReturnValidPrice() {
        $user               = $_ENV[ 'ICE_TEST_USER' ];
        $pass               = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip              = '17307GNX2';
        $date               = '2018-12-31';
        $item               = 'IEBID';
        $expectedPrice      = 90.48611;
        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusip( $cusip )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->run();

        $securityResponse = $remotePlusResponse->getByIdentifier( $cusip );
        $this->assertEquals( $expectedPrice, $securityResponse->items[ $item ] );


    }


    /**
     * @test
     */
    public function iebidOnMultipleSecuritiesShouldReturnMultipleResponses() {
        $user   = $_ENV[ 'ICE_TEST_USER' ];
        $pass   = $_ENV[ 'ICE_TEST_PASS' ];
        $cusips = [ '17307GNX2', '22541QFF4' ];
        $date   = '2018-12-31';
        $item   = 'IEBID';

        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addCusips( $cusips )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->run();

        $allSecurityResponses = $remotePlusResponse->getResponses();
        $this->assertCount( 2, $allSecurityResponses );

        $allBIEDPrices = $remotePlusResponse->getAllValuesForItem( $item );
        $this->assertCount( 2, $allBIEDPrices );
    }


    /**
     * @test
     */
    public function addIdentifiersShouldWorkJustLikeAddCusips() {
        $user   = $_ENV[ 'ICE_TEST_USER' ];
        $pass   = $_ENV[ 'ICE_TEST_PASS' ];
        $cusips = [ '17307GNX2', '22541QFF4' ];
        $date   = '2018-12-31';
        $item   = 'IEBID';

        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addIdentifiers( $cusips )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->run();

        $allSecurityResponses = $remotePlusResponse->getResponses();
        $this->assertCount( 2, $allSecurityResponses );
    }

    /**
     * @test
     */
    public function addingDuplicateIdentifierShouldNotAddTheIdentifier() {
        $user  = $_ENV[ 'ICE_TEST_USER' ];
        $pass  = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip = '17307GNX2';
        $date  = '2018-12-31';
        $item  = 'IEBID';

        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addIdentifier( $cusip )
                                              ->addIdentifier( $cusip )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->run();

        $allSecurityResponses = $remotePlusResponse->getResponses();
        $this->assertCount( 1, $allSecurityResponses );
    }


    /**
     * @test
     */
    public function addingDuplicateItemShouldNotAddTheItem() {
        $user  = $_ENV[ 'ICE_TEST_USER' ];
        $pass  = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip = '17307GNX2';
        $date  = '2018-12-31';
        $item  = 'IEBID';

        $remotePlusResponse = RemotePlusClient::instantiate( $user, $pass )
                                              ->addIdentifier( $cusip )
                                              ->addDate( $date )
                                              ->addItem( $item )
                                              ->addItem( $item )
                                              ->run();

        $securityResponse = $remotePlusResponse->getByIdentifier( $cusip );
        $this->assertCount( 1, $securityResponse->items );
    }


    /**
     * @test
     */
    public function dateThatCanNotBeParsedShouldThrowAnException() {
        $this->expectException( DateSentToConstructorIsNotParsable::class );
        $user  = $_ENV[ 'ICE_TEST_USER' ];
        $pass  = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip = '17307GNX2';
        $date  = 'dateThatCanNotBeParsed';
        $item  = 'IEBID';

        RemotePlusClient::instantiate( $user, $pass )
                        ->setDebug( TRUE )
                        ->addIdentifier( $cusip )
                        ->addDate( $date )
                        ->addItem( $item )
                        ->addItem( $item )
                        ->run();
    }


}