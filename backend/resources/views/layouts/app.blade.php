<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          sideNavCollapse: localStorage.getItem('sideNavCollapse') === 'true',
          mobileNavOpen: false,
          searchOpen: false,
          searchQuery: '',
          toggleDark() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          toggleSideNav() {
              this.sideNavCollapse = !this.sideNavCollapse;
              localStorage.setItem('sideNavCollapse', this.sideNavCollapse);
          }
      }"
      x-init="
          if (darkMode) { document.documentElement.classList.add('dark'); }
          else { document.documentElement.classList.remove('dark'); }
      "
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WhatsCRM') }} - WhatsApp Business SaaS</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50/70 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased font-sans flex h-screen overflow-hidden">

    <!-- ========================================== -->
    <!-- 1. MOBILE DRAWER NAVIGATION (For < lg)     -->
    <!-- ========================================== -->
    <div x-show="mobileNavOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileNavOpen = false" 
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 lg:hidden"
         style="display: none;"></div>

    <aside x-show="mobileNavOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col lg:hidden"
           style="display: none;">
        
        <!-- Mobile Drawer Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-gray-100 dark:border-gray-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img x-show="!darkMode" src="/img/logo/logo-light-full.png" alt="WhatsCRM" class="h-8 max-h-8">
                <img x-show="darkMode" src="/img/logo/logo-dark-full.png" alt="WhatsCRM" class="h-8 max-h-8">
            </a>
            <button @click="mobileNavOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Drawer Menu Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <div class="menu-title">Main Menu</div>
            <a href="{{ route('dashboard') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('dashboard') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('inbox') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable justify-between {{ request()->routeIs('inbox') ? 'menu-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Inbox</span>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary text-white">Live</span>
            </a>
            <a href="{{ route('crm') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('crm') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                <span>CRM Pipeline</span>
            </a>

            <div class="menu-title pt-2">Communication & Marketing</div>
            <a href="{{ route('contacts') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('contacts') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Contacts Directory</span>
            </a>
            <a href="{{ route('campaigns') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('campaigns') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Broadcast Campaigns</span>
            </a>
            <a href="{{ route('automations') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('automations') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Automations & Bots</span>
            </a>
            <a href="{{ route('devices') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('devices') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>WhatsApp Accounts</span>
            </a>

            <div class="menu-title pt-2">Administration</div>
            <a href="{{ route('team') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('team') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Team & Agents</span>
            </a>
            <a href="{{ route('developer') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('developer') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                <span>Developer API & Webhooks</span>
            </a>
            <a href="{{ route('settings') }}" @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('settings') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Workspace Settings</span>
            </a>
            <a href="/admin" target="_blank" class="menu-item menu-item-hoverable text-amber-600 dark:text-amber-400 justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>SuperAdmin</span>
                </div>
                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">Filament</span>
            </a>
        </nav>
    </aside>

    <!-- ========================================== -->
    <!-- 2. DESKTOP COLLAPSIBLE SIDENAV             -->
    <!-- ========================================== -->
    <aside :style="sideNavCollapse ? 'width: 80px;' : 'width: 280px;'"
           class="side-nav side-nav-bg hidden lg:flex flex-col flex-none flex-shrink-0 transition-all duration-200">
        
        <!-- SideNav Header with Template Logos -->
        <div class="h-16 flex items-center border-b border-gray-100 dark:border-gray-800 transition-all duration-200 overflow-hidden"
             :class="sideNavCollapse ? 'justify-center px-2' : 'justify-start px-6'">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <!-- Expanded Mode Logos -->
                <div x-show="!sideNavCollapse" class="flex items-center">
                    <img x-show="!darkMode" src="/img/logo/logo-light-full.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain">
                    <img x-show="darkMode" src="/img/logo/logo-dark-full.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain">
                </div>
                <!-- Collapsed Streamline Mode Logos -->
                <div x-show="sideNavCollapse" class="flex items-center justify-center">
                    <img x-show="!darkMode" src="/img/logo/logo-light-streamline.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain">
                    <img x-show="darkMode" src="/img/logo/logo-dark-streamline.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain">
                </div>
            </a>
        </div>

        <!-- SideNav Navigation Items -->
        <div class="side-nav-content px-3 py-4 space-y-1">
            <!-- Section 1: Main Menu -->
            <div x-show="!sideNavCollapse" class="menu-title">Main Menu</div>
            <div x-show="sideNavCollapse" class="my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               title="Dashboard"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('dashboard') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Dashboard</span>
            </a>

            <!-- Unified Live Inbox -->
            <a href="{{ route('inbox') }}" 
               title="Live Chat Inbox"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5 justify-between'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('inbox') ? 'menu-item-active' : '' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span x-show="!sideNavCollapse" class="truncate">Inbox</span>
                </div>
                <span x-show="!sideNavCollapse" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary text-white shadow-xs">Live</span>
            </a>

            <!-- CRM Pipeline -->
            <a href="{{ route('crm') }}" 
               title="CRM Pipeline"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('crm') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">CRM Pipeline</span>
            </a>

            <!-- Section 2: Communication & Marketing -->
            <div x-show="!sideNavCollapse" class="menu-title pt-3">Marketing & Channels</div>
            <div x-show="sideNavCollapse" class="my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Contacts Directory -->
            <a href="{{ route('contacts') }}" 
               title="Contacts Directory"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('contacts') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Contacts</span>
            </a>

            <!-- Broadcast Campaigns -->
            <a href="{{ route('campaigns') }}" 
               title="Broadcast Campaigns"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('campaigns') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Campaigns</span>
            </a>

            <!-- Automations & Bots -->
            <a href="{{ route('automations') }}" 
               title="Automations & Bots"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('automations') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Automations</span>
            </a>

            <!-- WhatsApp Accounts (Devices) -->
            <a href="{{ route('devices') }}" 
               title="WhatsApp Cloud API Accounts"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('devices') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">WhatsApp API</span>
            </a>

            <!-- Section 3: Administration -->
            <div x-show="!sideNavCollapse" class="menu-title pt-3">Administration</div>
            <div x-show="sideNavCollapse" class="my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Team & Agents -->
            <a href="{{ route('team') }}" 
               title="Team & RBAC"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('team') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Team & Agents</span>
            </a>

            <!-- Developer API & Webhooks -->
            <a href="{{ route('developer') }}" 
               title="Developer Hub & Webhooks"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('developer') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Developer API</span>
            </a>

            <!-- Workspace Settings -->
            <a href="{{ route('settings') }}" 
               title="Workspace Settings"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('settings') ? 'menu-item-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="!sideNavCollapse" class="truncate">Settings</span>
            </a>

            <!-- SuperAdmin Filament Portal -->
            <a href="/admin" target="_blank"
               title="Filament SuperAdmin Portal"
               :class="sideNavCollapse ? 'justify-center px-0' : 'px-3.5 justify-between'"
               class="menu-item menu-item-hoverable text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span x-show="!sideNavCollapse" class="truncate font-semibold">SuperAdmin</span>
                </div>
                <span x-show="!sideNavCollapse" class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">Admin</span>
            </a>
        </div>

        <!-- SideNav Footer / Current Tenant Status -->
        @if (isset($currentWorkspace))
            <div class="p-3 border-t border-gray-100 dark:border-gray-800">
                <div :class="sideNavCollapse ? 'justify-center px-2' : 'px-3'"
                     class="py-2.5 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 flex items-center justify-between transition-all">
                    <div x-show="!sideNavCollapse" class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate">{{ $currentWorkspace->name }}</span>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 truncate">{{ $currentWorkspace->plan?->name ?? 'Standard Plan' }}</span>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20 flex-shrink-0" title="Active Tenant Scope"></span>
                </div>
            </div>
        @endif
    </aside>

    <!-- ========================================== -->
    <!-- 3. MAIN WRAPPER & HEADER TOP BAR           -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Header Top Bar Component -->
        <header class="header shadow-xs">
            <div class="header-wrapper">
                <!-- Header Action Start -->
                <div class="header-action">
                    <!-- Mobile Hamburger Button -->
                    <button @click="mobileNavOpen = true" 
                            class="lg:hidden header-action-item text-gray-600 dark:text-gray-300"
                            title="Open Navigation Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Desktop SideNav Collapse Toggle -->
                    <button @click="toggleSideNav()" 
                            class="hidden lg:flex header-action-item"
                            title="Toggle Sidebar Collapse">
                        <!-- Menu Alt 2 icon -->
                        <svg x-show="!sideNavCollapse" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/></svg>
                        <!-- Menu icon -->
                        <svg x-show="sideNavCollapse" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Quick Global Search Button (Command Palette) -->
                    <button @click="searchOpen = true" 
                            class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700/80 text-gray-500 dark:text-gray-400 text-xs transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Search CRM...</span>
                        <kbd class="hidden md:inline-block px-1.5 py-0.5 text-[10px] font-semibold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-gray-500 dark:text-gray-300 shadow-2xs">Ctrl K</kbd>
                    </button>

                    <!-- Workspace Switcher Dropdown -->
                    <div class="relative" x-data="{ wsDropdownOpen: false }">
                        <button @click="wsDropdownOpen = !wsDropdownOpen" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/80 text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200 transition-colors shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                            <span class="truncate max-w-[130px] sm:max-w-[180px]">{{ $currentWorkspace->name ?? 'Default Workspace' }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="wsDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="wsDropdownOpen" 
                             @click.outside="wsDropdownOpen = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-64 dropdown-menu-panel shadow-2xl"
                             style="display: none;">
                            <div class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Available Workspaces</div>
                            @if (isset($userWorkspaces))
                                @foreach ($userWorkspaces as $ws)
                                    <form method="POST" action="{{ route('workspace.switch', $ws) }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ (isset($currentWorkspace) && $currentWorkspace->id === $ws->id) ? 'text-primary bg-primary-subtle dark:bg-primary-subtle font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="w-2 h-2 rounded-full {{ (isset($currentWorkspace) && $currentWorkspace->id === $ws->id) ? 'bg-primary' : 'bg-gray-400' }}"></span>
                                                <span class="truncate">{{ $ws->name }}</span>
                                            </div>
                                            @if (isset($currentWorkspace) && $currentWorkspace->id === $ws->id)
                                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- WhatsApp Cloud API Live Status Badge -->
                    <div class="hidden xl:flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Cloud API Live</span>
                    </div>
                </div>

                <!-- Header Action End -->
                <div class="header-action">
                    <!-- Quick Search Mobile Trigger -->
                    <button @click="searchOpen = true" class="sm:hidden header-action-item" title="Search">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    <!-- Notifications Dropdown Component -->
                    <div class="relative" x-data="{ notifOpen: false, hasUnread: true }">
                        <button @click="notifOpen = !notifOpen" 
                                class="header-action-item relative"
                                title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span x-show="hasUnread" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-primary ring-2 ring-white dark:ring-gray-900"></span>
                        </button>

                        <!-- Notifications Popover Menu -->
                        <div x-show="notifOpen" 
                             @click.outside="notifOpen = false" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 dropdown-menu-panel shadow-2xl"
                             style="display: none;">
                            <div class="px-3 py-2 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">Live Activity Alerts</span>
                                <button @click="hasUnread = false" class="text-xs font-semibold text-primary hover:underline">Mark all read</button>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800 p-1">
                                <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-xl transition-colors flex items-start gap-3 cursor-pointer">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 font-bold text-xs">WA</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">WhatsApp Webhook Sync</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">Cloud API webhook verified and receiving live messaging events.</p>
                                        <span class="text-[10px] text-gray-400">Just now</span>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span>
                                </div>
                                <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-xl transition-colors flex items-start gap-3 cursor-pointer">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 font-bold text-xs">CRM</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">Broadcast Completed</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">VIP Customer Campaign batch dispatch finished successfully.</p>
                                        <span class="text-[10px] text-gray-400">2 hours ago</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-100 dark:border-gray-800 text-center">
                                <a href="{{ route('inbox') }}" class="text-xs font-semibold text-primary hover:underline block py-1">View Realtime Inbox</a>
                            </div>
                        </div>
                    </div>

                    <!-- Dark / Light Mode Switcher -->
                    <button @click="toggleDark()" 
                            class="header-action-item"
                            title="Toggle Light / Dark Mode">
                        <!-- Sun icon for dark mode -->
                        <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <!-- Moon icon for light mode -->
                        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <!-- User Profile Dropdown Component -->
                    <div class="relative" x-data="{ userProfileOpen: false }">
                        <button @click="userProfileOpen = !userProfileOpen" 
                                class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-primary to-indigo-500 flex items-center justify-center text-white text-xs font-bold shadow-xs">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="hidden md:flex flex-col text-left">
                                <span class="text-xs font-bold text-gray-900 dark:text-gray-100 leading-tight">{{ auth()->user()->name ?? 'Account' }}</span>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 capitalize">{{ auth()->user()->role ?? 'Admin' }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- User Profile Popover -->
                        <div x-show="userProfileOpen" 
                             @click.outside="userProfileOpen = false" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 dropdown-menu-panel shadow-2xl"
                             style="display: none;">
                            <div class="px-3 py-2.5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-primary to-indigo-500 flex items-center justify-center text-white text-sm font-bold shadow-xs flex-shrink-0">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-gray-900 dark:text-gray-100 truncate">{{ auth()->user()->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                                </div>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Account Profile</span>
                                </a>
                                <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Workspace Settings</span>
                                </a>
                                <a href="{{ route('developer') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                    <span>API Keys & Webhooks</span>
                                </a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 dark:border-gray-800 pt-1">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- 4. MAIN WORKSPACE CONTENT CONTAINER        -->
        <!-- ========================================== -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    <!-- ========================================== -->
    <!-- 5. GLOBAL COMMAND PALETTE SEARCH MODAL     -->
    <!-- ========================================== -->
    <div x-show="searchOpen" 
         @keydown.window.cmd.k.prevent="searchOpen = true"
         @keydown.window.ctrl.k.prevent="searchOpen = true"
         @keydown.escape.window="searchOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20"
         style="display: none;">
        <!-- Backdrop -->
        <div x-show="searchOpen" 
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="searchOpen = false" 
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

        <!-- Modal Dialog -->
        <div x-show="searchOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative mx-auto max-w-xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
            
            <!-- Search Header Bar -->
            <div class="flex items-center px-4 border-b border-gray-100 dark:border-gray-800">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" 
                       x-model="searchQuery" 
                       x-ref="searchInput" 
                       x-init="$watch('searchOpen', value => { if (value) setTimeout(() => $refs.searchInput.focus(), 50) })"
                       placeholder="Jump to CRM feature, inbox, contacts, or automations..." 
                       class="w-full bg-transparent border-0 py-4 px-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-0">
                <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-500 rounded border border-gray-200 dark:border-gray-700">ESC</kbd>
            </div>

            <!-- Search Quick Results -->
            <div class="max-h-80 overflow-y-auto p-2 space-y-1">
                <a href="{{ route('dashboard') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard & Analytics Overview</span>
                </a>
                <a href="{{ route('inbox') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Live 3-Column Chat Inbox</span>
                </a>
                <a href="{{ route('crm') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>CRM Kanban Deal Stages</span>
                </a>
                <a href="{{ route('contacts') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Contacts Directory & CSV Import</span>
                </a>
                <a href="{{ route('campaigns') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Broadcast Campaigns Dispatcher</span>
                </a>
                <a href="{{ route('automations') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Automations, Bot Flows & AI</span>
                </a>
                <a href="{{ route('devices') }}" @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>WhatsApp Cloud API Account Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Compatibility Helper -->
    <div class="hidden">
        <livewire:layout.navigation />
    </div>

    @livewireScripts
</body>
</html>
