<?php

namespace DPRMC\InteractiveData;

use Carbon\Carbon;
use DPRMC\IceRemotePlusClient\Exceptions\ItemDoesNotExistInSecurityResponse;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailable;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseErrorCode5000;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseErrorCode6000;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseErrorCode7000;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseErrorCode8000;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseHoliday;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseNotExpected;
use DPRMC\IceRemotePlusClient\Exceptions\ItemValueNotAvailableBecauseNotReported;

/**
 * Represents all of the data items requested for a specific security.
 * Class SecurityResponse
 * @package DPRMC\InteractiveData
 */
class SecurityResponse {
    /**
     * @var string The security identifier
     */
    public $identifier;

    /**
     * @var Carbon The date of the data being pulled.
     */
    public $date;

    /**
     * @var array An array of itemCode => value for each item code you want to pull for this security.
     */
    public $items; // array itemCode => value

    /**
     * SecurityResponse constructor.
     */
    public function __construct() {
    }

    /**
     * @return SecurityResponse
     */
    public static function instantiate() {
        return new static();
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function addIdentifier( string $identifier ) {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function addDate( string $date ) {
        $this->date = Carbon::parse( $date );
        return $this;
    }

    /**
     * @param string $code
     * @param null $value
     * @return $this
     *
     * The following are special values that indicate some kind of non-response occurred.
     * “!NA” not available
     * “!NH” holiday (only applicable to US/Canadian securities)
     * “!NE” not expected (e.g., prices for future dates)
     * “!NR” not reported
     * “!N5” an error code 5000 was returned
     * “!N6” an error code 6000 was returned
     * “!N7” an error code 7000 was returned
     * “!N8” an error code 8000 was returned
     */
    public function addItem( string $code, $value = NULL ) {
        $this->items[ $code ] = trim( $value, ' "' );
        return $this;
    }

    /**
     * Logic method to improve readability.
     * @param string $item
     * @return bool
     */
    protected function itemExists( string $item ): bool {
        if ( isset( $this->items[ $item ] ) ):
            return TRUE;
        endif;
        return FALSE;
    }

    /**
     * @param string $item The item code of the value you want to retrieve.
     * @return mixed
     * @throws ItemDoesNotExistInSecurityResponse
     * @throws ItemValueNotAvailable
     * @throws ItemValueNotAvailableBecauseErrorCode5000
     * @throws ItemValueNotAvailableBecauseErrorCode6000
     * @throws ItemValueNotAvailableBecauseErrorCode7000
     * @throws ItemValueNotAvailableBecauseErrorCode8000
     * @throws ItemValueNotAvailableBecauseHoliday
     * @throws ItemValueNotAvailableBecauseNotExpected
     * @throws ItemValueNotAvailableBecauseNotReported
     */
    public function getItem( string $item ) {
        if ( FALSE === $this->itemExists( $item ) ):
            throw new ItemDoesNotExistInSecurityResponse();
        endif;

        $value = $this->items[ $item ];

        switch ( $value ):
            case '!NA':
                throw new ItemValueNotAvailable();
            case '!NH':
                throw new ItemValueNotAvailableBecauseHoliday();
            case '!NE':
                throw new ItemValueNotAvailableBecauseNotExpected();
            case '!NR':
                throw new ItemValueNotAvailableBecauseNotReported();
            case '!N5':
                throw new ItemValueNotAvailableBecauseErrorCode5000();
            case '!N6':
                throw new ItemValueNotAvailableBecauseErrorCode6000();
            case '!N7':
                throw new ItemValueNotAvailableBecauseErrorCode7000();
            case '!N8':
                throw new ItemValueNotAvailableBecauseErrorCode8000();
        endswitch;

        return $value;
    }


}