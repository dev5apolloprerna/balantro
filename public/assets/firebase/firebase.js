const firebaseConfig = {
    apiKey: "AIzaSyAWQNCO6w2XEe6Lt_71BbZsTNJrQoDyMlU",
    authDomain: "balantro-demo-142da.firebaseapp.com",
    projectId: "balantro-demo-142da",
    storageBucket: "balantro-demo-142da.firebasestorage.app",
    messagingSenderId: "1053801369930",
    appId: "1:1053801369930:web:1250f2b058695a6114b1b3",
};
//const VAPID_PUBLIC_KEY = "BAtNg7KoAvQUSWEVwoqVuoKMw5fZd7i49LMH0c3E-4jpFluzoQceMjT3Jb0ueHpv3jifLynddsEOR8C-Rl6AuuY";
const VAPID_PUBLIC_KEY =
    "BF_IgPZZYM0S3hf_BHJbAdHDq-aGyISbBcgeVtNW4IL8F5W2ru_XjSHuE5By4HAEIjqDtx3NImL07GjSXn8dGMc";

if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}
const messaging = firebase.messaging();
