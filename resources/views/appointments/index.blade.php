@extends('layouts.app')

@section('title', 'Randevu Takvimi | Diş Kliniği')

@push('styles')
<style>
    .fc { max-width: 100%; }
    @media (max-width: 639px) {
        .fc .fc-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.65rem;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        .fc .fc-toolbar-title {
            font-size: 1.05rem;
            text-align: center;
        }
        .fc .fc-button {
            padding: 0.45rem 0.65rem;
            font-size: 0.8rem;
        }
        .fc .fc-daygrid-day-frame {
            min-height: 54px;
        }
        .fc .fc-event {
            font-size: 0.72rem;
        }
        .fc .fc-list-event-title,
        .fc .fc-list-event-time {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-5">
    <!-- Üst Başlık, Renk Anahtarı ve Yeni Randevu Butonu -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Randevu &amp; Takvim Yönetimi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Boş bir güne tıklayarak yeni randevu ekleyebilir, mevcut bir randevuya tıklayarak saatini ve durumunu düzenleyebilirsiniz.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-2.5 sm:gap-3">
            <!-- Durum Renk Anahtarı -->
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium shadow-xs">
                @foreach ($statusMeta as $meta)
                    <div class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full" style="background-color: {{ $meta['color'] }}"></span>
                        <span class="text-slate-700">{{ $meta['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <button
                type="button"
                onclick="openNewAppointmentModal()"
                class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl shadow-sm transition shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Yeni Randevu</span>
            </button>
        </div>
    </div>

    <!-- FullCalendar Alanı -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3 sm:p-6">
        <div id="clinic-calendar" class="min-h-[420px] sm:min-h-[680px]"></div>
    </div>
</div>

<!-- Bildirim Toast -->
<div
    id="calendar-toast"
    class="hidden fixed bottom-20 md:bottom-5 left-3 right-3 sm:left-auto sm:right-5 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white transition-all"
></div>

<!-- Randevu Detay & Düzenleme Modalı (Event Click) -->
<div id="appointment-detail-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-lg w-full max-h-[92dvh] flex flex-col shadow-xl border border-slate-200 overflow-hidden safe-bottom">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="min-w-0 pr-2">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Randevu Detay &amp; Düzenleme</h3>
                <p id="modal-appointment-subtitle" class="text-xs text-slate-500 mt-0.5 truncate">—</p>
            </div>
            <button type="button" onclick="closeDetailModal()" class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 text-2xl leading-none shrink-0">&times;</button>
        </div>

        <div class="p-5 sm:p-6 space-y-5 overflow-y-auto">
            <!-- Hasta ve İşlem Bilgileri -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2">
                <div class="flex items-center justify-between gap-2">
                    <span id="modal-patient-name" class="text-base font-bold text-slate-900">—</span>
                    <a id="modal-patient-link" href="#" class="text-xs font-semibold text-sky-600 hover:text-sky-700 shrink-0">
                        Hasta Profili →
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                    <span id="modal-patient-phone"></span>
                    <span id="modal-current-status" class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold"></span>
                </div>
                <div class="pt-1 border-t border-slate-200/70">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Yapılacak İşlem</span>
                    <p id="modal-appointment-desc" class="text-sm text-slate-800 mt-0.5">—</p>
                </div>
            </div>

            <!-- Saat / Tarih Güncelleme -->
            <div class="space-y-2">
                <label for="modal-reschedule-date" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Randevu Tarih & Saati
                </label>
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <input
                        id="modal-reschedule-date"
                        type="datetime-local"
                        class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"
                    >
                    <button
                        type="button"
                        id="modal-reschedule-btn"
                        class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl shadow-xs transition shrink-0"
                    >
                        Saati Güncelle
                    </button>
                </div>
            </div>

            <!-- Durum Güncelleme Butonları -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2.5">Durumu Güncelle</p>
                <div class="grid grid-cols-2 gap-2.5">
                    <button
                        type="button"
                        data-status-btn="completed"
                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-sm font-semibold transition"
                    >
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Geldi</span>
                    </button>

                    <button
                        type="button"
                        data-status-btn="no_show"
                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-800 text-sm font-semibold transition"
                    >
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span>Gelmedi</span>
                    </button>

                    <button
                        type="button"
                        id="open-cancel-options-btn"
                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition"
                    >
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span>
                        <span>Gelmeyecek / İptal</span>
                    </button>

                    <button
                        type="button"
                        data-status-btn="pending"
                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-yellow-200 bg-yellow-50 hover:bg-yellow-100 text-yellow-800 text-sm font-semibold transition"
                    >
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                        <span>Bekliyor</span>
                    </button>
                </div>

                <!-- Gelmeyecek / İptal İçin 2 Seçenek Paneli (İptal Et veya Ertele) -->
                <div id="cancel-choice-panel" class="hidden mt-4 p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Gelmeyecek / İptal Seçeneği</span>
                        <button type="button" id="close-cancel-options-btn" class="text-xs text-slate-500 hover:text-slate-700">Kapat</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <button
                            type="button"
                            data-status-btn="cancelled"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold transition"
                        >
                            <span>1. Tamamen İptal Et</span>
                        </button>
                        <button
                            type="button"
                            id="show-postpone-box-btn"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition"
                        >
                            <span>2. Ertele (Yeni Tarih Seç)</span>
                        </button>
                    </div>

                    <div id="postpone-date-box" class="hidden pt-2 border-t border-slate-200 space-y-2">
                        <label for="modal-postpone-date" class="block text-xs font-semibold text-slate-700">Ertelenecek Yeni Tarih & Saat</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input
                                id="modal-postpone-date"
                                type="datetime-local"
                                class="flex-1 px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none"
                            >
                            <button
                                type="button"
                                id="confirm-postpone-btn"
                                class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-xl transition shrink-0"
                            >
                                Ertele & Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Randevu Ekle Modalı (Date Click) -->
<div id="new-appointment-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-md w-full max-h-[92dvh] flex flex-col shadow-xl border border-slate-200 overflow-hidden safe-bottom">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Yeni Randevu Ekle</h3>
            <button type="button" onclick="closeNewAppointmentModal()" class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 text-2xl leading-none">&times;</button>
        </div>

        <form id="new-appointment-form" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hasta *</label>
                <select id="new-appt-patient" name="patient_id" required class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                    <option value="">Hasta seçiniz...</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->phone }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Randevu Tarih &amp; Saat *</label>
                <input id="new-appt-date" name="appointment_date" type="datetime-local" required class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Yapılacak İşlem / Açıklama</label>
                <input id="new-appt-desc" name="description" type="text" placeholder="Örn: Dolgu, Kanal Tedavisi, Muayene..." class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
            </div>
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2">
                <button type="button" onclick="closeNewAppointmentModal()" class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">İptal</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm">Randevu Ekle</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- FullCalendar v6 CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/tr.global.min.js"></script>

<script>
(() => {
    const calendarEl = document.getElementById('clinic-calendar');
    const detailModal = document.getElementById('appointment-detail-modal');
    const newApptModal = document.getElementById('new-appointment-modal');
    const newApptForm = document.getElementById('new-appointment-form');
    const rescheduleInput = document.getElementById('modal-reschedule-date');
    const rescheduleBtn = document.getElementById('modal-reschedule-btn');
    const cancelChoicePanel = document.getElementById('cancel-choice-panel');
    const postponeDateBox = document.getElementById('postpone-date-box');
    const postponeDateInput = document.getElementById('modal-postpone-date');
    const confirmPostponeBtn = document.getElementById('confirm-postpone-btn');
    const toastEl = document.getElementById('calendar-toast');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const eventsUrl = @json(route('appointments.events'));
    const storeUrl = @json(route('appointments.store'));
    const rescheduleUrlTemplate = @json(route('appointments.reschedule', ['appointment' => '__ID__']));
    const updateStatusUrlTemplate = @json(route('appointments.update-status', ['appointment' => '__ID__']));

    let activeAppointmentId = null;
    let toastTimer = null;

    const statusBadgeClasses = {
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-emerald-100 text-emerald-800',
        no_show: 'bg-rose-100 text-rose-800',
        postponed: 'bg-violet-100 text-violet-800',
        cancelled: 'bg-slate-200 text-slate-700',
    };

    const showToast = (message, isError = false) => {
        clearTimeout(toastTimer);
        toastEl.textContent = message;
        toastEl.className = `fixed bottom-20 md:bottom-5 left-3 right-3 sm:left-auto sm:right-5 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white ${
            isError ? 'bg-rose-600' : 'bg-slate-900'
        }`;
        toastEl.classList.remove('hidden');
        toastTimer = setTimeout(() => toastEl.classList.add('hidden'), 3000);
    };

    const formatLocalDateTime = (dateObj, forInput = false) => {
        const pad = (n) => String(n).padStart(2, '0');
        const datePart = `${dateObj.getFullYear()}-${pad(dateObj.getMonth() + 1)}-${pad(dateObj.getDate())}`;
        const timePart = `${pad(dateObj.getHours())}:${pad(dateObj.getMinutes())}`;
        return forInput ? `${datePart}T${timePart}` : `${datePart} ${timePart}:00`;
    };

    const isMobileViewport = window.innerWidth < 640;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobileViewport ? 'listWeek' : 'dayGridMonth',
        locale: 'tr',
        firstDay: 1,
        dayMaxEvents: true,
        slotMinTime: '08:00:00',
        slotMaxTime: '19:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,
        nowIndicator: true,
        editable: true,
        height: 'auto',
        noEventsText: 'Bu hafta planlanmış randevu bulunmuyor.',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay',
            week: 'Hafta',
            day: 'Gün',
            list: 'Liste'
        },
        events: eventsUrl,

        // Boş bir güne tıklandığında "Yeni Randevu Ekle" modalını aç
        dateClick(info) {
            const targetDate = new Date(info.date.getTime());
            if (info.allDay) {
                targetDate.setHours(9, 0, 0, 0);
            }
            document.getElementById('new-appt-date').value = formatLocalDateTime(targetDate, true);
            newApptModal.classList.remove('hidden');
        },

        // Sürükle-Bırak (Drag & Drop) ile gün/saat güncelleme
        async eventDrop(info) {
            const url = rescheduleUrlTemplate.replace('__ID__', info.event.id);
            const newDate = formatLocalDateTime(info.event.start);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ appointment_date: newDate }),
                });

                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Randevu taşınamadı.');
                }

                showToast(result.message || 'Randevu güncellendi.');
                calendar.refetchEvents();
                window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
            } catch (err) {
                info.revert();
                showToast(err.message || 'Sunucu hatası oluştu.', true);
            }
        },

        // Var olan randevuya tıklama -> "Randevu Detay & Düzenleme" Modalı
        eventClick(info) {
            const props = info.event.extendedProps;
            activeAppointmentId = info.event.id;

            document.getElementById('modal-patient-name').textContent = props.patient_name || info.event.title;
            document.getElementById('modal-appointment-subtitle').textContent =
                `Randevu #${info.event.id} • ` + info.event.start.toLocaleString('tr-TR', {
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
            document.getElementById('modal-patient-phone').textContent = props.phone ? `Tel: ${props.phone}` : 'Telefon kayıtlı değil';
            document.getElementById('modal-appointment-desc').textContent = props.description || 'Genel Muayene';

            const badge = document.getElementById('modal-current-status');
            badge.textContent = props.status_label || 'Bekliyor';
            badge.className = `inline-flex items-center px-2 py-0.5 rounded-full font-semibold ${
                statusBadgeClasses[props.status] || 'bg-slate-100 text-slate-700'
            }`;

            rescheduleInput.value = formatLocalDateTime(info.event.start, true);
            const tomorrow = new Date(info.event.start.getTime());
            tomorrow.setDate(tomorrow.getDate() + 1);
            postponeDateInput.value = formatLocalDateTime(tomorrow, true);
            cancelChoicePanel.classList.add('hidden');
            postponeDateBox.classList.add('hidden');

            const linkEl = document.getElementById('modal-patient-link');
            if (props.patient_url) {
                linkEl.href = props.patient_url;
                linkEl.classList.remove('hidden');
            } else {
                linkEl.classList.add('hidden');
            }

            detailModal.classList.remove('hidden');
        }
    });

    calendar.render();

    window.closeDetailModal = () => {
        detailModal.classList.add('hidden');
        activeAppointmentId = null;
    };

    window.openNewAppointmentModal = () => {
        const now = new Date();
        now.setMinutes(now.getMinutes() < 30 ? 30 : 0, 0, 0);
        if (now.getMinutes() === 0) now.setHours(now.getHours() + 1);
        document.getElementById('new-appt-date').value = formatLocalDateTime(now, true);
        newApptModal.classList.remove('hidden');
    };

    window.closeNewAppointmentModal = () => {
        newApptModal.classList.add('hidden');
    };

    // Modal içerisinden "Saati Güncelle" (AJAX reschedule)
    rescheduleBtn.addEventListener('click', async () => {
        if (!activeAppointmentId || !rescheduleInput.value) return;

        const url = rescheduleUrlTemplate.replace('__ID__', activeAppointmentId);
        rescheduleBtn.disabled = true;
        rescheduleBtn.textContent = 'Güncelleniyor...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ appointment_date: rescheduleInput.value }),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Saat güncellenemedi.');
            }

            closeDetailModal();
            calendar.refetchEvents();
            window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
            showToast(result.message || 'Randevu saati güncellendi.');
        } catch (err) {
            showToast(err.message || 'Sunucu hatası oluştu.', true);
        } finally {
            rescheduleBtn.disabled = false;
            rescheduleBtn.textContent = 'Saati Güncelle';
        }
    });

    // Gelmeyecek / İptal 2 seçenek paneli
    document.getElementById('open-cancel-options-btn').addEventListener('click', () => {
        cancelChoicePanel.classList.remove('hidden');
    });
    document.getElementById('close-cancel-options-btn').addEventListener('click', () => {
        cancelChoicePanel.classList.add('hidden');
        postponeDateBox.classList.add('hidden');
    });
    document.getElementById('show-postpone-box-btn').addEventListener('click', () => {
        postponeDateBox.classList.remove('hidden');
    });

    confirmPostponeBtn.addEventListener('click', async () => {
        if (!activeAppointmentId || !postponeDateInput.value) return;

        const url = rescheduleUrlTemplate.replace('__ID__', activeAppointmentId);
        confirmPostponeBtn.disabled = true;
        confirmPostponeBtn.textContent = 'Kaydediliyor...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    appointment_date: postponeDateInput.value,
                    status: 'postponed',
                }),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Randevu ertelenemedi.');
            }

            closeDetailModal();
            calendar.refetchEvents();
            window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
            showToast(result.message || 'Randevu ertelendi.');
        } catch (err) {
            showToast(err.message || 'Sunucu hatası oluştu.', true);
        } finally {
            confirmPostponeBtn.disabled = false;
            confirmPostponeBtn.textContent = 'Ertele & Kaydet';
        }
    });

    // Modal içerisindeki durum butonlarına tıklandığında (AJAX updateStatus)
    document.querySelectorAll('[data-status-btn]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            if (!activeAppointmentId) return;

            const status = btn.dataset.statusBtn;
            const url = updateStatusUrlTemplate.replace('__ID__', activeAppointmentId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status }),
                });

                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Durum güncellenemedi.');
                }

                closeDetailModal();
                calendar.refetchEvents();
                window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
                showToast(result.message || 'Durum güncellendi.');
            } catch (err) {
                showToast(err.message || 'Sunucu hatası oluştu.', true);
            }
        });
    });

    window.addEventListener('clinic:appointment-updated', () => {
        calendar.refetchEvents();
    });

    // Yeni Randevu Ekleme (AJAX)
    newApptForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            patient_id: document.getElementById('new-appt-patient').value,
            appointment_date: document.getElementById('new-appt-date').value,
            description: document.getElementById('new-appt-desc').value.trim() || null,
        };

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Randevu eklenemedi.');
            }

            newApptForm.reset();
            closeNewAppointmentModal();
            calendar.refetchEvents();
            window.dispatchEvent(new CustomEvent('clinic:appointment-updated'));
            showToast(result.message || 'Randevu oluşturuldu.');
        } catch (err) {
            showToast(err.message || 'Sunucu hatası oluştu.', true);
        }
    });

    detailModal.addEventListener('click', (e) => {
        if (e.target === detailModal) closeDetailModal();
    });
    newApptModal.addEventListener('click', (e) => {
        if (e.target === newApptModal) closeNewAppointmentModal();
    });
})();
</script>
@endpush
@endsection
