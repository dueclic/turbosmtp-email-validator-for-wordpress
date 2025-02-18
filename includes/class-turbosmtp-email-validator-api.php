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
	): bool {
		return !is_null($this->getUserInfo($consumer_key, $consumer_secret));
	}

	public function validateEmail(
		$email
	){

		if (!$this->hasApiKeys()) {
			return null;
		}

		$api_url = $this->getApiUrl();

		$response = wp_remote_post( $api_url.'/emailvalidation/validateEmail', array(
			'timeout' => $this->api_timeout,
			'user-agent' => 'turboSMTP Email Validator (WordPress Plugin)',
			'headers' => array(
				'accept'         => 'application/json',
				'Content-Type'   => 'application/json',
				'consumerKey'    => $this->consumer_key,
				'consumerSecret' => $this->consumer_secret,
			),
			'body'    => json_encode( array( 'email' => $email ) ),
		) );

		if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
			$body = wp_remote_retrieve_body($response);

			$validationResult = json_decode($body, true);

			if (json_last_error() === JSON_ERROR_NONE) {
				return $validationResult;
			}
		}
		return null;
	}

	public function getUserInfo(
		$consumer_key = null,
		$consumer_secret = null
	){
		try {
			if ( ! $this->hasApiKeys() ) {
				return null;
			}
			$api_url = $this->getApiUrl();

			$response = wp_remote_get( $api_url . '/user/config', array(
				'timeout'    => $this->api_timeout,
				'user-agent' => 'turboSMTP Email Validator (WordPress Plugin)',
				'headers'    => array(
					'accept'         => 'application/json',
					'Content-Type'   => 'application/json',
					'consumerKey'    => ! is_null( $consumer_key ) ? $consumer_key : $this->consumer_key,
					'consumerSecret' => ! is_null( $consumer_secret ) ? $consumer_secret : $this->consumer_secret,
				),
			) );
			if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
				$body = wp_remote_retrieve_body($response);

				$user_info = json_decode($body, true);

				if (json_last_error() === JSON_ERROR_NONE) {
					return $user_info;
				}
			}
		} catch (\Exception $ex) {
			error_log($ex->getMessage());
		}

		return null;
	}

    public function getSubscription()
    {
        try {
            if (!$this->hasApiKeys()) {
                return null;
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
	                return $subscription;
                }
            }

        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return null;
    }



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
