<?php

namespace DPRMC\IceRemotePlusClient;


use DPRMC\InteractiveData\SecurityResponse;

/**
 * A wrapper class around a bunch of SecurityResponse objects. This represents all of the data returned by RemotePlus.
 * Class RemotePlusResponse
 * @package DPRMC\IceRemotePlusClient
 */
class RemotePlusResponse {

    /**
     * @var array An array of SecurityResponse objects.
     */
    protected $responses = [];

    /**
     * RemotePlusResponse constructor.
     */
    public function __construct() {
    }

    /**
     * "Setter" method to add a SecurityResponse object to our array of objects.
     * @param SecurityResponse $response
     */
    public function addResponse( SecurityResponse $response ){
        $this->responses[$response->identifier] = $response;
    }

    /**
     * Simple getter method to get an array of all the SecurityResponse objects.
     * @return array
     */
    public function getResponses(){
        return $this->responses;
    }


    /**
     * Pass an item code into this method, and it will return an array of identifier => itemValue. A convenience method.
     * @param string $item
     * @return array
     */
    public function getAllValuesForItem(string $item): array{
        $valuesToReturn = [];

        /**
         * @var SecurityResponse $response
         */
        foreach($this->responses as $response):
            $valuesToReturn[$response->identifier] = $response->items[$item];
        endforeach;

        return $valuesToReturn;
    }

    /**
     * Getter method to return the SecurityResponse object for a given security's data set.
     * @param string $identifier
     * @return SecurityResponse
     */
    public function getByIdentifier(string $identifier): SecurityResponse{
        return $this->responses[$identifier];
    }

}