@extends('layouts.app')

@section('title', 'Contact Us - DwellCasa')

@section('content')
<section class="py-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">Contact Us</h1>
            <p class="text-lg text-slate-700 max-w-2xl mx-auto">
                Get in touch with us for reservations, inquiries, or any questions about your stay.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white p-10 rounded-2xl shadow-lg border border-slate-100">
                <h2 class="text-3xl font-serif italic font-bold text-slate-900 mb-8">Send us a Message</h2>
                <form action="#" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">First Name</label>
                            <input type="text" name="first_name" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        </div>
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
                        <label class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                        <textarea name="message" rows="5" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
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
                                <p class="text-slate-700 mt-1">Lalitpur<br>Nepal</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">📞</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Phone</h3>
                                <p class="text-slate-700 mt-1">+977 123 456 789</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">✉️</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Email</h3>
                                <p class="text-slate-700 mt-1">info@dwellcasa.com</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-blue-200">
                                <span class="text-2xl">🕒</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">Business Hours</h3>
                                <p class="text-slate-700 mt-1">24/7 Reception<br>Check-in: 2:00 PM<br>Check-out: 12:00 PM</p>
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
@endsection