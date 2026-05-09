@extends('layouts.admin')

@section('title', 'Amenities - DwellCasa Admin')
@section('header_title', 'Amenities')

@section('content')
{{-- Temporary Mock Data for Previewing --}}
@php
if (!isset($amenities)) {
$amenities = collect([
(object)['id' => 1, 'name' => 'High-Speed WiFi', 'category' => 'utilities', 'icon' => '📶', 'description' => '', 'sort_order' => 0, 'is_active' => 1],
(object)['id' => 2, 'name' => 'Smart TV', 'category' => 'entertainment', 'icon' => '📺', 'description' => '', 'sort_order' => 0, 'is_active' => 1],
(object)['id' => 3, 'name' => 'Mini Bar', 'category' => 'kitchen', 'icon' => '🥂', 'description' => '', 'sort_order' => 0, 'is_active' => 0],
(object)['id' => 4, 'name' => 'Rain Shower', 'category' => 'bathroom', 'icon' => '🚿', 'description' => '', 'sort_order' => 0, 'is_active' => 1],
]);
}
@endphp

{{-- Page Header --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Amenities</h1>
        <p class="text-slate-500 mt-1">Create and manage amenities available for your properties and rooms.</p>
    </div>
    <button type="button" id="open-add-modal"
        class="inline-flex items-center gap-2 bg-primary cursor-pointer text-white px-5 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm text-sm flex-shrink-0">
        <i class="bi bi-plus-lg"></i> Add Amenity
    </button>
</div>

{{-- Filter Row --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3 mb-6 flex flex-wrap gap-3 items-center">
    <div class="relative flex-1 min-w-[180px]">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
        <input type="text" id="filter-search" placeholder="Search amenities..."
            class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-slate-200 focus:ring-primary focus:border-primary transition-colors">
    </div>
    <select id="filter-category"
        class="text-sm rounded-xl border border-slate-200 px-3 py-2 cursor-pointer focus:ring-primary focus:border-primary transition-colors min-w-[140px]">
        <option value="">All Categories</option>
        @foreach($amenities->pluck('category')->filter()->unique()->sort() as $cat)
        <option value="{{ strtolower($cat) }}">{{ ucfirst($cat) }}</option>
        @endforeach
    </select>
    <select id="filter-status"
        class="text-sm rounded-xl cursor-pointer border border-slate-200 px-3 py-2 focus:ring-primary focus:border-primary transition-colors min-w-[120px]">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

{{-- Amenities Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100" id="amenities-tbody">
                @forelse($amenities as $amenity)
                <tr class="hover:bg-slate-50/50 transition-colors"
                    data-row-category="{{ strtolower($amenity->category ?: '') }}"
                    data-row-status="{{ $amenity->is_active ? 'active' : 'inactive' }}">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-xl shadow-sm border border-slate-100 flex-shrink-0">
                                {!! $amenity->icon ?: '✨' !!}
                            </div>
                            <p class="font-bold text-slate-900">{{ $amenity->name }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600 capitalize">
                        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-sm font-medium">{{ $amenity->category ?: 'General' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($amenity->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200">Active</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="edit-amenity-btn relative cursor-pointer inline-flex items-center justify-center p-2 text-[#A89070] hover:bg-slate-50 hover:text-[#8E795E] rounded-lg transition-colors group"
                                data-id="{{ $amenity->id }}"
                                data-name="{{ $amenity->name }}"
                                data-category="{{ $amenity->category }}"
                                data-icon="{{ $amenity->icon }}"
                                data-description="{{ $amenity->description }}"
                                data-sort_order="{{ $amenity->sort_order }}"
                                data-is_active="{{ $amenity->is_active ? 1 : 0 }}">
                                <i class="bi bi-pencil text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Edit</span>
                            </button>
                            <button type="button" class="delete-amenity-btn cursor-pointer relative inline-flex items-center justify-center p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors group"
                                data-id="{{ $amenity->id }}">
                                <i class="bi bi-trash text-lg"></i>
                                <span class="absolute -bottom-8 right-0 w-max px-2 py-1 bg-slate-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 font-normal shadow-sm">Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-slate-400 text-sm italic">
                        No amenities found. Add your first amenity using the button above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="no-results-row" class="hidden px-5 py-10 text-center text-slate-400 text-sm italic">
            No amenities match the current filters.
        </div>
    </div>
</div>

{{-- Amenity Modal --}}
<div id="amenity-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" id="modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        {{-- Modal Header --}}
        <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h2 id="modal-title" class="text-xl font-serif font-bold text-slate-900 italic">Add New Amenity</h2>
            <button type="button" id="modal-close"
                class="w-8 h-8 flex items-center justify-center cursor-pointer text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="amenity-form" class="p-6">
            @csrf
            <input type="hidden" id="amenity_id" name="id" value="">

            {{-- Name + Category --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Amenity Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        placeholder="e.g. High-Speed WiFi"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                    <input type="text" name="category" id="category"
                        placeholder="e.g. Hygiene"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors">
                </div>
            </div>

            {{-- Icon Picker --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Select Icon <span class="text-red-500">*</span>
                </label>
                <input type="hidden" name="icon" id="icon" required>
                <div class="grid grid-cols-8 sm:grid-cols-10 gap-2 max-h-[200px] overflow-y-auto p-3 border border-slate-200 rounded-xl bg-slate-50 mb-2">
                    @php
                    $biIcons = [
                    // Connectivity & tech
                    'bi-wifi', 'bi-router', 'bi-pc-display', 'bi-tv', 'bi-projector',
                    'bi-speaker', 'bi-telephone', 'bi-phone', 'bi-modem',
                    'bi-plug', 'bi-power', 'bi-usb-plug', 'bi-battery-full',

                    // Climate & comfort
                    'bi-snow', 'bi-fan', 'bi-wind', 'bi-thermometer-half',
                    'bi-thermometer-snow', 'bi-thermometer-sun', 'bi-moisture', 'bi-brightness-high',

                    // Room & furniture
                    'bi-door-open', 'bi-door-closed', 'bi-window', 'bi-lamp',
                    'bi-lightbulb', 'bi-archive', 'bi-lock', 'bi-lock-fill',

                    // Safety & security
                    'bi-safe', 'bi-shield-check', 'bi-key',
                    'bi-person-badge', 'bi-bell', 'bi-alarm',

                    // Bathroom & personal care
                    'bi-droplet', 'bi-brush', 'bi-scissors',

                    // Food & drink (BI)
                    'bi-cup-hot', 'bi-cup-straw', 'bi-cup', 'bi-egg-fried',
                    'bi-basket', 'bi-water',

                    // Transport
                    'bi-car-front', 'bi-bicycle', 'bi-bus-front',
                    'bi-ev-station', 'bi-airplane', 'bi-p-circle',

                    // Nature & outdoor
                    'bi-tree', 'bi-flower1', 'bi-cloud-sun', 'bi-umbrella',
                    'bi-tsunami', 'bi-fire',

                    // Leisure & work
                    'bi-book', 'bi-music-note', 'bi-mic', 'bi-camera',
                    'bi-controller', 'bi-briefcase', 'bi-person-workspace',
                    'bi-newspaper', 'bi-pen', 'bi-printer',

                    // Misc
                    'bi-clock', 'bi-stars', 'bi-heart-pulse', 'bi-shop',
                    'bi-building', 'bi-house-door', 'bi-tools', 'bi-recycle',
                    'bi-bag-check', 'bi-lightning-charge', 'bi-bandaid',
                    ];

                    $lucideIcons = [
                    // Dining & meals
                    'utensils', 'utensils-crossed', 'chef-hat', 'soup', 'sandwich',
                    'salad', 'pizza', 'egg', 'fish', 'beef', 'ham', 'drumstick',
                    'cookie', 'cake-slice', 'croissant', 'popcorn', 'nut',

                    // Drinks
                    'coffee', 'wine', 'wine-off', 'beer', 'milk', 'martini',
                    'glass-water', 'cup-soda', 'flask-conical',

                    // Fruits & vegetables
                    'carrot', 'apple', 'grape', 'banana', 'cherry', 'wheat', 'citrus',

                    // Restaurant & service
                    'receipt', 'ticket', 'store', 'shopping-bag', 'package',
                    'bell-ring', 'concierge-bell', 'hand-platter',

                    // Kitchen & cooking
                    'flame', 'microwave', 'refrigerator', 'cooking-pot', 'scale', 'timer',

                    // Room & furniture
                    'armchair', 'sofa', 'bed', 'bed-single', 'bed-double', 'lamp-ceiling',
                    'lamp-desk', 'lamp-floor', 'blinds',

                    // Bathroom & personal care
                    'bath', 'shower-head', 'toilet',

                    // Clothing & laundry
                    'shirt', 'footprints',

                    // Safety & room access
                    'vault', 'shield', 'key-round', 'lock',
                    'door-open', 'door-closed',

                    // Luggage & travel
                    'luggage', 'package-2',

                    // Leisure
                    'book-open', 'pen-line', 'flower-2', 'leaf', 'sprout',

                    // Health
                    'pill', 'heart-pulse',

                    // Misc
                    'alarm-clock', 'calendar-check', 'air-vent',
                    'plug-zap', 'thermometer', 'clock-3',
                    ];
                    @endphp

                    @foreach($biIcons as $iconClass)
                    @php $iconName = ucwords(str_replace('-', ' ', substr($iconClass, 3))); @endphp
                    <button type="button" title="{{ $iconName }}"
                        class="icon-select-btn cursor-pointer flex items-center justify-center w-9 h-9 rounded-lg border-2 border-transparent hover:bg-white hover:border-primary hover:text-primary transition-all text-slate-500 bg-slate-100"
                        data-icon='<i class="bi {{ $iconClass }}"></i>'>
                        <i class="bi {{ $iconClass }} text-base pointer-events-none"></i>
                    </button>
                    @endforeach

                    @foreach($lucideIcons as $iconName)
                    @php $label = ucwords(str_replace('-', ' ', $iconName)); @endphp
                    <button type="button" title="{{ $label }}"
                        class="icon-select-btn flex items-center justify-center w-9 h-9 rounded-lg border-2 border-transparent hover:bg-white hover:border-primary hover:text-primary transition-all text-slate-500 bg-slate-100"
                        data-icon='<i data-lucide="{{ $iconName }}"></i>'>
                        <i data-lucide="{{ $iconName }}" class="pointer-events-none" style="width:16px;height:16px;"></i>
                    </button>
                    @endforeach
                </div>
                <p class="text-sm text-slate-500 flex items-center gap-2">
                    Selected:
                    <span id="selected-icon-preview"
                        class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded border border-slate-200 text-slate-400 text-lg">?</span>
                </p>
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    placeholder="Brief description..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors"></textarea>
            </div>

            {{-- Sort Order + Active --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-end">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-slate-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="0" min="0"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-primary focus:border-primary transition-colors">
                </div>
                <div class="flex items-center gap-3 pb-1">
                    <input type="checkbox" name="is_active" id="is_active"
                        class="rounded text-primary cursor-pointer focus:ring-primary w-5 h-5 border-slate-300" checked>
                    <label for="is_active" class="text-sm font-medium text-slate-700">Active (Visible)</label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-6 pt-5 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" id="modal-cancel"
                    class="px-5 py-2.5 rounded-xl cursor-pointer font-medium text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all text-sm">
                    Cancel
                </button>
                <button type="submit" id="submit-btn"
                    class="bg-primary text-white cursor-pointer px-6 py-2.5 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm text-sm">
                    Save Amenity
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ─── Modal helpers ─────────────────────────────────────────────────────────
    const modal       = document.getElementById('amenity-modal');
    const modalTitle  = document.getElementById('modal-title');
    const submitBtn   = document.getElementById('submit-btn');
    const iconInput   = document.getElementById('icon');
    const iconPreview = document.getElementById('selected-icon-preview');
    const iconBtns    = document.querySelectorAll('.icon-select-btn');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons({ root: modal });
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

    // ─── Icon picker ───────────────────────────────────────────────────────────
    iconBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            iconBtns.forEach(b => {
                b.classList.remove('border-primary', 'text-primary', 'bg-white');
                b.classList.add('border-transparent', 'text-slate-500', 'bg-slate-100');
            });
            this.classList.remove('border-transparent', 'text-slate-500', 'bg-slate-100');
            this.classList.add('border-primary', 'text-primary', 'bg-white');

            const iconHtml = this.dataset.icon;
            iconInput.value = iconHtml;
            iconPreview.innerHTML = iconHtml;
            iconPreview.classList.add('text-primary');

            if (iconHtml.includes('data-lucide') && window.lucide) {
                lucide.createIcons({ root: iconPreview });
            }
        });
    });

    // ─── Reset form ────────────────────────────────────────────────────────────
    function resetForm() {
        document.getElementById('amenity-form').reset();
        document.getElementById('amenity_id').value = '';
        iconInput.value = '';
        iconPreview.innerHTML = '?';
        iconPreview.classList.remove('text-primary');
        iconBtns.forEach(b => {
            b.classList.remove('border-primary', 'text-primary', 'bg-white');
            b.classList.add('border-transparent', 'text-slate-500', 'bg-slate-100');
        });
        modalTitle.innerText = 'Add New Amenity';
        submitBtn.innerText  = 'Save Amenity';
    }

    // ─── Open add ──────────────────────────────────────────────────────────────
    document.getElementById('open-add-modal').addEventListener('click', () => {
        resetForm();
        openModal();
    });

    // ─── Open edit ─────────────────────────────────────────────────────────────
    document.querySelectorAll('.edit-amenity-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();

            document.getElementById('amenity_id').value  = this.dataset.id;
            document.getElementById('name').value        = this.dataset.name;
            document.getElementById('category').value    = this.dataset.category;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('sort_order').value  = this.dataset.sort_order;
            document.getElementById('is_active').checked = this.dataset.is_active === '1';

            const iconHtml = this.dataset.icon;
            iconInput.value = iconHtml;
            iconPreview.innerHTML = iconHtml || '?';
            if (iconHtml) {
                iconPreview.classList.add('text-primary');
                if (iconHtml.includes('data-lucide') && window.lucide) {
                    lucide.createIcons({ root: iconPreview });
                }
            }
            iconBtns.forEach(b => {
                const active = b.dataset.icon === iconHtml;
                b.classList.toggle('border-primary',     active);
                b.classList.toggle('text-primary',       active);
                b.classList.toggle('bg-white',           active);
                b.classList.toggle('border-transparent', !active);
                b.classList.toggle('text-slate-500',     !active);
                b.classList.toggle('bg-slate-100',       !active);
            });

            modalTitle.innerText = 'Edit Amenity';
            submitBtn.innerText  = 'Update Amenity';
            openModal();
        });
    });

    // ─── Submit (add / update) ─────────────────────────────────────────────────
    document.getElementById('amenity-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data     = Object.fromEntries(formData.entries());
        data.is_active = this.querySelector('#is_active').checked;

        const id = data.id;
        delete data.id;

        const url    = id ? `/api/amenities/${id}` : '/api/amenities';
        const method = id ? 'put' : 'post';

        try {
            await axios[method](url, data);
            window.location.reload();
        } catch (err) {
            adminToast('Error saving amenity: ' + (err.response?.data?.message || 'Unknown error'));
        }
    });

    // ─── Delete ────────────────────────────────────────────────────────────────
    document.querySelectorAll('.delete-amenity-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!await adminConfirm('Are you sure you want to delete this amenity?', { confirmLabel: 'Delete', type: 'danger' })) return;

            try {
                await axios.delete(`/api/amenities/${this.dataset.id}`);
                window.location.reload();
            } catch (err) {
                adminToast('Error deleting amenity: ' + (err.response?.data?.message || 'Unknown error'));
            }
        });
    });

    // ─── Client-side filter ────────────────────────────────────────────────────
    const searchInput    = document.getElementById('filter-search');
    const categorySelect = document.getElementById('filter-category');
    const statusSelect   = document.getElementById('filter-status');
    const tbody          = document.getElementById('amenities-tbody');
    const noResults      = document.getElementById('no-results-row');

    function applyFilters() {
        const search   = searchInput.value.trim().toLowerCase();
        const category = categorySelect.value.toLowerCase();
        const status   = statusSelect.value;

        let visible = 0;
        tbody.querySelectorAll('tr[data-row-status]').forEach(row => {
            const nameEl = row.querySelector('td:first-child p');
            const name   = nameEl ? nameEl.textContent.toLowerCase() : '';
            const rowCat = row.dataset.rowCategory;
            const rowSt  = row.dataset.rowStatus;

            const show = (!search   || name.includes(search))
                      && (!category || rowCat === category)
                      && (!status   || rowSt  === status);

            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        noResults.classList.toggle('hidden', visible > 0);
    }

    searchInput.addEventListener('input', applyFilters);
    categorySelect.addEventListener('change', applyFilters);
    statusSelect.addEventListener('change', applyFilters);

    // ─── Lucide icons ──────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endpush
