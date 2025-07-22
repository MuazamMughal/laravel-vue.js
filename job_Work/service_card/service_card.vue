<template>
 <div
    class="flex flex-col w-[296px] gap-5 p-5 rounded-[25px] border border-[#0000000A] bg-white"
  >
    <!-- Avatar + Name -->
    <div class="flex items-center gap-4 w-full">
      <AvatarImg
        class="h-[52px] w-[52px] min-w-[52px] rounded-[12.75px] object-cover object-center"
        :img="data?.thumbnail ?? '/img/1.jpg'"
        :username="data?.product_name"
      />
      <div
        class="flex flex-col justify-center gap-1 w-full font-semibold text-black overflow-hidden flex-nowrap">
        <div
          class="text-base md:text-lg font-jakarta text-ellipsis text-wrap overflow-hidden w-fit max-w-full" >
          {{ data?.product_name }}
        </div>
       
      </div>
    </div>

    <!-- Description -->
    <div
      v-html="data?.description"
      class="font-jakarta flex-1 line-clamp-3 overflow-hidden text-ellipsis text-xs sm:text-sm md:text-[15px] font-normal leading-[16px] text-[#000000CC]"
    ></div>

    <!-- Debug Type (optional) -->
    <!-- <div class="text-xs italic text-gray-500">Type: {{ cardType }}</div> -->

    <!-- Consultation Card -->
    <template v-if="cardType === 'consultation'">
      <div class="text-green-600 text-sm  font-normal">
        ● Next Available in 3 days
      </div>
      <div class="flex items-center justify-between gap-2">

       
        <div class="flex items-center justify-between text-gray-800 text-xs">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 md:lg:w-7 md:lg:h-7 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 
          2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
            
        
      </div>
    </div>
    
    <div>

    
      <button
  @click="onlineMeetingBooking(expert_id, data.id)"
  class=" flex items-center  gap-1 text-white bg-[#3A189B] text-[12px] rounded-full px-2 py-1.5 text-xs text-center font-[11px] hover:bg-[#3A189B] transition-all duration-300 ease-in-out"
>
  Book Session <span class="flex items-center justify-center w-6 h-6  bg-white text-[#3A189B] rounded-full">→</span>
</button>
</div>

    </div>
    
    </template>

    <!-- Subscription Card -->
    <template v-else-if="cardType === 'subscription'">
      
      <div class="flex items-center justify-between gap-2">
      
      <div class="flex items-center  gap-1 justify-between text-gray-800 text-xs">
        <ExpertBriefcase class="h-6 font-semibold w-6" />
      <div class="flex flex-col  text-gray-800 text-xs">
        <div class="sm:text-[12px] md:lg:text-[15px] font-semibold">
          ${{ formattedPrice }} {{ data?.repeat_payment_every > 1 ? ('every ' + data?.repeat_payment_every) : 'per' }} {{ data?.repeat_payment_every_type }}{{ data?.repeat_payment_every > 1 ? 's' : '' }}
        </div>
     <div class="text-[11px]">
      Cancel at anytime

     </div>
      
        
      </div>
    </div>
      <button
   @click="handleBuyNow(data.id)"
  class=" font-inter flex items-center  gap-1 text-white bg-[#3A189B] rounded-full px-2 py-1.5 text-[12px] text-center  hover:bg-[#3A189B] transition-all duration-300 ease-in-out"
>
  Subscribe <span class="flex items-center justify-center w-6 h-6  bg-white text-[#3A189B] rounded-full">→</span>
</button>
</div>
    </template>
    
    <!-- Digital Good Card -->
    <template v-else-if="cardType === 'digital_good'">
      <div class="flex items-center justify-between gap-2">
       
      <div class="flex items-center justify-center  gap-1  text-gray-800 text-xs">
        <ExpertBriefcase class="h-6 w-6" />
      <div class="flex flex-col  text-gray-800 font-semibold text-[16px]">
      
          ${{ formattedPrice }} 
        
     
      
        
      </div>
    </div>
     <button
        v-if="!data?.logged_in_user_purchased"
        @click="handleBuyNow(data.id)"
        class=" font-inter flex items-center  gap-1 text-white bg-[#3A189B] rounded-full px-2 py-1.5 text-[12px] text-center  hover:bg-[#3A189B] transition-all duration-300 ease-in-out"
>
Purchase <span class="flex items-center justify-center w-6 h-6  bg-white text-[#3A189B] rounded-full">→</span>
</button>
   <button
        v-else
        class=" font-inter flex items-center  gap-1 text-white bg-[#3A189B] rounded-full px-2 py-1.5 text-[12px] text-center  hover:bg-[#3A189B] transition-all duration-300 ease-in-out"
  >
Already Purchased
</button>
      </div>
    </template>
  </div>
<!-- Calendar Modal -->
  <BookingCalendar
    v-if="openCalendarModal"
    :openModal="openCalendarModal"
    @close="openCalendarModal = false"
    :data="data"
    @proceed="handleProceed"
  />
</template>


<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import AvatarImg from '@/Components/AvatarImg.vue'
import LikeExpert from '@/Components/svg/LikeExpert.vue'
import ExpertBriefcase from '@/Components/svg/ExpertBriefcase.vue'
import BookingCalendar from '@/Components/BookingCalendar.vue'
import { ArrowRightCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  data: Object,
  expert_id: String,
})

// 1. Card type logic
const cardType = computed(() => {
  const type = props.data?.payment_type
  const isOnline = props.data?.online_meeting

  if (type === 'subscription') return 'subscription'
  if (type === 'single_payment' && isOnline) return 'consultation'
  if (type === 'single_payment' && !isOnline) return 'digital_good'
  return 'unknown'
})

// 2. Logic variables
const isLoggedIn = computed(() => usePage().props?.auth?.user)
const selectedDate = ref('')
const selectedTime = ref('')
const selectedDateAndTime = computed(() => selectedDate.value + ' ' + selectedTime.value)
const openCalendarModal = ref(false)

const formattedPrice = computed(() => {
  return props.data?.price
    ?.toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    .replace('.00', '')
})