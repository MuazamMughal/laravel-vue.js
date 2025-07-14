 <script setup>
import { ref, computed, onMounted, watch } from 'vue'
import dayjs from 'dayjs'
import axios from 'axios'
import ProceedArrow from './svg/ProceedArrow.vue'
import ViewModalV2 from './ViewModalV2.vue';

const props = defineProps({
  openModal: Boolean,
  data: Object,
})

const emit = defineEmits(["close"]);
const closeModal = () => {
  emit("close");
}

const displayMonth = computed(() => selectedDate.value.format('MMMM'));
const displayYear = computed(() => selectedDate.value.format('YYYY'));
const today = dayjs();
const selectedDate = ref(today);
const selectedTime = ref(null);
const timeSlots = ref([]);
const loading = ref(false);
const proceeding = ref(false);
const error = ref('');



// All days of the week (lower-case for easier comparison)
const weekDays = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
// This will hold the days that are NOT returned in `working_days` from the API
const offDays = ref([]);

const daysInMonth = computed(() => {
  const start = selectedDate.value.startOf('month')
  const end = selectedDate.value.endOf('month')
  const days = []

  for (let d = start; d.isBefore(end) || d.isSame(end, 'day'); d = d.add(1, 'day')) {
    days.push({ label: d.date(), date: d })
  }

  return days
})

const isSelectedDate = (date) => selectedDate.value.isSame(date, 'day')
const isToday = (date) => date && today.isSame(date, 'day')
const isPastDay = (date) => date && date.isBefore(today, 'day')
// A date is an off-day if its day name (e.g. "monday") exists in `offDays`
const isOffDay = (date) => {
  if (!date) return false
  return offDays.value.includes(date.format('dddd').toLowerCase())
}

const fetchAvailableSlots = async () => {
  loading.value = true
  timeSlots.value = []
  error.value = ''

  try {
    const res = await axios.get('/calendar/available-slots', {
      params: {
        date: selectedDate.value.format('YYYY-MM-DD'),
        slotDuration: props.data?.meeting_duration || 60,
        user_id: props.data?.owner_id
      }
    })
    timeSlots.value = res.data.available_slots || []

    const workingDays = (res.data.working_days || weekDays);
    offDays.value = weekDays.filter(d => !workingDays.includes(d));
  } catch (err) {
    error.value = err?.response?.data?.error || err.message || 'No Slots Available';
    console.error("Error fetching slots:", err)
  } finally {
    loading.value = false
  }
}

const selectDate = (date) => {
  if (date && !isPastDay(date) && !isOffDay(date)) {
    selectedDate.value = date
    selectedTime.value = null
    fetchAvailableSlots()
  }
}

const goToPreviousMonth = () => {
  const firstOfPrevMonth = selectedDate.value.subtract(1, 'month').startOf('month')
  let probe = firstOfPrevMonth;
  // Find the first date in the new month that is not an off-day and not in the past
  while ((isOffDay(probe) || isPastDay(probe)) && probe.month() === firstOfPrevMonth.month()) {
    probe = probe.add(1, 'day');
  }
  selectedDate.value = probe;
}
const goToNextMonth = () => {
  const firstOfNextMonth = selectedDate.value.add(1, 'month').startOf('month')
  let probe = firstOfNextMonth;
  // Find the first date in the new month that is not an off-day and not in the past
  while ((isOffDay(probe) || isPastDay(probe)) && probe.month() === firstOfNextMonth.month()) {
    probe = probe.add(1, 'day')
  }
  selectedDate.value = probe;
}

const proceedToPayment = () => {
  if (!selectedTime.value) return;
  
  const [hour, minute] = selectedTime.value.start.split(':');
  const [hourNumber] = hour.split(' ');
  
  const isPM = hour.includes('PM');
  const finalHour = isPM ? (parseInt(hourNumber) + 12) % 24 : parseInt(hourNumber);
  
  const formattedDate = selectedDate.value.format('YYYY-MM-DD');
  const formattedTime = `${finalHour.toString().padStart(2, '0')}:${minute.split(' ')[0]}:00`;

  proceeding.value = true;

  emit('proceed', {
    date: formattedDate,
    time: formattedTime
  })
}

onMounted(fetchAvailableSlots)
watch(selectedDate, fetchAvailableSlots)
</script>

<template>
  <ViewModalV2 :fullHeight="true" rounded="md:rounded-[10px]" maxWidth="5xl" class="!bg-white" @close="closeModal" :openModal="openModal" :crossBtn="false">
    <div class="relative p-6">
      <div class="bg-white rounded-lg shadow-md w-full max-w-5xl border border-[#3A189B]">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-[#3A189B] flex items-start justify-between">
          <div>
            <h2 class="text-xl font-semibold text-purple-800">{{ data?.product_name }} (${{ data?.price }}, {{ data?.meeting_duration }} Mins)</h2>
            <p class="text-sm text-gray-500 italic ">
              The calendar is as per EST: New York Time Zone
            </p>
          </div>
          <!-- Close Button Circle -->
           <button
            @click="$emit('close')"
            class="w-7 h-7 mt-2 mr-2 flex items-center justify-center rounded-full border border-[#3A189B] text-[#3A189B] hover:bg-[#3A189B] hover:text-white transition"
            aria-label="Close"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
         <!-- Calendar and Slots -->
        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-[#3A189B]">
          <!-- Calendar -->
          <div class="w-full md:w-2/3 p-6">
            <!-- Month Navigation -->
            <div class="flex items-center justify-between mb-4">
              <div class="ml-4 text-2xl  text-gray-800">
                  <div class="ml- text-2xl font-semibold text-gray-800 flex gap-1 items-baseline">
                  <span class="text-xl">{{ displayMonth }}</span>
                  <span class="text-gray-500  text-xl font-normal">{{ displayYear }}</span>
                  </div>
              </div>
              <div class="flex gap-1">
                <button
                  @click="goToPreviousMonth"
                  class="p-2 bg-gray-100 rounded-full hover:bg-gray-200"
                >
                  <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"></path>
                  </svg>
                </button>
                <button
                  @click="goToNextMonth"
                  class="p-2 bg-gray-100 rounded-full hover:bg-gray-200"
                >
                  <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6"></path>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Weekdays -->
            <div class="grid grid-cols-7 gap-2 text-center text-md font-medium text-gray-500 mb-2">
              <div v-for="day in weekDays" :key="day" class="capitalize">{{ day.slice(0,3) }}</div>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-4">
              <button
                v-for="day in daysInMonth"
                :key="day.date"
                @click="() => !isPastDay(day.date) && !isOffDay(day.date) && selectDate(day.date)"
                :class="[
                  day.label ? 'aspect-square bg-[#F5F6FA] rounded-md text-sm font-medium' : 'cursor-default',
                  isOffDay(day.date) ? 'bg-red-100  text-red-600 cursor-not-allowed' : '',
                  isSelectedDate(day.date) ? 'bg-violet-900 text-white'  : '',
                  (isPastDay(day.date) || isOffDay(day.date))
                    ? 'text-red-400  bg-red-100 cursor-not-allowed opacity-60'
                    : day.label ? 'text-gray-700  hover:border-[#3A189B] hover:border-2 cursor-pointer' : ''
                ]"
                :disabled="isPastDay(day.date) || isOffDay(day.date)"
              >
                {{ day.label }}
              </button>
            </div>
          </div>

          <!-- Time Slots -->
          <div class="w-full md:w-1/3 p-6">
            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
              <!-- Skeleton -->
              <template v-if="loading">
                <div
                  v-for="i in 9"
                  :key="i"
                  class="animate-pulse py-2 bg-gray-200 rounded-md border text-sm"
                >&nbsp;</div>
              </template>
              
              <!-- Available Slots -->
              <template v-else-if="timeSlots?.length > 0">
                <button
                  v-for="slot in timeSlots"
                  :key="slot.start"
                  @click="selectedTime = slot"
                  class="w-full px-4 py-2 text-sm rounded-md font-semibold text-left text-[#3A189B] flex items-center justify-around transition-all border "
                  :class="{
                    'bg-[#3A189B]  hover:text-white text-white': selectedTime?.start === slot.start,
                    'bg-[#F5F6FA] hover:bg-[#3A189B] hover:text-white': selectedTime?.start !== slot.start
                  }"
                >
                  <span class="flex items-center">
                    <span class="w-2 h-2 font-bold bg-green-500 rounded-full mr-2"></span>
                    {{ slot.start }} - {{ slot.end }}
                  </span>
                </button>
              </template>
              <!-- No Slots -->
              <div v-else class="text-sm text-gray-500">
                {{ error || 'No Slots Available' }}
              </div>
            </div>
          </div>
        </div>