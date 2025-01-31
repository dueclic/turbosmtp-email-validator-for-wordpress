<?php

/**
 * Define the API functionality.
 *
 * @since      1.0.0
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 * @author     dueclic (https://dueclic.com/)
 */
class Turbosmtp_Email_Validator_API
{
    /**
     * The turboSMTP Consumer Key
     *
     * @since    1.0.0
     * @access   private
     * @var      string $consumer_key The turboSMTP Consumer Key
     */
    private $consumer_key;

	/**
	 * The turboSMTP Consumer Key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $consumer_secret The turboSMTP Consumer Secret
	 */
	private $consumer_secret;

    /**
     * The turboSMTP API Timeout
     *
     * @since    1.0.0
     * @access   private
     * @var      int $api_timeout The turboSMTP API Timeout in seconds
     */
    private $api_timeout;

    /**
     * Save the API key for future usage.
     *
     * @since    1.0.0
     */
    public function __construct($consumer_key = "", $consumer_secret = "",  $api_timeout = 5)
    {
        $this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
        $this->api_timeout  = $api_timeout;
    }

    private function getApiUrl(): string
    {
        return apply_filters("turbosmtp_api_url", "https://pro.api.serversmtp.com/api/v2");
    }

	public function isValid(
		$consumer_key = null,
		$consumer_secret = null
	){
		try {
			$api_url = $this->getApiUrl();

			$response = wp_remote_get( $api_url.'/user/config', array(
				'timeout' => $this->api_timeout,
				'user-agent' => 'turboSMTP Email Validator (WordPress Plugin)',
				'headers' => array(
					'accept'         => 'application/json',
					'Content-Type'   => 'application/json',
					'consumerKey'    => !is_null($consumer_key) ? $consumer_key : $this->consumer_key,
					'consumerSecret' => !is_null($consumer_secret) ? $consumer_secret : $this->consumer_secret,
				),
			) );

			if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
				return true;
			}
		} catch (\Exception $ex) {
			error_log($ex->getMessage());
		}

		return false;
	}

	public function validateEmail(){

	}

    public function getSubscription($refresh = false)
    {
        try {
            if (!$this->hasApiKeys()) {
                return null;
            }

	        $transient_name = 'turbosmtp_email_validator_subscription';

	        if ( ! $refresh && false !== ( $subscription = get_transient( $transient_name ) ) ) {
		        return $subscription;
	        }

            $api_url = $this->getApiUrl();

	        $response = wp_remote_get( $api_url.'/emailvalidation/subscription', array(
		        'timeout' => $this->api_timeout,
		        'user-agent' => 'turboSMTP Email Validator (WordPress Plugin)',
		        'headers' => array(
			        'accept'         => 'application/json',
			        'Content-Type'   => 'application/json',
			        'consumerKey'    => $this->consumer_key,
			        'consumerSecret' => $this->consumer_secret,
		        ),
	        ) );

            if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
                $body = wp_remote_retrieve_body($response);

	            $subscription = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {
	                set_transient( $transient_name, $subscription, 12 * HOUR_IN_SECONDS );
	                return $subscription;
                }
            }

        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return null;
    }

	/*

    public function validate_email($email)
    {
        try {
            if (!$this->has_api_keys()) {
                return null;
            }
            $api = $this->getApiUrl();
            $response = wp_remote_get($api . '/validate?api_key=' . $this->consumer_key . '&email=' . urlencode($email), [
                'method' => 'GET',
                'data_format' => 'json',
                'timeout' => $this->api_timeout,
                'user-agent' => 'ZeroBounce Email Validator (WordPress Plugin)',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
                $body = wp_remote_retrieve_body($response);

                $body_json = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {

                    if (array_key_exists("error", $body_json)) {
                        return null;
                    }

                    return $body_json;
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return null;
    }

	*/

    public function hasApiKeys(): bool {
        if ( !strlen($this->consumer_key) || empty($this->consumer_key) ) {
            return false;
        }

	    if ( !strlen($this->consumer_secret) || empty($this->consumer_secret) ) {
		    return false;
	    }

        return true;
    }
}
