var TRANSACTION_STATUS = {
	PROCESSING: 1,
	CONFRIMING: 2,
	CANCEL: 3,
	FINISH: 4,
};
var urlParams = new URLSearchParams(window.location.search);
var invoice_id = urlParams.get("invoice");

var transactionUpdaterInterval;

function growlError(title, text) {
	var $modal = $("#modalGrowl");
	$modal.modal("show");
	$(".panel", $modal).addClass("panel-danger");
	$(".modal-title", $modal).html(title);
	$(".modal-title", $modal).html(title);
	$(".panel-body", $modal).html('<p class="text-center text-danger fa-5x"><i class="fas fa-exclamation-triangle"></i></p><p class="text-center">' + text + "</p>");
}

$(function () {
	$(".btn-copy").on("click", function (e) {
		e.preventDefault();
		var $btn = $(this);
		var $target = $($btn.data("target"));
		if ($target.length) {
			copyToClipboard($target.val());
		}
		$btn.tooltip("show");
		setTimeout(function () {
			$btn.tooltip("hide");
		}, 5000);
	}).tooltip({
		title: "کپی شد",
		trigger: "manual"
	});
	$("#crypto-payment-step1").on("submit", function (e) {
		e.preventDefault();
		gotoStep($("#crypto-payment-step2"));
	});
	$("#crypto-payment-step2").on("submit", function (e) {
		e.preventDefault();
		var $btn = $("button[type=submit]", this);
		if ($btn.prop("disabled")) {
			return;
		}
		$btn.parent().find("button").prop("disabled", true);
		$btn.data("orgHTML", $btn.html());
		$btn.html('<i class="fas fa-spinner fa-spin"></i> صبر کنید');
		var txID = $("input[name=tx_id]", this).val();
		var urlParameters = (new URLSearchParams({
			action: "submit-transaction",
			invoice: invoice_id
		})).toString();
		$.ajax({
			type: "POST",
			url: "?" + urlParameters,
			dataType: "json",
			data: {
				tx_id: txID
			},
			success: function (data) {
				$btn.parent().find("button").prop("disabled", false);
				$btn.html($btn.data("orgHTML"));
				if (!data.hasOwnProperty("status")) {
					return;
				}
				if (!data.status) {
					switch (data.error) {
						case "transaction-duplicate":
							growlError("خطا", "این تراکنش تکراری است");
							break;
						case "transaction-too-old":
							growlError("خطا", "این تراکنش خیلی قدیمی است");
							break;
						case "transaction-notfound":
							growlError("خطا", "ما هنوز این تراکنش را دریافت نکردیم!<br> لطفا ابتدا اطمینان حاصل کنید که شناسه تراکنش را به دسترسی وارد کردید.<br>اگر فکر میکنید که اشتباهی در روند ثبت تراکنش شما وجود دارد لطفا از طریق پشتیبانی آن را به ما گزارش دهید.");
							break;
						default:
							growlError("خطا", "کد خطا: " + data.error);
							break;
					}
					return;
				}
				handleTransactionUpdate(data.transaction);
			},
			error: function () {
				$btn.prop("disabled", false);
				$btn.html($btn.data("orgHTML"));
			}
		});
	});
	$("#crypto-payment-step2 .btn-back").on("click", function (e) {
		e.preventDefault();
		gotoStep($("#crypto-payment-step1"));
	});
	handleTransactionUpdate(initTransaction);
});
function copyToClipboard(txt) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val(txt).select();
	document.execCommand("copy");
	$temp.remove();
}
function handleTransactionUpdate(transaction) {
	initTransaction = transaction;
	if (transaction === null) {
		var $step1 = $("#crypto-payment-step1");
		gotoStep($step1);
		return;
	}
	var $step3 = $("#crypto-payment-step3");
	var $step4 = $("#crypto-payment-step4");
	if (transaction.status === TRANSACTION_STATUS.PROCESSING || transaction.status === TRANSACTION_STATUS.CONFRIMING) {
		setupTransactionUpdater();
		gotoStep($step3);
		updateTransactionTable($(".table-transaction", $step3), transaction);
	} else if (transaction.status === TRANSACTION_STATUS.CANCEL || transaction.status === TRANSACTION_STATUS.FINISH) {
		stopTransactionUpdater();
		gotoStep($step4);
		updateTransactionTable($(".table-transaction", $step4), transaction);
	}
}
function gotoStep($step) {
	$step.show();
	$(".crypto-payment-step").not($step).hide();
}
function updateTransactionTable($table, transaction) {
	$(".transaction-tx", $table).html(transaction.tx_id.substr(0, 20) + "...");
	$(".transaction-amount-currency", $table.parents(".crypto-payment-step")).html(transaction.amount + " " + transaction.coin);
	$(".transaction-submit-at", $table).html(transaction.submit_at);
	$(".transaction-confrimations", $table).html(transaction.confirmations);
	$(".transaction-status", $table).html(transactionStatusLabel(transaction.status));
}
function transactionStatusLabel(status) {
	switch (status) {
		case TRANSACTION_STATUS.PROCESSING:
			return '<span class="label label-default">در حال پردازش</span>';
		case TRANSACTION_STATUS.CONFRIMING:
			return '<span class="label label-info">منتظر تائید شبکه</span>';
		case TRANSACTION_STATUS.CANCEL:
			return '<span class="label label-danger">کنسل شده</span>';
		case TRANSACTION_STATUS.FINISH:
			return '<span class="label label-success">تائید شده</span>';
	}
	return "";
}
function transactionUpdater() {
	if (initTransaction === null) {
		return;
	}
	$.ajax({
		type: "GET",
		dataType: "json",
		data: {
			action: "get-transaction",
			invoice: invoice_id,
			tx_id: initTransaction.tx_id,
		},
		success: function (data) {
			if (!data.hasOwnProperty("status")) {
				return;
			}
			if (!data.status) {
				return;
			}
			handleTransactionUpdate(data.transaction);
		},
		error: function () {

		}
	});
}
function setupTransactionUpdater() {
	if (transactionUpdaterInterval) {
		return;
	}
	transactionUpdaterInterval = setInterval(transactionUpdater, 15 * 1000);
}
function stopTransactionUpdater() {
	if (transactionUpdaterInterval) {
		clearInterval(transactionUpdaterInterval);
		transactionUpdaterInterval = undefined;
	}
}
