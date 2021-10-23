<?php
namespace WHMCS\Module\Gateway\Coinex;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;
use GuzzleHttp\Middleware;
use GuzzleHttp\Message\RequestInterface;

class API {
	protected $guzzle;
	public function __construct(string $accessID, string $secretKey) {
		$signer = new API\RequestSigner($accessID, $secretKey);

		$options = array(
			'base_url' => 'https://api.coinex.com/v1/', // For guzzle 5.3
			'base_uri' => 'https://api.coinex.com/v1/', // For guzzle 7.0
			'headers' => array(
				'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36',
				'Content-Type' => 'application/json',
			),
			'defaults' => array(
				'headers' => array(
					'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36',
					'Content-Type' => 'application/json'
				)
			),
		);

		if (class_exists(HandlerStack::class)) {
			$stack = new HandlerStack();
			$stack->setHandler(Utils::chooseHandler());
			$stack->push(Middleware::mapRequest($signer));
			$options['handler'] = $stack;
		} else {
			$options['message_factory'] = new API\MessageFactory($signer);
		}

		$this->guzzle = new Client($options);
	}

	public function getDepositAddress(string $coin_type, ?string $smart_contract_name = null) {
		$query = $this->filterNulls(array(
			'smart_contract_name' => $smart_contract_name,
		));
		return $this->handleResponse($this->guzzle->get(
			"balance/deposit/address/" . $coin_type,
			['query' => $query]
		));
	}

	public function getDepositList(?string $coin_type = null, ?string $status = null, ?int $page = null, ?int $limit = null) {
		$query = $this->filterNulls(array(
			'coin_type' => $coin_type,
			'status' => $status,
			'page' => $page,
			'limit' => $limit,
		));
		return $this->handleResponse($this->guzzle->get(
			"balance/coin/deposit",
			['query' => $query]
		));
	}

	public function handleResponse($response) {
		$json = Utils::jsonDecode((string) $response->getBody(), true);
		if (isset($json['code']) and $json['code'] === 0) {
			return $json['data'];
		}
		return $json;
	}

	protected function filterNulls(array $data): array {
		return array_filter($data, function($value) {
			return $value !== null;
		});
	}

}