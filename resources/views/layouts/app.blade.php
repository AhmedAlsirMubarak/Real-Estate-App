<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ثروة') }} — {{ $title ?? (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --admin-bg-1: #f8fafc;
            --admin-bg-2: #eef2ff;
            --admin-bg-3: #e0e7ff;
            --admin-border: #e2e8f0;
            --admin-text: #0f172a;
            --admin-muted: #64748b;
        }
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Sora'" }}, sans-serif;
            color: var(--admin-text);
            background:
                radial-gradient(circle at 5% 10%, var(--admin-bg-3) 0%, transparent 32%),
                radial-gradient(circle at 95% 0%, #dbeafe 0%, transparent 36%),
                linear-gradient(180deg, var(--admin-bg-1) 0%, var(--admin-bg-2) 100%);
        }
        #sidebar { transition: transform 0.3s ease, width 0.3s ease; }
        #overlay { transition: opacity 0.3s ease; }
        .sidebar-link {
            transition: all 0.2s ease;
            border: 1px solid transparent;
            border-radius: 12px;
            margin: 0 8px 4px;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.10) !important;
            border-color: rgba(255,255,255,0.12);
            transform: translateX(-2px);
        }
        [dir="ltr"] .sidebar-link:hover { transform: translateX(2px); }
        .sidebar-link.active {
            background: rgba(255,255,255,0.16) !important;
            border-color: rgba(255,255,255,0.22);
            border-right: 3px solid #f59e0b;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        }
        [dir="rtl"] .sidebar-link.active { border-right: 3px solid #f59e0b; border-left: none; }
        [dir="ltr"] .sidebar-link.active { border-left: 3px solid #f59e0b; border-right: none; }
        [dir="ltr"] .text-right { text-align: left !important; }

        #sidebar {
            background: linear-gradient(190deg, #0f172a 0%, #1e293b 45%, #1e3a8a 100%);
            box-shadow: 0 24px 55px rgba(15, 23, 42, 0.40);
        }
        [dir="rtl"] #sidebar { border-left: 1px solid rgba(255,255,255,0.12); border-right: none; }
        [dir="ltr"] #sidebar { border-right: 1px solid rgba(255,255,255,0.12); border-left: none; }

        /* Sidebar nav scrollbar — thin, theme-matched, fade-in on hover */
        #sidebar nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.0) transparent;
            scrollbar-gutter: stable;
            transition: scrollbar-color 0.3s ease;
            scroll-behavior: smooth;
            overscroll-behavior: contain;
        }
        #sidebar:hover nav,
        #sidebar nav:focus-within {
            scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
        }
        #sidebar nav::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
            margin: 8px 0;
        }
        #sidebar nav::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 999px;
            transition: background-color 0.25s ease;
        }
        #sidebar:hover nav::-webkit-scrollbar-thumb,
        #sidebar nav:focus-within::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.55), rgba(148, 163, 184, 0.45));
        }
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.85), rgba(148, 163, 184, 0.7));
        }

        header {
            background: rgba(255,255,255,0.78) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(148,163,184,0.16);
        }

        main .bg-white.rounded-xl.shadow,
        main .bg-white.rounded-xl.shadow-lg,
        main .bg-white.rounded-2xl.shadow,
        main .bg-white.rounded-2xl.shadow-lg {
            border: 1px solid var(--admin-border);
            box-shadow: 0 10px 28px rgba(15,23,42,0.06);
        }

        main table thead {
            background: #f8fafc !important;
        }

        main table tbody tr:hover {
            background: #f8fbff !important;
        }

        main input,
        main select,
        main textarea {
            border-color: #dbe3ee !important;
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        main input:focus,
        main select:focus,
        main textarea:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
            outline: none;
        }

        main .bg-blue-600, main .hover\:bg-blue-700:hover {
            box-shadow: 0 10px 18px rgba(37,99,235,0.22);
        }
    </style>
</head>
<body class="text-gray-900">
@php
    $isAr = app()->getLocale() === 'ar';
    $tr = fn (string $ar, string $en) => $isAr ? $ar : $en;
    $displayUserName = function ($user) use ($isAr) {
        $name = $user?->name ?? '';

        if ($isAr || $name === '' || ! preg_match('/\p{Arabic}/u', $name)) {
            return $name;
        }

        return match (true) {
            $user->hasRole('manager') => 'Manager',
            $user->hasRole('employee') => 'Employee',
            $user->hasRole('accountant') => 'Accountant',
            $user->hasRole('tenant') => 'Tenant',
            $user->hasRole('owner') => 'Owner',
            $user->hasRole('buyer') => 'Buyer',
            default => 'User',
        };
    };
@endphp

{{-- Mobile Overlay --}}
<div id="overlay" class="fixed inset-0 bg-slate-950/60 backdrop-blur-[2px] z-20 hidden lg:hidden" onclick="closeSidebar()"></div>

<div class="flex min-h-screen" dir="{{ $isAr ? 'rtl' : 'ltr' }}">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="text-white flex-shrink-0 flex flex-col fixed top-0 {{ $isAr ? 'right-0 translate-x-full' : 'left-0 -translate-x-full' }} z-30 h-full w-64
                  lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0"
           style="direction:{{ $isAr ? 'rtl' : 'ltr' }};">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#fff;">
                   <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-4 h-auto">
                </div>
                <div>
                    <span class="text-xl font-black text-yellow-400">{{ $isAr ? 'ثروة' : 'Tharwa' }}</span>
                    <p class="text-xs text-blue-200/80 -mt-1">{{ $tr('للعقارات', 'Real Estate') }}</p>
                </div>
            </a>
            {{-- Close on mobile --}}
            <button onclick="closeSidebar()" class="lg:hidden text-blue-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto">
            @auth
                @php $user = auth()->user(); @endphp

                @if($user->hasRole('manager'))
                    {{-- ===== GENERAL ===== --}}
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-200/70 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('عام', 'General') }}</p>
                    </div>
                    <a href="{{ route('manager.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('لوحة التحكم', 'Dashboard') }}</span>
                    </a>
                    <a href="{{ route('manager.employees.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.employees.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('الموظفين', 'Employees') }}</span>
                    </a>
                    <a href="{{ route('manager.users.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.users.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-4.356-2.957M17 20H7m10 0v-2c0-.653-.084-1.286-.244-1.89M7 20H2v-2a3 3 0 014.356-2.957M7 20v-2c0-.653.084-1.286.244-1.89m0 0a5.002 5.002 0 019.512 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 2v6m3-3h-6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('إدارة المستخدمين', 'User Management') }}</span>
                    </a>
                    <a href="{{ route('manager.salaries.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.salaries.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('الرواتب', 'Salaries') }}</span>
                    </a>
                    <a href="{{ route('manager.contacts.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.contacts.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium flex items-center gap-1.5">
                            {{ $tr('رسائل التواصل', 'Contact Messages') }}
                            @php $unread = \App\Models\ContactMessage::where('is_read', false)->count(); @endphp
                            @if($unread > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </span>
                    </a>
                       <a href="{{ route('manager.customers.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.customers.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('العملاء والطلبات', 'Customers') }}</span>
                    </a>

                    {{-- ===== SECTION 1: OWNERS ASSOCIATION (HOA) ===== --}}
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-yellow-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('١. جمعية الملاك', '1. Owners Association') }}</p>
                    </div>
                    <a href="{{ route('manager.associations.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.associations.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('الجمعيات', 'Associations') }}</span>
                    </a>
                    <a href="{{ route('manager.dues.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.dues.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span class="text-sm font-medium">{{ $tr('الاشتراكات', 'Subscriptions') }}</span>
                    </a>
                    <a href="{{ route('manager.expenses.index') }}?section=hoa"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.expenses.*') && request('section') === 'hoa' ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('منصرفات الجمعية', 'HOA Expenses') }}</span>
                    </a>
                    <a href="{{ route('manager.meetings.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.meetings.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('الاجتماعات', 'Meetings') }}</span>
                    </a>
                    <a href="{{ route('manager.scheduled-reports.index') }}?section=hoa"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.scheduled-reports.*') && request('section') === 'hoa' ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('تقارير الجمعية', 'HOA Reports') }}</span>
                    </a>

                    {{-- ===== SECTION 2: BUILDING MANAGEMENT ===== --}}
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-yellow-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('٢. إدارة المباني', '2. Building Management') }}</p>
                    </div>
                    <a href="{{ route('manager.properties.index') }}?section=management"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.properties.*') && request('section') === 'management' ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-sm font-medium">{{ $tr('المباني والوحدات', 'Buildings & Units') }}</span>
                    </a>
                    <a href="{{ route('manager.tenants.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.tenants.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('المستأجرين', 'Tenants') }}</span>
                    </a>
                 
                    <a href="{{ route('manager.expenses.index') }}?section=management"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.expenses.*') && (request('section') === 'management' || ! request('section')) ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('المصروفات', 'Expenses') }}</span>
                    </a>
                    <a href="{{ route('manager.reports.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.reports.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('تقارير المباني', 'Property Reports') }}</span>
                    </a>
                    <a href="{{ route('manager.scheduled-reports.index') }}?section=management"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.scheduled-reports.*') && request('section') === 'management' ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('تقارير مجدولة', 'Scheduled Reports') }}</span>
                    </a>

                    {{-- ===== SECTION 3: EXTERNAL PROPERTIES ===== --}}
                    <div class="px-3 mt-4 mb-2">
                        <p class="text-xs text-yellow-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('٣. العقارات الخارجية', '3. External Properties') }}</p>
                    </div>
                    <a href="{{ route('manager.properties.index') }}?section=external"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('manager.properties.*') && request('section') === 'external' ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('العقارات الخارجية', 'External Properties') }}</span>
                    </a>
                @endif

                @if($user->hasRole('employee'))
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('موظف', 'Employee') }}</p>
                    </div>
                    <a href="{{ route('employee.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('لوحة التحكم', 'Dashboard') }}</span>
                    </a>
                    <a href="{{ route('employee.maintenance.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('employee.maintenance.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</span>
                    </a>
                    <a href="{{ route('employee.payments.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('employee.payments.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('المدفوعات', 'Payments') }}</span>
                    </a>
                @endif

                @if($user->hasRole('accountant'))
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('محاسب', 'Accountant') }}</p>
                    </div>
                    <a href="{{ route('accountant.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('accountant.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('لوحة التحكم', 'Dashboard') }}</span>
                    </a>
                    <a href="{{ route('accountant.payments.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('accountant.payments.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('المدفوعات', 'Payments') }}</span>
                    </a>
                @endif

                @if($user->hasRole('tenant'))
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('مستأجر', 'Tenant') }}</p>
                    </div>                    <a href="{{ route('tenant.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('لوحة التحكم', 'Dashboard') }}</span>
                    </a>
                    <a href="{{ route('tenant.maintenance.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('tenant.maintenance.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('طلبات الصيانة', 'Maintenance Requests') }}</span>
                    </a>
                    <a href="{{ route('tenant.payments.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('tenant.payments.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('إشعارات الدفع', 'Payment Notifications') }}</span>
                    </a>
                @endif

                @if($user->hasRole('owner'))
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('مالك', 'Owner') }}</p>
                    </div>
                    <a href="{{ route('owner.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('لوحة التحكم', 'Dashboard') }}</span>
                    </a>
                    <a href="{{ route('owner.properties.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('owner.properties.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-sm font-medium">{{ $tr('عقاراتي', 'My Properties') }}</span>
                    </a>
                    <a href="{{ route('owner.dues.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('owner.dues.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-sm font-medium">{{ $tr('مستحقاتي', 'My Dues') }}</span>
                    </a>
                    <a href="{{ route('owner.meetings.index') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('owner.meetings.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium">{{ $tr('اجتماعاتي', 'My Meetings') }}</span>
                    </a>
                @endif

                @if($user->hasRole('buyer'))
                    <div class="px-3 mb-2">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wider px-2 mb-1">{{ $tr('مشترٍ', 'Buyer') }}</p>
                    </div>
                    <a href="{{ route('buyer.dashboard') }}"
                       class="sidebar-link flex items-center gap-3 px-4 py-3 hover:bg-blue-800 {{ request()->routeIs('buyer.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">{{ $tr('عقودي وأقساطي', 'Contracts & Installments') }}</span>
                    </a>
                @endif
            @endauth
        </nav>

        {{-- User info + Logout --}}
        @auth
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center text-blue-900 font-black text-sm flex-shrink-0">
                    {{ mb_substr($displayUserName(auth()->user()), 0, 1) }}
                </div>
                <div class="overflow-hidden flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate text-white">{{ $displayUserName(auth()->user()) }}</p>
                    <p class="text-xs text-blue-200/75 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-xs text-blue-200 hover:text-white w-full transition py-1.5 rounded-lg hover:bg-white/10 px-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ $isAr ? 'تسجيل الخروج' : 'Logout' }}
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Header --}}
        <header class="shadow-sm sticky top-0 z-10">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 gap-3">
                {{-- Hamburger + Title --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="openSidebar()" class="lg:hidden text-slate-600 hover:text-slate-900 p-1.5 rounded-lg hover:bg-slate-100 transition flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-800 truncate">{{ $title ?? ($isAr ? 'لوحة التحكم' : 'Dashboard') }}</h1>
                </div>

                @auth
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    {{-- Greeting (hidden on small screens) --}}
                    <span class="hidden md:block text-sm text-slate-500">{{ $isAr ? 'مرحباً،' : 'Welcome,' }} <strong class="text-slate-700">{{ $displayUserName(auth()->user()) }}</strong></span>

                    <a href="{{ route('locale.switch', $isAr ? 'en' : 'ar') }}"
                       class="text-xs sm:text-sm bg-slate-50 text-slate-700 hover:bg-slate-100 px-2.5 sm:px-3 py-1.5 rounded-lg transition font-semibold border border-slate-200">
                        {{ $isAr ? 'EN' : 'AR' }}
                    </a>

                    {{-- Notifications Bell --}}
                    @php
                        $bellCount = 0;
                        $bellItems = collect();
                        $u = auth()->user();
                        if($u->hasRole('manager')) {
                            $um = \App\Models\ContactMessage::where('is_read', false)->count();
                            $pm = \App\Models\MaintenanceRequest::where('status', 'pending')->count();
                            $bellCount = $um + $pm;
                            if($um > 0) $bellItems->push(['label'=>$isAr ? "رسائل غير مقروءة ({$um})" : "Unread messages ({$um})", 'url'=>route('manager.contacts.index'), 'color'=>'bg-blue-500']);
                            if($pm > 0) $bellItems->push(['label'=>$isAr ? "صيانة معلقة ({$pm})" : "Pending maintenance ({$pm})", 'url'=>route('manager.dashboard'), 'color'=>'bg-yellow-500']);
                        } elseif($u->hasRole('employee')) {
                            $bellCount = \App\Models\MaintenanceRequest::whereHas('unit.property', fn($q) => $q->where('employee_id', $u->id))->where('status','pending')->count();
                            if($bellCount > 0) $bellItems->push(['label'=>$isAr ? "صيانة معلقة ({$bellCount})" : "Pending maintenance ({$bellCount})", 'url'=>route('employee.maintenance.index'), 'color'=>'bg-yellow-500']);
                        } elseif($u->hasRole('tenant')) {
                            $tenant = $u->tenant;
                            if($tenant) {
                                $bellCount = \App\Models\Payment::where('tenant_id',$tenant->id)->where('status','overdue')->count();
                                if($bellCount > 0) $bellItems->push(['label'=>$isAr ? "دفعات متأخرة ({$bellCount})" : "Overdue payments ({$bellCount})", 'url'=>route('tenant.payments.index'), 'color'=>'bg-red-500']);
                            }
                        }
                    @endphp

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                                class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($bellCount > 0)
                            <span class="absolute -top-0.5 {{ $isAr ? '-left-0.5' : '-right-0.5' }} bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold leading-none">
                                {{ $bellCount > 9 ? '9+' : $bellCount }}
                            </span>
                            @endif
                        </button>

                        <div x-show="open" x-transition
                             class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-100 z-50 py-2">
                            <p class="px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $isAr ? 'الإشعارات' : 'Notifications' }}</p>
                            @forelse($bellItems as $item)
                            <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition {{ $isAr ? 'flex-row-reverse text-right' : '' }}">
                                <span class="w-2 h-2 rounded-full {{ $item['color'] }} flex-shrink-0"></span>
                                <span class="text-sm text-slate-700">{{ $item['label'] }}</span>
                            </a>
                            @empty
                            <p class="px-4 py-4 text-sm text-slate-400 text-center">{{ $isAr ? 'لا توجد إشعارات جديدة' : 'No new notifications' }}</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm bg-red-50/90 text-red-600 hover:bg-red-100 px-2.5 sm:px-3 py-1.5 rounded-lg transition font-medium border border-red-100">
                            <span class="hidden sm:inline">{{ $isAr ? 'تسجيل الخروج' : 'Logout' }}</span>
                            <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-4 sm:px-6 pt-4">
            @php
                $successMessage = session('success');
                $errorMessage = session('error');
                if (! $isAr && is_string($successMessage) && preg_match('/\p{Arabic}/u', $successMessage)) {
                    $successMessage = 'Action completed successfully.';
                }
                if (! $isAr && is_string($errorMessage) && preg_match('/\p{Arabic}/u', $errorMessage)) {
                    $errorMessage = 'Something went wrong. Please try again.';
                }
            @endphp
            @if($successMessage)
            <div class="bg-green-50/95 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-4 flex items-start gap-2 text-sm shadow-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $successMessage }}
            </div>
            @endif
            @if($errorMessage)
            <div class="bg-red-50/95 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 flex items-start gap-2 text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $errorMessage }}
            </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 px-4 sm:px-6 pb-8">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const hiddenClass = document.documentElement.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full';
    sidebar.classList.remove(hiddenClass);
    sidebar.classList.add('translate-x-0');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const hiddenClass = document.documentElement.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full';
    sidebar.classList.add(hiddenClass);
    sidebar.classList.remove('translate-x-0');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}
// Close sidebar on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.getElementById('overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }
});
</script>
@stack('scripts')
</body>
</html>
