<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Diş Kliniği Yönetim Sistemi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('patients.index') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold shadow-sm">
                    DK
                </span>
                <div>
                    <div class="text-sm font-bold text-slate-900 leading-tight">Diş Kliniği</div>
                    <div class="text-[11px] text-slate-500">Hasta & Odontogram Takip</div>
                </div>
            </a>

            <!-- Anlık AJAX Hasta Arama -->
            <div class="relative flex-1 max-w-2xl">
                <div class="relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        id="global-patient-search"
                        type="text"
                        autocomplete="off"
                        placeholder="Hasta ara: Ad Soyad, Telefon veya TC Kimlik No..."
                        class="w-full pl-11 pr-4 py-2.5 text-sm bg-slate-100 border border-slate-200/80 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition"
                    >
                </div>
                <div
                    id="global-search-results"
                    class="hidden absolute left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50 divide-y divide-slate-100 max-h-80 overflow-y-auto"
                ></div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('patients.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('patients.index') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Kontrol Paneli
                </a>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('appointments.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Randevu Takvimi
                </a>
                <a href="{{ route('finance.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('finance.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Finans &amp; Muhasebe
                </a>

                <!-- Saati Geçen / Gelmeyen Randevu Bildirim Zili -->
                <div class="relative ml-1">
                    <button
                        type="button"
                        id="overdue-bell-btn"
                        title="Saati Geçen Randevu Bildirimleri"
                        class="relative inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 transition"
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
                        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-50"
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
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        (() => {
            const input = document.getElementById('global-patient-search');
            const resultsBox = document.getElementById('global-search-results');
            if (!input || !resultsBox) return;

            let debounceTimer = null;
            const searchUrl = @json(route('patients.search'));

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
                            <a href="${p.url}" class="flex items-center justify-between px-4 py-2.5 hover:bg-sky-50 transition">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">${escapeHtml(p.full_name)}</div>
                                    <div class="text-xs text-slate-500">
                                        Tel: ${escapeHtml(p.phone)}
                                        ${p.tc_no ? `• TC: ${escapeHtml(p.tc_no)}` : ''}
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-sky-600">Profil →</span>
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

            function escapeHtml(str) {
                return String(str ?? '').replace(/[&<>"']/g, (m) => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[m]));
            }
        })();
    </script>

    <!-- Site İçi Canlı Gecikmiş Randevu Bildirim Kartı -->
    <div id="overdue-live-alert" class="hidden fixed bottom-5 left-5 z-50 max-w-sm w-[calc(100vw-2.5rem)] bg-white border-l-4 border-rose-500 rounded-2xl shadow-2xl ring-1 ring-slate-200 p-4 space-y-3">
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
