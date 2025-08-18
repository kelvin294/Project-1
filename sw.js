const CACHE_NAME = "oasis-cache-v1";

const urlsToCache = [

"/",

"/index.html",

"/style.css",

"/database.js",

"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"

];

// ✅ Install SW and cache assets

self.addEventListener("install", e => {

e.waitUntil(

caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))

);

console.log("✅ Service Worker Installed");

});

// ✅ Fetch from cache first

self.addEventListener("fetch", e => {

e.respondWith(

caches.match(e.request).then(resp => resp || fetch(e.request))

);

});

// ✅ Activate

self.addEventListener("activate", e => {

console.log("✅ Service Worker Activated");

});

// 📢 Handle Push Notifications (server push)

self.addEventListener("push", function (event) {

let data = {};

if (event.data) {

data = event.data.json();

}

const options = {

body: data.body || "You have a new message from OASIS 🎬",

icon: "/icon-192x192.webp",

badge: "/icon-192x192.webp",

data: { url: data.url || "/" }

};

event.waitUntil(

self.registration.showNotification(data.title || "OASIS Notification", options)

);

});

// 📢 Handle Notification Click

self.addEventListener("notificationclick", function (event) {

event.notification.close();

event.waitUntil(

clients.openWindow(event.notification.data.url)

);

});

// 📢 Handle Messages from page (custom notifications)

self.addEventListener("message", (event) => {

if (event.data && event.data.type === "SHOW_NOTIFICATION") {

const options = {

body: event.data.body,

icon: "/icon-192x192.webp",

badge: "/icon-192x192.webp",

data: { url: "/" }

};

event.waitUntil(

self.registration.showNotification(event.data.title, options)

);

}

});