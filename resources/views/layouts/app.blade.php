<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Diş Kliniği Yönetim Sistemi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 safe-top">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-14 md:h-16 flex items-center justify-between gap-2 sm:gap-4">
            <a href="{{ route('patients.index') }}" class="flex items-center gap-2 sm:gap-2.5 shrink-0 min-w-0">
                <span class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold shadow-sm shrink-0">
                    DK
                </span>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-slate-900 leading-tight truncate">Diş Kliniği</div>
                    <div class="hidden sm:block md:hidden lg:block text-[11px] text-slate-500">Hasta &amp; Odontogram Takip</div>
                </div>
            </a>

            <!-- Masaüstü Anlık AJAX Hasta Arama -->
            <div class="hidden md:block relative flex-1 max-w-xl lg:max-w-2xl min-w-0">
                <div class="relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        id="global-patient-search"
                        type="text"
                        autocomplete="off"
                        placeholder="Hasta ara: Ad Soyad, Telefon veya TC Kimlik No..."
                        class="w-full pl-11 pr-4 py-2 text-sm bg-slate-100 border border-slate-200/80 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition"
                    >
                </div>
                <div
                    id="global-search-results"
                    class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50 divide-y divide-slate-100 max-h-80 overflow-y-auto"
                ></div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Masaüstü Navigasyon -->
                <nav class="hidden md:flex items-center gap-0.5 lg:gap-1.5">
                    <a href="{{ route('patients.index') }}" class="px-2 lg:px-3 py-2 text-xs lg:text-sm font-medium rounded-lg transition whitespace-nowrap {{ request()->routeIs('patients.index') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Kontrol Paneli
                    </a>
                    <a href="{{ route('appointments.index') }}" class="px-2 lg:px-3 py-2 text-xs lg:text-sm font-medium rounded-lg transition whitespace-nowrap {{ request()->routeIs('appointments.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Randevu Takvimi
                    </a>
                    <a href="{{ route('finance.index') }}" class="px-2 lg:px-3 py-2 text-xs lg:text-sm font-medium rounded-lg transition whitespace-nowrap {{ request()->routeIs('finance.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Finans &amp; Muhasebe
                    </a>
                </nav>

                <!-- Mobil Arama Açma Butonu -->
                <button
                    type="button"
                    id="mobile-search-toggle"
                    aria-label="Hasta Ara"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 active:bg-slate-100 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button>

                <!-- Saati Geçen / Gelmeyen Randevu Bildirim Zili -->
                <div class="relative">
                    <button
                        type="button"
                        id="overdue-bell-btn"
                        title="Saati Geçen Randevu Bildirimleri"
                        aria-label="Bildirimler"
                        class="relative inline-flex items-center justify-center w-10 h-10 md:w-9 md:h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span
                            id="overdue-badge"
                            class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center shadow-sm"
                        >0</span>
                    </button>

                    <div
                        id="overdue-dropdown"
                        class="hidden fixed sm:absolute left-3 right-3 sm:left-auto sm:right-0 top-16 sm:top-auto sm:mt-2 sm:w-96 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-50"
                    >
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Saati Geçen Randevular</span>
                            <span id="overdue-dropdown-count" class="text-[11px] font-semibold text-rose-600">0 bildirim</span>
                        </div>
                        <div id="overdue-dropdown-list" class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            <div class="px-4 py-6 text-center text-xs text-slate-400">Saati geçen bekleyen randevu yok.</div>
                        </div>
                    </div>
                </div>

                <!-- Mobil Hamburger Menü Butonu -->
                <button
                    type="button"
                    id="mobile-menu-btn"
                    aria-label="Menüyü Aç"
                    aria-expanded="false"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-700 active:bg-slate-100 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobil Açılır Arama Çubuğu -->
        <div id="mobile-search-panel" class="hidden md:hidden border-t border-slate-100 bg-white px-3 py-2.5">
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    id="mobile-patient-search"
                    type="text"
                    autocomplete="off"
                    placeholder="Hasta ara: Ad Soyad, Telefon, TC..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl focus:bg-white focus:border-sky-500 outline-none"
                >
                <div
                    id="mobile-search-results"
                    class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50 divide-y divide-slate-100 max-h-72 overflow-y-auto"
                ></div>
            </div>
        </div>
    </header>

    <!-- Mobil Hamburger Drawer (Sağdan Kayar) -->
    <div id="mobile-drawer-overlay" class="fixed inset-0 z-50 bg-slate-900/45 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>
    <aside
        id="mobile-drawer"
        aria-hidden="true"
        class="fixed top-0 right-0 bottom-0 z-50 w-72 max-w-[85vw] bg-white shadow-2xl border-l border-slate-200 translate-x-full transition-transform duration-300 ease-out flex flex-col md:hidden safe-top safe-bottom"
    >
        <div class="px-5 h-14 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-600 text-white text-xs font-bold flex items-center justify-center">DK</span>
                <span class="text-sm font-bold text-slate-900">Klinik Menüsü</span>
            </div>
            <button
                type="button"
                id="mobile-drawer-close"
                aria-label="Menüyü Kapat"
                class="w-9 h-9 rounded-lg text-slate-500 hover:bg-slate-100 flex items-center justify-center text-2xl leading-none"
            >&times;</button>
        </div>

        <nav class="p-4 space-y-1.5 flex-1 overflow-y-auto">
            <a href="{{ route('patients.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('patients.index') ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
                <span>Kontrol Paneli</span>
            </a>

            <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('appointments.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span>Randevu Takvimi</span>
            </a>

            <a href="{{ route('finance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('finance.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                </svg>
                <span>Finans &amp; Muhasebe</span>
            </a>

            <a href="{{ route('finance.index') }}#fiyat-listesi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                </svg>
                <span>Klinik Fiyat Listesi</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-100 text-[11px] text-slate-400">
            Diş Kliniği Hasta, Odontogram &amp; Cari Takip
        </div>
    </aside>

    <main class="flex-1 w-full max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-8">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobil Sabit Alt Navigasyon -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 safe-bottom shadow-[0_-4px_20px_-6px_rgba(15,23,42,0.08)]">
        <div class="grid grid-cols-3 h-16">
            <a
                href="{{ route('patients.index') }}"
                class="flex flex-col items-center justify-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('patients.index') ? 'text-sky-600' : 'text-slate-500 active:text-slate-900' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
                <span>Panel</span>
            </a>

            <a
                href="{{ route('appointments.index') }}"
                class="flex flex-col items-center justify-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('appointments.*') ? 'text-sky-600' : 'text-slate-500 active:text-slate-900' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span>Takvim</span>
            </a>

            <a
                href="{{ route('finance.index') }}"
                class="flex flex-col items-center justify-center gap-1 text-[11px] font-semibold transition {{ request()->routeIs('finance.*') ? 'text-emerald-600' : 'text-slate-500 active:text-slate-900' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                </svg>
                <span>Finans</span>
            </a>
        </div>
    </nav>

    <script>
        (() => {
            const searchUrl = @json(route('patients.search'));
            const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[m]));

            const bindPatientSearch = (input, resultsBox) => {
                if (!input || !resultsBox) return;
                let debounceTimer = null;

                const renderEmpty = (text) => {
                    resultsBox.innerHTML = `<div class="px-4 py-3 text-sm text-slate-500">${text}</div>`;
                    resultsBox.classList.remove('hidden');
                };

                input.addEventListener('input', () => {
                    const q = input.value.trim();
                    clearTimeout(debounceTimer);

                    if (q.length < 2) {
                        resultsBox.classList.add('hidden');
                        resultsBox.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(async () => {
                        try {
                            const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                                headers: { 'Accept': 'application/json' }
                            });
                            const payload = await res.json();
                            const items = payload.data || [];

                            if (!items.length) {
                                renderEmpty('Eşleşen hasta bulunamadı.');
                                return;
                            }

                            resultsBox.innerHTML = items.map((p) => `
                                <a href="${p.url}" class="flex items-center justify-between px-4 py-3 hover:bg-sky-50 transition">
                                    <div class="min-w-0 pr-2">
                                        <div class="text-sm font-semibold text-slate-900 truncate">${escapeHtml(p.full_name)}</div>
                                        <div class="text-xs text-slate-500 truncate">
                                            Tel: ${escapeHtml(p.phone)}
                                            ${p.tc_no ? `• TC: ${escapeHtml(p.tc_no)}` : ''}
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-sky-600 shrink-0">Profil →</span>
                                </a>
                            `).join('');
                            resultsBox.classList.remove('hidden');
                        } catch (err) {
                            renderEmpty('Arama sırasında bir hata oluştu.');
                        }
                    }, 220);
                });

                document.addEventListener('click', (e) => {
                    if (!resultsBox.contains(e.target) && e.target !== input) {
                        resultsBox.classList.add('hidden');
                    }
                });
            };

            bindPatientSearch(
                document.getElementById('global-patient-search'),
                document.getElementById('global-search-results')
            );
            bindPatientSearch(
                document.getElementById('mobile-patient-search'),
                document.getElementById('mobile-search-results')
            );

            // Mobil Arama Paneli Aç/Kapa
            const mobileSearchToggle = document.getElementById('mobile-search-toggle');
            const mobileSearchPanel = document.getElementById('mobile-search-panel');
            const mobileSearchInput = document.getElementById('mobile-patient-search');
            mobileSearchToggle?.addEventListener('click', () => {
                const opening = mobileSearchPanel.classList.contains('hidden');
                mobileSearchPanel.classList.toggle('hidden');
                if (opening) {
                    setTimeout(() => mobileSearchInput?.focus(), 60);
                }
            });

            // Mobil Hamburger Drawer Animasyon Kontrolü
            const menuBtn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const overlay = document.getElementById('mobile-drawer-overlay');
            const closeBtn = document.getElementById('mobile-drawer-close');

            const openDrawer = () => {
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                drawer.setAttribute('aria-hidden', 'false');
                menuBtn?.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            };

            const closeDrawer = () => {
                drawer.classList.add('translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                drawer.setAttribute('aria-hidden', 'true');
                menuBtn?.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            };

            menuBtn?.addEventListener('click', openDrawer);
            closeBtn?.addEventListener('click', closeDrawer);
            overlay?.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeDrawer();
            });
        })();
    </script>

    <!-- Site İçi Canlı Gecikmiş Randevu Bildirim Kartı -->
    <div id="overdue-live-alert" class="hidden fixed bottom-20 md:bottom-5 left-3 right-3 sm:left-5 sm:right-auto z-40 sm:max-w-sm bg-white border-l-4 border-rose-500 rounded-2xl shadow-2xl ring-1 ring-slate-200 p-4 space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </span>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-rose-600">Randevu Saati Geçti (Gelmedi)</div>
                    <div id="overdue-alert-title" class="text-sm font-bold text-slate-900 mt-0.5"></div>
                    <div id="overdue-alert-sub" class="text-xs text-slate-500 mt-0.5"></div>
                </div>
            </div>
            <button type="button" id="overdue-alert-dismiss" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>
        <div class="flex items-center gap-2 pt-1">
            <button type="button" id="overdue-alert-noshow" class="flex-1 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg transition">
                Gelmedi İşaretle
            </button>
            <button type="button" id="overdue-alert-arrived" class="flex-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition">
                Geldi
            </button>
            <a href="{{ route('appointments.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                Takvim
            </a>
        </div>
    </div>

    <script>
        (() => {
            const bellBtn = document.getElementById('overdue-bell-btn');
            const badgeEl = document.getElementById('overdue-badge');
            const dropdown = document.getElementById('overdue-dropdown');
            const dropdownCount = document.getElementById('overdue-dropdown-count');
            const dropdownList = document.getElementById('overdue-dropdown-list');

            const liveAlert = document.getElementById('overdue-live-alert');
            const alertTitle = document.getElementById('overdue-alert-title');
            const alertSub = document.getElementById('overdue-alert-sub');
            const alertDismissBtn = document.getElementById('overdue-alert-dismiss');
            const alertNoShowBtn = document.getElementById('overdue-alert-noshow');
            const alertArrivedBtn = document.getElementById('overdue-alert-arrived');

            const overdueUrl = @json(route('appointments.overdue'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let activeAlertItem = null;
            const dismissedKey = 'clinic_dismissed_overdue_ids';
            const getDismissed = () => {
                try { return JSON.parse(sessionStorage.getItem(dismissedKey) || '[]'); } catch { return []; }
            };
            const addDismissed = (id) => {
                const list = getDismissed();
                if (!list.includes(id)) {
                    list.push(id);
                    sessionStorage.setItem(dismissedKey, JSON.stringify(list));
                }
            };

            const escapeHtmlSafe = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[m]));

            const updateAppointmentStatusQuick = async (statusUrl, status) => {
                const res = await fetch(statusUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status }),
                });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
                    await fetchOverdueAppointments();
                }
            };

            const renderOverdue = (items) => {
                const count = items.length;
                badgeEl.textContent = count;
                badgeEl.classList.toggle('hidden', count === 0);
                dropdownCount.textContent = `${count} bildirim`;

                if (!count) {
                    dropdownList.innerHTML = '<div class="px-4 py-6 text-center text-xs text-slate-400">Saati geçen bekleyen randevu yok.</div>';
                    liveAlert.classList.add('hidden');
                    activeAlertItem = null;
                    return;
                }

                dropdownList.innerHTML = items.map((item) => `
                    <div class="px-4 py-3 space-y-2 hover:bg-slate-50 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">${escapeHtmlSafe(item.patient_name)}</div>
                                <div class="text-xs text-rose-600 font-medium">
                                    Saat ${escapeHtmlSafe(item.appointment_time)} (${item.minutes_late} dk geçti) • Gelmedi
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">${escapeHtmlSafe(item.description)}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" data-quick-status="no_show" data-status-url="${item.status_url}" class="px-2.5 py-1 text-[11px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-md transition">
                                Gelmedi İşaretle
                            </button>
                            <button type="button" data-quick-status="completed" data-status-url="${item.status_url}" class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md transition">
                                Geldi
                            </button>
                            <a href="{{ route('appointments.index') }}" class="ml-auto px-2.5 py-1 text-[11px] font-semibold text-sky-600 hover:text-sky-700">
                                Takvim →
                            </a>
                        </div>
                    </div>
                `).join('');

                const dismissed = getDismissed();
                const firstUnhandled = items.find((item) => !dismissed.includes(item.id));
                if (firstUnhandled) {
                    activeAlertItem = firstUnhandled;
                    alertTitle.textContent = `${firstUnhandled.patient_name} (${firstUnhandled.time_only})`;
                    alertSub.textContent = `${firstUnhandled.appointment_time} randevusuna gelmedi (${firstUnhandled.minutes_late} dk geçti).`;
                    liveAlert.classList.remove('hidden');
                } else {
                    liveAlert.classList.add('hidden');
                    activeAlertItem = null;
                }
            };

            const fetchOverdueAppointments = async () => {
                try {
                    const res = await fetch(overdueUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const payload = await res.json();
                    renderOverdue(payload.data || []);
                } catch {}
            };

            bellBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target) && e.target !== bellBtn) {
                    dropdown.classList.add('hidden');
                }
            });

            dropdownList?.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-quick-status]');
                if (btn) {
                    updateAppointmentStatusQuick(btn.dataset.statusUrl, btn.dataset.quickStatus);
                }
            });

            alertDismissBtn?.addEventListener('click', () => {
                if (activeAlertItem) addDismissed(activeAlertItem.id);
                liveAlert.classList.add('hidden');
            });

            alertNoShowBtn?.addEventListener('click', () => {
                if (activeAlertItem) updateAppointmentStatusQuick(activeAlertItem.statusUrl, 'no_show');
            });

            alertArrivedBtn?.addEventListener('click', () => {
                if (activeAlertItem) updateAppointmentStatusQuick(activeAlertItem.statusUrl, 'completed');
            });

            window.addEventListener('clinic:appointment-updated', fetchOverdueAppointments);

            fetchOverdueAppointments();
            setInterval(fetchOverdueAppointments, 30000);
        })();
    </script>

    @stack('scripts')
</body>
</html>
