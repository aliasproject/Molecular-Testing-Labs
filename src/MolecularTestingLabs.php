<?php namespace AliasProject\MolecularTestingLabs;

use Log;
class MolecularTestingLabs
{
    const ENDPOINT = 'https://lisapi.moleculartestinglabs.com';
    const ENDPOINT_STAGING = 'https://lisbeta4.moleculartestinglabs.com';

    private $endpoint;
    private $headers;

    /**
     * Create new instance
     *
     * @param string $user - API username
     * @param string $token - API token
     * @param string $env - production / staging
     */
    public function __construct(string $user, string $token, string $env = 'staging')
    {
        $this->endpoint = ($env === 'production') ? self::ENDPOINT : self::ENDPOINT_STAGING;

        // Set Request Headers
        $this->headers = [
            'Content-Type: application/json',
            'user: ' . $user,
            'token: ' . $token
        ];
    }

    /**
     * Create new order
     *
     * @param string $first_name
     * @param string $last_name
     * @param string $dob
     * @param string $gender
     * @param string $email
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param int $zip
     * @param string $home_phone
     * @param array $test_types
     * @param bool $take_tests_same_day
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(string $order_number, string $phone, string $email, string $gender, string $date_of_birth, array $panel_ids, string $physician_id, string $lob, bool $fulfillment = true, string $first_name, string $last_name, string $address1, string $address2, string $city, string $state, string $postcode, string $country)
    {
        // Generate Request Data
        $request_data = [[
            'shipping_info' => [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'address_1' => $address1,
                'address_2' => $address2,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode,
                'country' => $country
            ],
            'order_number' => $order_number,
            'ordered_date' => date('c', strtotime('now')), //'2018-07-09T19:53:08.885569Z',
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'email' => $email,
            'phone' => $phone,
            'panel_id' => $panel_ids, //[26, 27],
            'physician_id' => $physician_id,
			'lob' => $lob, //'SC',
			'fulfillment' => $fulfillment,
        ]];

        // Make request
        $response = $this->makeRequest($this->endpoint . '/PlaceOrder', $request_data, true);

        // Return Results
        return json_decode($response);
    }

    /**
     * Register Kit
     *
     * @param string $first_name
     * @param string $last_name
     * @param string $dob
     * @param string $gender
     * @param string $email
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param int $zip
     * @param string $home_phone
     * @param array $test_types
     * @param bool $take_tests_same_day
     * @return \Illuminate\Http\Response
     */
    public function registerKit(string $kit_id, string $pwn_req_number, string $lob, string $first_name, string $last_name, string $address1, string $address2, string $city, string $state, string $postcode, string $gender, string $date_of_birth, string $email, string $phone)
    {
        $registerKit = [
            [
         	    'lob' => $lob,
         		'kit_id' => $kit_id,
                'pwn_req_number' => $pwn_req_number,
         		'patient_info' => [
                    'first_name' => $first_name,
                 	'last_name' => $last_name,
                    'address_1' => $address1,
                    'address_2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'postcode' => $postcode,
                    'gender' => $gender,
                 	'date_of_birth' => $date_of_birth,
                 	'email' => $email,
                    'phone' => $phone
                ]
            ]
        ];

        // Make request
        $request = $this->makeRequest($this->endpoint . '/RegisterKit', $registerKit, true);

        // Return Results
        return json_decode($request, TRUE);
     }

    /**
     * Notifications
     *
     * @param  string  $lob - SC, DNA, FIT & CGX
     * @param  string  $type - kit_shipped, kit_received, kit_rejected
     * @param  string  $order_number
     * @return string
     */
    public function notification(string $lob = 'SC', string $type = 'kit_shipped', string $order_number = NULL)
    {
        $notification = [
            'lob' => $lob,
            'type' => $type,
        ];

        // If order number exists, add to array
        if ($order_number) {
            $notification_arr['order_number'] = $order_number;
        }

        // Make request
        $request = $this->makeRequest($this->endpoint . '/Notification', $notification);

        // Return Results
        return json_decode($request, TRUE);
    }

    /**
     * Cancel Order
     *
     * @param  string  $pdf
     * @return string
     */
    public function cancelOrder()
    {
        // /CancelOrder
    }

    /**
     * For retrieving a list of test result ready orders
     *
     * @param  string  $pdf
     * @return string
     */
    public function testResult()
    {
        // /TestResult
    }

    /**
     * Add new additional test to existing order
     *
     * @param  string  $pdf
     * @return string
     */
    public function addNewTest()
    {
        // /AddNewTest
    }

    /**
     * For a client to update / retrieve patient info
     *
     * @param  string  $pdf
     * @return string
     */
    public function patient()
    {
        //
    }

    /**
     * Decode PDF
     *
     * @param  string  $pdf
     * @return string
     */
    public function decodePDF(string $pdf)
    {
        return base64_decode($pdf);
    }

    /**
     * Make HTTP Request
     *
     * @param  string  $url
     * @return string
     */
    private function makeRequest(string $url, array $data = [], bool $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
