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
