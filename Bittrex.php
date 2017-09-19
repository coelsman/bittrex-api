<?php
require_once "BittrexPublic.php";

class Bittrex {
	public function __construct ($apikey, $apiSecret) {
		$this->Public = new BittrexPublic($apikey, $apiSecret);
	}
}