<?php

return array(

    'Rider'     => array(
        'environment' =>'development',
        'certificate' =>app_path().'/GoferRiderDev.pem',
        'passPhrase'  =>'password',
        'service'     =>'apns'
    ),
    'Driver'     => array(
        'environment' =>'development',
        'certificate' => app_path().'/GoferDriverDev.pem',
        'passPhrase'  =>'password',
        'service'     =>'apns'
    )

);



?>