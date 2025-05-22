import { initializeApp } from "firebase/app";
import { getMessaging, getToken } from "firebase/messaging";

const firebaseConfig = {
  apiKey: "AIzaSyAeiaT-Pr-2o0AWUnLToh73q54kTPQcfak",
  authDomain: "arvicoin-new.firebaseapp.com",
  projectId: "arvicoin-new",
  storageBucket: "arvicoin-new.appspot.com",
  messagingSenderId: 581519256184,
  appId: "1:581519256184:web:375074328c621ba7ae9b8d",
  measurementId: "G-T4F9KFJXBK"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app)

Notification.requestPermission()
  .then(function () {
    console.log('Notification permission granted.');
  })
  .catch(() => {
    console.log('Notification permission is not granted.');
  });

getToken(messaging, { vapidKey: "BETz5deNAV3TxsT4zaUpmAsThDIEoQD5OrKanqIhC_I-D16ybY8UecAF5zrJo25y_s48luDyFRsyw_GkPAUCQsc" })
  .then((token) => {
    if (token) {
      fetch('/admin/fcm/register?token=' + token)
    }
  }).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
  });
