@extends('layouts.admin')

@section('title', 'Supplies - DwellCasa Admin')
@section('header_title', 'Supplies')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Supplies</h1>
        <p class="text-slate-500 mt-1">Track supply stock levels, restock, and log usage.</p>
    </div>
    @can('edit inventory')
    <div class="flex items-center gap-3">
        <button onclick="openCategoryModal()"
            class="px-4 py-2 rounded-xl text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
            <i class="bi bi-tag mr-1.5"></i>Manage Categories
        </button>
        <button onclick="openItemModal()"
            class="px-4 py-2 rounded-xl text-sm font-medium bg-[#A89070] text-white hover:bg-[#967860] transition-colors">
            <i class="bi bi-plus-lg mr-1.5"></i>Add Supply Item
        </button>
    </div>
    @endcan
</div>

<!-- Stats Bar -->
<div id="stats-bar" class="flex flex-wrap gap-3 mb-6">
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-100 shadow-sm text-sm">
        <span class="text-slate-500">Total:</span>
        <span class="font-semibold text-slate-900" id="stat-total">—</span>
    </div>
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-sm">
        <i class="bi bi-exclamation-triangle text-amber-500"></i>
        <span class="text-amber-700">Low Stock:</span>
        <span class="font-semibold text-amber-800" id="stat-low">—</span>
    </div>
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-sm">
        <i class="bi bi-x-circle text-rose-500"></i>
        <span class="text-rose-700">Out of Stock:</span>
        <span class="font-semibold text-rose-800" id="stat-out">—</span>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Loading -->
    <div id="table-loading" class="flex items-center justify-center py-16">
        <div class="w-7 h-7 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
    </div>
    <!-- Table content -->
    <div id="table-content" class="hidden overflow-x-auto">
        <table class="w-full min-w-[700px] text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Unit</th>
                    <th class="px-5 py-3 font-medium">Min Stock</th>
                    <th class="px-5 py-3 font-medium">On Hand</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="supplies-tbody" class="text-sm divide-y divide-slate-100"></tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div id="pagination" class="hidden px-5 py-3 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
        <span id="page-info"></span>
        <div class="flex items-center gap-2">
            <button id="btn-prev" onclick="changePage(-1)"
                class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                <i class="bi bi-chevron-left"></i> Prev
            </button>
            <button id="btn-next" onclick="changePage(1)"
                class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                Next <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- ── Modals ──────────────────────────────────────────────────── -->

<!-- Add/Edit Supply Item Modal -->
<div id="item-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic" id="item-modal-title">Add Supply Item</h2>
            <button onclick="closeItemModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="item-form" class="p-6 space-y-4">
            <input type="hidden" id="item-id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name <span class="text-rose-500">*</span></label>
                <input type="text" id="item-name" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="e.g. Hand Soap">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
                <select id="item-category" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                    <option value="">Select category...</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Unit</label>
                    <input type="text" id="item-unit" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="e.g. bottles">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Minimum Stock</label>
                    <input type="number" id="item-min-stock" min="0" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea id="item-description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none" placeholder="Optional notes..."></textarea>
            </div>
        </form>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeItemModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveItem()" id="item-save-btn" class="px-5 py-2.5 rounded-xl font-medium bg-[#A89070] text-white hover:bg-[#967860] transition-colors text-sm">Save</button>
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div id="restock-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Restock Supply</h2>
                <p class="text-sm text-slate-500 mt-0.5" id="restock-item-name"></p>
            </div>
            <button onclick="closeRestockModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="restock-item-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Quantity <span class="text-rose-500">*</span></label>
                    <input type="number" id="restock-quantity" min="0.01" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Cost <span class="text-rose-500">*</span></label>
                    <input type="number" id="restock-cost" min="0" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="0.00">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="restock-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none" placeholder="Optional..."></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeRestockModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveRestock()" class="px-5 py-2.5 rounded-xl font-medium bg-green-600 text-white hover:bg-green-700 transition-colors text-sm">
                <i class="bi bi-plus-circle mr-1.5"></i>Restock
            </button>
        </div>
    </div>
</div>

<!-- Use Modal -->
<div id="use-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Log Usage</h2>
                <p class="text-sm text-slate-500 mt-0.5" id="use-item-name"></p>
            </div>
            <button onclick="closeUseModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="use-item-id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Quantity Used <span class="text-rose-500">*</span></label>
                <input type="number" id="use-quantity" min="0.01" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Room <span class="text-slate-400 font-normal">(optional)</span></label>
                <select id="use-room" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                    <option value="">No specific room</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="use-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none" placeholder="Optional..."></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeUseModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveUse()" class="px-5 py-2.5 rounded-xl font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors text-sm">
                <i class="bi bi-dash-circle mr-1.5"></i>Log Usage
            </button>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="category-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[85vh]">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Supply Categories</h2>
            <button onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="flex flex-col sm:flex-row flex-1 overflow-hidden">
            <!-- Category List -->
            <div class="flex-1 overflow-y-auto border-b sm:border-b-0 sm:border-r border-slate-100 p-5">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Existing Categories</h3>
                <div id="category-list" class="space-y-2">
                    <div class="flex items-center justify-center py-8">
                        <div class="w-5 h-5 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>
            <!-- Add Category Form -->
            <div class="w-full sm:w-64 flex-shrink-0 p-5">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Add Category</h3>
                <div class="space-y-3">
                    <input type="text" id="new-category-name" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="Category name">
                    <button onclick="addCategory()" class="w-full px-4 py-2 rounded-xl text-sm font-medium bg-[#A89070] text-white hover:bg-[#967860] transition-colors">
                        <i class="bi bi-plus-lg mr-1"></i>Add Category
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 flex justify-end bg-slate-50/50 flex-shrink-0">
            <button onclick="closeCategoryModal()" class="px-5 py-2 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors text-sm">Close</button>
        </div>
    </div>
</div>

<!-- Stock History Slide-Over -->
<div id="history-panel" class="fixed inset-0 z-[90] hidden">
    <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" onclick="closeHistoryPanel()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="history-drawer">
        <div class="p-5 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <div>
                <h2 class="text-lg font-serif font-bold text-slate-900 italic">Stock History</h2>
                <p class="text-sm text-slate-500 mt-0.5" id="history-item-name"></p>
            </div>
            <button onclick="closeHistoryPanel()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto">
            <div id="history-loading" class="flex items-center justify-center py-12">
                <div class="w-6 h-6 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div id="history-content" class="hidden overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                            <th class="px-4 py-3 font-medium">Action</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Room</th>
                            <th class="px-4 py-3 font-medium">By</th>
                            <th class="px-4 py-3 font-medium">Cost</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody" class="divide-y divide-slate-100"></tbody>
                </table>
                <p id="history-empty" class="hidden text-center text-slate-400 italic py-8 text-sm">No stock history found.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const canEditInventory = @json(auth()->user()->can('edit inventory'));
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let currentPage = 1;
let lastMeta = {};
let allItems = [];

// ── Helpers ────────────────────────────────────────────────────────

function openModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
    setTimeout(() => {
        m.classList.remove('opacity-0');
        m.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('opacity-0');
    m.querySelector('div').classList.add('scale-95');
    setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
}

const statusBadge = {
    available:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>',
    low_stock:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Low Stock</span>',
    out_of_stock: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Out of Stock</span>',
};

const actionBadge = {
    restocked:         '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">Restocked</span>',
    used:              '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Used</span>',
    assigned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-teal-50 text-teal-700 border border-teal-200">Assigned</span>',
    returned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Returned</span>',
    condition_changed: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Condition Changed</span>',
    written_off:       '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Written Off</span>',
};

// ── Table ──────────────────────────────────────────────────────────

async function loadSupplies(page = 1) {
    document.getElementById('table-loading').classList.remove('hidden');
    document.getElementById('table-content').classList.add('hidden');
    document.getElementById('pagination').classList.add('hidden');

    try {
        const res = await axios.get(`/api/inventory-items?type=supply&page=${page}`);
        const items = res.data.data ?? [];
        allItems = items;
        lastMeta = res.data.meta ?? {};

        // Stats
        const total   = items.length;
        const low     = items.filter(i => i.stock?.status === 'low_stock').length;
        const out     = items.filter(i => i.stock?.status === 'out_of_stock').length;
        document.getElementById('stat-total').textContent = lastMeta.total ?? total;
        document.getElementById('stat-low').textContent   = low;
        document.getElementById('stat-out').textContent   = out;

        renderTable(items);

        if (lastMeta.last_page > 1) {
            document.getElementById('pagination').classList.remove('hidden');
            document.getElementById('page-info').textContent = `Page ${lastMeta.current_page} of ${lastMeta.last_page}`;
            document.getElementById('btn-prev').disabled = lastMeta.current_page <= 1;
            document.getElementById('btn-next').disabled = lastMeta.current_page >= lastMeta.last_page;
        }
    } catch (e) {
        adminToast('Failed to load supplies.', 'error');
    }

    document.getElementById('table-loading').classList.add('hidden');
    document.getElementById('table-content').classList.remove('hidden');
}

function renderTable(items) {
    const tbody = document.getElementById('supplies-tbody');
    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-slate-400 italic text-sm">No supply items found.</td></tr>`;
        return;
    }
    tbody.innerHTML = items.map(item => {
        const stock  = item.stock ?? {};
        const qty    = stock.quantity_on_hand != null ? parseFloat(stock.quantity_on_hand).toFixed(2) : '0.00';
        const status = stock.status ?? 'out_of_stock';
        const editBtn = canEditInventory
            ? `<button onclick="openItemModal(${JSON.stringify(item).replace(/"/g, '&quot;')})"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-[#A89070] rounded-md hover:bg-slate-100 transition-colors" title="Edit">
                <i class="bi bi-pencil"></i></button>
               <button onclick="deleteItem(${item.id}, '${escHtml(item.name)}')"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 rounded-md hover:bg-rose-50 transition-colors" title="Delete">
                <i class="bi bi-trash3"></i></button>`
            : '';
        return `<tr class="hover:bg-slate-50/30 transition-colors cursor-pointer" onclick="openHistoryPanel(${item.id}, '${escHtml(item.name)}')">
            <td class="px-5 py-3.5">
                <p class="font-medium text-slate-900">${escHtml(item.name)}</p>
                ${item.description ? `<p class="text-xs text-slate-400 truncate max-w-[180px]">${escHtml(item.description)}</p>` : ''}
            </td>
            <td class="px-5 py-3.5 text-slate-600">${escHtml(item.category?.name ?? '—')}</td>
            <td class="px-5 py-3.5 text-slate-500">${escHtml(item.unit ?? '—')}</td>
            <td class="px-5 py-3.5 text-slate-600">${item.minimum_stock ?? '0.00'}</td>
            <td class="px-5 py-3.5 font-medium text-slate-900">${qty} ${item.unit ? `<span class="text-slate-400 text-xs font-normal">${escHtml(item.unit)}</span>` : ''}</td>
            <td class="px-5 py-3.5">${statusBadge[status] ?? status}</td>
            <td class="px-5 py-3.5" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="openRestockModal(${item.id}, '${escHtml(item.name)}')"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-green-600 rounded-md hover:bg-green-50 transition-colors" title="Restock">
                        <i class="bi bi-plus-circle"></i></button>
                    <button onclick="openUseModal(${item.id}, '${escHtml(item.name)}')"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-600 rounded-md hover:bg-blue-50 transition-colors" title="Log usage">
                        <i class="bi bi-dash-circle"></i></button>
                    ${editBtn}
                </div>
            </td>
        </tr>`;
    }).join('');
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function changePage(dir) {
    currentPage += dir;
    loadSupplies(currentPage);
}

// ── Item Modal ─────────────────────────────────────────────────────

window.openItemModal = async function(item = null) {
    document.getElementById('item-id').value = item?.id ?? '';
    document.getElementById('item-name').value = item?.name ?? '';
    document.getElementById('item-unit').value = item?.unit ?? '';
    document.getElementById('item-min-stock').value = item?.minimum_stock ?? '';
    document.getElementById('item-description').value = item?.description ?? '';
    document.getElementById('item-modal-title').textContent = item ? 'Edit Supply Item' : 'Add Supply Item';

    // Load categories
    try {
        const res = await axios.get('/api/inventory-categories?type=supply');
        const cats = res.data.data ?? [];
        const sel = document.getElementById('item-category');
        sel.innerHTML = '<option value="">Select category...</option>' +
            cats.map(c => `<option value="${c.id}" ${item?.category_id == c.id ? 'selected' : ''}>${escHtml(c.name)}</option>`).join('');
    } catch {}

    openModal('item-modal');
};

window.closeItemModal = () => closeModal('item-modal');

window.saveItem = async function() {
    const id   = document.getElementById('item-id').value;
    const data = {
        name:          document.getElementById('item-name').value.trim(),
        category_id:   document.getElementById('item-category').value,
        type:          'supply',
        unit:          document.getElementById('item-unit').value.trim() || null,
        minimum_stock: document.getElementById('item-min-stock').value || null,
        description:   document.getElementById('item-description').value.trim() || null,
    };
    if (!data.name || !data.category_id) { adminToast('Name and category are required.', 'error'); return; }

    try {
        if (id) {
            await axios.put(`/api/inventory-items/${id}`, data);
        } else {
            await axios.post('/api/inventory-items', data);
        }
        closeItemModal();
        adminToast(id ? 'Item updated successfully.' : 'Item created successfully.', 'success');
        loadSupplies(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to save item.', 'error');
    }
};

window.deleteItem = async function(id, name) {
    if (!await adminConfirm(`Delete "${name}"? This cannot be undone.`, { confirmLabel: 'Delete', type: 'danger' })) return;
    try {
        await axios.delete(`/api/inventory-items/${id}`);
        adminToast('Item deleted.', 'success');
        loadSupplies(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to delete item.', 'error');
    }
};

// ── Restock Modal ──────────────────────────────────────────────────

window.openRestockModal = function(id, name) {
    document.getElementById('restock-item-id').value = id;
    document.getElementById('restock-item-name').textContent = name;
    document.getElementById('restock-quantity').value = '';
    document.getElementById('restock-cost').value = '';
    document.getElementById('restock-notes').value = '';
    openModal('restock-modal');
};

window.closeRestockModal = () => closeModal('restock-modal');

window.saveRestock = async function() {
    const id  = document.getElementById('restock-item-id').value;
    const qty = document.getElementById('restock-quantity').value;
    const cost = document.getElementById('restock-cost').value;
    if (!qty || !cost) { adminToast('Quantity and cost are required.', 'error'); return; }

    try {
        await axios.post(`/api/inventory-items/${id}/restock`, {
            quantity: parseFloat(qty),
            cost:     parseFloat(cost),
            notes:    document.getElementById('restock-notes').value.trim() || null,
        });
        closeRestockModal();
        adminToast('Stock updated successfully.', 'success');
        loadSupplies(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to restock.', 'error');
    }
};

// ── Use Modal ──────────────────────────────────────────────────────

window.openUseModal = async function(id, name) {
    document.getElementById('use-item-id').value = id;
    document.getElementById('use-item-name').textContent = name;
    document.getElementById('use-quantity').value = '';
    document.getElementById('use-notes').value = '';
    document.getElementById('use-room').innerHTML = '<option value="">No specific room</option>';

    try {
        const res = await axios.get('/api/rooms');
        const rooms = res.data.data ?? [];
        const sel = document.getElementById('use-room');
        rooms.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = `Room ${r.room_number}`;
            sel.appendChild(opt);
        });
    } catch {}

    openModal('use-modal');
};

window.closeUseModal = () => closeModal('use-modal');

window.saveUse = async function() {
    const id  = document.getElementById('use-item-id').value;
    const qty = document.getElementById('use-quantity').value;
    if (!qty) { adminToast('Quantity is required.', 'error'); return; }

    const roomId = document.getElementById('use-room').value;
    try {
        await axios.post(`/api/inventory-items/${id}/use`, {
            quantity: parseFloat(qty),
            room_id:  roomId ? parseInt(roomId) : null,
            notes:    document.getElementById('use-notes').value.trim() || null,
        });
        closeUseModal();
        adminToast('Usage logged successfully.', 'success');
        loadSupplies(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.errors?.quantity?.[0] ?? e.response?.data?.message ?? 'Failed to log usage.', 'error');
    }
};

// ── Category Modal ─────────────────────────────────────────────────

window.openCategoryModal = async function() {
    openModal('category-modal');
    loadCategories();
};

window.closeCategoryModal = () => closeModal('category-modal');

async function loadCategories() {
    const list = document.getElementById('category-list');
    list.innerHTML = '<div class="flex items-center justify-center py-8"><div class="w-5 h-5 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div></div>';
    try {
        const res = await axios.get('/api/inventory-categories?type=supply');
        const cats = res.data.data ?? [];
        if (!cats.length) {
            list.innerHTML = '<p class="text-sm text-slate-400 italic text-center py-4">No categories yet.</p>';
            return;
        }
        list.innerHTML = cats.map(c => `
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                <span class="text-sm font-medium text-slate-700">${escHtml(c.name)}</span>
                <button onclick="deleteCategory(${c.id}, '${escHtml(c.name)}')"
                    class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-500 rounded-md hover:bg-rose-50 transition-colors">
                    <i class="bi bi-trash3 text-xs"></i>
                </button>
            </div>`).join('');
    } catch {
        list.innerHTML = '<p class="text-sm text-rose-500 italic text-center py-4">Failed to load categories.</p>';
    }
}

window.addCategory = async function() {
    const name = document.getElementById('new-category-name').value.trim();
    if (!name) { adminToast('Category name is required.', 'error'); return; }
    try {
        await axios.post('/api/inventory-categories', { name, type: 'supply' });
        document.getElementById('new-category-name').value = '';
        adminToast('Category added.', 'success');
        loadCategories();
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to add category.', 'error');
    }
};

window.deleteCategory = async function(id, name) {
    if (!await adminConfirm(`Delete category "${name}"? This will fail if it has items.`, { confirmLabel: 'Delete', type: 'danger' })) return;
    try {
        await axios.delete(`/api/inventory-categories/${id}`);
        adminToast('Category deleted.', 'success');
        loadCategories();
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to delete category.', 'error');
    }
};

// ── History Panel ──────────────────────────────────────────────────

window.openHistoryPanel = async function(id, name) {
    document.getElementById('history-item-name').textContent = name;
    document.getElementById('history-loading').classList.remove('hidden');
    document.getElementById('history-content').classList.add('hidden');

    const panel = document.getElementById('history-panel');
    const drawer = document.getElementById('history-drawer');
    panel.classList.remove('hidden');
    setTimeout(() => drawer.classList.remove('translate-x-full'), 10);

    try {
        const res = await axios.get(`/api/inventory-items/${id}/stock/logs`);
        const logs = res.data.data ?? [];
        const tbody = document.getElementById('history-tbody');
        const empty = document.getElementById('history-empty');

        if (!logs.length) {
            tbody.innerHTML = '';
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
            tbody.innerHTML = logs.map(l => `
                <tr class="hover:bg-slate-50/30">
                    <td class="px-4 py-3">${actionBadge[l.action] ?? escHtml(l.action)}</td>
                    <td class="px-4 py-3 text-slate-700">${l.quantity != null ? parseFloat(l.quantity).toFixed(2) : '—'}</td>
                    <td class="px-4 py-3 text-slate-600">${l.room ? `Room ${escHtml(l.room.room_number)}` : '—'}</td>
                    <td class="px-4 py-3 text-slate-600">${escHtml(l.performed_by?.name ?? '—')}</td>
                    <td class="px-4 py-3 text-slate-600">${l.cost != null ? 'Rs. ' + parseFloat(l.cost).toLocaleString('en-IN', {maximumFractionDigits: 2}) : '—'}</td>
                    <td class="px-4 py-3 text-slate-400 text-xs whitespace-nowrap">${l.created_at ? new Date(l.created_at).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'}) : '—'}</td>
                </tr>`).join('');
        }
    } catch {
        adminToast('Failed to load stock history.', 'error');
    }

    document.getElementById('history-loading').classList.add('hidden');
    document.getElementById('history-content').classList.remove('hidden');
};

window.closeHistoryPanel = function() {
    const drawer = document.getElementById('history-drawer');
    drawer.classList.add('translate-x-full');
    setTimeout(() => document.getElementById('history-panel').classList.add('hidden'), 300);
};

// ── Init ───────────────────────────────────────────────────────────

['item-modal','restock-modal','use-modal','category-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

loadSupplies();
</script>
@endpush
