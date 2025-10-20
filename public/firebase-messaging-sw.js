importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyCAaSz4c4Z0bCQQyLn2dvLUfINSPt8h82A",
        authDomain: "yummeals-app.firebaseapp.com",
        projectId: "yummeals-app",
        storageBucket: "yummeals-app.firebasestorage.app",
        messagingSenderId: "800217855525",
        appId: "1:800217855525:web:28024bd8904f72216ef1cc",
        measurementId: "G-MN35W1JL3N",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw] background message', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/default/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
