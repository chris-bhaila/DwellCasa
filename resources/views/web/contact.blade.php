@extends('layouts.app')

@section('title', 'Contact Us - DwellCasa')

@section('content')
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Contact Us</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                {{ $webInfo->contact_sub_heading }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white p-10 rounded-2xl shadow-lg border border-slate-100">
                <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Send us a Message</h2>
                <form id="contact-form" action="#" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Inquiry Type:</label>
                        <select name="inquiry_type" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="general">General Inquiry</option>
                            <option value="booking">Booking & Reservation</option>
                            <option value="amenities">Amenities & Facilities</option>
                            <option value="pricing">Pricing & Rates</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                        <textarea name="message" rows="5" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white px-6 py-4 rounded-lg font-semibold hover:shadow-lg hover:bg-primary-dark transition-all">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div>
                <div class="bg-white p-10 rounded-2xl shadow-lg border border-slate-100 mb-8">
                    <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Get in Touch</h2>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">📍</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Address</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_address }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">📞</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Phone</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_phone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">✉️</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Email</h3>
                                <p class="text-slate-700 mt-1">{{ $webInfo->contact_email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">🕒</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Business Hours</h3>
                                <p class="text-slate-700 mt-1">Check-in: {{ \Carbon\Carbon::parse($webInfo->check_in)->format('g:i A') }}<br>Check-out: {{ \Carbon\Carbon::parse($webInfo->check_out)->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div class="bg-gradient-to-br from-slate-200 to-slate-300 h-72 rounded-2xl flex items-center justify-center shadow-lg border border-slate-200">
                    <span class="text-slate-600 font-medium">Interactive Map</span>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.getElementById('contact-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending...';

            const response = await fetch('/api/inquiries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Thank you! Your message has been sent successfully.');
                form.reset();
            } else {
                const error = await response.json();
                let errorMessage = 'Error sending message: ' + (error.message || 'Unknown error');
                if (error.errors) {
                    errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while sending your message.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Send Message';
        }
    });
</script>
@endpush
@endsection