@extends('layouts.admin')

@section('title', 'Home Page Content - DwellCasa Admin')
@section('header_title', 'Home Page Content')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Home Page Content</h1>
        <p class="text-slate-500 mt-1">Edit the global landing page content shown before a location is selected.</p>
    </div>
</div>

<form id="home-info-form" class="space-y-8" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Locations Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Locations Section</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Eyebrow Label</label>
                    <input type="text" name="front_page_sub_heading_1" value="{{ $info?->front_page_sub_heading_1 }}"
                        placeholder="e.g. Our Properties"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Main Heading</label>
                    <input type="text" name="front_page_main_heading" value="{{ $info?->front_page_main_heading }}"
                        placeholder="e.g. Where Would You Like to Stay?"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Brand Promise Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Brand Promise Section</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="front_page_end_heading" value="{{ $info?->front_page_end_heading }}"
                        placeholder="e.g. The DwellCasa Standard"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Body Text</label>
                    <textarea name="front_page_sub_heading_2" rows="4"
                        placeholder="e.g. Whether in the cultural heart of Patan..."
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">{{ $info?->front_page_sub_heading_2 }}</textarea>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Reviews Section</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading</label>
                    <input type="text" name="reviews_sub_heading" value="{{ $info?->reviews_sub_heading }}"
                        placeholder="e.g. Guest Experiences"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="reviews_heading" value="{{ $info?->reviews_heading }}"
                        placeholder="e.g. What They Say"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Footer</h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                    <textarea name="footer_description" rows="3"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">{{ $info?->footer_description }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                    <input type="text" name="contact_address" value="{{ $info?->contact_address }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                    <input type="text" name="contact_phone" value="{{ $info?->contact_phone }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" name="contact_email" value="{{ $info?->contact_email }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Facebook URL</label>
                    <input type="url" name="facebook_link" value="{{ $info?->facebook_link }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Instagram URL</label>
                    <input type="url" name="instagram_link" value="{{ $info?->instagram_link }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

    </div>

    <div id="form-error" class="hidden p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm"></div>

    <div class="flex justify-end">
        <button type="submit" id="submit-btn"
            class="bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm">
            Save Changes
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.getElementById('home-info-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submit-btn');
        const errorDiv = document.getElementById('form-error');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;
        errorDiv.classList.add('hidden');

        const formData = new FormData(this);

        try {
            const response = await fetch('/api/home-info', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            });

            const result = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                let msg = result.message || 'Unknown error';
                if (result.errors) msg += '\n' + Object.values(result.errors).flat().join('\n');
                errorDiv.innerText = msg;
                errorDiv.classList.remove('hidden');
            }
        } catch (err) {
            errorDiv.innerText = 'An error occurred while saving.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
</script>
@endpush
