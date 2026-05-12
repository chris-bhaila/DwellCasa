<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Share Your Experience - DwellCasa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, .font-serif { font-family: 'Montserrat', serif; line-height: 1.2 !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#fbfbf9] min-h-screen">

<section class="min-h-screen py-16 px-4">

    @if(session('oauth_error'))
    <div class="max-w-2xl mx-auto mb-6">
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-3">
            <i class="bi bi-exclamation-circle text-red-500"></i>
            <p class="text-sm text-red-700">{{ session('oauth_error') }}</p>
            <a href="{{ route('review.form', $token) }}" class="ml-auto text-sm font-medium text-red-700 underline">
                Try again
            </a>
        </div>
    </div>
    @endif

    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <p class="text-[10px] font-bold tracking-[0.4em] uppercase text-primary mb-3">Your Experience</p>
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 leading-tight mb-4">
                How was your<br>stay?
            </h1>
            <div class="w-12 h-px bg-primary mx-auto"></div>
        </div>

        {{-- Room Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
            <div class="flex items-center gap-6 p-6">
                @php
                $imageUrl = $booking->roomType->thumbnail
                    ? asset('storage/' . $booking->roomType->thumbnail)
                    : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=400';
                @endphp
                <div class="w-24 h-24 rounded-2xl overflow-hidden flex-shrink-0">
                    <img src="{{ $imageUrl }}" alt="{{ $booking->roomType->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-bold tracking-widest uppercase text-slate-400 mb-1">You stayed in</p>
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-1">{{ $booking->roomType->name }}</h2>
                    <div class="flex items-center gap-3 text-sm text-slate-500">
                        <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }}</span>
                        <span class="text-slate-300">→</span>
                        <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</span>
                        <span class="text-slate-300">·</span>
                        <span>{{ $booking->num_guests }} {{ Str::plural('guest', $booking->num_guests) }}</span>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <p class="text-[9px] font-bold tracking-widest uppercase text-slate-400 mb-1">Ref</p>
                    <p class="text-xs font-mono font-bold text-slate-600">{{ $booking->booking_ref }}</p>
                </div>
            </div>
        </div>

        {{-- Avatar --}}
        <div class="flex items-center gap-3 mb-8">
            <img src="{{ asset('storage/' . $googleAvatar) }}"
                 alt="Your profile"
                 class="w-10 h-10 rounded-full object-cover border-2 border-slate-100">
            <p class="text-sm text-slate-500">
                Reviewing as <span class="font-semibold text-slate-800">{{ $booking->guest->full_name }}</span>
            </p>
        </div>

        {{-- Review Form --}}
        <form action="{{ route('review.store', $review->review_token) }}" method="POST">
            @csrf

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">

                {{-- Star Rating --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-4">Your Rating <span class="text-red-400">*</span></label>
                    <div class="flex items-center gap-2" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" data-value="{{ $i }}"
                                class="star-btn text-4xl text-slate-200 hover:text-primary transition-colors duration-150 focus:outline-none cursor-pointer"
                                aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="">
                    @error('rating')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Your Review <span class="text-red-400">*</span></label>
                    <textarea name="body" rows="5"
                        placeholder="Tell us about your stay — the room, the service, what made it special..."
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-800 placeholder-slate-400 resize-none">{{ old('body') }}</textarea>
                    @error('body')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reviewer info (pre-filled, read only) --}}
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Name</label>
                        <input type="text" value="{{ $booking->guest->full_name }}" readonly
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 cursor-not-allowed text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Email</label>
                        <input type="email" value="{{ $booking->guest->email }}" readonly
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 cursor-not-allowed text-sm">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" id="submit-btn"
                        class="w-full bg-slate-900 text-white px-8 py-4 rounded-xl font-medium hover:bg-primary transition-all duration-300 hover:-translate-y-0.5 transform shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 cursor-pointer">
                        Submit Review
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">Your review will be visible after approval.</p>
                </div>

            </div>
        </form>

        {{-- Bottom note --}}
        <p class="text-center text-xs text-slate-400 mt-6">
            This review link is unique to your stay and can only be used once.
        </p>

    </div>
</section>

<script>
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            stars.forEach((s, i) => { s.style.color = i < val ? '#A89070' : '#e2e8f0'; });
        });

        star.addEventListener('mouseleave', function() {
            stars.forEach((s, i) => { s.style.color = i < selectedRating ? '#A89070' : '#e2e8f0'; });
        });

        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.value);
            ratingInput.value = selectedRating;
            stars.forEach((s, i) => { s.style.color = i < selectedRating ? '#A89070' : '#e2e8f0'; });
        });
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!ratingInput.value) {
            e.preventDefault();
            alert('Please select a star rating before submitting.');
        }
    });
</script>

</body>
</html>
