<template>
    <nav
        class="flex items-center justify-between py-3 px-5 fixed bottom-0 left-0 z-20 w-full shadow-xl-top bg-white lg:hidden">
        <router-link :class="checkIsPathAndRoutePathSame('/home') ? 'text-primary' : ''"
            class="flex flex-col items-center gap-1" :to="{ name: 'frontend.home' }">
            <i class="fa-solid fa-house text-sm leading-none"></i>
            <span class="text-xs capitalize">{{ $t('menu.home') }}</span>
        </router-link>

        <router-link :class="checkIsPathAndRoutePathSame('/menu') ? 'text-primary' : ''"
            class="flex flex-col items-center gap-1" :to="{ name: 'frontend.menu', query: { s: categoryProps.slug } }">
            <i class="fa-solid fa-layer-group text-base leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.menu') }}</span>
        </router-link>

        <div class="relative">
            <button @click.prevent="openCanvas('cart')" type="button" ref="mobileCartButton" data-cart-icon class="mobcart text-base w-12 h-12 leading-[48px] text-center rounded-full -mt-12 text-white bg-primary">
                <i class="fa-solid fa-bag-shopping"></i>
            </button>
            <span
                v-if="itemCount > 0"
                :class="badgeAnimation ? 'animate-badge-pop' : ''"
                class="absolute -top-2 -right-2 bg-primary text-white text-[12px] font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-white shadow-lg transition-all duration-300"
            >
                {{ itemCount }}
            </span>
        </div>

        <router-link v-if="logged && profile.role_id !== enums.roleEnum.ADMIN && profile.is_guest !== enums.askEnum.YES" :class="checkIsPathAndRoutePathSame('/offers') ? 'text-primary' : ''"
            class="flex flex-col items-center gap-1" :to="{ name: 'frontend.offers' }">
            <i class="fa-solid fa-tags text-base leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.offers') }}</span>
        </router-link>

        <router-link :class="checkIsPathAndRoutePathSame('/login') ? 'text-primary' : ''" v-if="!logged"
            class="flex flex-col items-center gap-1" :to="{ name: 'auth.login' }">
            <i class="fa-solid fa-circle-user text-base leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.login') }}</span>
        </router-link>

        <button @click.prevent="openCanvas('user-profile-dropdown-box')" type="button" v-else
            class="user-profile-dropdown-box flex flex-col items-center gap-1">
            <i class="fa-solid fa-circle-user text-base leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.profile') }}</span>
        </button>
    </nav>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import roleEnum from "../../../enums/modules/roleEnum";
import askEnum from "../../../enums/modules/askEnum";
import appService from "../../../services/appService";
export default {
    name: "FrontendMobileNavBarComponent",
    data() {
        return {
            loading: {
                isActive: false,
            },
            currentRoute: "",
            badgeAnimation: false,
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                },
                slug: '',
            },
            enums: {
                roleEnum: roleEnum,
                askEnum: askEnum,
            },
        };
    },
    watch: {
        $route(to, from) {
            this.currentRoute = to.path;
        },
        categories: {
            deep: true,
            handler(category) {
                if (category.length > 0) {
                    if (category[0].slug !== "undefined") {
                        this.categoryProps.slug = category[0].slug;
                    }
                }
            }
        }
    },
    computed: {
        logged: function () {
            return this.$store.getters.authStatus;
        },
        profile: function () {
            return this.$store.getters.authInfo;
        },
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        itemCount: function () {
            return this.$store.getters['frontendCart/itemCount'];
        }
    },
    mounted() {
        this.currentRoute = this.$route.path;
        this.loading.isActive = true;
        this.$store.dispatch('frontendItemCategory/lists', this.categoryProps.search).then().catch();
        this.loading.isActive = false;
    },
    methods: {
        checkIsPathAndRoutePathSame(path) {
            if (this.currentRoute === path) {
                return true;
            }
        },
        openCanvas: function (id) {
            return appService.openCanvas(id);
        },
        profileActive: function () {
            return appService.profileOpen('.user-profile-dropdown-box');
        },
        triggerCartAnimation: function (buttonElement) {
            // Trigger badge pop animation
            this.badgeAnimation = true;
            setTimeout(() => {
                this.badgeAnimation = false;
            }, 300);
        },
    }

}
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.cart-icon-pop {
    animation: cartIconPop 0.3s ease-out;
}

@keyframes cartIconPop {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.6);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes badgePop {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.3);
    }
    100% {
        transform: scale(1);
    }
}

.animate-badge-pop {
    animation: badgePop 0.3s ease-out;
}

.badge-pop {
    animation: badgePop 0.3s ease-out;
}
</style>
