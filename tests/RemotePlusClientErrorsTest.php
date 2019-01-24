<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
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


}