<?php
require_once "BittrexResponse.php";

class BittrexPrivate {
	protected $_url = 'https://bittrex.com/api/v1.1/market/';

	public function __construct ($apikey, $apiSecret) {
		$this->setApikey($apikey);
		$this->setApiSecret($apiSecret);
		$this->Response = new BittrexResponse();
	}

	public function setApikey ($apikey) {
		$this->_apikey = $apikey;
	}

	public function setApiSecret ($apiSecret) {
		$this->_apiSecret = $apiSecret;
	}

	public function getApikey () {
		return $this->_apikey;
	}

	public function getApiSecret () {
		return $this->_apiSecret;
	}

	public function setVerificationToken ($token) {
		$this->_verificationToken = $token
	}

	public function getVerificationToken () {
		return $this->_verificationToken;
	}

	public function tradeSell ($pair, $quantity, $rate) {
		return $this->request('market/selllimit', array(
			'market' => $pair,
			'quantity' => $quantity,
			'rate' => $rate
		));
	}

	public function request ($endpoint, $params = array()) {
		$url = $this->_url.$endpoint;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);

		$output = curl_exec($ch);
		$httpResponse = curl_getinfo($ch);
		curl_close($ch);

		if ($httpResponse['http_code'] == 200) {
			return $this->Response->response(true, $httpResponse['http_code'], json_decode($output));
		} else {
			return $this->Response->response(false, $httpResponse['http_code'], json_decode($output));
		}
	}
}