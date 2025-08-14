<template>
    <LoadingComponent :props="loading" />
    <section class="pt-8 pb-16">
        <div class="container max-w-3xl">
            <router-link :to="{ name: 'frontend.myOrder' }" class="mb-3 inline-flex items-center gap-2 text-primary">
                <i class="lab lab-undo lab-font-size-16"></i>
                <span class="text-xs font-medium leading-6">{{ $t('label.back_to_orders') }}</span>
            </router-link>
            <div class="flex items-start flex-col md:flex-row gap-6">
                <div class="w-full">
                    <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-sm leading-6 mb-1 font-medium">{{ $t("label.order_id") }}: <span
                                class="text-[#008BBA]">#{{ order.order_serial_no }}</span></h3>
                        <p class="text-xs font-light mb-3">{{ order.order_datetime }}</p>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-sm capitalize">{{ $t("label.order_type") }}:</span>
                            <span class="text-sm capitalize text-heading">
                                {{ enums.orderTypeEnumArray[order.order_type] }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span v-if="parseInt(order.order_type) == parseInt(enums.orderTypeEnum.TAKEAWAY)"
                                class="text-sm capitalize">{{ $t("label.takeaway_time") }} :</span>
                            <span v-else class="text-sm capitalize">{{ $t("label.delivery_time") }}:</span>
                            <span class="text-sm capitalize text-heading">
                                {{ order.delivery_date }} {{ order.delivery_time }}
                            </span>
                        </div>
                       <div v-if="order.otp" class="flex flex-wrap items-center gap-2 mb-3">
                         <div
                           class="inline-flex flex-col max-[320px]:items-start max-[320px]:gap-1 sm:flex-row sm:items-center sm:space-x-2 border border-gray-300 rounded-xl px-2 py-1 bg-white shadow-sm w-auto"
                         >
                           <div class="flex items-center gap-2">
                             <span class="text-[14px] font-semibold text-gray-600">
                               {{ $t("label.otp") }}:
                             </span>
                             <span class="text-[16px] font-bold text-orange-500 tracking-widest">
                               {{ order.otp }}
                             </span>
                           </div>
                         </div>
                       </div>
                        <OrderStatusComponent :props="order" />

                        <div>
                            <h3 class="font-medium mb-2">{{ orderBranch.name }}</h3>
                            <div class="flex items-center justify-between gap-5">
                                <div class="flex items-start justify-start gap-2.5">
                                    <i
                                        class="lab lab-location text-xs leading-none mt-1.5 flex-shrink-0 lab-font-size-14"></i>
                                    <span class="text-sm leading-6 text-heading">{{ orderBranch.address }}</span>
                                </div>
                                <div class="flex gap-4"
                                    v-if="parseInt(order.status) !== parseInt(enums.orderStatusEnum.REJECTED) && parseInt(order.status) !== parseInt(enums.orderStatusEnum.CANCELED)">
                                    <OrderDetailsMapComponent :order="order" :branch="orderBranch" />
                                    <router-link
                                        class="w-8 h-8 rounded-full flex items-center justify-center bg-[#FFEDF4]"
                                        :to="{ name: 'frontend.chat', query: { id: orderBranch.id } }">
                                        <i class="lab lab-messages-2 font-fill-primary lab-font-size-16"></i>
                                    </router-link>
                                    <a :href="'tel:' + orderBranch.phone"
                                        class="w-8 h-8 rounded-full flex items-center justify-center bg-[#FFEDF4]">
                                        <i class="lab lab-call-calling font-fill-primary lab-font-size-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4" v-if="parseInt(order.status) === parseInt(enums.orderStatusEnum.REJECTED)">
                            <h3 class="capitalize font-medium text-sm leading-6 mb-2">{{ $t("label.reason") }}:</h3>
                            <p class="text-sm text-heading mb-2">{{ order.reason }}</p>
                        </div>
                    </div>

                    <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white"
                        v-if="orderAddress && order.order_type === enums.orderTypeEnum.DELIVERY">
                        <h3 class="text-sm leading-6 font-medium mb-2">{{ $t("label.delivery_address") }}</h3>
                        <div class="flex items-start justify-start gap-2.5">
                            <i class="lab lab-location leading-none mt-1.5 flex-shrink-0 lab-font-size-14"></i>
                            <span class="text-sm leading-6 text-heading">
                                {{ orderAddress.apartment ? orderAddress.apartment + ', ' : '' }}
                                {{ orderAddress.address }}
                            </span>
                        </div>
                    </div>

                    <div v-if="parseInt(order.status) !== parseInt(enums.orderStatusEnum.REJECTED) && parseInt(order.status) !== parseInt(enums.orderStatusEnum.CANCELED)"
                        class="p-4 rounded-2xl shadow-xs bg-white">
                        <h3 class="capitalize font-medium text-sm leading-6 mb-2">{{ $t("label.payment_info") }}</h3>
                        <ul class="flex flex-col gap-2 mb-6">
                            <li class="flex items-center gap-2" v-if="parseInt(order.source) === enums.sourceEnum.POS">
                                <span class="capitalize text-sm leading-6">{{ $t('label.method') }}:</span>
                                <span class="capitalize text-sm leading-6 text-heading">
                                    {{ enums.posPaymentMethodEnumArray[order.pos_payment_method] }}

                                    <span
                                        v-if="order.pos_payment_method !== enums.posPaymentMethodEnum.CASH && order.pos_payment_note">
                                        ({{ order.pos_payment_note }})</span>
                                </span>
                            </li>
                            <li class="flex items-center gap-2" v-else>
                                <span class="capitalize text-sm leading-6">{{ $t('label.method') }}:</span>
                                <span v-if="order.transaction" class="capitalize text-sm leading-6 text-heading">
                                    {{ order.transaction.payment_method }} ({{ order.transaction.transaction_no }})
                                </span>
                                <span v-else class="capitalize text-sm leading-6 text-heading">
                                    {{ enums.paymentTypeEnumArray[order.payment_method] }}
                                </span>
                            </li>

                            <li class="flex items-center gap-2">
                                <span class="capitalize text-sm leading-6">{{ $t("label.status") }}:</span>
                                <span class="capitalize text-sm leading-6"
                                    :class="enums.paymentStatusEnum.PAID === order.payment_status ? 'text-green-600' : 'text-[#FB4E4E]'">
                                    {{ enums.paymentStatusEnumArray[order.payment_status] }}
                                </span>
                            </li>
                        </ul>

                        <a :href="'/payment/' + order.id + '/pay'"
                            v-if="setting.site_online_payment_gateway === enums.activityEnum.ENABLE && order.transaction === null && order.payment_status === enums.paymentStatusEnum.UNPAID"
                            class="w-full rounded-3xl capitalize font-medium text-center leading-6 py-3 text-white bg-primary">
                            {{ $t('button.pay_now') }}
                        </a>
                    </div>
                </div>

                <div class="w-full rounded-2xl shadow-xs bg-white">
                    <div class="p-4 border-b">
                        <h3 class="font-medium text-sm leading-6 capitalize mb-4">{{ $t('label.order_details') }}</h3>
                        <div class="pl-3">
                            <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2"
                                v-if="orderItems.length > 0" v-for="item in orderItems" :key="item">
                                <div class="flex items-center gap-3 relative">
                                    <h3
                                        class="absolute top-5 -left-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                        {{ item.quantity }}
                                    </h3>
                                    <img class="w-16 h-16 rounded-lg flex-shrink-0" :src="item.item_image"
                                        alt="thumbnail">
                                    <div class="w-full">
                                        <a href="#"
                                            class="text-sm font-medium capitalize transition text-heading hover:underline">
                                            {{ item.item_name }}
                                        </a>

                                        <p v-if="item.item_variations.length > 0" class="capitalize text-xs mb-1.5">
                                            <span v-for="variation in item.item_variations" :key="variation">
                                                <span class="capitalize text-xs w-fit whitespace-nowrap">
                                                    {{ variation.variation_name }}:&nbsp;
                                                </span>
                                                <span class="text-xs">
                                                    {{ variation.name }}
                                                </span>
                                            </span>
                                        </p>

                                        <h3 class="text-xs font-semibold">{{ item.total_currency_price }}</h3>
                                    </div>
                                </div>
                                <ul class="flex flex-col gap-1.5 mt-2">
                                    <li class="flex gap-1" v-if="item.item_extras.length > 0">
                                        <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{
                                            $t('label.extras')
                                            }}:</h3>
                                        <p class="text-xs" v-for="(extra, index) in item.item_extras">
                                            {{ extra.name }}<span v-if="index + 1 < item.item_extras.length">, </span>
                                        </p>
                                    </li>
                                    <li class="flex gap-1" v-if="item.instruction">
                                        <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                            {{ $t('label.instruction') }}:</h3>
                                        <p class="text-xs">{{ item.instruction }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="rounded-xl border border-[#EFF0F6]">
                            <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                                <li class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6 capitalize">{{ $t("label.subtotal") }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{ order.subtotal_currency_price }}
                                    </span>
                                </li>
                                <li class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6 capitalize">{{ $t("label.discount") }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{ order.discount_currency_price }}
                                    </span>
                                </li>
                                <li class="flex items-center justify-between text-heading"
                                    v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                                    <span class="text-sm leading-6 capitalize">{{ $t("label.delivery_charge") }}</span>
                                    <span class="text-sm leading-6 capitalize font-medium text-[#1AB759]">
                                        {{ order.delivery_charge_currency_price }}</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between p-3">
                                <h4 class="text-sm leading-6 font-semibold capitalize">{{ $t("label.total") }}</h4>
                                <h5 class="text-sm leading-6 font-semibold capitalize">
                                    {{ order.total_currency_price }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="p-4" v-if="order.status === enums.orderStatusEnum.PENDING">
                        <button @click="changeStatus(enums.orderStatusEnum.CANCELED)"
                            class="w-full rounded-3xl capitalize font-medium leading-6 py-3 text-white bg-[#FB4E4E] mb-3">
                            {{ $t('label.cancel_order') }}
                        </button>
                    </div> -->
                    <div class="p-4">
                        <button type="button" v-print="printObj"
                            class="flex w-full items-center justify-center gap-2 px-4 py-3.5 rounded-full shadow-db-card bg-[#1AB759]">
                            <i class="lab lab-printer-line text-base font-medium text-white"></i>
                            <span class="text-base font-medium capitalize text-white"> Print Order Details</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Receipt Modal -->
    <div id="receipt" class="modal">
        <div class="modal-dialog max-w-[340px] rounded-none" id="print" :dir="direction">
            <div class="modal-body">
                <div class="text-center pb-3.5 border-b border-dashed border-gray-400">
                    <h3 class="text-2xl font-bold mb-1">{{ setting.company_name }}</h3>
                    <h4 class="text-sm font-normal">{{ orderBranch.address }}</h4>
                    <h5 class="text-sm font-normal">Tel: {{ orderBranch.phone }}</h5>
                </div>

                <table class="w-full my-1.5">
                    <tbody>
                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">{{ $t('button.order') }}
                                #{{ order.order_serial_no }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">{{ order.order_date }}</td>
                            <td class="text-xs text-right py-0.5 text-heading">{{ order.order_time }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <thead class="border-t border-b border-dashed border-gray-400">
                        <tr>
                            <th scope="col" class="py-1 font-normal text-xs capitalize text-left text-heading w-8">
                                {{ $t('label.qty') }}
                            </th>
                            <th scope="col"
                                class="py-1 font-normal text-xs capitalize flex items-center justify-between text-heading">
                                <span>{{ $t('label.item_description') }}</span>
                                <span>{{ $t('label.price') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="border-b border-dashed border-gray-400">
                        <tr v-if="orderItems.length > 0" v-for="item in orderItems" :key="item">
                            <td class="text-left font-normal align-top py-1">
                                <p class="text-xs leading-5 text-heading">{{ item.quantity }}</p>
                            </td>
                            <td class="text-left font-normal align-top py-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-normal capitalize">{{ item.item_name }}</h4>
                                    <p class="text-xs leading-5 text-heading">{{ item.total_without_tax_currency_price }}
                                    </p>
                                </div>
                                <p v-if="Object.keys(item.item_variations).length !== 0"
                                    class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    <span v-for="(variation, index) in item.item_variations">
                                        {{ variation.variation_name }}: {{ variation.name }}
                                        <span v-if="index + 1 < Object.keys(item.item_variations).length">, </span>
                                    </span>
                                </p>
                                <p v-if="item.item_extras.length > 0"
                                    class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    {{ $t('label.extras') }}:
                                    <span v-for="(extra, index) in item.item_extras">
                                        {{ extra.name }}
                                        <span v-if="index + 1 < item.item_extras.length">, </span>
                                    </span>
                                </p>
                                <p v-if="item.instruction" class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    {{ $t('label.instruction') }}: {{ item.instruction }}
                                </p>

                                <div class="flex items-center justify-between" v-if="item.tax_rate > 0">
                                    <p class="text-xs leading-5 font-normal text-heading">{{ item.tax_name }}
                                        ({{ item.tax_currency_rate }} {{ item.tax_type }})</p>
                                    <p class="text-xs leading-5 font-normal text-heading">
                                        {{ item.tax_currency_amount }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="py-2 pl-7">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.subtotal') }}:</td>
                                <td class="text-xs text-right py-0.5 text-heading">
                                    {{ order.subtotal_without_tax_currency_price }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">
                                    {{ $t('label.total_tax') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">
                                    {{ order.total_tax_currency_price }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.discount') }}:</td>
                                <td class="text-xs text-right py-0.5 text-heading">{{ order.discount_currency_price }}</td>
                            </tr>

                            <tr v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.delivery_charge')
                                }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">{{ order.delivery_charge_currency_price
                                }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-xs text-left py-0.5 font-bold uppercase text-heading">
                                    {{ $t('label.total') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 font-bold text-heading">
                                    {{ order.total_currency_price }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-xs py-1 border-t border-b border-dashed border-gray-400 text-heading">
                    <table class="w-full">
                        <tbody>
                            <tr v-if="parseInt(order.source) === enums.sourceEnum.POS && order.cash_back_amount > 0">
                                <td class="pt-1 pb-1 pr-1 align-top text-start">{{ $t('label.payment_type') }}: {{ enums.posPaymentMethodEnumArray[order.pos_payment_method] }}</td>
                                <td class="pt-1 pb-1 text-end" v-if="order.cash_back_amount > 0">
                                    <div>{{ $t('label.cash') }}: {{ order.pos_received_currency_amount }}</div>
                                    <span>{{ $t('label.change') }} : {{ order.cash_back_currency_amount }}</span>
                                </td>
                            </tr>
                            <tr v-else-if="parseInt(order.source) === enums.sourceEnum.POS">
                                <td class="pt-1 pb-1 pr-1 align-top text-start">{{ $t('label.payment_type') }}: </td>
                                <td class="pt-1 pb-1">{{ enums.posPaymentMethodEnumArray[order.pos_payment_method] }}</td>
                            </tr>
                            <tr v-else>
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.payment_type') }}:</td>
                                <td v-if="order.transaction" class="pt-1 pb-1">{{ order.transaction.payment_method }}</td>
                                <td v-else class="pt-1 pb-1">{{ enums.paymentTypeEnumArray[order.payment_method] }}</td>
                            </tr>
                            <tr>
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.order_type') }}:</td>
                                <td class="pt-1 pb-1">{{ enums.orderTypeEnumArray[order.order_type] }}</td>
                            </tr>
                            <tr>
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.delivery_time') }}:</td>
                                <td class="pt-1 pb-1">{{ order.delivery_date }} {{ order.delivery_time }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="text-xs py-1 border-b border-dashed border-gray-400 text-heading">
                    <table>
                        <tbody>
                            <tr>
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.customer') }}:</td>
                                <td class="pt-1 pb-1">{{ orderUser.name }}</td>
                            </tr>
                            <tr>
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.phone') }}:</td>
                                <td class="pt-1 pb-1">{{ orderUser.country_code + '' + orderUser.phone }}</td>
                            </tr>
                            <tr v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                                <td class="pt-1 pb-1 pr-1">{{ $t('label.address') }}:</td>
                                <td class="pt-1 pb-1">
                                    {{ orderAddress.apartment ? orderAddress.apartment + ', ' : '' }}
                                    {{ orderAddress.address }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-center pt-2 pb-4">
                    <p class="text-[11px] leading-[14px] capitalize text-heading">{{ $t('message.thank_you') }}</p>
                </div>

                <div class="flex flex-col items-end">
                    <h5 class="text-[8px] font-normal text-left w-[46px] leading-[10px]">
                        {{ $t('label.powered_by') }}
                    </h5>
                    <h6 class="text-xs font-normal leading-4">{{ setting.company_name }}</h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import print from "vue3-print-nb";
import LoadingComponent from "../../../admin/components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum";
import paymentTypeEnum from "../../../../enums/modules/paymentTypeEnum";
import OrderStatusComponent from "../../components/OrderStatusComponent";
import OrderDetailsMapComponent from "./OrderDetailsMapComponent";
import activityEnum from "../../../../enums/modules/activityEnum";
import sourceEnum from "../../../../enums/modules/sourceEnum";
import posPaymentMethodEnum from "../../../../enums/modules/posPaymentMethodEnum";
import displayModeEnum from "../../../../enums/modules/displayModeEnum";

export default {
    name: "OrderDetailsComponent",
    components: { OrderDetailsMapComponent, LoadingComponent, OrderStatusComponent },
    directives: {
        print
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.order_receipt"),
            },
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
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                },
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                paymentTypeEnumArray: {
                    [paymentTypeEnum.CASH_ON_DELIVERY]: this.$t("label.cash_on_delivery"),
                    [paymentTypeEnum.E_WALLET]: this.$t("label.e_wallet"),
                    [paymentTypeEnum.PAYPAL]: this.$t("label.paypal")
                },
                posPaymentMethodEnumArray: {
                    [posPaymentMethodEnum.CASH]: this.$t("label.cash"),
                    [posPaymentMethodEnum.CARD]: this.$t("label.card"),
                    [posPaymentMethodEnum.MOBILE_BANKING]: this.$t("label.mobile_banking"),
                    [posPaymentMethodEnum.OTHER]: this.$t("label.other"),
                },
            },
            activeOrder: {},
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        order: function () {
            return this.$store.getters['frontendOrder/show'];
        },
        orderItems: function () {
            return this.$store.getters['frontendOrder/orderItems'];
        },
        orderUser: function () {
            return this.$store.getters['frontendOrder/orderUser'];
        },
        orderBranch: function () {
            return this.$store.getters['frontendOrder/orderBranch'];
        },
        orderAddress: function () {
            return this.$store.getters['frontendOrder/orderAddress'];
        },
        direction: function () {
            return this.$store.getters['frontendLanguage/show'].display_mode === displayModeEnum.RTL ? 'rtl' : 'ltr';
        },
    },
    mounted() {
        this.loading.isActive = true;
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOrder/show", this.$route.params.id).then(res => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        // formatOtpExpiry: function (c) {
        //     try {
        //         const date = new Date(expiry);
        //         return date.toLocaleString();
        //     } catch (e) {
        //         return expiry;
        //     }
        // },
        changeStatus: function (status) {
            appService.cancelOrder().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("frontendOrder/changeStatus", {
                        id: this.$route.params.id,
                        status: status,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            1,
                            this.$t("label.status")
                        );
                        this.$store.dispatch("frontendEditProfile/profile").then((res) => {
                            this.$store.dispatch('updateAuthInfo', res.data.data).then(res => {
                                this.loading.isActive = false;
                            }).catch((err) => {
                                this.loading.isActive = false;
                            });
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    }
}
</script>
