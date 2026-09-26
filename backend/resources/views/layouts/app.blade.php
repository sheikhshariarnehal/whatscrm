<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WhatsCRM') }} - WhatsApp Business SaaS</title>

    <!-- Anti-FOUC Theme & Layout Pre-hydration Script (Zero-Popping) -->
    <style>
        [x-cloak] { display: none !important; }
        .preload-no-transition, .preload-no-transition * { transition: none !important; }
        html.side-nav-collapsed .side-nav { width: 80px !important; min-width: 80px !important; }
        html:not(.side-nav-collapsed) .side-nav { width: 280px !important; min-width: 280px !important; }
        html.side-nav-collapsed .nav-text-expanded, html.side-nav-collapsed .logo-expanded, html.side-nav-collapsed .menu-title, html.side-nav-collapsed .nav-toggle-expanded { display: none !important; }
        html.side-nav-collapsed .logo-collapsed, html.side-nav-collapsed .nav-toggle-collapsed { display: flex !important; }
        html.side-nav-collapsed .nav-collapsed-divider { display: block !important; }
        html.side-nav-collapsed .nav-indicator-collapsed { display: block !important; }
        html:not(.side-nav-collapsed) .nav-text-expanded, html:not(.side-nav-collapsed) .logo-expanded, html:not(.side-nav-collapsed) .nav-toggle-expanded { display: flex !important; }
        html:not(.side-nav-collapsed) .menu-title { display: block !important; }
        html:not(.side-nav-collapsed) .logo-collapsed, html:not(.side-nav-collapsed) .nav-toggle-collapsed, html:not(.side-nav-collapsed) .nav-collapsed-divider, html:not(.side-nav-collapsed) .nav-indicator-collapsed { display: none !important; }
        html.side-nav-collapsed .side-nav .menu-item, html.side-nav-collapsed .side-nav .nav-tenant-card { justify-content: center !important; padding: 0 !important; width: 2.75rem !important; height: 2.75rem !important; margin-left: auto !important; margin-right: auto !important; }
    </style>
    <script>
        (function() {
            document.documentElement.classList.add('preload-no-transition');

            // Theme Pre-hydration (Synchronous before first paint)
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // SideNav Collapse Pre-hydration (Synchronous before first paint)
            if (localStorage.getItem('sideNavCollapse') === 'true') {
                document.documentElement.classList.add('side-nav-collapsed');
            } else {
                document.documentElement.classList.remove('side-nav-collapsed');
            }
        })();
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ 
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
              if (this.sideNavCollapse) {
                  document.documentElement.classList.add('side-nav-collapsed');
              } else {
                  document.documentElement.classList.remove('side-nav-collapsed');
              }
          }
      }"
      x-init="
          if (darkMode) { document.documentElement.classList.add('dark'); }
          else { document.documentElement.classList.remove('dark'); }
          if (sideNavCollapse) { document.documentElement.classList.add('side-nav-collapsed'); }
          else { document.documentElement.classList.remove('side-nav-collapsed'); }
          requestAnimationFrame(() => {
              document.documentElement.classList.remove('preload-no-transition');
          });
      "
      class="bg-gray-50/70 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased font-sans flex h-screen overflow-hidden">

    <!-- ========================================== -->
    <!-- 1. MOBILE DRAWER NAVIGATION (For < lg)     -->
    <!-- ========================================== -->
    <div x-show="mobileNavOpen" 
         x-cloak
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
           x-cloak
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
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                <img src="/img/logo/logo-light-full.png" alt="WhatsCRM" class="h-8 max-h-8 dark:hidden">
                <img src="/img/logo/logo-dark-full.png" alt="WhatsCRM" class="h-8 max-h-8 hidden dark:block">
            </a>
            <button @click="mobileNavOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                <x-ph-icon name="x" weight="bold" class="text-lg" />
            </button>
        </div>

        <!-- Mobile Drawer Menu Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <div class="menu-title">Main Menu</div>
            <a href="{{ route('dashboard') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('dashboard') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="dashboard" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Dashboard</span>
            </a>
            <a href="{{ route('inbox') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable justify-between {{ request()->routeIs('inbox') ? 'menu-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <x-nav-icon name="inbox" class="w-5 h-5 flex-shrink-0 text-xl" />
                    <span>Inbox</span>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary text-white">Live</span>
            </a>
            <a href="{{ route('crm') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('crm') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="crm" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>CRM Pipeline</span>
            </a>

            <div class="menu-title pt-3">Marketing & Channels</div>
            <a href="{{ route('contacts') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('contacts') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="contacts" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Contacts</span>
            </a>
            <a href="{{ route('campaigns') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('campaigns') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="campaigns" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Campaigns</span>
            </a>
            <a href="{{ route('automations') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('automations') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="automations" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Automations</span>
            </a>
            <a href="{{ route('devices') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('devices') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="devices" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>WhatsApp API</span>
            </a>

            <div class="menu-title pt-3">Administration</div>
            <a href="{{ route('team') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('team') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="team" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Team & Agents</span>
            </a>
            <a href="{{ route('developer') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('developer') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="developer" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Developer API</span>
            </a>
            <a href="{{ route('settings') }}" wire:navigate @click="mobileNavOpen = false" class="menu-item menu-item-hoverable {{ request()->routeIs('settings') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="settings" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span>Settings</span>
            </a>
            <a href="/admin" target="_blank" class="menu-item menu-item-hoverable text-amber-600 dark:text-amber-400 justify-between">
                <div class="flex items-center gap-3">
                    <x-nav-icon name="admin" class="w-5 h-5 flex-shrink-0 text-xl" />
                    <span>SuperAdmin</span>
                </div>
                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">Admin</span>
            </a>
        </nav>
    </aside>

    <!-- ========================================== -->
    <!-- 2. DESKTOP COLLAPSIBLE SIDENAV             -->
    <!-- ========================================== -->
    <aside class="side-nav side-nav-bg hidden lg:flex flex-col flex-none flex-shrink-0 transition-[width] duration-200 ease-in-out">
        
        <!-- SideNav Header with Template Logos -->
        <div class="h-16 flex items-center border-b border-gray-100 dark:border-gray-800 overflow-hidden"
             :class="sideNavCollapse ? 'justify-center px-0' : 'justify-start px-6'">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center">
                <!-- Expanded Mode Logos -->
                <div class="logo-expanded items-center">
                    <img src="/img/logo/logo-light-full.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain dark:hidden">
                    <img src="/img/logo/logo-dark-full.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain hidden dark:block">
                </div>
                <!-- Collapsed Streamline Mode Logos -->
                <div class="logo-collapsed items-center justify-center">
                    <img src="/img/logo/logo-light-streamline.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain dark:hidden">
                    <img src="/img/logo/logo-dark-streamline.png" alt="WhatsCRM" class="h-9 max-h-9 object-contain hidden dark:block">
                </div>
            </a>
        </div>

        <!-- SideNav Navigation Items -->
        <div class="side-nav-content px-3 py-4 space-y-1">
            <!-- Section 1: Main Menu -->
            <div class="menu-title">Main Menu</div>
            <div class="nav-collapsed-divider my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               wire:navigate
               title="Dashboard"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('dashboard') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="dashboard" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Dashboard</span>
            </a>

            <!-- Unified Live Inbox -->
            <a href="{{ route('inbox') }}" 
               wire:navigate
               title="Live Chat Inbox"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto relative' : 'w-full px-3.5 py-2.5 justify-between'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('inbox') ? 'menu-item-active' : '' }}">
                <div class="flex items-center gap-3 min-w-0" :class="sideNavCollapse ? 'justify-center' : ''">
                    <x-nav-icon name="inbox" class="w-5 h-5 flex-shrink-0 text-xl" />
                    <span class="nav-text-expanded truncate">Inbox</span>
                </div>
                <span class="nav-text-expanded px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary text-white shadow-xs">Live</span>
                <span class="nav-indicator-collapsed absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-primary ring-2 ring-white dark:ring-gray-900" title="Live"></span>
            </a>

            <!-- CRM Pipeline -->
            <a href="{{ route('crm') }}" 
               wire:navigate
               title="CRM Pipeline"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('crm') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="crm" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">CRM Pipeline</span>
            </a>

            <!-- Section 2: Marketing & Channels -->
            <div class="menu-title pt-3">Marketing & Channels</div>
            <div class="nav-collapsed-divider my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Contacts Directory -->
            <a href="{{ route('contacts') }}" 
               wire:navigate
               title="Contacts Directory"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('contacts') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="contacts" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Contacts</span>
            </a>

            <!-- Broadcast Campaigns -->
            <a href="{{ route('campaigns') }}" 
               wire:navigate
               title="Broadcast Campaigns"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('campaigns') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="campaigns" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Campaigns</span>
            </a>

            <!-- Automations & Bots -->
            <a href="{{ route('automations') }}" 
               wire:navigate
               title="Automations & Bots"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('automations') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="automations" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Automations</span>
            </a>

            <!-- WhatsApp Accounts (Devices) -->
            <a href="{{ route('devices') }}" 
               wire:navigate
               title="WhatsApp Cloud API Accounts"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('devices') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="devices" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">WhatsApp API</span>
            </a>

            <!-- Section 3: Administration -->
            <div class="menu-title pt-3">Administration</div>
            <div class="nav-collapsed-divider my-2 border-t border-gray-200/60 dark:border-gray-800"></div>

            <!-- Team & Agents -->
            <a href="{{ route('team') }}" 
               wire:navigate
               title="Team & RBAC"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('team') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="team" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Team & Agents</span>
            </a>

            <!-- Developer API & Webhooks -->
            <a href="{{ route('developer') }}" 
               wire:navigate
               title="Developer Hub & Webhooks"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('developer') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="developer" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Developer API</span>
            </a>

            <!-- Workspace Settings -->
            <a href="{{ route('settings') }}" 
               wire:navigate
               title="Workspace Settings"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'w-full px-3.5 py-2.5'"
               class="menu-item menu-item-hoverable {{ request()->routeIs('settings') ? 'menu-item-active' : '' }}">
                <x-nav-icon name="settings" class="w-5 h-5 flex-shrink-0 text-xl" />
                <span class="nav-text-expanded truncate">Settings</span>
            </a>

            <!-- SuperAdmin Filament Portal -->
            <a href="/admin" target="_blank"
               title="Filament SuperAdmin Portal"
               :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto relative' : 'w-full px-3.5 py-2.5 justify-between'"
               class="menu-item menu-item-hoverable text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30">
                <div class="flex items-center gap-3 min-w-0" :class="sideNavCollapse ? 'justify-center' : ''">
                    <x-nav-icon name="admin" class="w-5 h-5 flex-shrink-0 text-xl" />
                    <span class="nav-text-expanded truncate font-semibold">SuperAdmin</span>
                </div>
                <span class="nav-text-expanded text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">Admin</span>
            </a>
        </div>

        <!-- SideNav Footer / Current Tenant Status -->
        @if (isset($currentWorkspace))
            <div class="p-3 border-t border-gray-100 dark:border-gray-800">
                <div :class="sideNavCollapse ? 'justify-center p-0 w-11 h-11 mx-auto' : 'px-3 py-2.5 justify-between'"
                     class="nav-tenant-card rounded-xl bg-gray-50/80 dark:bg-gray-800/60 flex items-center transition-all"
                     :title="sideNavCollapse ? '{{ $currentWorkspace->name }}' : ''">
                    <div class="nav-text-expanded flex-col min-w-0">
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
                            class="lg:hidden header-action-item text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                            title="Open Navigation Menu">
                        <x-ph-icon name="list" weight="bold" class="text-2xl" />
                    </button>

                    <!-- Desktop SideNav Collapse Toggle (Zero-Popping) -->
                    <button @click="toggleSideNav()" 
                            class="hidden lg:flex header-action-item text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                            title="Toggle Sidebar Collapse">
                        <span class="nav-toggle-expanded">
                            <x-ph-icon name="text-indent" weight="bold" class="text-2xl" />
                        </span>
                        <span class="nav-toggle-collapsed">
                            <x-ph-icon name="list" weight="bold" class="text-2xl" />
                        </span>
                    </button>

                    <!-- Quick Global Search Button (Command Palette) -->
                    <button @click="searchOpen = true" 
                            class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 bg-gray-50/80 dark:bg-gray-800/80 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-medium transition-colors h-9 shadow-2xs">
                        <x-ph-icon name="magnifying-glass" weight="bold" class="text-base text-gray-400 dark:text-gray-500" />
                        <span>Search CRM...</span>
                        <kbd class="hidden md:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold font-mono bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-gray-500 dark:text-gray-300 shadow-2xs">⌘K</kbd>
                    </button>

                </div>

                <!-- Header Action End -->
                <div class="header-action">
                    <!-- Quick Search Mobile Trigger -->
                    <button @click="searchOpen = true" class="sm:hidden header-action-item text-gray-600 dark:text-gray-300" title="Search CRM">
                        <x-ph-icon name="magnifying-glass" weight="bold" class="text-xl" />
                    </button>

                    <!-- Notifications Dropdown Component using x-dropdown -->
                    <x-dropdown placement="bottom-end" width="w-80 sm:w-96" :close-on-click="false">
                        <x-slot:trigger>
                            <div class="header-action-item relative" title="Notifications">
                                <x-badge :dot="true" color="primary">
                                    <x-ph-icon name="bell" weight="duotone" class="text-2xl text-gray-600 dark:text-gray-300" />
                                </x-badge>
                            </div>
                        </x-slot:trigger>

                        <div class="px-3.5 py-2.5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">Notifications</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-primary-subtle text-primary">2 new</span>
                            </div>
                            <span class="text-xs font-semibold text-primary hover:underline flex items-center gap-1 cursor-pointer">
                                <x-ph-icon name="check-circle" weight="duotone" class="text-sm" />
                                <span>Mark all read</span>
                            </span>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800 p-1">
                            <div class="p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-xl transition-colors flex items-start gap-3 cursor-pointer">
                                <x-avatar shape="circle" class="bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 shrink-0">
                                    <x-ph-icon name="whatsapp-logo" weight="fill" class="text-base" />
                                </x-avatar>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">WhatsApp Webhook Sync</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">Cloud API webhook verified and receiving live messaging events.</p>
                                    <span class="text-[10px] text-gray-400">Just now</span>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0 mt-1"></span>
                            </div>
                            <div class="p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-xl transition-colors flex items-start gap-3 cursor-pointer">
                                <x-avatar shape="circle" class="bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shrink-0">
                                    <x-ph-icon name="broadcast" weight="duotone" class="text-base" />
                                </x-avatar>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">Broadcast Completed</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">VIP Customer Campaign batch dispatch finished successfully.</p>
                                    <span class="text-[10px] text-gray-400">2 hours ago</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('inbox') }}" wire:navigate class="w-full py-2 px-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700/80 text-xs font-bold text-gray-800 dark:text-gray-200 text-center block transition-colors">
                                View All Activity
                            </a>
                        </div>
                    </x-dropdown>

                    <!-- Dark / Light Mode Switcher (Pure Tailwind Instant Paint, Zero Pop) -->
                    <button @click="toggleDark()" 
                            class="header-action-item text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                            title="Toggle Light / Dark Mode">
                        <span class="hidden dark:inline-flex">
                            <x-ph-icon name="sun" weight="duotone" class="text-2xl text-amber-400 hover:rotate-45 transition-transform" />
                        </span>
                        <span class="inline-flex dark:hidden">
                            <x-ph-icon name="moon" weight="duotone" class="text-2xl text-gray-600 hover:-rotate-12 transition-transform" />
                        </span>
                    </button>

                    <!-- User Profile Dropdown Component matching Elstar starter -->
                    <x-dropdown placement="bottom-end" width="w-64">
                        <x-slot:trigger>
                            <div class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                <x-avatar :name="auth()->user()->name ?? 'User'" size="sm" shape="circle" class="bg-primary text-white font-bold" />
                                <div class="hidden md:flex flex-col text-left">
                                    <span class="text-xs font-bold text-gray-900 dark:text-gray-100 leading-tight truncate max-w-[120px]">{{ auth()->user()->name ?? 'Account' }}</span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 capitalize leading-tight">{{ auth()->user()->role ?? 'Admin' }}</span>
                                </div>
                                <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 hidden md:block" />
                            </div>
                        </x-slot:trigger>

                        <div class="px-3.5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                            <x-avatar :name="auth()->user()->name ?? 'User'" size="md" shape="circle" class="bg-primary text-white font-bold" />
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-xs text-gray-900 dark:text-gray-100 truncate">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="py-1 space-y-0.5">
                            <x-dropdown-item href="{{ route('profile') }}" wire:navigate icon="user" iconWeight="duotone">
                                Account Profile
                            </x-dropdown-item>
                            <x-dropdown-item href="{{ route('settings') }}" wire:navigate icon="settings" iconWeight="duotone">
                                Workspace Settings
                            </x-dropdown-item>
                            <x-dropdown-item href="{{ route('developer') }}" wire:navigate icon="developer" iconWeight="duotone">
                                API Keys & Webhooks
                            </x-dropdown-item>
                        </div>
                        <x-dropdown-divider />
                        <form id="header-logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-item :danger="true" icon="logout" iconWeight="duotone" onclick="document.getElementById('header-logout-form').submit(); return false;">
                                Sign Out
                            </x-dropdown-item>
                        </form>
                    </x-dropdown>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- 4. MAIN WORKSPACE CONTENT CONTAINER        -->
        <!-- ========================================== -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto overflow-x-hidden relative">
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
                <x-ph-icon name="magnifying-glass" weight="bold" class="text-lg text-gray-400" />
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
                <a href="{{ route('dashboard') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="squares-four" weight="duotone" class="text-lg text-primary shrink-0" />
                    <span>Dashboard & Analytics Overview</span>
                </a>
                <a href="{{ route('inbox') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="chats-circle" weight="duotone" class="text-lg text-emerald-500 shrink-0" />
                    <span>Live 3-Column Chat Inbox</span>
                </a>
                <a href="{{ route('crm') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="kanban" weight="duotone" class="text-lg text-purple-500 shrink-0" />
                    <span>CRM Kanban Deal Stages</span>
                </a>
                <a href="{{ route('contacts') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="users" weight="duotone" class="text-lg text-blue-500 shrink-0" />
                    <span>Contacts Directory & CSV Import</span>
                </a>
                <a href="{{ route('campaigns') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="megaphone-simple" weight="duotone" class="text-lg text-amber-500 shrink-0" />
                    <span>Broadcast Campaigns Dispatcher</span>
                </a>
                <a href="{{ route('automations') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="robot" weight="duotone" class="text-lg text-rose-500 shrink-0" />
                    <span>Automations, Bot Flows & AI</span>
                </a>
                <a href="{{ route('devices') }}" wire:navigate @click="searchOpen = false" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="whatsapp-logo" weight="fill" class="text-lg text-cyan-500 shrink-0" />
                    <span>WhatsApp Cloud API Account Settings</span>
                </a>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
