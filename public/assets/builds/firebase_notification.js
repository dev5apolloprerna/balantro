// app/javascript/firebase_notification.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.1/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.11.1/firebase-messaging.js";
var vapidKeyToken = "BOQWMhaVQAuj7AT0uHqIKd7z_RJugA-lMORCyDC9VCsFkuPq_nN11-n8dKyIKn9r0SstsiF20D7BhbM0YxQpkWo";
var firebaseConfig = {
  apiKey: "AIzaSyAx5_feC-dtrvs_38F-VfrcdzIw7R09rTU",
  authDomain: "my-project-4736d.firebaseapp.com",
  projectId: "my-project-4736d",
  storageBucket: "my-project-4736d.firebasestorage.app",
  messagingSenderId: "1042270493025",
  appId: "1:1042270493025:web:53bad14e8fe4cc73f0cd20",
  measurementId: "G-LSSF6KH138"
};
var firebaseApp = initializeApp(firebaseConfig);
var messaging = getMessaging(firebaseApp);
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/firebase-messaging-sw.js").then(function(registration) {
    console.log("Service Worker registered with scope:", registration.scope);
  }).catch(function(err) {
    console.log("Service Worker registration failed:", err);
  });
}
document.addEventListener("DOMContentLoaded", () => {
  Notification.requestPermission().then((permission) => {
    if (permission === "granted") {
      getToken(messaging, { vapidKey: vapidKeyToken }).then((currentToken) => {
        console.log("currentToken::", currentToken);
        if (currentToken) {
          fetch("/user_devices", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").content
            },
            body: JSON.stringify({
              device_token: currentToken,
              device_type: "web",
              browser_name: navigator.userAgent,
              os_name: navigator.platform
            })
          });
        }
      });
    }
  });
});
onMessage(messaging, (payload) => {
  console.log("Message received:", payload);
  const { title, body } = payload.notification;
  new Notification(title, { body });
});
document.addEventListener("DOMContentLoaded", () => {
  const logoutLink = document.getElementById("custom-logout-button");
  if (logoutLink) {
    logoutLink.addEventListener("click", async (e) => {
      e.preventDefault();
      getToken(messaging, { vapidKey: vapidKeyToken }).then((currentToken) => {
        if (currentToken) {
          fetch("/user_devices", {
            method: "DELETE",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ device_token: currentToken })
          });
        }
      });
      document.getElementById("logout-form").submit();
    });
  }
});
//# sourceMappingURL=/assets/firebase_notification.js.map
