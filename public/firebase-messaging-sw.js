importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyAeiaT-Pr-2o0AWUnLToh73q54kTPQcfak",
    authDomain: "arvicoin-new.firebaseapp.com",
    projectId: "arvicoin-new",
    storageBucket: "arvicoin-new.appspot.com",
    messagingSenderId: 581519256184,
    appId: "1:581519256184:web:375074328c621ba7ae9b8d",
    measurementId: "G-T4F9KFJXBK"
});

const messaging = firebase.messaging();

self.addEventListener('push', event => {
    const { notification } = event.data.json();
    if (notification) {
        return event.waitUntil(
            self.registration.showNotification(notification.title, {
                body: notification.body,
                renotify: true,
                tag: `${Date.now()}-${notification.click_action}`,
                badge: 'arvicoin.com/favicon-32x32.png',
                icon: 'arvicoin.com/favicon-32x32.png',
                vibrate: [200, 100, 200, 100, 200, 100, 200],
            })
        );
    }
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.tag.split('-')[1] ?? 'https://arvicoin.com/admin/order/orders')
    );
});