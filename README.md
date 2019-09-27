# The ICE Remote Plus Client

[![](https://img.shields.io/packagist/v/dprmc/ice-remote-plus-client.svg)](https://packagist.org/packages/dprmc/ice-remote-plus-client)
![](https://img.shields.io/coveralls/github/DPRMC/IceRemotePlusClient.svg)
[![](https://img.shields.io/scrutinizer/g/DPRMC/IceRemotePlusClient.svg)](https://scrutinizer-ci.com/g/DPRMC/IceRemotePlusClient)
![](https://img.shields.io/travis/DPRMC/IceRemotePlusClient.svg)
![](https://img.shields.io/packagist/l/DPRMC/ice-remote-plus-client.svg)
![](https://img.shields.io/github/issues/DPRMC/IceRemotePlusClient.svg)
![Packagist](https://img.shields.io/packagist/dt/dprmc/ice-remote-plus-client)

## Important Notice Regarding RMBS PRC Availability
> RMBS securities are expected to post by 16:00 EST.
>
> -- <cite>Jon Anderson - Client Support - ICE  Data Services - 2019-01-29</cite>


## Documentation from The ICE
https://rplus.intdata.com/documentation/RemotePlus_UserGuide.pdf

You will have to ask your rep at theice.com for a document titled, "RemotePlusSM Guide to Data" for details about each of the available item codes you can request.



## Summary
This PHP library acts as a client for The ICE's RemotePlus API.

## Examples

Below is the basic syntax for using the RemotePlusClient.
```php
$user   = $_ENV[ 'ICE_USER' ];
$pass   = $_ENV[ 'ICE_PASS' ];
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
                                      
// Get all of the MID prices
$midPrices = $remotePlusResponse->getAllValuesForItem( 'IEMID' );
print_r($midPrices);
/*
Array
(
    [17307GNX2] => 91.31362
    [07325KAG3] => 90.33492
    [22541QFF4] => !NA
    [933095AF8] => 29.60287
    [86358EUD6] => 66.0742
    [07384YTS5] => 71.06705
)
*/

// Get the SecurityResponse object for a given CUSIP.
// (this one does not have a valid price for that date, so this will throw a ItemValueNotAvailable exception.
$securityResponse = $remotePlusResponse->getByIdentifier( '22541QFF4' );
$price            = $securityResponse->getItem( 'IEMID' );
```

## Other Available Functions
The RemotePlusClient run() method will return my RemotePlusResponse object. Below is an example of how you would interact with that object.

```php
// Returns an associative array of all the SecurityResponse objects, using the security identifier as the key. 
$allSecurityResponses = $remotePlusResponse->getResponses();
foreach($allSecurityResponses as $cusip => $securityResponse):
    $midPrice = $securityResponse->getItem('IEMID');
    echo $midPrice; // 90.413
endforeach;
```

### Get an array of MID prices.
```php
$midPrices = $remotePlusResponse->getAllValuesForItem('IEMID');
print_r($midPrices);
/*
Array
(
    [17307GNX2] => 91.31362
    [07325KAG3] => 90.33492
    [22541QFF4] => !NA
    [933095AF8] => 29.60287
    [86358EUD6] => 66.0742
    [07384YTS5] => 71.06705
)
*/
```

### Get the SecurityResponse object for a security
```php
$securityResponse = $remotePlusResponse->getByIdentifier('17307GNX2');
$midPrice = $securityResponse->getItem('IEMID'); 
echo $midPrice; // 90.413
```

