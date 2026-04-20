@extends('layouts.admin')

@section('title', 'Website Information - DwellCasa Admin')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Website Information</h1>
        <p class="text-slate-500 mt-1">Manage global content, headings, and settings for the public website.</p>
    </div>
</div>

<form id="website-info-form" action="#" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Home Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Home Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Main Heading</label>
                    <input type="text" name="front_page_main_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading 1</label>
                    <input type="text" name="front_page_sub_heading_1" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading 2</label>
                    <input type="text" name="front_page_sub_heading_2" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">End Section Heading</label>
                    <input type="text" name="front_page_end_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">End Section Sub Heading</label>
                    <input type="text" name="front_page_end_sub_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- About Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">About Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="about_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Description</label>
                    <textarea name="about_sub_description" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Main Description</label>
                    <textarea name="about_main_description" rows="5" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
            </div>
        </div>

        <!-- Gallery Page Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Gallery Page</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Heading</label>
                    <input type="text" name="gallery_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Sub Heading</label>
                    <input type="text" name="gallery_sub_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Contact & Policies -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Contact & Policies</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contact Sub Heading</label>
                    <input type="text" name="contact_sub_heading" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                    <input type="text" name="contact_address" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="text" name="contact_phone" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="contact_email" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Check-in Time</label>
                        <input type="time" name="check_in" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Time</label>
                        <input type="time" name="check_out" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>
        </div>

        <!-- Social & Footer -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-6 border-b border-slate-100 pb-4">Social & Footer</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Facebook Link</label>
                        <input type="url" name="facebook_link" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Instagram Link</label>
                        <input type="url" name="instagram_link" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Footer Description</label>
                    <textarea name="footer_description" rows="5" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-primary focus:border-primary"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-medium hover:bg-[#8E795E] transition-all shadow-sm flex items-center gap-2">
            <i class="bi bi-save"></i> Save Changes
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch('/api/website-info');
        const result = await response.json();
        
        if (response.ok && result.data) {
            const data = result.data;
            const form = document.getElementById('website-info-form');
            
            Object.keys(data).forEach(key => {
                const input = form.elements[key];
                if (input) {
                    // Format time down to H:i to match the 'time' input parsing and validation rules
                    if (input.type === 'time' && data[key]) {
                        input.value = data[key].substring(0, 5);
                    } else {
                        input.value = data[key] || '';
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error fetching website info:', error);
    }
});

document.getElementById('website-info-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    // API requires time formats to be H:i
    if (data.check_in) data.check_in = data.check_in.substring(0, 5);
    if (data.check_out) data.check_out = data.check_out.substring(0, 5);

    try {
        const response = await fetch('/api/website-info', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Website information updated successfully.');
        } else {
            const error = await response.json();
            let errorMsg = 'Error saving changes: ' + (error.message || 'Unknown error');
            if (error.errors) {
                errorMsg += '\n' + Object.values(error.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the changes.');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endpush
