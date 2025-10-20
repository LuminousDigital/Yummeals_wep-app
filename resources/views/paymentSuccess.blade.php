<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company['company_name']  }}</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
</head>
<body style="height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center;">

<div class="success-page-container max-w-[600px] py-6 p-4 mb-6 sm:mt-12 sm:pb-12  sm:pt-8 sm:px-12 md:shadow-md rounded-2xl bg-white md:border flex flex-col items-center justify-center border">
    <a href="{{ route('home') }}" class="w-36 mb-8">
        <img class="w-full" src="{{ $logo->logo }}" alt="logo">
    </a>

    <img class="w-full max-w-[120px] mb-3" src="{{ asset('images/default/payment-success.gif') }}" alt="success">

    <h3 class="text-sm font-normal leading-[34px] text-center text-heading-light mb-12">
        <span class="text-lg block">{{ __('all.label.congratulations') }}</span>
        {{ __('all.message.payment_successful') }}
    </h3>
    <div class="w-full max-w-[360px]">
        <dl class="text-center shadow-xs w-full mb-8">
            <dt class="uppercase py-2.5 rounded-tl-lg rounded-tr-lg  bg-[#F7F7FC]">{{ __('all.label.transaction_id')  }}</dt>
            <dd class="uppercase py-3 rounded-bl-lg rounded-br-lg payment-font-size font-normal leading-10 bg-white">{{ $order?->transaction?->transaction_no }}</dd>
        </dl>
        <a id="home-route" href="{{ route('home') }}" class="py-3 w-full rounded-lg text-center text-sm font-ormal bg-primary text-white">{{ __('all.label.back_to_home') }}</a>
    </div>
</div>

<script type="application/javascript">
    let data       = JSON.parse(localStorage.getItem('vuex'));
    const url      = '<?=URL::to('/') . "/table-order/"?>';
    const order_id = '<?=$order?->uuid?>';
    if (data.tableCart.paymentMethod) {
        document.getElementById('home-route').setAttribute('href', url + data.tableCart.table.slug + '/' + order_id);
    }
</script>

</body>
</html>
