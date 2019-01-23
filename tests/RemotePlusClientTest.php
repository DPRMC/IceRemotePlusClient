<?php

namespace DPRMC\IceRemotePlusClient\Tests;

use DPRMC\IceRemotePlusClient\RemotePlusClient;
use PHPUnit\Framework\TestCase;

class RemotePlusClientTest extends TestCase {

    /**
     * @test
     */
    public function prcShouldReturnValidPrice() {
        $user     = $_ENV[ 'ICE_TEST_USER' ];
        $pass     = $_ENV[ 'ICE_TEST_PASS' ];
        $cusip    = '17307GNX2';
        $date     = '2018-12-31';
        $item     = 'IEBID';
        $response = RemotePlusClient::instantiate( $user, $pass )
                                    ->addCusip( $cusip )
                                    ->addDate( $date )
                                    ->addItem( $item )
                                    ->run();

        print_r( $response );
    }
}