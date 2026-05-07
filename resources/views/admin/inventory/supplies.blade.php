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
    @canany('manage inventory categories', 'manage inventory items')
    <div class="flex items-center gap-3">
        @can('manage inventory items')
        <button onclick="openItemModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-[#A89070] hover:bg-[#8E795E] text-white text-sm font-medium rounded-xl transition-colors">
            <i class="bi bi-plus-lg"></i> Add Supply
        </button>
        @endcan
        @can('manage inventory categories')
        <button onclick="openCategoryModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-xl border border-slate-200 transition-colors">
            <i class="bi bi-tags"></i> Manage Categories
        </button>
        @endcan
    </div>
    @endcanany
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
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
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
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Total Cost <span class="text-rose-500">*</span></label>
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
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Existing Categories</h3>
                <div id="category-list" class="space-y-2">
                    <div class="flex items-center justify-center py-8">
                        <div class="w-5 h-5 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>
            <!-- Add Category Form -->
            <div class="w-full sm:w-64 flex-shrink-0 p-5">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Add Category</h3>
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

<!-- Supply Log Modal -->
<div id="supply-log-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-3xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[92vh]">

        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic" id="slm-title">— Stock History</h2>
                <p class="text-slate-500 text-sm mt-0.5" id="slm-subtitle"></p>
            </div>
            <button onclick="closeSupplyLogModal()" class="text-slate-400 hover:text-slate-600 transition-colors mt-1">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Stats Row -->
        <div class="px-6 py-4 border-b border-slate-100 flex gap-8 flex-shrink-0">
            <div class="text-center">
                <div class="text-lg font-bold text-slate-900" id="slm-stat-qty">—</div>
                <div class="text-sm text-slate-500 mt-0.5">Current Stock</div>
            </div>
            <div class="text-center">
                <div class="mt-0.5" id="slm-stat-status">—</div>
                <div class="text-sm text-slate-500 mt-1">Status</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-bold text-slate-900" id="slm-stat-cost">—</div>
                <div class="text-sm text-slate-500 mt-0.5">Total Cost</div>
            </div>
        </div>

        <!-- Log History -->
        <div class="flex-1 overflow-y-auto">
            <div id="slm-loading" class="flex items-center justify-center py-10">
                <div class="w-6 h-6 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div id="slm-content" class="hidden overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                            <th class="px-6 py-3 font-medium">Action</th>
                            <th class="px-6 py-3 font-medium">Qty</th>
                            <th class="px-6 py-3 font-medium">Room</th>
                            <th class="px-6 py-3 font-medium">By</th>
                            <th class="px-6 py-3 font-medium">Cost</th>
                            <th class="px-6 py-3 font-medium">Notes</th>
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody id="slm-tbody" class="divide-y divide-slate-100"></tbody>
                </table>
                <p id="slm-empty" class="hidden text-center text-slate-400 italic py-8 text-sm">No stock history found.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-slate-100 flex justify-end bg-slate-50/50 flex-shrink-0">
            <button onclick="closeSupplyLogModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors">Close</button>
        </div>

    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjust-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Adjust Stock Usage</h2>
            <button onclick="closeAdjustModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="p-3 bg-slate-50 rounded-xl text-sm text-slate-600">
                Original quantity logged: <span class="font-semibold text-slate-900" id="adjust-original-qty">—</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Adjustment <span class="text-rose-500">*</span>
                    <span class="text-slate-400 font-normal">(+ to add back, − to remove more)</span>
                </label>
                <input type="number" id="adjust-amount" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]"
                    placeholder="e.g. 2 or -1.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Reason <span class="text-rose-500">*</span></label>
                <textarea id="adjust-reason" rows="2"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"
                    placeholder="Why is this adjustment being made?"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeAdjustModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveAdjust()" class="px-5 py-2.5 rounded-xl font-medium bg-amber-600 text-white hover:bg-amber-700 transition-colors text-sm">Save Adjustment</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const canEditInventory = @json(auth()->user()->can('edit inventory'));
const canManageItems = @json(auth()->user()->can('manage inventory items'));
const canManageCategories = @json(auth()->user()->can('manage inventory categories'));
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const staffWindowMinutes   = 30;
const adminWindowMinutes   = 1440;
const userRoles            = @json(auth()->user()->getRoleNames());
const isAdminOrSuper       = userRoles.includes('admin') || userRoles.includes('super_admin');
const correctionWindowMinutes = isAdminOrSuper ? adminWindowMinutes : staffWindowMinutes;

function isWithinWindow(createdAt) {
    const created = new Date(createdAt);
    const now     = new Date();
    const diffMinutes = (now - created) / 1000 / 60;
    return diffMinutes <= correctionWindowMinutes;
}

function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'Just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}

function formatLogDate(dateStr) {
    const d = new Date(dateStr);
    const relative = timeAgo(d);
    const full = d.toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric'
    }) + ', ' + d.toLocaleTimeString('en-GB', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
    return `<span title="${full}" class="cursor-help border-b border-dashed border-slate-300">${relative}</span>`;
}

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
    available:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Available</span>',
    low_stock:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Low Stock</span>',
    out_of_stock: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Out of Stock</span>',
};

const actionBadge = {
    restocked:         '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Restocked</span>',
    used:              '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Used</span>',
    assigned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-teal-50 text-teal-700 border border-teal-200">Assigned</span>',
    returned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">Returned</span>',
    condition_changed: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Condition Changed</span>',
    written_off:       '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Written Off</span>',
    adjusted:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Adjusted</span>',
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
        const editBtn = canManageItems
            ? `<button onclick="openItemModal(${JSON.stringify(item).replace(/"/g, '&quot;')})"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-[#A89070] rounded-md hover:bg-slate-100 transition-colors" title="Edit">
                <i class="bi bi-pencil"></i></button>
               <button onclick="deleteItem(${item.id}, '${escHtml(item.name)}')"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 rounded-md hover:bg-rose-50 transition-colors" title="Delete">
                <i class="bi bi-trash3"></i></button>`
            : '';
        return `<tr class="hover:bg-slate-50/30 transition-colors cursor-pointer" onclick="openSupplyLogModal(${item.id}, '${escHtml(item.name)}')">
            <td class="px-5 py-3.5">
                <p class="font-medium text-slate-900">${escHtml(item.name)}</p>
                ${item.description ? `<p class="text-sm text-slate-400 truncate max-w-[180px]">${escHtml(item.description)}</p>` : ''}
            </td>
            <td class="px-5 py-3.5 text-slate-600">${escHtml(item.category?.name ?? '—')}</td>
            <td class="px-5 py-3.5 text-slate-500">${escHtml(item.unit ?? '—')}</td>
            <td class="px-5 py-3.5 text-slate-600">${item.minimum_stock ?? '0.00'}</td>
            <td class="px-5 py-3.5 font-medium text-slate-900">${qty} ${item.unit ? `<span class="text-slate-400 text-sm font-normal">${escHtml(item.unit)}</span>` : ''}</td>
            <td class="px-5 py-3.5">${statusBadge[status] ?? status}</td>
            <td class="px-5 py-3.5" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-1">
                    ${canEditInventory ? `<button onclick="openRestockModal(${item.id}, '${escHtml(item.name)}')"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-green-600 rounded-md hover:bg-green-50 transition-colors" title="Restock">
                        <i class="bi bi-plus-circle"></i></button>
                    <button onclick="openUseModal(${item.id}, '${escHtml(item.name)}')"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-600 rounded-md hover:bg-blue-50 transition-colors" title="Log usage">
                        <i class="bi bi-dash-circle"></i></button>` : ''}
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
        const res = await axios.get('/api/rooms/for-inventory');
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
                    <i class="bi bi-trash3 text-sm"></i>
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

// ── Supply Log Modal ──────────────────────────────────────────────

let currentSupplyLogItemId = null;

window.openSupplyLogModal = async function(id, name) {
    currentSupplyLogItemId = id;

    const item   = allItems.find(i => i.id === id);
    const stock  = item?.stock ?? {};
    const qty    = stock.quantity_on_hand != null ? parseFloat(stock.quantity_on_hand).toFixed(2) : '0.00';
    const status = stock.status ?? 'out_of_stock';
    const unit   = item?.unit ?? '';

    document.getElementById('slm-title').textContent = name + ' — Stock History';
    document.getElementById('slm-subtitle').innerHTML = escHtml(qty + (unit ? ' ' + unit : '')) + ' &nbsp;·&nbsp; ' + (statusBadge[status] ?? escHtml(status));
    document.getElementById('slm-stat-qty').textContent = qty + (unit ? ' ' + unit : '');
    document.getElementById('slm-stat-status').innerHTML = statusBadge[status] ?? escHtml(status);
    document.getElementById('slm-stat-cost').textContent = '—';

    document.getElementById('slm-loading').classList.remove('hidden');
    document.getElementById('slm-content').classList.add('hidden');
    openModal('supply-log-modal');

    try {
        const res  = await axios.get(`/api/inventory-items/${id}/stock/logs`);
        const logs = res.data.data ?? [];

        const totalCost = logs
            .filter(l => l.action === 'restocked' && l.cost != null)
            .reduce((sum, l) => sum + parseFloat(l.cost), 0);
        document.getElementById('slm-stat-cost').textContent = totalCost > 0
            ? 'Rs. ' + totalCost.toLocaleString('en-IN', {maximumFractionDigits: 2})
            : '—';

        const tbody = document.getElementById('slm-tbody');
        const empty = document.getElementById('slm-empty');

        if (!logs.length) {
            tbody.innerHTML = '';
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
            tbody.innerHTML = logs.map(l => {
                let qtyStr = '—';
                if (l.quantity != null) {
                    const q = parseFloat(l.quantity);
                    if (l.action === 'restocked') qtyStr = '+' + q.toFixed(2);
                    else if (l.action === 'used') qtyStr = '-' + q.toFixed(2);
                    else if (l.action === 'adjusted') qtyStr = (q >= 0 ? '+' : '') + q.toFixed(2);
                    else qtyStr = q.toFixed(2);
                }
                const notesStr = l.action === 'adjusted' && !l.notes ? 'Corrects a previous entry' : escHtml(l.notes ?? '—');
                const adjustCell = l.action === 'used' && isWithinWindow(l.created_at)
                    ? `<button onclick="openAdjustModal(${id}, ${l.id}, ${l.quantity ?? 0})" class="text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">Adjust</button>`
                    : '<span class="text-slate-300 text-xs">—</span>';
                return `<tr class="${l.action === 'adjusted' ? 'text-slate-400' : 'hover:bg-slate-50/30'}">
                    <td class="px-6 py-3">${actionBadge[l.action] ?? escHtml(l.action)}</td>
                    <td class="px-6 py-3 text-slate-700">${qtyStr}</td>
                    <td class="px-6 py-3 text-slate-600">${l.room ? `Room ${escHtml(l.room.room_number)}` : '—'}</td>
                    <td class="px-6 py-3 text-slate-600">${escHtml(l.performed_by?.name ?? '—')}</td>
                    <td class="px-6 py-3 text-slate-600">${l.cost != null ? 'Rs. ' + parseFloat(l.cost).toLocaleString('en-IN', {maximumFractionDigits: 2}) : '—'}</td>
                    <td class="px-6 py-3 text-slate-500 text-sm max-w-[140px] truncate">${notesStr}</td>
                    <td class="px-6 py-3 text-slate-400 text-sm whitespace-nowrap">${l.created_at ? formatLogDate(l.created_at) : '—'}</td>
                    <td class="px-6 py-3">${adjustCell}</td>
                </tr>`;
            }).join('');
        }
    } catch {
        adminToast('Failed to load stock history.', 'error');
    }

    document.getElementById('slm-loading').classList.add('hidden');
    document.getElementById('slm-content').classList.remove('hidden');
};

window.closeSupplyLogModal = () => closeModal('supply-log-modal');

// ── Adjust Modal ───────────────────────────────────────────────────

let currentAdjustItemId = null;
let currentAdjustLogId  = null;

window.openAdjustModal = function(itemId, logId, quantity) {
    currentAdjustItemId = itemId;
    currentAdjustLogId  = logId;
    document.getElementById('adjust-original-qty').textContent = parseFloat(quantity).toFixed(2);
    document.getElementById('adjust-amount').value  = '';
    document.getElementById('adjust-reason').value  = '';
    openModal('adjust-modal');
};

window.closeAdjustModal = () => closeModal('adjust-modal');

window.saveAdjust = async function() {
    const adjustment = parseFloat(document.getElementById('adjust-amount').value);
    const reason     = document.getElementById('adjust-reason').value.trim();

    if (!adjustment || adjustment === 0) {
        adminToast('Please enter a non-zero adjustment amount.', 'error');
        return;
    }
    if (!reason) {
        adminToast('Reason is required.', 'error');
        return;
    }

    try {
        await axios.post(`/api/inventory-items/${currentAdjustItemId}/adjust`, {
            original_log_id: currentAdjustLogId,
            adjustment,
            reason,
        });
        adminToast('Stock adjusted successfully.', 'success');
        closeAdjustModal();
        const adjItem = allItems.find(i => i.id === currentAdjustItemId);
        openSupplyLogModal(currentAdjustItemId, adjItem?.name ?? '');
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to adjust stock.', 'error');
    }
};

// ── Init ───────────────────────────────────────────────────────────

['item-modal','restock-modal','use-modal','category-modal','adjust-modal','supply-log-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

loadSupplies();
</script>
@endpush
