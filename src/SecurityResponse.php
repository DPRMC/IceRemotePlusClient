<?php

namespace DPRMC\InteractiveData;

use Carbon\Carbon;

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
    public function addDate(string $date){
        $this->date = Carbon::parse($date);
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
        $this->items[ $code ] = $value;
        return $this;
    }




}