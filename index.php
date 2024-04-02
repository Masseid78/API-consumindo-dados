<?php

function getBTCPrice()
{
    $api_key = 'your_api_key_here';
    $url = 'https://api.coindesk.com/v1/bpi/currentprice/BTC.json';

    $options = [
        'http' => [
            'method'  => 'GET',
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return ['error' => 'Error fetching data from Coindesk API.'];
    }

    $response = json_decode($result, true);
    if (empty($response['bpi']['USD']['rate_float'])) {
        return ['error' => 'No data found for Bitcoin price.'];
    }

    return ['BTC' => $response['bpi']['USD']['rate_float']];
}

function getUSDCurrencyRate()
{
    $url = 'https://api.exchangerate-api.com/v4/latest/USD';

    $options = [
        'http' => [
            'method'  => 'GET',
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return ['error' => 'Error fetching data from ExchangeRate-API.'];
    }

    $response = json_decode($result, true);
    if (empty($response['rates'])) {
        return ['error' => 'No data found for US dollar currency rate.'];
    }

    return ['USD' => $response['rates']['USD']];
}

function getCurrentPrices()
{
    $btcPrice = getBTCPrice();
    $usdRate = getUSDCurrencyRate();

    if (isset($btcPrice['error'])) {
        return $btcPrice;
    }

    if (isset($usdRate['error'])) {
        return $usdRate;
    }

    return array_merge($btcPrice, $usdRate);
}

$prices = getCurrentPrices();

echo "Bitcoin price: " . $prices['BTC'] . " USD" . PHP_EOL;
echo "US dollar rate: " . $prices['USD'] . PHP_EOL;

?>