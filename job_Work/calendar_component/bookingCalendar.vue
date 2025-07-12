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
