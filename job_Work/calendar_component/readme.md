# 🗓️ Calendar Booking Component (Vue 3 + Inertia)
![Calendar UI](calendar component.jpg)
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



## 📁 Component Breakdown

### `<ViewModalV2 />`
Custom modal component used to display the calendar in a styled overlay.

### `<ProceedArrow />`
A simple SVG arrow used in the Proceed to Payment button.
### Emits

- `close` — Triggered when the user closes the modal.
- `proceed` — Triggered with selected date & time to initiate payment flow.

## 🛠️ Customization

You can adjust:
- Time slot duration via `props.data.meeting_duration`
- Owner/User ID via `props.data.owner_id`
- Price and Product name from `props.data`
## 💡 Example API Format

The backend API should respond with:
```json
{"available_slots": [
    { "start": "10:00 AM", "end": "11:00 AM" },
    { "start": "12:00 PM", "end": "1:00 PM" }
  ],
  "working_days": ["monday", "tuesday", "wednesday", "thursday", "friday"]
}