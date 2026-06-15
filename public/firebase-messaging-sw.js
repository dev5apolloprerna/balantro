/* global self, importScripts, firebase */
importScripts("https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js");

// Use the SAME web config as in fcm-test.html
// firebase.initializeApp({
//     apiKey: "AIzaSyDIerByo_ZNYT6KCsTchcsirjROygT5gLE",
//     authDomain: "balantro-demo-142da.firebaseapp.com",
//     projectId: "balantro-demo-142da",
//     storageBucket: "balantro-demo-142da.firebasestorage.app",
//     messagingSenderId: "1012298227120",
//     appId: "1:1012298227120:web:e311df362e22e7b8034a75",
//     measurementId: "G-J3NSBE581J"
// });

firebase.initializeApp({
    apiKey: "AIzaSyAWQNCO6w2XEe6Lt_71BbZsTNJrQoDyMlU",
    authDomain: "balantro-demo-142da.firebaseapp.com",
    projectId: "balantro-demo-142da",
    storageBucket: "balantro-demo-142da.firebasestorage.app",
    messagingSenderId: "1053801369930",
    appId: "1:1053801369930:web:1250f2b058695a6114b1b3",
});


const messaging = firebase.messaging();

// Optional: display a notification when a background message arrives
messaging.onBackgroundMessage((payload) => {
    const { title, body, icon, image } = payload.notification || {};
    self.registration.showNotification(title || "Notification", {
        body: body || "",
        icon: icon || "/icon-192.png",
        image,
        data: payload?.data || {},
    });
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();
    const url = event.notification?.data?.url || "/";
    event.waitUntil(clients.openWindow(url));
});
