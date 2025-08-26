<template>
    <transition name="fade">
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-5 disabled:cursor-not-allowed"
            @click="closeModal"
        >
            <div
                class="bg-white rounded-2xl p-6 max-w-[450px] w-full max-h-[95vh] relative shadow-2xl animate-fade-in"
                @click.stop
            >
                <button
                    class="absolute p-1 text-gray-400 transition-colors top-2 right-4 hover:text-gray-700 disabled:cursor-not-allowed"
                    @click="closeModal"
                >
                    <img
                        src="/images/delivery/Close.png"
                        alt="Close"
                        class="w-5 h-5"
                    />
                </button>

                <h2 class="text-lg font-semibold text-orange-600">
                    Delivery Address
                </h2>
                <img :src="imageSrc" alt="Delivery" class="w-40 mx-auto" />

                <div class="mb-3">
                    <label class="block mb-3 text-sm font-medium text-gray-700"
                        >Enter Delivery Address</label
                    >
                    <div class="relative">
                        <div class="relative flex items-center">
                            <div class="absolute z-10 text-gray-400 left-3">
                                <img
                                    src="/images/delivery/LocatioIcon.png"
                                    alt="Location Icon"
                                    width="12"
                                    height="12"
                                />
                            </div>

                            <input
                                v-model="addressInput"
                                type="text"
                                placeholder="Delivery Address"
                                class="w-full px-4 py-3 pl-10 text-base transition-colors duration-200 border border-gray-200 rounded-lg outline-none focus:border-orange-500"
                                @input="onAddressInput"
                                @focus="showPredictions = true"
                                @blur="handleBlur"
                            />

                            <button
                                v-if="addressInput"
                                @click="clearAddress"
                                class="absolute p-1 text-gray-400 transition-colors duration-200 border-none rounded cursor-pointer right-3 bg-none hover:text-gray-700"
                            >
                                <div
                                    v-if="isLoadingPredictions"
                                    class="w-4 h-4 border-2 border-gray-300 rounded-full border-t-orange-500 animate-spin"
                                ></div>
                                <svg
                                    v-else
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <div
                            v-if="showPredictions && predictions.length > 0"
                            class="bg-white border border-gray-200 rounded-lg shadow-lg max-h-[150px] overflow-y-auto mt-1 scrollbar-hide"
                        >
                            <div
                                v-for="prediction in predictions"
                                :key="prediction.place_id"
                                @click="selectPrediction(prediction)"
                                class="flex items-center px-4 py-3 transition-all duration-200 border-b border-gray-100 cursor-pointer hover:bg-orange-50 last:border-b-0 group"
                            >
                                <div
                                    class="flex-shrink-0 mr-3 text-orange-500 transition-transform duration-200 group-hover:scale-110"
                                >
                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                                        />
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-sm font-medium leading-tight text-gray-800 truncate"
                                    >
                                        {{
                                            prediction.structured_formatting
                                                .main_text
                                        }}
                                    </div>
                                    <div
                                        class="mt-1 text-xs leading-tight text-gray-500 truncate"
                                    >
                                        {{
                                            prediction.structured_formatting
                                                .secondary_text
                                        }}
                                    </div>
                                </div>

                                <div
                                    class="text-gray-300 transition-colors duration-200 group-hover:text-orange-400"
                                >
                                    <svg
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M9 18l6-6-6-6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    @click="getCurrentLocation"
                    :disabled="isLoadingLocation"
                    class="flex items-center w-full gap-2 px-4 py-3 mb-3 text-sm text-gray-700 transition-colors duration-200 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg
                        v-if="!isLoadingLocation"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6l4 2"></path>
                    </svg>
                    <div
                        v-else
                        class="w-4 h-4 border-2 border-gray-100 rounded-full border-t-orange-600 animate-spin"
                    ></div>
                    <span>{{
                        isLoadingLocation
                            ? "Getting location..."
                            : "Use current location"
                    }}</span>
                </button>

                <button
                    @click="checkAddressCoverage"
                    :disabled="!canProceed || isCheckingCoverage"
                    class="flex items-center justify-center w-full gap-2 py-3 text-base font-semibold text-white transition-colors duration-200 bg-orange-600 border-none rounded-lg cursor-pointer hover:bg-orange-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                    <div
                        v-if="isCheckingCoverage"
                        class="w-4 h-4 border-2 border-gray-300 rounded-full border-t-orange-500 animate-spin"
                    ></div>
                    <span>{{
                        isCheckingCoverage ? "Checking coverage..." : "Continue"
                    }}</span>
                </button>
            </div>
        </div>
    </transition>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import { v4 as uuidv4 } from "uuid";

export default {
    name: "DeliveryAddressModal",
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        },
    },
    emits: ["close", "location-covered", "location-not-covered"],
    setup(props, { emit }) {
        const addressInput = ref("");
        const predictions = ref([]);
        const showPredictions = ref(false);
        const isLoadingPredictions = ref(false);
        const isLoadingLocation = ref(false);
        const isCheckingCoverage = ref(false);
        const canProceed = ref(false);
        const sessionToken = ref("");
        const imageSrc = ref("/images/delivery/delivery.png");
        const debounceTimer = ref(null);

        const selectedAddress = reactive({
            formatted: "",
            coordinates: null,
        });

        const GOOGLE_PLACES_API_KEY = "AIzaSyAajyqtgqXkk3Ib-9FHy6iovvEiZ5XBueI";

        const initializeSession = () => {
            sessionToken.value = uuidv4();
        };

        const resetForm = () => {
            addressInput.value = "";
            predictions.value = [];
            showPredictions.value = false;
            canProceed.value = false;
        };

        const closeModal = () => {
            resetForm();
            emit("close");
        };

        const clearAddress = () => {
            resetForm();
        };

        const onAddressInput = () => {
            const input = addressInput.value.trim();
            canProceed.value = input.length > 0;

            if (debounceTimer.value) clearTimeout(debounceTimer.value);

            debounceTimer.value = setTimeout(() => {
                if (input.length > 2) {
                    getPlacePredictions(input);
                } else {
                    predictions.value = [];
                    showPredictions.value = false;
                }
            }, 300);
        };

        const getPlacePredictions = async (input) => {
            isLoadingPredictions.value = true;
            try {
                const res = await fetch(
                    `/api/places/autocomplete?input=${encodeURIComponent(
                        input
                    )}&sessiontoken=${sessionToken.value}`
                );
                const data = await res.json();
                predictions.value = data.predictions || [];
                showPredictions.value = predictions.value.length > 0;
            } catch (error) {
                console.error("Prediction error:", error);
                predictions.value = [];
            } finally {
                isLoadingPredictions.value = false;
            }
        };

        const getPlaceDetails = async (placeId) => {
            try {
                isLoadingPredictions.value = true;
                const res = await fetch(
                    `/api/places/details?place_id=${placeId}&sessiontoken=${sessionToken.value}`
                );
                const data = await res.json();

                const result = data.result;
                if (result) {
                    const cleaned = cleanAddress(result.formatted_address);
                    addressInput.value = cleaned;
                    selectedAddress.formatted = cleaned;
                    selectedAddress.coordinates = {
                        lat: result.geometry.location.lat,
                        lng: result.geometry.location.lng,
                    };
                    canProceed.value = true;

                    localStorage.setItem("cachedAddress", cleaned);
                    localStorage.setItem(
                        "cachedAddressTimestamp",
                        Date.now().toString()
                    );
                    localStorage.setItem(
                        "cachedCoordinates",
                        JSON.stringify(selectedAddress.coordinates)
                    );

                    predictions.value = [];
                    showPredictions.value = false;
                    sessionToken.value = uuidv4();
                }
            } catch (err) {
                console.error("Place detail error:", err);
            } finally {
                isLoadingPredictions.value = false;
            }
        };

        const selectPrediction = (prediction) => {
            if (prediction?.place_id) {
                getPlaceDetails(prediction.place_id);
            }
        };

        const handleBlur = () => {
            setTimeout(() => (showPredictions.value = false), 200);
        };

        const getCurrentLocation = async () => {
            if (!navigator.geolocation) {
                alert("Geolocation not supported.");
                return;
            }

            isLoadingLocation.value = true;
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                    });
                });

                const { latitude, longitude } = position.coords;

                const res = await fetch(
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${GOOGLE_PLACES_API_KEY}`
                );
                const data = await res.json();

                const result = data?.results?.[0];
                if (result) {
                    const cleaned = cleanAddress(result.formatted_address);
                    addressInput.value = cleaned;
                    selectedAddress.formatted = cleaned;
                    selectedAddress.coordinates = {
                        lat: latitude,
                        lng: longitude,
                    };
                    canProceed.value = true;

                    localStorage.setItem("cachedAddress", cleaned);
                    localStorage.setItem(
                        "cachedAddressTimestamp",
                        Date.now().toString()
                    );
                    localStorage.setItem(
                        "cachedCoordinates",
                        JSON.stringify(selectedAddress.coordinates)
                    );
                }
            } catch (err) {
                console.error("Location error:", err);
            } finally {
                isLoadingLocation.value = false;
            }
        };

        const checkAddressCoverage = async () => {
            if (!addressInput.value.trim() || !selectedAddress.coordinates) {
                return showNotification(
                    "Error",
                    "Valid address required",
                    "warning"
                );
            }

            isCheckingCoverage.value = true;

            try {
                const res = await fetch(
                    "https://api.yummealsapp.com/api/v1/calculate-distance",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-API-KEY":
                                "z8p53xn6-n2f5-29w7-7193-s500c15553h171620",
                        },
                        body: JSON.stringify({
                            destinations: [
                                `${selectedAddress.coordinates.lat},${selectedAddress.coordinates.lng}`,
                            ],
                        }),
                    }
                );

                const data = await res.json();
                const coverage = data?.data?.distances?.[0];

                localStorage.setItem(
                    "isLocationCovered",
                    coverage?.isLocationCovered ? "true" : "false"
                );

                if (data.success && coverage?.isLocationCovered) {
                    localStorage.setItem("userAddress", addressInput.value);
                    localStorage.setItem("hasEnteredAddress", "true");
                    showNotification(
                        "Success",
                        "We deliver to your area!",
                        "success"
                    );
                    emit("location-covered");
                } else {
                    showNotification(
                        "Notice",
                        "We currently do not deliver to this area.",
                        "warning"
                    );
                    emit("location-not-covered", selectedAddress);
                }
            } catch (err) {
                console.error("Coverage error:", err);
                emit("location-not-covered", selectedAddress);
            } finally {
                isCheckingCoverage.value = false;
            }
        };

        const tryUseCachedAddress = () => {
            const timestamp = parseInt(
                localStorage.getItem("cachedAddressTimestamp")
            );
            const cachedAddress = localStorage.getItem("cachedAddress");
            const cachedCoordinates = localStorage.getItem("cachedCoordinates");

            if (
                cachedAddress &&
                timestamp &&
                Date.now() - timestamp < 3600000
            ) {
                addressInput.value = cachedAddress;
                selectedAddress.formatted = cachedAddress;
                selectedAddress.coordinates = JSON.parse(cachedCoordinates);
                canProceed.value = true;
                return true;
            }
            return false;
        };

        const cleanAddress = (address) => {
            return (
                address
                    ?.replace(/^[a-z]{2}\+[a-z]{2}\s*/i, "")
                    ?.replace(/^[^a-zA-Z0-9\s]+/, "")
                    ?.replace(/^\s+/, "")
                    ?.replace(/\s+/g, " ")
                    ?.trim() || ""
            );
        };

        const showNotification = (title, message, type = "info") => {
            console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        };

        onMounted(() => {
            initializeSession();
            tryUseCachedAddress();
        });

        return {
            imageSrc,
            addressInput,
            predictions,
            showPredictions,
            isLoadingPredictions,
            isLoadingLocation,
            isCheckingCoverage,
            canProceed,
            closeModal,
            clearAddress,
            onAddressInput,
            selectPrediction,
            handleBlur,
            getCurrentLocation,
            checkAddressCoverage,
        };
    },
};
</script>

<style scoped>
@keyframes fade-in {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
