@extends('layouts.app')

@section('title', 'About Us - DwellCasa')

@section('content')
<section class="py-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 mb-4">About DwellCasa</h1>
            <p class="text-lg text-slate-700 max-w-3xl mx-auto">
                Welcome to DwellCasa, where luxury meets comfort in the heart of Lalitpur. We pride ourselves on providing exceptional hospitality and creating memorable experiences for our guests.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h2 class="text-3xl md:text-4xl font-serif italic font-bold text-slate-900 mb-6">Our Story</h2>
                <p class="text-gray-600 mb-4">
                    Founded in 2024, DwellCasa has quickly become Lalitpur's premier luxury hotel destination. Our commitment to excellence in service, attention to detail, and creating unforgettable experiences sets us apart from the rest.
                </p>
                <p class="text-gray-600 mb-4">
                    Located in the vibrant heart of the city, we offer easy access to Lalitpur's most popular attractions while providing a serene oasis of luxury and comfort.
                </p>
                <p class="text-gray-700">
                    Our team of dedicated professionals ensures that every guest receives personalized service and attention throughout their stay.
                </p>
            </div>
            <div class="bg-gradient-to-br from-slate-200 to-slate-300 h-96 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="text-slate-600 font-medium">Hotel Image</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 rounded-2xl bg-white shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-blue-200">
                    <span class="text-2xl">🏨</span>
                </div>
                <h3 class="text-2xl font-serif italic font-semibold text-slate-900 mb-3">Luxury Accommodations</h3>
                <p class="text-gray-600">
                    Spacious, well-appointed rooms designed for comfort and relaxation.
                </p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-white shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-blue-200">
                    <span class="text-2xl">👥</span>
                </div>
                <h3 class="text-2xl font-serif italic font-semibold text-slate-900 mb-3">Exceptional Service</h3>
                <p class="text-gray-600">
                    Our dedicated staff provides personalized service around the clock.
                </p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-white shadow-sm hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-blue-200">
                    <span class="text-2xl">📍</span>
                </div>
                <h3 class="text-2xl font-serif italic font-semibold text-slate-900 mb-3">Prime Location</h3>
                <p class="text-gray-600">
                    Centrally located with easy access to Lalitpur's attractions.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection