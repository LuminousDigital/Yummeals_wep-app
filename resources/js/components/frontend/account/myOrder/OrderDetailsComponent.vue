<template>
    <LoadingComponent :props="loading" />
    <section class="pt-28 pb-16">
        <div class="container max-w-3xl">
            <router-link
                :to="{ name: 'frontend.myOrder' }"
                class="mb-3 inline-flex items-center gap-2 text-primary"
            >
                <i class="lab lab-undo lab-font-size-16"></i>
                <span class="text-xs font-medium leading-6">{{
                    $t("label.back_to_orders")
                }}</span>
            </router-link>
            <div class="flex items-start flex-col md:flex-row gap-6">
                <div class="w-full">
                    <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-sm leading-6 mb-1 font-medium">
                            {{ $t("label.order_id") }}:
                            <span class="text-[#008BBA]"
                                >#{{ order.order_serial_no }}</span
                            >
                        </h3>
                        <p class="text-xs font-light mb-3">
                            {{ order.order_datetime }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-sm capitalize"
                                >{{ $t("label.order_type") }}:</span
                            >
                            <span class="text-sm capitalize text-heading">
                                {{ enums.orderTypeEnumArray[order.order_type] }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mb-5">
                            <span
                                v-if="
                                    parseInt(order.order_type) ==
                                    parseInt(enums.orderTypeEnum.TAKEAWAY)
                                "
                                class="text-sm capitalize"
                                >{{ $t("label.takeaway_time") }} :</span
                            >
                            <span v-else class="text-sm capitalize"
                                >{{ $t("label.delivery_time") }}:</span
                            >
                            <span class="text-sm capitalize text-heading">
                                {{ order.delivery_date }}
                                {{ order.delivery_time }}
                            </span>
                        </div>
                        <div
                            v-if="order.otp && order.otp_expiry"
                            class="lex flex-wrap items-center gap-2 mb-5"
                        >
                            <div
                                class="inline-flex flex-col max-[320px]:items-start max-[320px]:gap-1 sm:flex-row sm:items-center sm:space-x-2 border border-gray-300 rounded-xl px-3 py-2 bg-white shadow-sm w-auto"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-sm font-semibold text-gray-600"
                                    >
                                        {{ $t("label.otp") }}:
                                    </span>
                                    <span
                                        class="text-lg font-bold text-orange-500 tracking-widest"
                                    >
                                        {{ order.otp }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs text-gray-500 max-[320px]:mt-1 pr-2"
                                >
                                    {{ $t("label.expires") }}:
                                    {{ formatOtpExpiry(order.otp_expiry) }}
                                </span>
                            </div>
                        </div>
                        <OrderStatusComponent :props="order" />

                        <div v-if="orderBranch">
                            <h3 class="font-medium mb-2">
                                {{ orderBranch.name }}
                            </h3>
                            <div
                                class="flex items-center justify-between gap-5"
                            >
                                <div
                                    class="flex items-start justify-start gap-2.5"
                                >
                                    <i
                                        class="lab lab-location text-xs leading-none mt-1.5 flex-shrink-0 lab-font-size-14"
                                    ></i>
                                    <span
                                        class="text-sm leading-6 text-heading"
                                        >{{ orderBranch.address }}</span
                                    >
                                </div>
                                <div
                                    class="flex gap-4"
                                    v-if="
                                        parseInt(order.status) !==
                                            parseInt(
                                                enums.orderStatusEnum.REJECTED
                                            ) &&
                                        parseInt(order.status) !==
                                            parseInt(
                                                enums.orderStatusEnum.CANCELED
                                            )
                                    "
                                >
                                    <OrderDetailsMapComponent
                                        :order="order"
                                        :branch="orderBranch"
                                    />
                                    <router-link
                                        class="w-8 h-8 rounded-full flex items-center justify-center bg-[#FFEDF4]"
                                        :to="{
                                            name: 'frontend.chat',
                                            query: { id: orderBranch.id },
                                        }"
                                    >
                                        <i
                                            class="lab lab-messages-2 font-fill-primary lab-font-size-16"
                                        ></i>
                                    </router-link>
                                    <a
                                        :href="'tel:' + orderBranch.phone"
                                        class="w-8 h-8 rounded-full flex items-center justify-center bg-[#FFEDF4]"
                                    >
                                        <i
                                            class="lab lab-call-calling font-fill-primary lab-font-size-16"
                                        ></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-4"
                            v-if="
                                parseInt(order.status) ===
                                parseInt(enums.orderStatusEnum.REJECTED)
                            "
                        >
                            <h3
                                class="capitalize font-medium text-sm leading-6 mb-2"
                            >
                                {{ $t("label.reason") }}:
                            </h3>
                            <p class="text-sm text-heading mb-2">
                                {{ order.reason }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="p-4 mb-6 rounded-2xl shadow-xs bg-white"
                        v-if="
                            orderAddress &&
                            order.order_type === enums.orderTypeEnum.DELIVERY
                        "
                    >
                        <h3 class="text-sm leading-6 font-medium mb-2">
                            {{ $t("label.delivery_address") }}
                        </h3>
                        <div class="flex items-start justify-start gap-2.5">
                            <i
                                class="lab lab-location leading-none mt-1.5 flex-shrink-0 lab-font-size-14"
                            ></i>
                            <span class="text-sm leading-6 text-heading">
                                {{
                                    orderAddress.apartment
                                        ? orderAddress.apartment + ", "
                                        : ""
                                }}
                                {{ orderAddress.address }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="
                            parseInt(order.status) !==
                                parseInt(enums.orderStatusEnum.REJECTED) &&
                            parseInt(order.status) !==
                                parseInt(enums.orderStatusEnum.CANCELED)
                        "
                        class="p-4 rounded-2xl shadow-xs bg-white"
                    >
                        <h3
                            class="capitalize font-medium text-sm leading-6 mb-2"
                        >
                            {{ $t("label.payment_info") }}
                        </h3>
                        <ul class="flex flex-col gap-2 mb-6">
                            <li
                                class="flex items-center gap-2"
                                v-if="
                                    parseInt(order.source) ===
                                    enums.sourceEnum.POS
                                "
                            >
                                <span class="capitalize text-sm leading-6"
                                    >{{ $t("label.method") }}:</span
                                >
                                <span
                                    class="capitalize text-sm leading-6 text-heading"
                                >
                                    {{
                                        enums.posPaymentMethodEnumArray[
                                            order.pos_payment_method
                                        ]
                                    }}

                                    <span
                                        v-if="
                                            order.pos_payment_method !==
                                                enums.posPaymentMethodEnum
                                                    .CASH &&
                                            order.pos_payment_note
                                        "
                                    >
                                        ({{ order.pos_payment_note }})</span
                                    >
                                </span>
                            </li>
                            <li class="flex items-center gap-2" v-else>
                                <span class="capitalize text-sm leading-6"
                                    >{{ $t("label.method") }}:</span
                                >
                                <span
                                    v-if="order.transaction"
                                    class="capitalize text-sm leading-6 text-heading"
                                >
                                    {{ order.transaction.payment_method }} ({{
                                        order.transaction.transaction_no
                                    }})
                                </span>
                                <span
                                    v-else
                                    class="capitalize text-sm leading-6 text-heading"
                                >
                                    {{
                                        enums.paymentTypeEnumArray[
                                            order.payment_method
                                        ]
                                    }}
                                </span>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="capitalize text-sm leading-6"
                                    >{{ $t("label.status") }}:</span
                                >
                                <span
                                    class="capitalize text-sm leading-6"
                                    :class="
                                        enums.paymentStatusEnum.PAID ===
                                        order.payment_status
                                            ? 'text-green-600'
                                            : 'text-[#FB4E4E]'
                                    "
                                >
                                    {{
                                        enums.paymentStatusEnumArray[
                                            order.payment_status
                                        ]
                                    }}
                                </span>
                            </li>
                        </ul>

                        <button
                            v-if="
                                setting.site_online_payment_gateway ===
                                    enums.activityEnum.ENABLE &&
                                order.transaction === null &&
                                order.payment_status ===
                                    enums.paymentStatusEnum.UNPAID
                            "
                            @click="openPaymentModal"
                            class="w-full rounded-lg capitalize font-normal text-center leading-6 py-3 text-white bg-primary"
                        >
                            {{ $t("button.pay_now") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Modal -->
    <div
        v-if="showPaymentModal"
        ref="paymentModal"
        id="payment-modal"
        class="modal payment-modal ff-modal flex items-center justify-center"
    >
        <div class="modal-dialog max-w-[800px] relative w-full h-[50vh]">
            <button
                class="modal-close fa-regular fa-circle-xmark absolute top-5 right-2.5 z-10 bg-white rounded-full p-1"
                @click.prevent="closePaymentModal"
            ></button>
            <div class="modal-body h-full">
                <!-- Loading overlay for iframe using the same LoadingComponent -->
                <div 
                    v-if="iframeLoading.isActive" 
                    class="absolute inset-0 flex items-center justify-center bg-none z-10 rounded-lg"
                >
                    <LoadingComponent :props="iframeLoading" />
                </div>
                
                <div class="w-full h-full bg-white rounded-lg overflow-hidden relative">
                    <iframe
                        ref="paymentIframe"
                        :src="paymentUrl"
                        class="w-full h-full border-0 modern-scrollbar"
                        frameborder="0"
                        allow="payment"
                        @load="handleIframeLoad"
                        @error="handleIframeError"
                    >
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../admin/components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum";
import paymentTypeEnum from "../../../../enums/modules/paymentTypeEnum";
import OrderStatusComponent from "../../components/OrderStatusComponent";
import OrderDetailsMapComponent from "./OrderDetailsMapComponent";
import FrontendOrderReceiptComponent from "./FrontendOrderReceiptComponent";
import activityEnum from "../../../../enums/modules/activityEnum";
import sourceEnum from "../../../../enums/modules/sourceEnum";
import posPaymentMethodEnum from "../../../../enums/modules/posPaymentMethodEnum";
import ENV from "../../../../config/env";
import axios from "axios";

export default {
    name: "OrderDetailsComponent",
    components: {
        OrderDetailsMapComponent,
        LoadingComponent,
        OrderStatusComponent,
        FrontendOrderReceiptComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            showPaymentModal: false,
            paymentUrl: "",
            iframeLoading: {
                isActive: false,
                class: "!bg-transparent"
            },
            selectedPaymentMethod: null,
            paymentFormData: null,
            enums: {
                activityEnum: activityEnum,
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                paymentStatusEnum: paymentStatusEnum,
                paymentTypeEnum: paymentTypeEnum,
                sourceEnum: sourceEnum,
                posPaymentMethodEnum: posPaymentMethodEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t(
                        "label.out_for_delivery"
                    ),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table"),
                },
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid"),
                },
                paymentTypeEnumArray: {
                    [paymentTypeEnum.CASH_ON_DELIVERY]: this.$t(
                        "label.cash_on_delivery"
                    ),
                    [paymentTypeEnum.E_WALLET]: this.$t("label.e_wallet"),
                    [paymentTypeEnum.PAYPAL]: this.$t("label.paypal"),
                },
                posPaymentMethodEnumArray: {
                    [posPaymentMethodEnum.CASH]: this.$t("label.cash"),
                    [posPaymentMethodEnum.CARD]: this.$t("label.card"),
                    [posPaymentMethodEnum.MOBILE_BANKING]: this.$t(
                        "label.mobile_banking"
                    ),
                    [posPaymentMethodEnum.OTHER]: this.$t("label.other"),
                },
            },
            activeOrder: {},
            ENV: ENV
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters["frontendSetting/lists"];
        },
        order: function () {
            return this.$store.getters["frontendOrder/show"];
        },
        orderItems: function () {
            return this.$store.getters["frontendOrder/orderItems"];
        },
        orderUser: function () {
            return this.$store.getters["frontendOrder/orderUser"];
        },
        orderBranch: function () {
            return this.$store.getters["frontendOrder/orderBranch"];
        },
        orderAddress: function () {
            return this.$store.getters["frontendOrder/orderAddress"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.$store
                .dispatch("frontendOrder/show", this.$route.params.id)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((error) => {
                    this.loading.isActive = false;
                });
        }

        // Add message listener for iframe communication
        window.addEventListener("message", this.handleIframeMessage);
        console.log("Parent: Message listener added");
    },
    beforeUnmount() {
        this.closePaymentModal();
        window.removeEventListener("message", this.handleIframeMessage);
        console.log("Parent: Message listener removed");
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        formatOtpExpiry: function (expiry) {
            try {
                const date = new Date(expiry);
                return date.toLocaleString();
            } catch (e) {
                return expiry;
            }
        },
        openPaymentModal: function () {
            if (this.order.uuid) {
                this.paymentUrl =
                    "/payment/" + this.order.uuid + "/pay?isIframe=true";
                this.showPaymentModal = true;
                this.iframeLoading.isActive = true;

                // Reset payment data when opening modal
                this.selectedPaymentMethod = null;
                this.paymentFormData = null;

                // Add active class to modal
                setTimeout(() => {
                    const modalTarget = this.$refs.paymentModal;
                    modalTarget?.classList?.add("active");
                    document.body.style.overflowY = "hidden";
                }, 10);

                console.log(
                    "Parent: Payment modal opened with URL:",
                    this.paymentUrl
                );
            }
        },
        closePaymentModal: function () {
            this.showPaymentModal = false;
            this.iframeLoading.isActive = false;
            const modalTarget = this.$refs.paymentModal;
            modalTarget?.classList?.remove("active");
            document.body.style.overflowY = "auto";
            console.log("Parent: Payment modal closed");
        },
        handleIframeLoad: function () {
            console.log("Parent: Payment iframe loaded successfully");
            this.iframeLoading.isActive = false;
        },
        handleIframeError: function () {
            console.error("Parent: Payment iframe failed to load");
            this.iframeLoading.isActive = false;
            alertService.error("Failed to load payment gateway. Please try again.");
        },
        handleIframeMessage: function (event) {
            console.log("Parent: Received message from iframe:", event.data);

            // Validate origin for security using APP_URL from environment
            const allowedOrigin =
                ENV.API_URL || "https://app.yummealsapp.com";
            console.log(
                "Parent: Checking origin. Allowed:",
                allowedOrigin,
                "Received:",
                event.origin
            );

            if (event.origin !== allowedOrigin) {
                console.warn(
                    "Parent: Message from unauthorized origin:",
                    event.origin
                );
                return;
            }

            if (event.data && event.data.type) {
                console.log(
                    "Parent: Processing message type:",
                    event.data.type
                );

                switch (event.data.type) {
                    case "PAYMENT_METHOD_SELECTED":
                        this.selectedPaymentMethod = event.data.paymentMethod;
                        this.paymentFormData = event.data.formData;
                        console.log(
                            "Parent: Payment method selected:",
                            this.selectedPaymentMethod
                        );
                        console.log("Parent: Form data:", this.paymentFormData);
                        break;

                    case "PAYMENT_FORM_SUBMIT":
                        console.log(
                            "Parent: Payment form submitted from iframe"
                        );
                        console.log(
                            "Parent: Selected method from event:",
                            event.data.paymentMethod
                        );
                        console.log(
                            "Parent: Form data from event:",
                            event.data.formData
                        );

                        // Use the data directly from the event, not from data properties
                        const paymentMethod = event.data.paymentMethod;
                        const formData = event.data.formData;

                        if (!paymentMethod) {
                            console.error(
                                "Parent: No payment method in event data"
                            );
                            alertService.error(
                                "Please select a payment method"
                            );
                            return;
                        }

                        // Close modal and process payment from parent using event data directly
                        this.closePaymentModal();
                        this.processPaymentFromParent(paymentMethod, formData);
                        break;

                    case "PAYMENT_IFRAME_READY":
                        console.log("Parent: Iframe is ready and listening");
                        break;
                }
            }
        },

        processPaymentFromParent: function (paymentMethod, formData) {
            if (!paymentMethod) {
                console.error(
                    "Parent: No payment method provided to processPaymentFromParent"
                );
                alertService.error("Please select a payment method");
                return;
            }

            console.log(
                "Parent: Starting payment processing with method:",
                paymentMethod
            );
            console.log("Parent: Form data received:", formData);

            // For ALL payment methods, use traditional form submission
            // This avoids CORS issues with payment gateways and works with your existing backend logic
            console.log(
                "Parent: Using form submission for payment method:",
                paymentMethod
            );
            this.submitPaymentForm(paymentMethod, formData);
        },

        // Handle all payment methods via form submission
        submitPaymentForm: function (paymentMethod, formData) {
            console.log(
                "Parent: Creating payment form for method:",
                paymentMethod
            );

            // Create a hidden form
            const form = document.createElement("form");
            form.method = "POST";
            form.action = `/payment/${this.order.uuid}/pay`;
            form.target = "_self"; // Open in same window
            form.style.display = "none";

            // Add payment method
            const paymentMethodInput = document.createElement("input");
            paymentMethodInput.type = "hidden";
            paymentMethodInput.name = "paymentMethod";
            paymentMethodInput.value = paymentMethod;
            form.appendChild(paymentMethodInput);

            // Add CSRF token
            if (formData && formData._token) {
                const tokenInput = document.createElement("input");
                tokenInput.type = "hidden";
                tokenInput.name = "_token";
                tokenInput.value = formData._token;
                form.appendChild(tokenInput);
            }

            // Add other form fields from iframe
            if (formData) {
                Object.keys(formData).forEach((key) => {
                    if (key !== "_token" && key !== "paymentMethod") {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        input.value = formData[key];
                        form.appendChild(input);
                    }
                });
            }

            // Add form to document and submit
            document.body.appendChild(form);
            console.log("Parent: Submitting payment form");

            // Close the modal first
            this.closePaymentModal();

            // Show loading state briefly (the page will redirect soon)
            this.loading.isActive = true;

            // Submit the form - this will cause a full page redirect
            // For cash/cash-on-delivery: will redirect to success page
            // For payment gateways: will redirect to gateway (Paystack, etc.)
            form.submit();

            // The page will redirect, so we don't need to handle the response
        },
        changeStatus: function (status) {
            appService
                .cancelOrder()
                .then((res) => {
                    try {
                        this.loading.isActive = true;
                        this.$store
                            .dispatch("frontendOrder/changeStatus", {
                                id: this.$route.params.id,
                                status: status,
                            })
                            .then((res) => {
                                this.loading.isActive = false;
                                alertService.successFlip(
                                    1,
                                    this.$t("label.status")
                                );
                                this.$store
                                    .dispatch("frontendEditProfile/profile")
                                    .then((res) => {
                                        this.$store
                                            .dispatch(
                                                "updateAuthInfo",
                                                res.data.data
                                            )
                                            .then((res) => {
                                                this.loading.isActive = false;
                                            })
                                            .catch((err) => {
                                                this.loading.isActive = false;
                                            });
                                    });
                            })
                            .catch((err) => {
                                this.loading.isActive = false;
                                alertService.error(err.response.data.message);
                            });
                    } catch (err) {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    }
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
    },
};
</script>
