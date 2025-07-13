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