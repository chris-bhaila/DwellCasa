@extends('layouts.admin')

@section('title', 'FAQs - DwellCasa Admin')
@section('header_title', 'FAQs')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">FAQs</h1>
        <p class="text-slate-500 mt-1">Manage frequently asked questions displayed on your website.</p>
    </div>
    <button type="button" id="open-add-modal"
        class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm text-sm flex-shrink-0 cursor-pointer">
        <i class="bi bi-plus-lg"></i> Add FAQ
    </button>
</div>

{{-- Filter Row --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3 mb-6 flex flex-wrap gap-3 items-center">
    <div class="relative flex-1 min-w-[180px]">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
        <input type="text" id="filter-search" placeholder="Search questions..."
            class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-slate-200 focus:ring-primary focus:border-primary transition-colors">
    </div>
    <select id="filter-status"
        class="text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-primary focus:border-primary transition-colors min-w-[120px]">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

{{-- FAQ Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Question</th>
                    <th class="px-5 py-3 font-medium w-24 text-center">Order</th>
                    <th class="px-5 py-3 font-medium w-28">Status</th>
                    <th class="px-5 py-3 font-medium text-right w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100" id="faqs-tbody">
                @forelse($faqs as $faq)
                <tr class="hover:bg-slate-50/50 transition-colors"
                    data-row-status="{{ $faq->is_active ? 'active' : 'inactive' }}">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-slate-900 line-clamp-1">{{ $faq->question }}</p>
                        <p class="text-slate-400 text-xs mt-0.5 line-clamp-1">{{ Str::limit($faq->answer, 80) }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-center text-slate-500">{{ $faq->sort_order }}</td>
                    <td class="px-5 py-3.5">
                        @if($faq->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Active</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="view-faq-btn relative inline-flex items-center justify-center p-2 text-slate-400 hover:bg-slate-50 hover:text-slate-600 rounded-lg transition-colors group cursor-pointer"
                                data-id="{{ $faq->id }}"
                                data-question="{{ $faq->question }}"
                                data-answer="{{ $faq->answer }}">
                                <i class="bi bi-eye text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">View</span>
                            </button>
                            <button type="button" class="edit-faq-btn relative inline-flex items-center justify-center p-2 text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-lg transition-colors group cursor-pointer"
                                data-id="{{ $faq->id }}"
                                data-question="{{ $faq->question }}"
                                data-answer="{{ $faq->answer }}"
                                data-sort_order="{{ $faq->sort_order }}"
                                data-is_active="{{ $faq->is_active ? 1 : 0 }}">
                                <i class="bi bi-pencil text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Edit</span>
                            </button>
                            <button type="button" class="delete-faq-btn relative inline-flex items-center justify-center p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors group cursor-pointer"
                                data-id="{{ $faq->id }}">
                                <i class="bi bi-trash text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-slate-400 text-sm italic">
                        No FAQs found. Add your first FAQ using the button above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="no-results-row" class="hidden px-5 py-10 text-center text-slate-400 text-sm italic">
            No FAQs match the current filters.
        </div>
    </div>
</div>

{{-- Add / Edit Modal --}}
<div id="faq-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" id="modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 id="modal-title" class="text-xl font-serif font-bold text-slate-900 italic">Add New FAQ</h2>
            <button type="button" id="modal-close"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="faq-form" class="p-6">
            @csrf
            <input type="hidden" id="faq_id" name="id" value="">

            <div class="mb-5">
                <label for="question" class="block text-sm font-medium text-slate-700 mb-2">
                    Question <span class="text-red-500">*</span>
                </label>
                <input type="text" name="question" id="question" required
                    placeholder="e.g. What time is check-in?"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors">
            </div>

            <div class="mb-5">
                <label for="answer" class="block text-sm font-medium text-slate-700 mb-2">
                    Answer <span class="text-red-500">*</span>
                </label>
                <textarea name="answer" id="answer" rows="5" required
                    placeholder="Write the answer here..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors resize-y"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-end">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="0" min="0"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors">
                </div>
                <div class="flex items-center gap-3 pb-1">
                    <input type="checkbox" name="is_active" id="is_active"
                        class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300" checked>
                    <label for="is_active" class="text-sm font-medium text-slate-700">Active (Visible)</label>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" id="modal-cancel"
                    class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all text-sm cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="submit-btn"
                    class="bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm text-sm cursor-pointer">
                    Save FAQ
                </button>
            </div>
        </form>
    </div>
</div>

{{-- View Modal --}}
<div id="view-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" id="view-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">FAQ Details</h2>
            <button type="button" id="view-modal-close"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-6">
            <p class="text-[10px] font-bold tracking-widest uppercase text-primary mb-2">Question</p>
            <p id="view-question" class="font-semibold text-slate-900 text-base mb-6"></p>

            <p class="text-[10px] font-bold tracking-widest uppercase text-primary mb-2">Answer</p>
            <p id="view-answer" class="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap"></p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ─── Add / Edit modal helpers ──────────────────────────────────────────────
    const modal      = document.getElementById('faq-modal');
    const modalTitle = document.getElementById('modal-title');
    const submitBtn  = document.getElementById('submit-btn');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        resetForm();
    }

    document.getElementById('modal-close').addEventListener('click', closeModal);
    document.getElementById('modal-cancel').addEventListener('click', closeModal);
    document.getElementById('modal-backdrop').addEventListener('click', closeModal);

    function resetForm() {
        document.getElementById('faq-form').reset();
        document.getElementById('faq_id').value = '';
        document.getElementById('sort_order').value = '0';
        document.getElementById('is_active').checked = true;
        modalTitle.innerText = 'Add New FAQ';
        submitBtn.innerText  = 'Save FAQ';
    }

    // ─── Open add ──────────────────────────────────────────────────────────────
    document.getElementById('open-add-modal').addEventListener('click', () => {
        resetForm();
        openModal();
    });

    // ─── Open edit ─────────────────────────────────────────────────────────────
    document.querySelectorAll('.edit-faq-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            document.getElementById('faq_id').value       = this.dataset.id;
            document.getElementById('question').value     = this.dataset.question;
            document.getElementById('answer').value       = this.dataset.answer;
            document.getElementById('sort_order').value   = this.dataset.sort_order;
            document.getElementById('is_active').checked  = this.dataset.is_active === '1';
            modalTitle.innerText = 'Edit FAQ';
            submitBtn.innerText  = 'Update FAQ';
            openModal();
        });
    });

    // ─── Submit (add / update) ─────────────────────────────────────────────────
    document.getElementById('faq-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data     = Object.fromEntries(formData.entries());
        data.is_active = this.querySelector('#is_active').checked;

        const id = data.id;
        delete data.id;

        const url    = id ? `/api/faqs/${id}` : '/api/faqs';
        const method = id ? 'put' : 'post';

        try {
            await axios[method](url, data);
            window.location.reload();
        } catch (err) {
            adminToast('Error saving FAQ: ' + (err.response?.data?.message || 'Unknown error'));
        }
    });

    // ─── Delete ────────────────────────────────────────────────────────────────
    document.querySelectorAll('.delete-faq-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!await adminConfirm('Are you sure you want to delete this FAQ?', { confirmLabel: 'Delete', type: 'danger' })) return;

            try {
                await axios.delete(`/api/faqs/${this.dataset.id}`);
                window.location.reload();
            } catch (err) {
                adminToast('Error deleting FAQ: ' + (err.response?.data?.message || 'Unknown error'));
            }
        });
    });

    // ─── View modal ────────────────────────────────────────────────────────────
    const viewModal = document.getElementById('view-modal');

    function openViewModal() {
        viewModal.classList.remove('hidden');
        viewModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeViewModal() {
        viewModal.classList.add('hidden');
        viewModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('view-modal-close').addEventListener('click', closeViewModal);
    document.getElementById('view-modal-backdrop').addEventListener('click', closeViewModal);

    document.querySelectorAll('.view-faq-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('view-question').textContent = this.dataset.question;
            document.getElementById('view-answer').textContent   = this.dataset.answer;
            openViewModal();
        });
    });

    // ─── Client-side filter ────────────────────────────────────────────────────
    const searchInput  = document.getElementById('filter-search');
    const statusSelect = document.getElementById('filter-status');
    const tbody        = document.getElementById('faqs-tbody');
    const noResults    = document.getElementById('no-results-row');

    function applyFilters() {
        const search = searchInput.value.trim().toLowerCase();
        const status = statusSelect.value;

        let visible = 0;
        tbody.querySelectorAll('tr[data-row-status]').forEach(row => {
            const questionEl = row.querySelector('td:first-child p');
            const text       = questionEl ? questionEl.textContent.toLowerCase() : '';
            const rowSt      = row.dataset.rowStatus;

            const show = (!search || text.includes(search))
                      && (!status || rowSt === status);

            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        noResults.classList.toggle('hidden', visible > 0);
    }

    searchInput.addEventListener('input', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
</script>
@endpush
