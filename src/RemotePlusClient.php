<?php

namespace DPRMC\IceRemotePlusClient;

use DPRMC\CUSIP;
use DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable;
use DPRMC\InteractiveData\SecurityResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * This is the parent class that all API calls must extend.
 * Class RemotePlusClient
 * @package DPRMC\InteractiveData
 */
class RemotePlusClient {

    /**
     * The base URI for the Remote Plus system.
     */
    const BASE_URI = 'http://rplus.interactivedata.com';

    /**
     * The page (resource) to POST your Remote Plus query.
     */
    const PAGE = '/cgi/nph-rplus';

    /**
     * @var string Your username supplied by Interactive Data.
     */
    protected $user = '';

    /**
     * @var string The password assigned to your username from Interactive Data.
     */
    protected $pass = '';

    /**
     * @var \GuzzleHttp\Client The GuzzleHttp client used to POST to the Remote Plus API.
     */
    protected $client;

    /**
     * @var Request; The request to the Remote Plus API
     */
    protected $request;

    /**
     * @var Response The response from the Remote Plus API
     */
    protected $response;

    /**
     * @var string The value required by Remote Plus for authentication.
     */
    protected $authorizationHeaderValue = '';

    /**
     * @var bool A parameter we pass in the request to Remote Plus to enable debugging information to be returned.
     */
    protected $remotePlusDebug = TRUE;

    /**
     * @var float The HTTP version that Remote Plus expects for requests.
     */
    protected $remotePlusHttpVersion = 1.0;

    /**
     * @var string The Content-Type header value that Remote Plus is expecting.
     */
    protected $remotePlusContentType = 'application/x-www-form-urlencoded';


    /**
     * @var string The formatted body of the request being sent to the Remote Plus API.
     */
    protected $requestBody = '';

    /**
     * @var array An array of security identifiers that you want to pull data on.
     */
    protected $identifiers = [];

    /**
     * @var array An array of item codes that represent data points that you want to pull for each security.
     */
    protected $items = [];

    /**
     * @var string The date you want to get item values on these securities.
     */
    protected $date;

    /**
     * RemotePlusClient constructor.
     *
     * @param $user string The username given to you by Interactive Data
     * @param $pass string The password for the above username.
     */
    public function __construct( $user, $pass ) {
        $this->user                     = $user;
        $this->pass                     = $pass;
        $this->client                   = new Client( [ 'base_uri' => self::BASE_URI ] );
        $this->authorizationHeaderValue = $this->getAuthenticationHeaderValue( $this->user, $this->pass );
    }

    public static function instantiate( string $user, string $pass ) {
        return new static( $user, $pass );
    }

    /**
     * Adds an array of security identifiers to the list you want to retrieve.
     * @param array $identifiers An array of security identifiers that you want to retrieve data on.
     * @return $this
     */
    public function addIdentifiers( array $identifiers ) {
        foreach ( $identifiers as $identifier ):
            $this->addIdentifier( $identifier );
        endforeach;
        return $this;
    }

    /**
     * Adds an individual security identifier to the list you want to retrieve.
     * @param string $identifier
     * @return $this
     */
    public function addIdentifier( string $identifier ) {
        if ( FALSE == $this->identifierExists( $identifier ) ):
            $this->identifiers[] = trim( $identifier );
        endif;
        return $this;
    }

    /**
     * Logic function to improve readability.
     * @param string $identifier
     * @return bool
     */
    protected function identifierExists( string $identifier ): bool {
        if ( FALSE === array_search( $identifier, $this->identifiers ) ):
            return FALSE;
        endif;
        return TRUE;
    }

    /**
     * A wrapper around the addIdentifiers() fluent method when the identifiers are known to a list of CUSIPs.
     * @param array $cusips An array of CUSIP security identifiers.
     * @return $this
     */
    public function addCusips( array $cusips ) {
        foreach ( $cusips as $cusip ):
            $this->addCusip( $cusip );
        endforeach;
        return $this;
    }


    /**
     * Adds an individual CUSIP to the list of security identifiers you want to retrieve.
     * @param string $cusip A CUSIP security identifier.
     * @return $this
     */
    public function addCusip( string $cusip ) {
        if ( CUSIP::isCUSIP( $cusip ) ):
            $this->addIdentifier( $cusip );
        endif;
        return $this;
    }


    /**
     * @param string $item
     * @return $this
     */
    public function addItem( string $item ) {
        if ( FALSE === $this->itemExists( $item ) ):
            $this->items[] = $item;
        endif;
        return $this;
    }

    /**
     * A logic function to improve readability.
     * @param string $item
     * @return bool
     */
    protected function itemExists( string $item ): bool {
        if ( FALSE === array_search( $item, $this->items ) ):
            return FALSE;
        endif;
        return TRUE;
    }

    /**
     * @param string $date A string date that can be parsed by PHP's strtotime() function.
     * @return $this
     * @throws DateSentToConstructorIsNotParsable
     */
    public function addDate( string $date ) {
        $this->date = $this->formatDateForRemotePlus( $date );
        return $this;
    }


    /**
     * Returns the value required by Remote Plus for the Authorization header.
     *
     * @param string $username The username set by Interactive Data
     * @param string $pass The password assigned by Interactive Data
     *
     * @return string The value needed for the Authorization header.
     */
    protected function getAuthenticationHeaderValue( $username, $pass ) {
        return "Basic " . $this->encodeUserAndPassForBasicAuthentication( $username, $pass );
    }

    /**
     * Encodes the user and pass as required by the Basic Authorization.
     * @see https://en.wikipedia.org/wiki/Basic_access_authentication
     *
     * @param string $username The username set by Interactive Data
     * @param string $pass The password assigned by Interactive Data
     *
     * @return string The base64 encoded user:pass string.
     */
    protected function encodeUserAndPassForBasicAuthentication( $username, $pass ) {
        return base64_encode( $username . ':' . $pass );
    }

    /**
     * @return RemotePlusResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run(): RemotePlusResponse {
        $this->generateBodyForRequest();
        $this->sendRequest();

        return $this->processResponse();
    }

    /**
     * Sends the request to Remote Plus, and saves the Response object into our local $response property.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest() {
        $this->response = $this->client->request( 'POST', self::PAGE, [
            'debug'   => $this->remotePlusDebug,
            'version' => $this->remotePlusHttpVersion,
            'headers' => [ 'Content-Type'  => $this->remotePlusContentType,
                           'Authorization' => $this->getAuthenticationHeaderValue( $this->user, $this->pass ), ],
            'body'    => $this->requestBody,
        ] );
    }


    /**
     * The RemotePlus system requires dates to be formatted as yyyymmdd
     * @param string $date Any string that can be parsed by PHP's strtotime()
     * @return string The $date parameter formatted as yyyymmdd (or in PHP's syntax: Ymd)
     * @throws \DPRMC\IceRemotePlusClient\Exceptions\DateSentToConstructorIsNotParsable
     */
    protected function formatDateForRemotePlus( string $date ) {
        $strTime = strtotime( $date );
        if ( $strTime === FALSE ):
            throw new DateSentToConstructorIsNotParsable( "We could not parse the date you sent to the constructor: [" . $date . "]" );
        endif;
        $date = date( 'Ymd', $strTime );

        return (string)$date;
    }


    /**
     * Extracted this into it's own function so I can stub and test without
     * having to make a request to the IDC server.
     * @return string
     */
    protected function getBodyFromResponse(): string {
        return (string)$this->response->getBody();
    }


    /**
     * @return RemotePlusResponse
     */
    protected function processResponse(): RemotePlusResponse {
        $body = $this->getBodyFromResponse();

        $itemValues = explode( "\n", $body );
        $itemValues = array_map( 'trim', $itemValues );
        $itemValues = array_filter( $itemValues );
        array_pop( $itemValues ); // Remove the CRC check.

        $remotePlusResponse = new RemotePlusResponse();

        foreach ( $this->identifiers as $i => $identifier ):
            $securityResponse     = SecurityResponse::instantiate()
                                                    ->addIdentifier( $identifier )
                                                    ->addDate( $this->date );
            $individualItemValues = explode( ',', $itemValues[ $i ] );

            foreach ( $individualItemValues as $j => $item ):
                $securityResponse->addItem(
                    $this->items[ $j ],
                    $item
                );
            endforeach;

            $remotePlusResponse->addResponse( $securityResponse );
        endforeach;

        return $remotePlusResponse;
    }

    /**
     * Sets the $this->requestBody property. Every type of request sent to
     * Remote Plus has a different syntax. It makes sense to force the child
     * classes to implement that code.
     */
    /**
     * The Remote Plus API requires the request body to be formatted in a very specific way.
     * The following body is formatted to pull the prices for a list of CUSIPs from a specific date.
     */
    protected function generateBodyForRequest() {

        $identifiers = implode( ',', $this->identifiers );
        $items       = implode( ',', $this->items );

        $this->requestBody = 'Request=' . urlencode( "GET,(" . $identifiers ) . "),(" . $items . ")," . $this->date . "&Done=flag\n";
    }
}