importScripts("https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js");

// Firebase configuration (replace with your actual Firebase config values)
const firebaseConfig = {
  apiKey: "AIzaSyCO38M7kDoh15hYUaCdkj_SmlN5zEJzkqY", // Replace with your API key
  authDomain: "inlaw-1a86b.firebaseapp.com", // Replace with your auth domain
  projectId: "inlaw-1a86b", // Replace with your project ID
  storageBucket: "inlaw-1a86b.firebasestorage.app", // Replace with your storage bucket
  messagingSenderId: "258998046435", // Replace with your sender ID
  appId: "1:258998046435:web:75d11b3ef48d0ef8907fd6", // Replace with your app ID
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Get Firebase Messaging instance
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function (payload) {
  console.log("Received background message ", payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon,
  };

  // Show notification
  self.registration.showNotification(notificationTitle, notificationOptions);
});
