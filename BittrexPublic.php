<?php
require_once "BittrexResponse.php";

class BittrexPublic {
	protected $_version = '1.1';
	protected $_url = 'https://bittrex.com/api/v1.1/';

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

	public function getMarkets () {
		return $this->request('public/getmarkets');
	}

	public function getMarketSummaries () {
		return $this->request('public/getmarketsummaries');
	}

	public function getOpenOrders () {
		return $this->request('market/getopenorders', array(
			'apikey' => $this->getApikey()
		));
	}

	public function cancelOrder ($uuid) {
		return $this->request('market/cancel', array(
			'apikey' => $this->getApikey(),
			'uuid' => $uuid
		));
	}

	public function getBalance ($currency) {
		$data = $this->request('account/getbalance', array(
			'apikey' => $this->getApikey(),
			'currency' => $currency
		));

		return @$data->result->Available;
	}

	public function getBalances () {
		return $this->request('account/getbalances', array(
			'apikey' => $this->getApikey()
		));
	}

	public function tradeSell ($pair, $quantity, $rate) {
		return $this->request('market/selllimit', array(
			'market' => $pair,
			'quantity' => $quantity,
			'rate' => $rate,
			'apikey' => $this->getApikey()
		));
	}

	public function tradeBuy ($pair, $quantity, $rate) {
		return $this->request('market/buylimit', array(
			'market' => $pair,
			'quantity' => $quantity,
			'rate' => $rate,
			'apikey' => $this->getApikey()
		));
	}

	public function request ($endpoint, $params = array()) {
		$url = $this->_url.$endpoint;

		$params['nonce'] = time();

		if (!empty($params)) {
			$url = $url.'?'.http_build_query($params);
		}

		$sign = hash_hmac('sha512', $url, $this->getApiSecret());
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
		curl_setopt($ch, CURLOPT_URL, $url);

		$output = curl_exec($ch);
		$httpResponse = curl_getinfo($ch);
		curl_close($ch);

		if ($httpResponse['http_code'] == 200) {
			return $this->Response->response(true, $httpResponse['http_code'], json_decode($output));
		} else {
			return $this->Response->response(false, $httpResponse['http_code'], json_decode($output));
		}
		// $sign = hash_hmac('sha512', data, $this->getApiSecret());
	}
}