<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function coinex_MetaData()
{
    return array(
        'DisplayName' => 'Coinex Payment',
        'APIVersion' => '1.1',
    );
}


function coinex_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Coinex Payment',
        ),
        'accessID' => array(
            'FriendlyName' => 'Access ID',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your access ID here',
        ),
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter secret key here',
        ),
        'network' => array(
            'FriendlyName' => 'Network',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'trc20',
            'Description' => 'Enter a network here. Like trc20 or btc or eth',
        ),
        'slippageTolerance' => array(
            'FriendlyName' => 'Slippage Tolerance',
            'Type' => 'text',
            'Size' => 25,
            'Default' => 1,
            'Description' => 'Enter tolerance in percent. Default is 1%'
        ),
        'discount' => array(
            'FriendlyName' => 'Discount',
            'Type' => 'text',
            'Size' => 25,
            'Default' => 0,
            'Description' => 'Enter discount in percent. Default is 0%'
        )
    );
}


function coinex_link($params) {
    return '<form method="get" action="' . $params['systemurl'] . '/crypto-payment.php">
        <input type="hidden" name="invoice" value="' . $params['invoiceid'] . '" />
        <input type="submit" value="' . $params['langpaynow'] . '" />
        </form>';
}
