// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries are not available in the service worker.
importScripts("https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js");
importScripts(
  "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging-compat.js"
);

// Initialize the Firebase app in the service worker by passing in your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
  apiKey: "AIzaSyCO38M7kDoh15hYUaCdkj_SmlN5zEJzkqY", // Replace with your API key
  authDomain: "inlaw-1a86b.firebaseapp.com", // Replace with your auth domain
  databaseURL: "https://inlaw-1a86b.firebaseio.com",
  projectId: "inlaw-1a86b", // Replace with your project ID
  storageBucket: "inlaw-1a86b.firebasestorage.app", // Replace with your storage bucket
  messagingSenderId: "258998046435", // Replace with your sender ID
  appId: "1:258998046435:web:75d11b3ef48d0ef8907fd6", // Replace with your app ID
  measurementId: "G-MYC012N43D",
});

// Retrieve an instance of Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function (payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message:",
    payload
  );
  // Customize your notification here
  const notificationTitle = payload.notification.title;
  const notificationBody = payload.notification.body;
  const notificationIcon = payload.notification.icon;
  // ...
  // Send a notification to the user
  self.registration.showNotification(notificationTitle, {
    body: notificationBody,
    icon: notificationIcon,
    // ... other notification options
  });
});
