// import {useToast} from "vue-toastification";
// /*
//  * Position
//  * --------------
//  * top-right
//  * top-center
//  * top-left
//  * bottom-right
//  * bottom-center
//  * bottom-left
//  * */
// export default {
//     default: function (message = "Default", position = "top-right") {
//         const toast = useToast();
//         toast(message, {
//             position: position,
//         });
//     },

//     success: function (message = "Success", position = "top-right") {
//         const toast = useToast();
//         toast.success(message, {
//             position: position,
//         });
//     },

//     info: function (message = "Info", position = "top-right") {
//         const toast = useToast();
//         toast.info(message, {
//             position: position,
//         });
//     },

//     warning: function (message = "Warning", position = "top-right") {
//         const toast = useToast();
//         toast.warning(message, {
//             position: position,
//         });
//     },

//     error: function (message = "Error", position = "top-right") {
//         const toast = useToast();
//         toast.error(message, {
//             position: position,
//         });
//     },

//     successFlip: function (status = null, message = "", position = "top-right") {
//         const toast = useToast();
//         if (status != null) {
//             if (status) {
//                 message = message + " Updated Successfully.";
//             } else {
//                 message = message + " Created Successfully.";
//             }
//         } else {
//             message = message + " Deleted Successfully.";
//         }

//         toast.success(message, {
//             position: position,
//         });
//     },

//     successInfo: function (status = null, message = "", position = "top-right") {
//         const toast = useToast();
//         toast.success(message, {
//             position: position,
//         });
//     },
// };

// import {useToast} from "vue-toastification";

// export default {
//     default: function (message = "Default", position = "top-right") {
//         const toast = useToast();
//         toast(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-default",
//         });
//     },

//     success: function (message = "Success", position = "top-right") {
//         const toast = useToast();
//         toast.success(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-success",
//             icon: false,
            
//         });
//     },

//     info: function (message = "Info", position = "top-right") {
//         const toast = useToast();
//         toast.info(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-info",
//             icon: false,
//         });
//     },

//     warning: function (message = "Warning", position = "top-right") {
//         const toast = useToast();
//         toast.warning(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-warning",
//             icon: false,
//         });
//     },

//     error: function (message = "Error", position = "top-right") {
//         const toast = useToast();
//         toast.error(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-error",
//             icon: false,
//         });
//     },

//     successFlip: function (status = null, message = "", position = "top-right") {
//         const toast = useToast();
//         if (status != null) {
//             if (status) {
//                 message = message + " Updated Successfully.";
//             } else {
//                 message = message + " Created Successfully.";
//             }
//         } else {
//             message = message + " Deleted Successfully.";
//         }

//         toast.success(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-success",
//             icon: false,
//         });
//     },

//     successInfo: function (status = null, message = "", position = "top-right") {
//         const toast = useToast();
//         toast.success(message, {
//             position: position,
//             toastClassName: "custom-toast custom-toast-success",
//             icon: false,
//         });
//     },
// };

import {useToast} from "vue-toastification";

export default {
    default: function (message = "Default", position = "top-right") {
        const toast = useToast();
        toast(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-default",
            timeout: 300000, // 5 minutes for debugging
        });
    },

    success: function (message = "Success", position = "top-right") {
        const toast = useToast();
        toast.success(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-success",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },

    info: function (message = "Info", position = "top-right") {
        const toast = useToast();
        toast.info(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-info",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },

    warning: function (message = "Warning", position = "top-right") {
        const toast = useToast();
        toast.warning(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-warning",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },

    error: function (message = "Error", position = "top-right") {
        const toast = useToast();
        toast.error(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-error",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },

    successFlip: function (status = null, message = "", position = "top-right") {
        const toast = useToast();
        if (status != null) {
            if (status) {
                message = message + " Updated Successfully.";
            } else {
                message = message + " Created Successfully.";
            }
        } else {
            message = message + " Deleted Successfully.";
        }

        toast.success(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-success",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },

    successInfo: function (status = null, message = "", position = "top-right") {
        const toast = useToast();
        toast.success(message, {
            position: position,
            toastClassName: "custom-toast custom-toast-success",
            icon: false,
            timeout: 300000, // 5 minutes for debugging
        });
    },
};