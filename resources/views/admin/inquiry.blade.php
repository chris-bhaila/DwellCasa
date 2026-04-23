@extends('layouts.admin')

@section('title', 'Inquiries - DwellCasa Admin')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Inquiries Management</h1>
        <p class="text-slate-500 mt-1">View and manage messages submitted through the website contact form.</p>
    </div>
</div>

<!-- List Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Date</th>
                    <th class="p-4 font-medium">Contact Details</th>
                    <th class="p-4 font-medium">Type</th>
                    <th class="p-4 font-medium">Message Snapshot</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($inquiries ?? [] as $inquiry)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4 text-slate-700 whitespace-nowrap">
                        {{ $inquiry->created_at ? \Carbon\Carbon::parse($inquiry->created_at)->format('M d, Y') : 'N/A' }}
                        <div class="text-xs text-slate-400 mt-0.5">{{ $inquiry->created_at ? \Carbon\Carbon::parse($inquiry->created_at)->format('h:i A') : '' }}</div>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-900 mb-0.5">{{ $inquiry->name }}</div>
                        <div class="text-slate-500 text-xs">{{ $inquiry->email }}</div>
                        @if($inquiry->phone)
                        <div class="text-slate-500 text-xs mt-0.5"><i class="bi bi-telephone text-slate-400 mr-1"></i>{{ $inquiry->phone }}</div>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 capitalize">
                            {{ str_replace('_', ' ', $inquiry->inquiry_type) }}
                        </span>
                    </td>
                    <td class="p-4 text-slate-600 max-w-md truncate">
                        {{ \Illuminate\Support\Str::limit($inquiry->message, 60) }}
                    </td>
                    <td class="p-4">
                        @if($inquiry->status === 'replied')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">Replied</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Unreplied</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="view-inquiry-btn w-8 h-8 flex items-center justify-center text-slate-400 hover:text-primary transition-colors font-medium rounded-md hover:bg-slate-100"
                                data-inquiry="{{ json_encode($inquiry) }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="delete-inquiry-btn w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors font-medium rounded-md hover:bg-red-50"
                                data-id="{{ $inquiry->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        No inquiries found. When guests submit the contact form, they will appear here.
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
            <h2 class="text-xl font-serif font-bold text-slate-900 italic">Inquiry Details</h2>
            <button type="button" onclick="closeViewModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">From</label>
                    <p class="font-medium text-slate-900" id="modal-name"></p>
                    <p class="text-slate-600 text-sm" id="modal-email"></p>
                    <p class="text-slate-600 text-sm" id="modal-phone"></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Type & Date</label>
                    <p class="font-medium text-primary capitalize" id="modal-type"></p>
                    <p class="text-slate-600 text-sm" id="modal-date"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Message</label>
                <div class="bg-slate-50 rounded-xl p-4 text-slate-700 text-sm whitespace-pre-wrap leading-relaxed" id="modal-message"></div>
            </div>

            <!-- Reply Form Section (Initially Hidden) -->
            <div id="reply-section" class="hidden pt-6 border-t border-slate-200 mt-6">
                <form id="reply-form" onsubmit="return false;">
                    <input type="hidden" id="reply-inquiry-id">
                    <h3 class="text-lg font-serif font-bold text-slate-800 italic mb-4">Reply to this Inquiry</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="reply-subject" class="block text-sm font-medium text-slate-700 mb-1">Subject</label>
                            <input type="text" id="reply-subject" name="subject" class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors" required>
                        </div>
                        <div>
                            <label for="reply-message" class="block text-sm font-medium text-slate-700 mb-1">Your Message</label>
                            <textarea id="reply-message" name="message" rows="5" class="w-full rounded-xl border-slate-200 px-4 py-2.5 border focus:ring-primary focus:border-primary transition-colors" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50/50">
            <button type="button" onclick="closeViewModal()" class="px-6 py-2.5 rounded-xl font-medium text-slate-600 hover:bg-slate-200 transition-colors">Close</button>
            <button type="button" id="modal-phone-reply-btn" class="px-6 py-2.5 rounded-xl font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm">
                <i class="bi bi-telephone-fill mr-2"></i>Replied via Phone
            </button>
            <button type="button" id="modal-reply-toggle-btn" class="px-6 py-2.5 rounded-xl font-medium bg-slate-600 text-white hover:bg-slate-700 transition-colors shadow-sm">
                <i class="bi bi-reply-fill mr-2"></i>Reply
            </button>
            <button type="button" id="modal-send-btn" class="hidden px-6 py-2.5 rounded-xl font-medium bg-primary text-white hover:bg-[#8E795E] transition-colors shadow-sm">
                <i class="bi bi-send-fill mr-2"></i>Send Reply
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle View Modal
    document.querySelectorAll('.view-inquiry-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const inquiry = JSON.parse(this.dataset.inquiry);

            // Populate view section
            document.getElementById('modal-name').textContent = inquiry.name;
            document.getElementById('modal-email').textContent = inquiry.email;
            document.getElementById('modal-phone').textContent = inquiry.phone || 'Not provided';
            document.getElementById('modal-type').textContent = inquiry.inquiry_type ? inquiry.inquiry_type.replace(/_/g, ' ') : 'General';
            document.getElementById('modal-date').textContent = new Date(inquiry.created_at).toLocaleString();
            document.getElementById('modal-message').textContent = inquiry.message;

            // Populate reply form
            document.getElementById('reply-inquiry-id').value = inquiry.id;
            document.getElementById('reply-subject').value = `Re: Your Inquiry to DwellCasa`;

            // Hide reply buttons if already replied
            if (inquiry.status === 'replied') {
                document.getElementById('modal-phone-reply-btn').classList.add('hidden');
                document.getElementById('modal-reply-toggle-btn').classList.add('hidden');
            } else {
                document.getElementById('modal-phone-reply-btn').classList.remove('hidden');
                document.getElementById('modal-reply-toggle-btn').classList.remove('hidden');
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
            // Hide reply section and reset buttons on close
            document.getElementById('reply-section').classList.add('hidden');
            document.getElementById('modal-phone-reply-btn').classList.remove('hidden');
            document.getElementById('modal-reply-toggle-btn').classList.remove('hidden');
            document.getElementById('modal-send-btn').classList.add('hidden');
            document.getElementById('reply-form').reset();
        }, 300);
    };

    // Handle Reply Toggle
    document.getElementById('modal-reply-toggle-btn').addEventListener('click', function() {
        document.getElementById('reply-section').classList.toggle('hidden');
        document.getElementById('modal-phone-reply-btn').classList.toggle('hidden');
        this.classList.toggle('hidden');
        document.getElementById('modal-send-btn').classList.toggle('hidden');
    });

    // Handle Send Reply
    document.getElementById('modal-send-btn').addEventListener('click', async function() {
        const form = document.getElementById('reply-form');
        if (!form.reportValidity()) {
            return;
        }

        const id = document.getElementById('reply-inquiry-id').value;
        const subject = document.getElementById('reply-subject').value;
        const message = document.getElementById('reply-message').value;

        const submitBtn = this;
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(`/api/inquiries/${id}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ subject, message })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Reply sent successfully!');
                window.location.reload();
            } else {
                alert('Error sending reply: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while sending the reply.');
        } finally {
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        }
    });

    // Handle Phone Reply
    document.getElementById('modal-phone-reply-btn').addEventListener('click', async function() {
        if (!confirm('Are you sure you want to mark this inquiry as replied via phone?')) return;

        const id = document.getElementById('reply-inquiry-id').value;
        const submitBtn = this;
        const originalHtml = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(`/api/inquiries/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'replied' })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.reload();
            } else {
                alert('Error updating inquiry: ' + (data.message || 'Unknown error'));
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating the inquiry.');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        }
    });

    // Handle Delete
    document.querySelectorAll('.delete-inquiry-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this inquiry?')) return;

            const id = this.dataset.id;

            try {
                const response = await fetch(`/api/inquiries/${id}`, {
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
                    alert('Error deleting inquiry: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the inquiry.');
            }
        });
    });
</script>
@endpush
