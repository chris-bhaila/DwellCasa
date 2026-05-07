@extends('layouts.app')

@section('title', 'Share Your Experience - DwellCasa')

@section('content')
<section class="min-h-screen bg-[#fbfbf9] py-16 px-4">

    @if(session('oauth_error'))
    <div class="max-w-2xl mx-auto mb-6">
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3
                    flex items-center gap-3">
            <i class="bi bi-exclamation-circle text-red-500"></i>
            <p class="text-sm text-red-700">{{ session('oauth_error') }}</p>
            <a href="{{ route('review.form', $token) }}"
               class="ml-auto text-sm font-medium text-red-700 underline">
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
                $imageUrl = $review->roomType->thumbnail
                ? asset('storage/' . $review->roomType->thumbnail)
                : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&q=80&w=400';
                @endphp
                <div class="w-24 h-24 rounded-2xl overflow-hidden flex-shrink-0">
                    <img src="{{ $imageUrl }}" alt="{{ $review->roomType->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-bold tracking-widest uppercase text-slate-400 mb-1">You stayed in</p>
                    <h2 class="text-xl font-serif font-bold text-slate-900 italic mb-1">{{ $review->roomType->name }}</h2>
                    <div class="flex items-center gap-3 text-sm text-slate-500">
                        <span>{{ \Carbon\Carbon::parse($review->booking->check_in_date)->format('M d') }}</span>
                        <span class="text-slate-300">→</span>
                        <span>{{ \Carbon\Carbon::parse($review->booking->check_out_date)->format('M d, Y') }}</span>
                        <span class="text-slate-300">·</span>
                        <span>{{ $review->booking->num_guests }} {{ Str::plural('guest', $review->booking->num_guests) }}</span>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <p class="text-[9px] font-bold tracking-widest uppercase text-slate-400 mb-1">Ref</p>
                    <p class="text-xs font-mono font-bold text-slate-600">{{ $review->booking->booking_ref }}</p>
                </div>
            </div>
        </div>

        {{-- Avatar --}}
        <div class="flex items-center gap-3 mb-8">
            <img src="{{ asset('storage/' . $googleAvatar) }}"
                 alt="Your profile"
                 class="w-10 h-10 rounded-full object-cover border-2 border-slate-100">
            <p class="text-sm text-slate-500">
                Reviewing as <span class="font-semibold text-slate-800">{{ $review->name }}</span>
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
                            class="star-btn text-4xl text-slate-200 hover:text-primary transition-colors duration-150 focus:outline-none"
                            aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                            ★
                            </button>
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
                        <input type="text" value="{{ $review->name }}" readonly
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 cursor-not-allowed text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Email</label>
                        <input type="email" value="{{ $review->email }}" readonly
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 cursor-not-allowed text-sm">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" id="submit-btn"
                        class="w-full bg-slate-900 text-white px-8 py-4 rounded-xl font-medium hover:bg-primary transition-all duration-300 hover:-translate-y-0.5 transform shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
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
@endsection

@push('scripts')
<script>
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const submitBtn = document.getElementById('submit-btn');
    let selectedRating = 0;

    stars.forEach(star => {
        // Hover effect
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            stars.forEach((s, i) => {
                s.style.color = i < val ? '#A89070' : '#e2e8f0';
            });
        });

        // Reset on mouse leave
        star.addEventListener('mouseleave', function() {
            stars.forEach((s, i) => {
                s.style.color = i < selectedRating ? '#A89070' : '#e2e8f0';
            });
        });

        // Select rating
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.value);
            ratingInput.value = selectedRating;
            stars.forEach((s, i) => {
                s.style.color = i < selectedRating ? '#A89070' : '#e2e8f0';
            });
        });
    });

    // Validate rating on submit
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!ratingInput.value) {
            e.preventDefault();
            alert('Please select a star rating before submitting.');
        }
    });
</script>
@endpush