<!-- Hasta Bilgilerini Düzenleme Modalı -->
<div id="edit-patient-modal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Hasta Bilgilerini Düzenle</h3>
                <p id="edit-patient-subtitle" class="text-xs text-slate-500">—</p>
            </div>
            <button type="button" id="close-edit-patient-modal" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>

        <form id="edit-patient-form" class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-full-name" class="block text-xs font-semibold text-slate-700 mb-1">Ad Soyad *</label>
                    <input id="edit-full-name" name="full_name" type="text" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label for="edit-phone" class="block text-xs font-semibold text-slate-700 mb-1">Telefon *</label>
                    <input id="edit-phone" name="phone" type="text" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label for="edit-tc-no" class="block text-xs font-semibold text-slate-700 mb-1">TC Kimlik No</label>
                    <input id="edit-tc-no" name="tc_no" type="text" maxlength="11" placeholder="11 haneli TC" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label for="edit-birth-date" class="block text-xs font-semibold text-slate-700 mb-1">Doğum Tarihi</label>
                    <input id="edit-birth-date" name="birth_date" type="date" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
                <div>
                    <label for="edit-gender" class="block text-xs font-semibold text-slate-700 mb-1">Cinsiyet</label>
                    <select id="edit-gender" name="gender" class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                        <option value="">Belirtilmemiş</option>
                        <option value="female">Kadın</option>
                        <option value="male">Erkek</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>
                <div>
                    <label for="edit-email" class="block text-xs font-semibold text-slate-700 mb-1">E-posta</label>
                    <input id="edit-email" name="email" type="email" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none">
                </div>
            </div>

            <div>
                <label for="edit-medical-notes" class="block text-xs font-semibold text-rose-700 mb-1">Alerjiler / Kronik Rahatsızlık / Tıbbi Notlar</label>
                <textarea id="edit-medical-notes" name="medical_notes" rows="3" placeholder="Örn: Penisilin alerjisi, diyabet, tansiyon ilacı..." class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none"></textarea>
            </div>

            <div id="edit-patient-error" class="hidden text-xs text-rose-600 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2"></div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancel-edit-patient-modal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">İptal</button>
                <button type="submit" id="save-edit-patient-btn" class="px-5 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm disabled:opacity-60">
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('edit-patient-modal');
    const form = document.getElementById('edit-patient-form');
    const errorBox = document.getElementById('edit-patient-error');
    const saveBtn = document.getElementById('save-edit-patient-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentUpdateUrl = null;

    window.openEditPatientModal = (patient) => {
        currentUpdateUrl = patient.update_url;
        document.getElementById('edit-patient-subtitle').textContent = `Hasta #${patient.id} • ${patient.full_name}`;
        document.getElementById('edit-full-name').value = patient.full_name || '';
        document.getElementById('edit-phone').value = patient.phone || '';
        document.getElementById('edit-tc-no').value = patient.tc_no || '';
        document.getElementById('edit-birth-date').value = patient.birth_date || '';
        document.getElementById('edit-gender').value = patient.gender || '';
        document.getElementById('edit-email').value = patient.email || '';
        document.getElementById('edit-medical-notes').value = patient.medical_notes || '';
        errorBox.classList.add('hidden');
        modal.classList.remove('hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        currentUpdateUrl = null;
    };

    document.getElementById('close-edit-patient-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-edit-patient-modal').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!currentUpdateUrl) return;

        errorBox.classList.add('hidden');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Kaydediliyor...';

        const payload = {
            full_name: document.getElementById('edit-full-name').value.trim(),
            phone: document.getElementById('edit-phone').value.trim(),
            tc_no: document.getElementById('edit-tc-no').value.trim() || null,
            birth_date: document.getElementById('edit-birth-date').value || null,
            gender: document.getElementById('edit-gender').value || null,
            email: document.getElementById('edit-email').value.trim() || null,
            medical_notes: document.getElementById('edit-medical-notes').value.trim() || null,
        };

        try {
            const response = await fetch(currentUpdateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                const firstErr = result.errors ? Object.values(result.errors).flat()[0] : null;
                throw new Error(firstErr || result.message || 'Bilgiler güncellenemedi.');
            }

            window.location.reload();
        } catch (err) {
            errorBox.textContent = err.message || 'Sunucu hatası oluştu.';
            errorBox.classList.remove('hidden');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Değişiklikleri Kaydet';
        }
    });
})();
</script>
@endpush
