<?php
namespace WHMCS\Module\Gateway\Coinex\API;


use GuzzleHttp\Message\RequestInterface as GuzzleRequestInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use GuzzleHttp\Psr7\Uri;

class RequestSigner {

	/**
	 * @var string
	 */
	protected $accessID;

	/**
	 * @var string
	 */
	protected $secretKey;

	public function __construct(string $accessID, string $secretKey) {
		$this->accessID = $accessID;
		$this->secretKey = $secretKey;
	}

	/**
	 * @var GuzzleRequestInterface|PsrRequestInterface
	 */
	public function __invoke($request) {
		if ($request instanceof PsrRequestInterface) {
			$uri = $request->getUri();
			$uri = Uri::withQueryValue($uri, "tonce", time() * 1000);
			$uri = Uri::withQueryValue($uri, "access_id", $this->accessID);
			$request = $request->withUri($uri);
			parse_str($request->getUri()->getQuery(), $params);
		} else {
			$query = $request->getQuery();
			$query->set("tonce", time() * 1000);
			$query->set("access_id", $this->accessID);
			$params = $query->toArray();
		}
		ksort($params);
		$params['secret_key'] = $this->secretKey;
		$signature = strtoupper(md5(http_build_query($params)));
		if ($request instanceof PsrRequestInterface) {
			$request = $request->withHeader("Authorization", $signature);
		} else {
			$request->addHeader("Authorization", $signature);
		}
		return $request;
	}
}