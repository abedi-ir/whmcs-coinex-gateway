<script>
var initTransaction = {$jsonTransaction};
</script>
<link href="{$WEB_ROOT}/modules/gateways/coinex/assets/css/payment-page.css" rel="stylesheet">
<script src="{$WEB_ROOT}/modules/gateways/coinex/assets/js/payment-page.js"></script>
<div class="crypto-payment-container">
	<h2>پرداخت با {$payableCurrency}</h2>
	<form class="crypto-payment-step" id="crypto-payment-step1">
		<div class="qr-code">
			<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$walletAddress}">
		</div>
		<div class="instructions">
			<p>لطفا مبلغ <span class="ltr">{$payableAmount} {$payableCurrency}</span> را به آدرس کیف پول زیر در شبکه ی <span class="label label-success ltr">{$walletNetwork}</span> ارسال بفرمایید.قیمت فوق بدون احتساب کارمزد شبکه است.</p>
			<p>مبلغ نهایی با احتساب کارمزد شبکه : <span class="ltr">{$payableAmount+1} {$payableCurrency}</span></p>
			{if $gatewayDiscount}
			<p><span class="ltr label label-info">{$gatewayDiscount}%</span> تخفیف برای شما محاسبه شده است.</p>
			{/if}
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-default btn-copy" data-target="#wallet-address" type="button"><i class="far fa-copy"></i></button>
				</span>
				<input type="text" class="form-control ltr" id="wallet-address" readonly value="{$walletAddress}">
			</div>
			
		
			<p class="alert alert-danger">لطفا توجه داشته باشید که از ولت فوق برای فقط و فقط ارز {$payableCurrency}  در شبکه ی <span class="ltr">{$walletNetwork}</span> استفاده بفرمایید، در غیراینصورت امکان
				گم شدن توکن های ارسالی شما وجود دارد.</p>
		
			<p class="text-center">
				<button type="submit" class="btn btn-default"> <i class="fas fa-arrow-circle-left"></i> مرحله ی بعد</button>
			</p>
		
		</div>
	</form>
	<form class="crypto-payment-step" id="crypto-payment-step2">
		<div class="qr-code">
			<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$walletAddress}">
		</div>
		<div class="instructions">
			<p>پس از انجام تراکنش لطفا شماره Transaction ID را وارد نمایید.</p>
			<div class="form-group">
				<input type="text" class="form-control ltr" name="tx_id" value="">
			</div>
			<p class="text-center">
				<button type="button" class="btn btn-default btn-back"> <i class="fas fa-arrow-circle-right"></i> مرحله ی قبل</button>
				<button type="submit" class="btn btn-success"> <i class="fas fa-arrow-circle-left"></i> مرحله ی بعد</button>
			</p>
		</div>
	</form>
	<form class="crypto-payment-step" id="crypto-payment-step3">
		<div class="icon-spinner text-info">
			<i class="fas fa-spinner fa-spin"></i>
		</div>
		<h3 class="text-center">صبر کنید...</h3>
		<p>تراکنش شما انجام شده اما ما منتظر هستیم تا از شبکه تائید دریافت کنیم. معمولا این مرحله کمتر از چند دقیقه زمان خواهد
			برد و از طرف شما اقدامی لازم نیست انجام شود.</p>

		<p class="text-info"><i class="fas fa-exclamation-circle"></i> شما می‌توانید با خیال آسوده این صفحه را ترک کنید، تراکنش
			با موفقیت در سیستم ثبت شده و هر زمان توسط شبکه تائید شود صورتحسابتان پرداخت خواهد شد.</p>

		<table class="table table-transaction">
			<tr>
				<th>شناسه:</th>
				<td class="ltr transaction-tx"></td>
			</tr>
			<tr>
				<th>مبلغ:</th>
				<td class="ltr transaction-amount-currency"></td>
			</tr>
			{if $gatewayDiscount}
			<tr>
				<th>تخفیف:</th>
				<td class="ltr"><span class="ltr label label-info">{$gatewayDiscount}%</span></td>
			</tr>
			{/if}
			<tr>
				<th>زمان:</th>
				<td class="ltr transaction-submit-at"></td>
			</tr>
			<tr>
				<th>تائیدیه ها:</th>
				<td><span class="transaction-confrimations"></span> عدد</td>
			</tr>
			<tr>
				<th>وضعیت:</th>
				<td class="transaction-status"></td>
			</tr>
		</table>
	</form>
	<form class="crypto-payment-step" id="crypto-payment-step4">
		<div class="icon-success text-success">
			<i class="fas fa-check-square"></i>
		</div>
		<h3 class="text-center">موفقیت آمیز!</h3>
		<p>پرداخت شما به مبلغ <span class="ltr transaction-amount-currency"></span> تائید شد. اکنون می‌توانید به صورتحسابتان بازگردید.</p>
		<table class="table table-transaction">
			<tr>
				<th>شناسه:</th>
				<td class="ltr transaction-tx"></td>
			</tr>
			<tr>
				<th>مبلغ:</th>
				<td class="ltr transaction-amount-currency"></td>
			</tr>
			{if $gatewayDiscount}
			<tr>
				<th>تخفیف:</th>
				<td class="ltr"><span class="ltr label label-info">{$gatewayDiscount}%</span></td>
			</tr>
			{/if}
			<tr>
				<th>زمان:</th>
				<td class="ltr transaction-submit-at"></td>
			</tr>
			<tr>
				<th>تائیدیه ها:</th>
				<td><span class="transaction-confrimations"></span> عدد</td>
			</tr>
			<tr>
				<th>وضعیت:</th>
				<td class="transaction-status"></td>
			</tr>
		</table>
		<p class="text-center">
			<a type="submit" class="btn btn-default" href="{$invoice->getViewInvoiceUrl()}"> <i class="fas fa-arrow-circle-left"></i> مشاهده ی صورتحساب</a>
		</p>
	</form>
</div>

<div class="modal system-modal fade" id="modalGrowl" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content panel ">
			<div class="modal-header panel-heading">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title">Title</h4>
			</div>
			<div class="modal-body panel-body"></div>
			<div class="modal-footer panel-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">متوجه شدم</button>
			</div>
		</div>
	</div>
</div>