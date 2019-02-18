<?php namespace AliasProject\MolecularTestingLabs;

class MolecularTestingLabs
{
    const ENDPOINT = 'ENDPOINT_HERE';
    const ENDPOINT_STAGING = 'STAGING_ENDPOINT HERE';

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
     * @param string $address
     * @param string $city
     * @param string $state
     * @param int $zip
     * @param string $home_phone
     * @param array $test_types
     * @param bool $take_tests_same_day
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(string $order_number, int $phone, string $email, string $gender, string $date_of_birth, array $panel_id, string $physician_id, string $practice_id, string $lob, bool $fulfillment = false, string $ordered_date, array $shipping_details)
    {
        // Generate Request Data
        $request_data = $this->buildRequestData($order_number, $phone, $email, $gender, $date_of_birth, $panel_id, $physician_id, $practice_id, $lob, $fulfillment = false, $ordered_date, $shipping_details);

        // Make request
        $request = $this->makeRequest($this->endpoint . '/PlaceOrder', $request_data, $this->headers, true);

        // Return Results
        return json_decode($request, TRUE);
    }

    /**
     * Notifications
     *
     * @param  string  $pdf
     * @return string
     */
    public function notification()
    {
        // /Notification
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
     * Build Request Data
     *
     * @param  string  $url
     * @return string
     */
    private function buildRequestData(string $order_number, int $phone, string $email, string $gender, string $date_of_birth, array $panel_ids, string $physician_id, string $practice_id, string $lob, bool $fulfillment, string $order_date, array $shipping_details, $pwn_req_number)
    {
        $data = [
            'shipping_info' => $shipping_info,
            'order_number' => $order_number,
            'ordered_date' => $order_date, //'2018-07-09T19:53:08.885569Z',
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'email': $email,
            'phone': $phone,
            'shipping_method' => 7,
            'panel_id' => $panel_ids, //[26, 27],
            'physician_id' => $physician_id,
			'lob' => $lob, //'SC',
			'fulfillment' => $fulfullment,
			'kit_id' => null,
			'patient_signature' => null,
            'pwn_req_number' => $pwn_req_number
        ];

        return $data;
    }

    /**
     * Make HTTP Request
     *
     * @param  string  $url
     * @return string
     */
    private function makeRequest(string $url, string $data = '', bool $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
