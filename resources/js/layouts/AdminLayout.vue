<template>
  <div class="flex h-screen bg-slate-50 font-['Source_Sans_Pro'] text-slate-800 antialiased overflow-hidden">
    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-[#15161a] text-slate-300 transition-all duration-300 ease-in-out shrink-0 border-r border-white/5 shadow-2xl z-20 relative',
        isSidebarOpen ? 'w-72' : 'w-20'
      ]"
    >
      <!-- Sidebar Header -->
      <div class="flex items-center justify-between h-20 px-5 shrink-0 relative">
        <h1
          v-if="isSidebarOpen"
          class="font-['Montserrat'] font-extrabold text-xl text-white tracking-wide truncate transition-opacity duration-300 ease-in-out ml-2"
        >
          ADMIN<span class="text-indigo-500">PANEL</span>
        </h1>
        
        <button
          @click="toggleSidebar"
          class="p-2 ml-auto rounded-xl hover:bg-white/5 text-slate-400 hover:text-white transition-all duration-200 focus:outline-none"
          title="Toggle Sidebar"
        >
          <!-- Menu Icon -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path v-if="isSidebarOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto py-6 space-y-3 no-scrollbar px-3">
        <!-- Dashboard -->
        <a
          href="#"
          class="group flex items-center px-3 py-3.5 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-200"
          :class="{'justify-center': !isSidebarOpen}" 
        >
          <div class="p-2 rounded-lg bg-white/5 group-hover:bg-indigo-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <span
            v-if="isSidebarOpen"
            class="ml-4 font-semibold tracking-wide truncate"
          >
            Dashboard
          </span>
        </a>

        <!-- Manajemen Konten -->
        <div class="space-y-1">
          <button
            @click="toggleContentMenu"
            class="group w-full flex items-center px-3 py-3.5 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-200 focus:outline-none"
            :class="{'justify-center': !isSidebarOpen, 'bg-white/10 text-white': isContentMenuOpen && isSidebarOpen}"
          >
            <div class="p-2 rounded-lg bg-white/5 group-hover:bg-indigo-600 transition-colors" :class="{'bg-indigo-600 text-white': isContentMenuOpen && isSidebarOpen}">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <span
              v-if="isSidebarOpen"
              class="ml-4 font-semibold tracking-wide flex-1 text-left truncate"
            >
              Manajemen Konten
            </span>
            <svg
              v-if="isSidebarOpen"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 shrink-0 transition-transform duration-300 ease-in-out text-slate-500 group-hover:text-white"
              :class="isContentMenuOpen ? 'rotate-180 text-white' : 'rotate-0'"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Submenu -->
          <div
            v-show="isSidebarOpen"
            class="grid transition-[grid-template-rows] duration-300 ease-in-out overflow-hidden"
            :style="{ gridTemplateRows: isContentMenuOpen ? '1fr' : '0fr' }"
          >
            <div class="overflow-hidden">
              <div class="pl-14 pr-2 py-2 mt-1 space-y-1">
                <a href="#" class="group relative flex items-center px-4 py-2.5 text-sm font-semibold text-slate-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200 before:absolute before:-left-6 before:top-0 first:before:-top-2 before:-bottom-1 before:w-px before:bg-white/10 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:bg-transparent last:before:border-l last:before:border-b last:before:border-white/10 group-hover:last:before:border-indigo-500 last:before:rounded-bl-xl before:transition-colors after:absolute after:-left-6 after:top-1/2 after:w-4 after:h-px after:bg-white/10 group-hover:after:bg-indigo-500 after:transition-colors last:after:hidden">
                  Carousel Home
                </a>
                <a href="#" class="group relative flex items-center px-4 py-2.5 text-sm font-semibold text-slate-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200 before:absolute before:-left-6 before:top-0 first:before:-top-2 before:-bottom-1 before:w-px before:bg-white/10 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:bg-transparent last:before:border-l last:before:border-b last:before:border-white/10 group-hover:last:before:border-indigo-500 last:before:rounded-bl-xl before:transition-colors after:absolute after:-left-6 after:top-1/2 after:w-4 after:h-px after:bg-white/10 group-hover:after:bg-indigo-500 after:transition-colors last:after:hidden">
                  Pre-order Home
                </a>
                <a href="#" class="group relative flex items-center px-4 py-2.5 text-sm font-semibold text-slate-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200 before:absolute before:-left-6 before:top-0 first:before:-top-2 before:-bottom-1 before:w-px before:bg-white/10 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:bg-transparent last:before:border-l last:before:border-b last:before:border-white/10 group-hover:last:before:border-indigo-500 last:before:rounded-bl-xl before:transition-colors after:absolute after:-left-6 after:top-1/2 after:w-4 after:h-px after:bg-white/10 group-hover:after:bg-indigo-500 after:transition-colors last:after:hidden">
                  Tiktok Feed
                </a>
                <a href="#" class="group relative flex items-center px-4 py-2.5 text-sm font-semibold text-slate-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200 before:absolute before:-left-6 before:top-0 first:before:-top-2 before:-bottom-1 before:w-px before:bg-white/10 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:bg-transparent last:before:border-l last:before:border-b last:before:border-white/10 group-hover:last:before:border-indigo-500 last:before:rounded-bl-xl before:transition-colors after:absolute after:-left-6 after:top-1/2 after:w-4 after:h-px after:bg-white/10 group-hover:after:bg-indigo-500 after:transition-colors last:after:hidden">
                  Galeri Karya
                </a>
                <a href="#" class="group relative flex items-center px-4 py-2.5 text-sm font-semibold text-slate-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200 before:absolute before:-left-6 before:top-0 first:before:-top-2 before:-bottom-1 before:w-px before:bg-white/10 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:bg-transparent last:before:border-l last:before:border-b last:before:border-white/10 group-hover:last:before:border-indigo-500 last:before:rounded-bl-xl before:transition-colors after:absolute after:-left-6 after:top-1/2 after:w-4 after:h-px after:bg-white/10 group-hover:after:bg-indigo-500 after:transition-colors last:after:hidden">
                  WhatsApp Admin
                </a>
              </div>
            </div>
          </div>
        </div>
      </nav>
      
      <!-- User Profile -->
      <div class="p-4 border-t border-white/5 shrink-0 bg-[#15161a]">
         <div class="flex items-center" :class="{'justify-center': !isSidebarOpen}">
            <div class="w-11 h-11 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shrink-0">
               <span class="text-white font-extrabold font-['Montserrat'] text-lg">A</span>
            </div>
            <div v-if="isSidebarOpen" class="ml-4 truncate">
               <p class="text-sm font-bold text-white truncate">Administrator</p>
               <p class="text-xs font-semibold text-slate-500 truncate mt-0.5">admin@bogorsneakers.com</p>
            </div>
         </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-slate-50 overflow-hidden relative">
      <!-- Header -->
      <header class="h-20 flex items-center px-8 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shrink-0 sticky top-0 z-10">
        <div class="flex flex-col">
          <h2 class="font-['Montserrat'] font-extrabold text-2xl text-slate-800 tracking-tight">
            <slot name="header">Dashboard</slot>
          </h2>
          <!-- Optional Subtitle -->
          <p class="text-sm font-bold text-slate-500 mt-0.5 hidden sm:block">Overview & Statistik Sistem</p>
        </div>
        
        <div class="ml-auto flex items-center space-x-6">
          <!-- Notification Button -->
          <button class="relative p-2.5 text-slate-400 hover:text-indigo-600 transition-colors rounded-xl hover:bg-indigo-50 focus:outline-none">
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </button>
        </div>
      </header>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-auto p-8">
        <div class="max-w-7xl mx-auto">
          <slot>
            <!-- Default Content Placeholder -->
            <div class="p-12 bg-white border border-slate-200/80 rounded-2xl shadow-sm">
              <div class="flex flex-col items-center justify-center py-10 text-center">
                 <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mb-5 text-slate-400 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                 </div>
                 <h3 class="font-['Montserrat'] font-extrabold text-xl text-slate-800">Tidak Ada Konten</h3>
                 <p class="text-slate-500 font-semibold mt-2">Konten utama akan tampil di area ini. Gunakan <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded-md text-sm mx-1">&lt;slot&gt;</code> untuk menyisipkan.</p>
              </div>
            </div>
          </slot>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isSidebarOpen = ref(true)
const isContentMenuOpen = ref(true)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
  if (!isSidebarOpen.value) {
    isContentMenuOpen.value = false
  }
}

const toggleContentMenu = () => {
  if (isSidebarOpen.value) {
    isContentMenuOpen.value = !isContentMenuOpen.value
  } else {
    isSidebarOpen.value = true
    isContentMenuOpen.value = true
  }
}
</script>

<style scoped>
/* Custom utilitas jika scrollbar ingin dihilangkan pada sidebar */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
