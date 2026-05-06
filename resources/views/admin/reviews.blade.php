@extends('layouts.admin')

@section('title', 'Reviews - DwellCasa Admin')
@section('header_title', 'Reviews')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Reviews Management</h1>
        <p class="text-slate-500 mt-1">View and moderate guest reviews for your property and rooms.</p>
    </div>
</div>

<!-- List Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Date</th>
                    <th class="p-4 font-medium">Reviewer</th>
                    <th class="p-4 font-medium">Rating</th>
                    <th class="p-4 font-medium">Review Snapshot</th>
                    <th class="p-4 font-medium">Type</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($reviews ?? [] as $review)
                @php
                $targetName = $review->type === 'room_type' && $review->roomType ? $review->roomType->name : 'Hotel';
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4 text-slate-700 whitespace-nowrap">
                        {{ $review->created_at ? \Carbon\Carbon::parse($review->created_at)->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-900">{{ $review->name }}</div>
                        <div class="text-slate-500 text-xs">{{ $review->email }}</div>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <=$review->rating)
                                <i class="bi bi-star-fill"></i>
                                @else
                                <i class="bi bi-star text-slate-300"></i>
                                @endif
                                @endfor
                        </div>
                    </td>
                    <td class="p-4 text-slate-600 max-w-md truncate">
                        {{ \Illuminate\Support\Str::limit($review->body, 50) }}
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 capitalize">
                            {{ $targetName }}
                        </span>
                    </td>
                    <td class="p-4">
                        @if($review->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Approved</span>
                        @elseif($review->status === 'rejected')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200">Rejected</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="view-review-btn w-8 h-8 flex items-center justify-center text-slate-400 hover:text-primary transition-colors font-medium rounded-md hover:bg-slate-100"
                                data-review="{{ json_encode(array_merge($review->toArray(), ['target_name' => $targetName])) }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="delete-review-btn w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors font-medium rounded-md hover:bg-red-50"
                                data-id="{{ $review->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-500">
                        No reviews found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- View Modal -->
<div id="view-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Review Details</h2>
            <button type="button" onclick="closeViewModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Reviewer</label>
                    <p class="font-medium text-slate-900" id="modal-name"></p>
                    <p class="text-slate-600 text-sm" id="modal-email"></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Details</label>
                    <p class="font-medium text-primary capitalize" id="modal-type"></p>
                    <div id="modal-rating" class="flex items-center text-yellow-400 text-sm mt-1"></div>
                    <p class="text-slate-600 text-sm mt-1" id="modal-date"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Review Content</label>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-slate-700 text-sm whitespace-pre-wrap leading-relaxed" id="modal-body"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex gap-4">
                <input type="hidden" id="modal-review-id">
                <button type="button" id="btn-approve" class="flex-1 py-3 rounded-xl font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                    <i class="bi bi-check-circle mr-2"></i> Approve Review
                </button>
                <button type="button" id="btn-reject" class="flex-1 py-3 rounded-xl font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border border-red-200">
                    <i class="bi bi-x-circle mr-2"></i> Reject Review
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle View Modal
    document.querySelectorAll('.view-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const review = JSON.parse(this.dataset.review);

            // Populate view section
            document.getElementById('modal-review-id').value = review.id;
            document.getElementById('modal-name').textContent = review.name;
            document.getElementById('modal-email').textContent = review.email;
            document.getElementById('modal-type').textContent = review.target_name || 'Hotel';
            document.getElementById('modal-date').textContent = new Date(review.created_at).toLocaleString();
            document.getElementById('modal-body').textContent = review.body;

            // Rating stars
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= review.rating) {
                    starsHtml += '<i class="bi bi-star-fill mr-1"></i>';
                } else {
                    starsHtml += '<i class="bi bi-star text-slate-300 mr-1"></i>';
                }
            }
            document.getElementById('modal-rating').innerHTML = starsHtml;

            // Highlight active status
            const btnApprove = document.getElementById('btn-approve');
            const btnReject = document.getElementById('btn-reject');

            if (review.status === 'approved') {
                btnApprove.classList.replace('bg-green-50', 'bg-green-600');
                btnApprove.classList.replace('text-green-700', 'text-white');
                btnReject.classList.replace('bg-red-600', 'bg-red-50');
                btnReject.classList.replace('text-white', 'text-red-700');
            } else if (review.status === 'rejected') {
                btnReject.classList.replace('bg-red-50', 'bg-red-600');
                btnReject.classList.replace('text-red-700', 'text-white');
                btnApprove.classList.replace('bg-green-600', 'bg-green-50');
                btnApprove.classList.replace('text-white', 'text-green-700');
            } else {
                btnApprove.classList.replace('bg-green-600', 'bg-green-50');
                btnApprove.classList.replace('text-white', 'text-green-700');
                btnReject.classList.replace('bg-red-600', 'bg-red-50');
                btnReject.classList.replace('text-white', 'text-red-700');
            }

            // Open modal
            const modal = document.getElementById('view-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
        });
    });

    window.closeViewModal = function() {
        const modal = document.getElementById('view-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    };

    // Handle Status Update (Approve/Reject)
    async function updateStatus(status) {
        const id = document.getElementById('modal-review-id').value;
        try {
            const response = await fetch(`/api/reviews/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json();
                adminToast('Error updating status: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            adminToast('An error occurred while updating the review status.');
        }
    }

    document.getElementById('btn-approve').addEventListener('click', () => updateStatus('approved'));
    document.getElementById('btn-reject').addEventListener('click', () => updateStatus('rejected'));

    // Handle Delete
    document.querySelectorAll('.delete-review-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!await adminConfirm('Are you sure you want to delete this review? This action cannot be undone.', { confirmLabel: 'Delete', type: 'danger' })) return;

            const id = this.dataset.id;

            try {
                const response = await fetch(`/api/reviews/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const error = await response.json();
                    adminToast('Error deleting review: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                adminToast('An error occurred while deleting the review.');
            }
        });
    });
</script>
@endpush