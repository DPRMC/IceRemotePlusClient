<?php

namespace DPRMC\IceRemotePlusClient\Exceptions;

use Exception;
use Throwable;

/**
 * Class RemotePlusError
 * @package DPRMC\IceRemotePlusClient\Exceptions
 * @see https://rplus.intdata.com/documentation/RemotePlus_UserGuide.pdf
 */
class RemotePlusError extends Exception {

    /**
     * @var string Starts with !E
     * @see https://rplus.intdata.com/documentation/RemotePlus_UserGuide.pdf
     */
    public $remotePlusErrorCode;

    /**
     * RemotePlusError constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|NULL $previous
     * @param string|NULL $remotePlusErrorCode
     */
    public function __construct( string $message = "", int $code = 0, Throwable $previous = NULL, string $remotePlusErrorCode=null ) {
        parent::__construct( $message, $code, $previous );
        $this->remotePlusErrorCode = $remotePlusErrorCode;
    }
}