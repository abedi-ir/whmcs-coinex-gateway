<?php
namespace WHMCS\Module\Gateway\Coinex\Pages;

use WHMCS\ClientArea;
use WHMCS\Billing\Currency;
use WHMCS\Billing\Invoice;
use WHMCS\Module\Gateway;
use WHMCS\Module\Gateway\Coinex\API;

class InvoicePage extends ClientArea {
	
	protected $invoiceID;

	/**
	 * @var Gateway|null
	 */
	protected $gateway;

	/**
	 * @var Coinex|null
	 */
	protected $api;

	/**
	 * @var Currency|null
	 */
	protected $gatewayCurrency;

	/**
	 * @var Invoice|null
	 */
	protected $invoice;


	/**
	 * @var Currency|null
	 */
	protected $invoiceCurrency;

	public function __construct(int $invoiceID) {
		$this->invoiceID = $invoiceID;
		parent::__construct();
	}

	public function init() {
		$this->initPage();
		$this->requireLogin();
	}

	public function getGateway(): Gateway {
		if (!$this->gateway) {
			$this->gateway = Gateway::factory("coinex");
			$gatewayCurrencyID = $this->gateway->getParam("convertto");
			if (!$gatewayCurrencyID) {
				throw new Exception("You must set a currency for this gateway");
			}
			$gatewayNetwork = $this->gateway->getParam("network");
			if (!$gatewayNetwork) {
				throw new Exception("You must set a blockchain network for this gateway");
			}
			$gatewayAccessID = $this->gateway->getParam("accessID");
			if (!$gatewayAccessID) {
				throw new Exception("You must set a blockchain access ID for this gateway");
			}
			$gatewaySecretKey = $this->gateway->getParam("secretKey");
			if (!$gatewaySecretKey) {
				throw new Exception("You must set a blockchain secret key for this gateway");
			}
		}
		return $this->gateway;
	}

	public function getGatewayCurrency(): Currency {
		if (!$this->gatewayCurrency) {
			$this->gatewayCurrency = Currency::query()->findOrFail($this->getGateway()->getParam("convertto"));
		}
		return $this->gatewayCurrency;
	}

	public function getAPI(): API {
		if (!$this->api) {
			$gateway = $this->getGateway();
			$this->api = new API($gateway->getParam("accessID"), $gateway->getParam("secretKey"));
		}
		return $this->api;
	}

	public function getWalletAddress(): string {
		$currency = $this->getGatewayCurrency()->code;
		$network = $this->getGateway()->getParam("network");
		return $this->getAPI()->getDepositAddress($currency, $network)['coin_address'];
	}

	public function getInvoice(): Invoice {
		if (!$this->invoice) {
			$this->invoice = Invoice::query()
				->where("userid", $_SESSION['uid'])
				->findOrFail($this->invoiceID);
		}
		return $this->invoice;
	}

	public function getInvoiceCurrency(): Currency {
		if (!$this->invoiceCurrency) {
			$this->invoiceCurrency = Currency::query()->findOrFail($this->getInvoice()->getCurrency()['id']);
		}
		return $this->invoiceCurrency;
	}

	public function getPayableAmount(Currency $currency): float {
		$invoice = $this->getInvoice();
		return (new Invoice\Helper())
			->convertCurrency($invoice->getBalanceAttribute(), $currency, $invoice);
	}
}