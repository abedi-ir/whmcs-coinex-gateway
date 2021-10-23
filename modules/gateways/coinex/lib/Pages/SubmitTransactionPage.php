<?php
namespace WHMCS\Module\Gateway\Coinex\Pages;

use Carbon\Carbon;
use WHMCS\Module\Gateway\Coinex\Models\Transaction;
use WHMCS\Module\Gateway\Coinex\Exceptions\DepositNotFoundException;


class SubmitTransactionPage extends GetTransactionPage {

	public function __construct(int $invoiceID, string $txID) {
		parent::__construct($invoiceID, $txID);
	}

	public function getOutputData() {
		if (Transaction::query()->where("tx_id", $this->txID)->first()) {
			return array(
				'status' => false,
				'error' => 'transaction-duplicate'
			);
		}
		$api = $this->getAPI();

		$this->transaction = new Transaction();
		$this->transaction->invoice_id = $this->getInvoice()->id;
		$this->transaction->tx_id = $this->txID;
		$this->transaction->submit_at = Carbon::now();
		$this->transaction->coin = $this->getGatewayCurrency()->code;
		$this->transaction->status = Transaction::STATUS_PROCESSING;
		try {
			$deposit = $this->transaction->getDeposit($api);
		} catch(DepositNotFoundException $e) {
			return array(
				'status' => false,
				'error' => 'transaction-notfound'
			);
		}
		if ($deposit['create_time'] < time() - 86400 * 2) {
			return array(
				'status' => false,
				'error' => 'transaction-too-old'
			);
		}

		$this->transaction->refreshFromAPI($api, false);
		return parent::getOutputData();
	}
}