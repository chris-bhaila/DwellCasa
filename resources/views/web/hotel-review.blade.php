@extends('layouts.app')

@section('title', 'Review Our Hotel - DwellCasa')

@section('content')
<section class="min-h-screen bg-[#fbfbf9] py-16 px-4">
    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <p class="text-[10px] font-bold tracking-[0.4em] uppercase text-primary mb-3">Guest Feedback</p>
            <h1 class="text-5xl md:text-6xl font-serif italic font-bold text-slate-900 leading-tight mb-4">
                Review DwellCasa
            </h1>
            <div class="w-12 h-px bg-primary mx-auto"></div>
            <p class="text-slate-500 mt-6 max-w-md mx-auto">We'd love to hear about your experience with us. Your feedback helps us improve and serve you better.</p>
        </div>

        {{-- Review Form --}}
        <form action="{{ route('web.hotel-review.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">

                {{-- Personal Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-800 placeholder-slate-400">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-800 placeholder-slate-400">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Star Rating --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-4">Your Rating <span class="text-red-400">*</span></label>
                    <div class="flex items-center gap-2" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" data-value="{{ $i }}"
                            class="star-btn text-4xl text-slate-200 hover:text-primary transition-colors duration-150 focus:outline-none">
                            ★
                            </button>
                            @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                    @error('rating')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Your Review <span class="text-red-400">*</span></label>
                    <textarea name="body" rows="5" required
                        placeholder="Tell us about your stay, our facilities, and the service you received..."
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-800 placeholder-slate-400 resize-none">{{ old('body') }}</textarea>
                    @error('body')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" id="submit-btn"
                        class="w-full bg-slate-900 text-white px-8 py-4 rounded-xl font-medium hover:bg-primary transition-all duration-300 hover:-translate-y-0.5 transform shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        Submit Review
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">Your review will be visible on the homepage after approval.</p>
                </div>

            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = ratingInput.value ? parseInt(ratingInput.value) : 0;

    if (selectedRating > 0) updateStars(selectedRating);

    stars.forEach(star => {
        star.addEventListener('mouseenter', () => updateStars(parseInt(star.dataset.value)));
        star.addEventListener('mouseleave', () => updateStars(selectedRating));
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.dataset.value);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
        });
    });

    function updateStars(val) {
        stars.forEach((s, i) => s.style.color = i < val ? '#A89070' : '#e2e8f0');
    }
</script>
@endpush