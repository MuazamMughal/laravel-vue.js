# 🗓️ Calendar Booking Component (Vue 3 + Inertia)

A fully interactive calendar component built using **Vue 3 Composition API**, integrated with a backend to fetch and display available time slots for bookings. Ideal for appointment scheduling or productized services with Stripe-based payment.

## 🔧 Features

- 📅 **Monthly calendar view** with previous/next navigation
- ❌ **Disables past dates and off-days** based on backend working days
- ⏱️ **Live time slot fetching** from the API for selected dates
- 💳 **Proceed to Stripe payment** after selecting a slot
- 🌍 **Timezone note shown** (EST – New York Time)
- 🧾 **Skeleton loading states** for better UX
- ✅ **Highlighting for selected date and slot**
- 🔁 **Auto-refresh of slots** on date change
- 🎨 Clean and responsive UI with **TailwindCSS**

## 🧩 Dependencies

- [`vue`](https://vuejs.org/) (Composition API)
- [`axios`](https://axios-http.com/) – for HTTP requests
- [`dayjs`](https://day.js.org/) – for date handling
- [`TailwindCSS`](https://tailwindcss.com/) – for utility-first styling
- [`Inertia.js`](https://inertiajs.com/) – for Laravel-Vue integration