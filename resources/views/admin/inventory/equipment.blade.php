@extends('layouts.admin')

@section('title', 'Equipment - DwellCasa Admin')
@section('header_title', 'Equipment')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Equipment</h1>
        <p class="text-slate-500 mt-1">Track individual equipment units, assignments, and condition.</p>
    </div>
    @canany('manage inventory categories', 'manage inventory items')
    <div class="flex items-center gap-3">
        @can('manage inventory items')
        <button onclick="openEquipmentTypeModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-[#A89070] hover:bg-[#8E795E] text-white text-sm font-medium rounded-xl transition-colors">
            <i class="bi bi-plus-lg"></i> Add Equipment Type
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

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div id="table-loading" class="flex items-center justify-center py-16">
        <div class="w-7 h-7 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div id="table-content" class="hidden overflow-x-auto">
        <table class="w-full min-w-[800px] text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="px-5 py-3 font-medium w-8"></th>
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Total</th>
                    <th class="px-5 py-3 font-medium">Available</th>
                    <th class="px-5 py-3 font-medium">Assigned</th>
                    <th class="px-5 py-3 font-medium">Damaged</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="equipment-tbody" class="text-sm divide-y divide-slate-100"></tbody>
        </table>
    </div>
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

<!-- Add Equipment Type Modal -->
<div id="equip-type-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Add Equipment Type</h2>
            <button onclick="closeEquipmentTypeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name <span class="text-rose-500">*</span></label>
                <input type="text" id="etype-name" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="e.g. Television">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
                <select id="etype-category" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                    <option value="">Select category...</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea id="etype-description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeEquipmentTypeModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveEquipmentType()" class="px-5 py-2.5 rounded-xl font-medium bg-[#A89070] text-white hover:bg-[#967860] transition-colors text-sm">Save</button>
        </div>
    </div>
</div>

<!-- Add Unit Modal -->
<div id="unit-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic">Add Equipment Unit</h2>
                <p class="text-sm text-slate-500 mt-0.5" id="unit-item-name"></p>
            </div>
            <button onclick="closeUnitModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="unit-item-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Condition <span class="text-rose-500">*</span></label>
                    <select id="unit-condition" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                        <option value="new">New</option>
                        <option value="good" selected>Good</option>
                        <option value="fair">Fair</option>
                        <option value="damaged">Damaged</option>
                        <option value="under_repair">Under Repair</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <select id="unit-status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                        <option value="available" selected>Available</option>
                        <option value="assigned">Assigned</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Serial Number</label>
                <input type="text" id="unit-serial" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="Optional">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase Date</label>
                    <input type="date" id="unit-purchased-at" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase Cost</label>
                    <input type="number" id="unit-cost" min="0" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]" placeholder="0.00">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="unit-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeUnitModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveUnit()" class="px-5 py-2.5 rounded-xl font-medium bg-[#A89070] text-white hover:bg-[#967860] transition-colors text-sm">Add Unit</button>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div id="assign-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Assign to Room</h2>
            <button onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="assign-unit-id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Room <span class="text-rose-500">*</span></label>
                <select id="assign-room" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                    <option value="">Loading rooms...</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="assign-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeAssignModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveAssign()" class="px-5 py-2.5 rounded-xl font-medium bg-teal-600 text-white hover:bg-teal-700 transition-colors text-sm">
                <i class="bi bi-box-arrow-in-right mr-1.5"></i>Assign
            </button>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="return-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Return to Storage</h2>
            <button onclick="closeReturnModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="return-unit-id">
            <p class="text-sm text-slate-600">This unit will be returned from its room back to storage.</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="return-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeReturnModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveReturn()" class="px-5 py-2.5 rounded-xl font-medium bg-slate-700 text-white hover:bg-slate-800 transition-colors text-sm">
                <i class="bi bi-box-arrow-left mr-1.5"></i>Return
            </button>
        </div>
    </div>
</div>

<!-- Update Condition Modal -->
<div id="condition-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Update Condition</h2>
            <button onclick="closeConditionModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="condition-unit-id">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Condition <span class="text-rose-500">*</span></label>
                <select id="condition-value" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070]">
                    <option value="new">New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="damaged">Damaged</option>
                    <option value="under_repair">Under Repair</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea id="condition-notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeConditionModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveCondition()" class="px-5 py-2.5 rounded-xl font-medium bg-amber-600 text-white hover:bg-amber-700 transition-colors text-sm">
                <i class="bi bi-tools mr-1.5"></i>Update Condition
            </button>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="category-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[85vh]">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Equipment Categories</h2>
            <button onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="flex flex-col sm:flex-row flex-1 overflow-hidden">
            <div class="flex-1 overflow-y-auto border-b sm:border-b-0 sm:border-r border-slate-100 p-5">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Existing Categories</h3>
                <div id="category-list" class="space-y-2">
                    <div class="flex items-center justify-center py-8">
                        <div class="w-5 h-5 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>
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

<!-- Correct Assignment Modal -->
<div id="correct-modal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Undo Assignment</h2>
            <button onclick="closeCorrectModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-slate-600">This will return the equipment to storage and mark the original assignment as corrected.</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Reason <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea id="correct-reason" rows="2"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#A89070]/40 focus:border-[#A89070] resize-none"
                    placeholder="Why is this assignment being undone?"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-3">
            <button onclick="closeCorrectModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 transition-colors text-sm">Cancel</button>
            <button onclick="saveCorrect()" class="px-5 py-2.5 rounded-xl font-medium bg-rose-600 text-white hover:bg-rose-700 transition-colors text-sm">Undo Assignment</button>
        </div>
    </div>
</div>

<!-- Unit Detail Modal -->
<div id="unit-detail-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-3xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[92vh]">

        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-start bg-slate-50/50 flex-shrink-0">
            <div>
                <h2 class="text-xl font-serif font-bold text-slate-900 italic" id="udet-title">Unit Details</h2>
                <p class="text-slate-500 text-sm mt-0.5" id="udet-serial"></p>
            </div>
            <button onclick="closeUnitDetailModal()" class="text-slate-400 hover:text-slate-600 transition-colors mt-1">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Stats Row -->
        <div class="px-6 py-4 border-b border-slate-100 flex gap-8 flex-shrink-0">
            <div class="text-center">
                <div class="text-lg font-bold text-slate-900" id="udet-stat-room">—</div>
                <div class="text-sm text-slate-500 mt-0.5">Current Room</div>
            </div>
            <div class="text-center">
                <div class="mt-0.5" id="udet-stat-condition">—</div>
                <div class="text-sm text-slate-500 mt-1">Condition</div>
            </div>
            <div class="text-center">
                <div class="mt-0.5" id="udet-stat-status">—</div>
                <div class="text-sm text-slate-500 mt-1">Status</div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="px-6 py-4 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Details</h3>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div class="flex gap-2">
                    <span class="text-slate-500 flex-shrink-0">Purchase Date</span>
                    <span class="text-slate-900 font-medium" id="udet-purchased-at">—</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-slate-500 flex-shrink-0">Purchase Cost</span>
                    <span class="text-slate-900 font-medium" id="udet-cost">—</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-slate-500 flex-shrink-0">Location</span>
                    <span class="text-slate-900 font-medium" id="udet-location">—</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-slate-500 flex-shrink-0">Notes</span>
                    <span class="text-slate-900 font-medium" id="udet-notes">—</span>
                </div>
            </div>
        </div>

        <!-- Log History -->
        <div class="flex-1 overflow-y-auto">
            <div class="px-6 pt-5 pb-2 flex-shrink-0">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Log History</h3>
            </div>
            <div id="udet-log-loading" class="flex items-center justify-center py-10">
                <div class="w-6 h-6 border-2 border-[#A89070] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div id="udet-log-content" class="hidden overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                            <th class="px-6 py-3 font-medium">Action</th>
                            <th class="px-6 py-3 font-medium">Room</th>
                            <th class="px-6 py-3 font-medium">By</th>
                            <th class="px-6 py-3 font-medium">Condition Change</th>
                            <th class="px-6 py-3 font-medium">Notes</th>
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody id="udet-log-tbody" class="divide-y divide-slate-100"></tbody>
                </table>
                <p id="udet-log-empty" class="hidden text-center text-slate-400 italic py-8 text-sm">No log history for this unit.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-slate-100 flex justify-end bg-slate-50/50 flex-shrink-0">
            <button onclick="closeUnitDetailModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors">Close</button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
const canEditInventory = @json(auth()->user()->can('edit inventory'));
const canManageItems = @json(auth()->user()->can('manage inventory items'));
const canManageCategories = @json(auth()->user()->can('manage inventory categories'));

const staffWindowMinutes      = 30;
const adminWindowMinutes      = 1440;
const userRoles               = @json(auth()->user()->getRoleNames());
const isAdminOrSuper          = userRoles.includes('admin') || userRoles.includes('super_admin');
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

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let currentPage = 1;
let lastMeta = {};
let expandedItems = new Set();
let cachedItems = [];
let currentDetailUnitId  = null;
let currentCorrectUnitId = null;
let currentCorrectLogId  = null;

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

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

const conditionBadge = {
    new:         '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">New</span>',
    good:        '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Good</span>',
    fair:        '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Fair</span>',
    damaged:     '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Damaged</span>',
    under_repair:'<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Under Repair</span>',
};

const statusBadge = {
    available:   '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Available</span>',
    assigned:    '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Assigned</span>',
    maintenance: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Maintenance</span>',
    retired:     '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">Retired</span>',
};

const actionBadge = {
    restocked:         '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-200">Restocked</span>',
    used:              '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">Used</span>',
    assigned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-teal-50 text-teal-700 border border-teal-200">Assigned</span>',
    returned:          '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">Returned</span>',
    condition_changed: '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">Condition Changed</span>',
    written_off:       '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-sm font-medium bg-rose-50 text-rose-700 border border-rose-200">Written Off</span>',
};

// ── Table ──────────────────────────────────────────────────────────

async function loadEquipment(page = 1) {
    document.getElementById('table-loading').classList.remove('hidden');
    document.getElementById('table-content').classList.add('hidden');
    document.getElementById('pagination').classList.add('hidden');

    try {
        const res = await axios.get(`/api/inventory-items?type=equipment&page=${page}`);
        const items = res.data.data ?? [];
        lastMeta = res.data.meta ?? {};

        const promises = items.map(item =>
            axios.get(`/api/inventory-items/${item.id}/equipment`)
                .then(r => ({ ...item, units: r.data.data ?? [] }))
                .catch(() => ({ ...item, units: [] }))
        );
        const enriched = await Promise.all(promises);
        cachedItems = enriched;

        renderTable(enriched);
        expandedItems.forEach(id => injectSubRows(id));

        if (lastMeta.last_page > 1) {
            document.getElementById('pagination').classList.remove('hidden');
            document.getElementById('page-info').textContent = `Page ${lastMeta.current_page} of ${lastMeta.last_page}`;
            document.getElementById('btn-prev').disabled = lastMeta.current_page <= 1;
            document.getElementById('btn-next').disabled = lastMeta.current_page >= lastMeta.last_page;
        }
    } catch (e) {
        adminToast('Failed to load equipment.', 'error');
    }

    document.getElementById('table-loading').classList.add('hidden');
    document.getElementById('table-content').classList.remove('hidden');
}

function renderTable(items) {
    const tbody = document.getElementById('equipment-tbody');
    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-8 text-center text-slate-400 italic text-sm">No equipment types found.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(item => {
        const units    = item.units ?? [];
        const total    = units.length;
        const available= units.filter(u => u.status === 'available').length;
        const assigned = units.filter(u => u.status === 'assigned').length;
        const damaged  = units.filter(u => ['damaged','under_repair'].includes(u.condition)).length;

        const addUnitBtn = canManageItems
            ? `<button onclick="event.stopPropagation(); openUnitModal(${item.id}, '${escHtml(item.name)}')"
                class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-[#A89070] rounded-md hover:bg-slate-100 transition-colors" title="Add unit">
                <i class="bi bi-plus-circle"></i></button>`
            : '';

        return `<tr id="parent-row-${item.id}" class="hover:bg-slate-50/30 transition-colors cursor-pointer select-none" onclick="toggleItem(${item.id})">
            <td class="px-5 py-3.5">
                <i class="bi bi-chevron-right text-slate-400 text-sm transition-transform" id="chevron-${item.id}"></i>
            </td>
            <td class="px-5 py-3.5">
                <p class="font-medium text-slate-900">${escHtml(item.name)}</p>
            </td>
            <td class="px-5 py-3.5 text-slate-600">${escHtml(item.category?.name ?? '—')}</td>
            <td class="px-5 py-3.5 font-medium text-slate-900">${total}</td>
            <td class="px-5 py-3.5 text-green-700 font-medium">${available}</td>
            <td class="px-5 py-3.5 text-blue-700 font-medium">${assigned}</td>
            <td class="px-5 py-3.5 ${damaged > 0 ? 'text-rose-600 font-medium' : 'text-slate-500'}">${damaged}</td>
            <td class="px-5 py-3.5" onclick="event.stopPropagation()">
                <div class="flex items-center justify-end gap-1">${addUnitBtn}</div>
            </td>
        </tr>`;
    }).join('');
}

function buildSubRows(item) {
    return (item.units ?? []).map(unit => {
        const assignBtn = (canEditInventory && unit.status === 'available')
            ? `<button onclick="openAssignModal(${unit.id})" class="w-7 h-7 flex items-center justify-center text-teal-500 hover:text-teal-700 rounded-md hover:bg-teal-50 transition-colors" title="Assign"><i class="bi bi-box-arrow-in-right text-sm"></i></button>`
            : '';
        const returnBtn = (canEditInventory && unit.status === 'assigned')
            ? `<button onclick="openReturnModal(${unit.id})" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-700 rounded-md hover:bg-slate-100 transition-colors" title="Return"><i class="bi bi-box-arrow-left text-sm"></i></button>`
            : '';
        const condBtn = canEditInventory
            ? `<button onclick="openConditionModal(${unit.id}, '${escHtml(unit.condition)}')" class="w-7 h-7 flex items-center justify-center text-amber-500 hover:text-amber-700 rounded-md hover:bg-amber-50 transition-colors" title="Update condition"><i class="bi bi-tools text-sm"></i></button>`
            : '';
        const writeOffBtn = (canEditInventory && unit.status !== 'retired')
            ? `<button onclick="writeOffUnit(${unit.id})" class="w-7 h-7 flex items-center justify-center text-rose-400 hover:text-rose-600 rounded-md hover:bg-rose-50 transition-colors" title="Write off"><i class="bi bi-x-octagon text-sm"></i></button>`
            : '';
        const detailBtn  = `<button onclick="openUnitDetailModal(${unit.id})" class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-[#A89070] transition-colors rounded-md hover:bg-slate-100" title="View details"><i class="bi bi-eye"></i></button>`;

        return `<tr data-parent="${item.id}" class="bg-slate-50/40 hover:bg-slate-50 transition-colors border-l-2 border-[#A89070]/30">
            <td class="px-5 py-2.5"></td>
            <td class="px-5 py-2.5 text-slate-500 text-sm">${unit.serial_number ? escHtml(unit.serial_number) : '<span class="italic">No serial</span>'}</td>
            <td class="px-5 py-2.5 text-slate-600 text-sm">${unit.current_room ? `Room ${escHtml(unit.current_room.room_number)}` : 'Storage'}</td>
            <td class="px-5 py-2.5">${conditionBadge[unit.condition] ?? escHtml(unit.condition)}</td>
            <td class="px-5 py-2.5">${statusBadge[unit.status] ?? escHtml(unit.status)}</td>
            <td class="px-5 py-2.5 text-slate-500 text-sm">${unit.purchased_at ? new Date(unit.purchased_at).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'}) : '—'}</td>
            <td class="px-5 py-2.5" colspan="2">
                <div class="flex items-center justify-end gap-1">
                    ${assignBtn}${returnBtn}${condBtn}${writeOffBtn}${detailBtn}
                </div>
            </td>
        </tr>`;
    }).join('');
}

function injectSubRows(id) {
    const item = cachedItems.find(i => i.id === id);
    if (!item) return;
    const parentRow = document.getElementById(`parent-row-${id}`);
    if (!parentRow) return;
    const chevron = document.getElementById(`chevron-${id}`);
    if (chevron) {
        chevron.classList.remove('bi-chevron-right');
        chevron.classList.add('bi-chevron-down');
    }
    parentRow.insertAdjacentHTML('afterend', buildSubRows(item));
}

window.toggleItem = function(id) {
    if (expandedItems.has(id)) {
        expandedItems.delete(id);
        document.querySelectorAll(`tr[data-parent="${id}"]`).forEach(r => r.remove());
        const chevron = document.getElementById(`chevron-${id}`);
        if (chevron) {
            chevron.classList.remove('bi-chevron-down');
            chevron.classList.add('bi-chevron-right');
        }
    } else {
        expandedItems.add(id);
        injectSubRows(id);
    }
};

function changePage(dir) {
    currentPage += dir;
    loadEquipment(currentPage);
}

// ── Equipment Type Modal ───────────────────────────────────────────

window.openEquipmentTypeModal = async function() {
    document.getElementById('etype-name').value = '';
    document.getElementById('etype-description').value = '';
    try {
        const res = await axios.get('/api/inventory-categories?type=equipment');
        const cats = res.data.data ?? [];
        const sel = document.getElementById('etype-category');
        sel.innerHTML = '<option value="">Select category...</option>' +
            cats.map(c => `<option value="${c.id}">${escHtml(c.name)}</option>`).join('');
    } catch {}
    openModal('equip-type-modal');
};

window.closeEquipmentTypeModal = () => closeModal('equip-type-modal');

window.saveEquipmentType = async function() {
    const name  = document.getElementById('etype-name').value.trim();
    const catId = document.getElementById('etype-category').value;
    if (!name || !catId) { adminToast('Name and category are required.', 'error'); return; }
    try {
        await axios.post('/api/inventory-items', {
            name, category_id: catId, type: 'equipment',
            description: document.getElementById('etype-description').value.trim() || null,
        });
        closeEquipmentTypeModal();
        adminToast('Equipment type created.', 'success');
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to save.', 'error');
    }
};

// ── Unit Modal ─────────────────────────────────────────────────────

window.openUnitModal = function(itemId, itemName) {
    document.getElementById('unit-item-id').value = itemId;
    document.getElementById('unit-item-name').textContent = itemName;
    document.getElementById('unit-serial').value = '';
    document.getElementById('unit-condition').value = 'good';
    document.getElementById('unit-status').value = 'available';
    document.getElementById('unit-purchased-at').value = '';
    document.getElementById('unit-cost').value = '';
    document.getElementById('unit-notes').value = '';
    openModal('unit-modal');
};

window.closeUnitModal = () => closeModal('unit-modal');

window.saveUnit = async function() {
    const itemId = document.getElementById('unit-item-id').value;
    try {
        await axios.post('/api/inventory-equipment', {
            inventory_item_id: parseInt(itemId),
            serial_number:     document.getElementById('unit-serial').value.trim() || null,
            condition:         document.getElementById('unit-condition').value,
            status:            document.getElementById('unit-status').value,
            purchased_at:      document.getElementById('unit-purchased-at').value || null,
            purchase_cost:     document.getElementById('unit-cost').value ? parseFloat(document.getElementById('unit-cost').value) : null,
            notes:             document.getElementById('unit-notes').value.trim() || null,
        });
        closeUnitModal();
        adminToast('Unit added.', 'success');
        expandedItems.add(parseInt(itemId));
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to add unit.', 'error');
    }
};

// ── Assign Modal ───────────────────────────────────────────────────

window.openAssignModal = async function(unitId) {
    document.getElementById('assign-unit-id').value = unitId;
    document.getElementById('assign-notes').value = '';
    const sel = document.getElementById('assign-room');
    sel.innerHTML = '<option value="">Loading rooms...</option>';
    try {
        const res = await axios.get('/api/rooms');
        const rooms = res.data.data ?? [];
        sel.innerHTML = '<option value="">Select room...</option>' +
            rooms.map(r => `<option value="${r.id}">Room ${escHtml(r.room_number)}</option>`).join('');
    } catch { sel.innerHTML = '<option value="">Failed to load rooms</option>'; }
    openModal('assign-modal');
};

window.closeAssignModal = () => closeModal('assign-modal');

window.saveAssign = async function() {
    const id = document.getElementById('assign-unit-id').value;
    const roomId = document.getElementById('assign-room').value;
    if (!roomId) { adminToast('Please select a room.', 'error'); return; }
    try {
        await axios.post(`/api/inventory-equipment/${id}/assign`, {
            room_id: parseInt(roomId),
            notes:   document.getElementById('assign-notes').value.trim() || null,
        });
        closeAssignModal();
        adminToast('Equipment assigned successfully.', 'success');
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to assign.', 'error');
    }
};

// ── Return Modal ───────────────────────────────────────────────────

window.openReturnModal = function(unitId) {
    document.getElementById('return-unit-id').value = unitId;
    document.getElementById('return-notes').value = '';
    openModal('return-modal');
};

window.closeReturnModal = () => closeModal('return-modal');

window.saveReturn = async function() {
    const id = document.getElementById('return-unit-id').value;
    try {
        await axios.post(`/api/inventory-equipment/${id}/return`, {
            notes: document.getElementById('return-notes').value.trim() || null,
        });
        closeReturnModal();
        adminToast('Equipment returned successfully.', 'success');
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to return equipment.', 'error');
    }
};

// ── Condition Modal ────────────────────────────────────────────────

window.openConditionModal = function(unitId, currentCondition) {
    document.getElementById('condition-unit-id').value = unitId;
    document.getElementById('condition-value').value = currentCondition ?? 'good';
    document.getElementById('condition-notes').value = '';
    openModal('condition-modal');
};

window.closeConditionModal = () => closeModal('condition-modal');

window.saveCondition = async function() {
    const id = document.getElementById('condition-unit-id').value;
    try {
        await axios.patch(`/api/inventory-equipment/${id}/condition`, {
            condition: document.getElementById('condition-value').value,
            notes:     document.getElementById('condition-notes').value.trim() || null,
        });
        closeConditionModal();
        adminToast('Condition updated successfully.', 'success');
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to update condition.', 'error');
    }
};

// ── Write Off ──────────────────────────────────────────────────────

window.writeOffUnit = async function(id) {
    if (!await adminConfirm('Write off this unit? It will be soft-deleted and marked retired.', { confirmLabel: 'Write Off', type: 'danger' })) return;
    try {
        await axios.delete(`/api/inventory-equipment/${id}/write-off`);
        adminToast('Equipment written off.', 'success');
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to write off.', 'error');
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
        const res = await axios.get('/api/inventory-categories?type=equipment');
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
        await axios.post('/api/inventory-categories', { name, type: 'equipment' });
        document.getElementById('new-category-name').value = '';
        adminToast('Category added.', 'success');
        loadCategories();
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to add category.', 'error');
    }
};

window.deleteCategory = async function(id, name) {
    if (!await adminConfirm(`Delete category "${name}"?`, { confirmLabel: 'Delete', type: 'danger' })) return;
    try {
        await axios.delete(`/api/inventory-categories/${id}`);
        adminToast('Category deleted.', 'success');
        loadCategories();
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to delete category.', 'error');
    }
};

// ── Unit Detail Modal ──────────────────────────────────────────────

window.openUnitDetailModal = async function(id) {
    let foundUnit = null, foundItem = null;
    for (const item of cachedItems) {
        const u = (item.units ?? []).find(u => u.id === id);
        if (u) { foundUnit = u; foundItem = item; break; }
    }
    if (!foundUnit) return;

    document.getElementById('udet-title').textContent = foundItem.name;
    document.getElementById('udet-serial').textContent = foundUnit.serial_number || 'No serial number';
    document.getElementById('udet-stat-room').textContent = foundUnit.current_room
        ? `Room ${foundUnit.current_room.room_number}` : 'Storage';
    document.getElementById('udet-stat-condition').innerHTML = conditionBadge[foundUnit.condition] ?? escHtml(foundUnit.condition);
    document.getElementById('udet-stat-status').innerHTML   = statusBadge[foundUnit.status]    ?? escHtml(foundUnit.status);

    document.getElementById('udet-purchased-at').textContent = foundUnit.purchased_at
        ? new Date(foundUnit.purchased_at).toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'})
        : '—';
    document.getElementById('udet-cost').textContent = foundUnit.purchase_cost
        ? 'Rs. ' + parseFloat(foundUnit.purchase_cost).toLocaleString('en-IN', {maximumFractionDigits: 2})
        : '—';
    document.getElementById('udet-location').textContent = foundItem.location?.name ?? '—';
    document.getElementById('udet-notes').textContent = foundUnit.notes || '—';

    currentDetailUnitId = id;
    document.getElementById('udet-log-loading').classList.remove('hidden');
    document.getElementById('udet-log-content').classList.add('hidden');
    openModal('unit-detail-modal');

    try {
        const res  = await axios.get(`/api/inventory-equipment/${id}/logs`);
        const logs = res.data.data ?? [];
        const tbody = document.getElementById('udet-log-tbody');
        const empty = document.getElementById('udet-log-empty');

        if (!logs.length) {
            tbody.innerHTML = '';
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
            tbody.innerHTML = logs.map(l => {
                const condChange = (l.previous_condition && l.new_condition)
                    ? `${escHtml(l.previous_condition)} → ${escHtml(l.new_condition)}`
                    : '—';
                return `<tr class="${l.action === 'corrected' ? 'text-slate-400' : 'hover:bg-slate-50/30'}">
                    <td class="px-6 py-3">${actionBadge[l.action] ?? escHtml(l.action)}</td>
                    <td class="px-6 py-3 text-slate-600">${l.room ? `Room ${escHtml(l.room.room_number)}` : '—'}</td>
                    <td class="px-6 py-3 text-slate-600">${escHtml(l.performed_by?.name ?? '—')}</td>
                    <td class="px-6 py-3 text-slate-500 text-sm">${condChange}</td>
                    <td class="px-6 py-3 text-slate-500 text-sm max-w-[140px] truncate">${escHtml(l.notes ?? '—')}</td>
                    <td class="px-6 py-3 text-slate-400 text-sm whitespace-nowrap">${l.created_at ? formatLogDate(l.created_at) : '—'}</td>
                    ${l.action === 'assigned' && isWithinWindow(l.created_at) ? `<td class="px-6 py-3"><button onclick="openCorrectModal(${id}, ${l.id})" class="text-xs px-2 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition-colors">Undo</button></td>` : '<td class="px-6 py-3 text-slate-300 text-xs">—</td>'}
                </tr>`;
            }).join('');
        }
    } catch {
        adminToast('Failed to load unit log history.', 'error');
    }

    document.getElementById('udet-log-loading').classList.add('hidden');
    document.getElementById('udet-log-content').classList.remove('hidden');
};

window.closeUnitDetailModal = () => closeModal('unit-detail-modal');

// ── Correct Modal ──────────────────────────────────────────────────

window.openCorrectModal = function(unitId, logId) {
    currentCorrectUnitId = unitId;
    currentCorrectLogId  = logId;
    document.getElementById('correct-reason').value = '';
    openModal('correct-modal');
};

window.closeCorrectModal = () => closeModal('correct-modal');

window.saveCorrect = async function() {
    const reason = document.getElementById('correct-reason').value.trim();
    try {
        await axios.post(`/api/inventory-equipment/${currentCorrectUnitId}/correct`, {
            original_log_id: currentCorrectLogId,
            reason: reason || null,
        });
        adminToast('Assignment undone successfully.', 'success');
        closeCorrectModal();
        openUnitDetailModal(currentCorrectUnitId);
        loadEquipment(currentPage);
    } catch (e) {
        adminToast(e.response?.data?.message ?? 'Failed to undo assignment.', 'error');
    }
};

// ── Init ───────────────────────────────────────────────────────────

['equip-type-modal','unit-modal','assign-modal','return-modal','condition-modal','category-modal','unit-detail-modal','correct-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

loadEquipment();
</script>
@endpush
