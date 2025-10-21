<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company['company_name'] }}</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/lab/lab.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
</head>

<body>
    <div class="py-2 px-4 w-full max-w-3xl mx-auto">
        @if (!$isIframe)
            <a id="home-route1" href="{{ route('home') }}" class="block mx-auto w-36 mb-8 home-routes">
                <img class="w-full" src="{{ $logo->logo }}" alt="logo">
            </a>
        @endif
        <h3 class="text-[22px] text-center font-normal font-poppins leading-[34px] mb-6">
            {{ __('all.message.select_your_payment_method') }}</h3>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ $error }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer close-button">
                        <i class="lab lab-close-circle-line margin-top-5-px"></i>
                    </span>
                </div>
            @endforeach
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer close-button">
                    <i class="lab lab-close-circle-line margin-top-5-px"></i>
                </span>
            </div>
        @endif

        <form id="paymentForm" method="POST" action="{{ route('payment.store', ['order' => $order]) }}">
            @csrf
            <!-- Debug: Show that CSRF token is present -->
            <input type="hidden" id="debugCsrfToken" value="{{ csrf_token() }}">
            <fieldset class="payment-fieldset">
                @if (!blank($paymentGateways))
                    @foreach ($paymentGateways as $paymentGateway)
                        @if (!$credit && $paymentGateway->slug === 'credit')
                            @continue
                        @endif
                        <label class="payment-label" for="{{ $paymentGateway->slug }}">
                            <input class="paymentMethod" id="{{ $paymentGateway->slug }}" type="radio"
                                name="paymentMethod" value="{{ $paymentGateway->slug }}"
                                @if (old('paymentMethod') == $paymentGateway->slug) checked @endif>
                            <img src="{{ $paymentGateway->image }}" alt="payment">
                            <span>{{ $paymentGateway->name }}</span>
                            @if ($paymentGateway->slug === 'credit')
                                <span>
                                    {{ $creditAmount }}
                                </span>
                            @endif
                        </label>
                    @endforeach
                @endif
            </fieldset>

            @if (!blank($paymentGateways))
                @foreach ($paymentGateways as $paymentGateway)
                    @if ($paymentGateway->misc !== null)
                        @if (!blank(json_decode($paymentGateway->misc)))
                            @if (!blank(json_decode($paymentGateway->misc)->input))
                                @foreach (json_decode($paymentGateway->misc)->input as $input)
                                    @include('paymentGateways.' . str_replace('.blade.php', '', $input))
                                @endforeach
                            @endif
                        @endif
                    @endif
                @endforeach
            @endif

            @if (!blank($paymentGateways))
                <button type="submit"
                    class="py-3 w-full rounded-lg text-center text-sm font-normal font-poppins tracking-[1px] bg-primary text-white confirn-btn"
                    id="confirmBtn">
                    {{ __('all.label.confirm') }}
                </button>
            @endif

            @if (!$isIframe)
                <div class="py-5 px-4 w-full max-w-3xl mx-auto flex flex-col items-center justify-center">
                    <a id="home-route" class="text-primary"
                        href="{{ route('home') }}">{{ __('all.label.back_to_home') }}</a>
                </div>
            @endif
        </form>

    </div>

    <script>
        console.log('Iframe: Payment page loaded, isIframe:', {{ $isIframe ? 'true' : 'false' }});

        const appUrl = '{{ config('app.url') }}';
        console.log('Iframe: APP_URL from environment:', appUrl);

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iframe: DOM content loaded');

            const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
            const paymentForm = document.getElementById('paymentForm');
            const confirmBtn = document.getElementById('confirmBtn');
            const isIframe = {{ $isIframe ? 'true' : 'false' }};

            console.log('Iframe: Found', paymentMethods.length, 'payment methods');
            console.log('Iframe: Found form:', !!paymentForm);
            console.log('Iframe: Found confirm button:', !!confirmBtn);

            // Notify parent that iframe is ready
            if (isIframe && window.parent !== window) {
                console.log('Iframe: Sending ready message to parent');
                window.parent.postMessage({
                    type: 'PAYMENT_IFRAME_READY',
                    message: 'Iframe is ready'
                }, appUrl);
            }

            // Notify parent about initial state
            notifyParentAboutSelection();

            // Add change listeners to payment methods
            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    console.log('Iframe: Payment method changed to:', this.value);
                    notifyParentAboutSelection();
                });
            });

            // Handle form submission differently based on iframe mode
            if (paymentForm) {
                console.log('Iframe: Setting up form handling, isIframe:', isIframe);

                if (isIframe) {
                    // In iframe mode: prevent actual submission, only notify parent
                    console.log('Iframe: Setting up iframe-specific form handling');

                    // Replace the submit button click handler
                    if (confirmBtn) {
                        confirmBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log('Iframe: Confirm button clicked in iframe mode');

                            const selectedMethod = document.querySelector(
                                'input[name="paymentMethod"]:checked');
                            if (!selectedMethod) {
                                console.error('Iframe: No payment method selected!');
                                alert('Please select a payment method');
                                return;
                            }

                            // Get all form data
                            const formData = new FormData(paymentForm);
                            const data = {};
                            for (let [key, value] of formData.entries()) {
                                data[key] = value;
                            }

                            console.log('Iframe: Selected payment method:', selectedMethod.value);
                            console.log('Iframe: Form data to send to parent:', data);

                            // Notify parent about payment confirmation
                            if (window.parent !== window) {
                                console.log('Iframe: Sending payment confirmation to parent');
                                window.parent.postMessage({
                                    type: 'PAYMENT_FORM_SUBMIT',
                                    paymentMethod: selectedMethod.value,
                                    formData: data
                                }, appUrl);
                            }
                        });
                    }

                    // Also prevent form submission via other means (like pressing enter)
                    paymentForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Iframe: Form submission prevented in iframe mode');
                    });
                } else {
                    // Not in iframe: normal form submission
                    console.log('Iframe: Setting up normal form submission');
                    paymentForm.addEventListener('submit', function(e) {
                        console.log('Iframe: Normal form submission proceeding');
                        // Allow normal form submission
                    });
                }
            }

            function notifyParentAboutSelection() {
                if (isIframe && window.parent !== window) {
                    const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
                    const formData = new FormData(paymentForm);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        data[key] = value;
                    }

                    console.log('Iframe: Notifying parent about selection:', selectedMethod?.value);
                    window.parent.postMessage({
                        type: 'PAYMENT_METHOD_SELECTED',
                        paymentMethod: selectedMethod?.value,
                        formData: data
                    }, appUrl);
                }
            }

            // Also trigger on any input changes for additional form fields
            const additionalInputs = paymentForm ? paymentForm.querySelectorAll(
                'input:not([name="paymentMethod"]), select, textarea') : [];
            additionalInputs.forEach(input => {
                input.addEventListener('change', function() {
                    console.log('Iframe: Additional form field changed:', this.name, '=', this
                        .value);
                    notifyParentAboutSelection();
                });

                input.addEventListener('input', function() {
                    console.log('Iframe: Additional form field input:', this.name, '=', this.value);
                    notifyParentAboutSelection();
                });
            });
        });
    </script>

    @php
        $jsGateway = [];
        $submitGateway = [];
    @endphp
    @if (!blank($paymentGateways))
        @foreach ($paymentGateways as $paymentGateway)
            @if ($paymentGateway->misc != null)
                @if (!blank(json_decode($paymentGateway->misc)))
                    @if (!blank(json_decode($paymentGateway->misc)->js))
                        @foreach (json_decode($paymentGateway->misc)->js as $js)
                            @include('paymentGateways.' . str_replace('.blade.php', '', $js))
                        @endforeach
                    @endif
                    @if (!blank(json_decode($paymentGateway->misc)->input))
                        @if (isset(json_decode($paymentGateway->misc)->input[0]))
                            @php $jsGateway[$paymentGateway->slug] = isset(json_decode($paymentGateway->misc)->input[0]); @endphp
                        @endif
                    @endif
                    @if (!blank(json_decode($paymentGateway->misc)->submit))
                        @if (isset(json_decode($paymentGateway->misc)->submit) && json_decode($paymentGateway->misc)->submit)
                            @php $submitGateway[$paymentGateway->slug] = json_decode($paymentGateway->misc)->submit; @endphp
                        @endif
                    @endif
                @endif
            @endif
        @endforeach
    @endif
    @php
        $jsGateway = json_encode($jsGateway);
        $submitGateway = json_encode($submitGateway);
    @endphp

    <script src="{{ asset('lib/jquery-v3.2.1.min.js') }}"></script>
    <script>
        const gateway = <?= $jsGateway ?>;
        const submitGateway = <?= $submitGateway ?>;
        console.log('Iframe: Gateway configuration loaded:', gateway);
    </script>
    <script src="{{ asset('paymentGateways/payment.js') }}"></script>

    @if (!$isIframe)
        <script type="application/javascript">
            let data       = JSON.parse(localStorage.getItem('vuex'));
            const url      = '<?=URL::to('/') . "/menu/"?>';
            if (data.tableCart.paymentMethod) {
                document.getElementById('home-route').setAttribute('href', url + data.tableCart.table.slug);
                document.getElementById('home-route1').setAttribute('href', url + data.tableCart.table.slug);
            }
        </script>
    @endif
</body>

</html>
