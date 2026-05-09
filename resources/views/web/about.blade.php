@extends('layouts.app')

@section('title', 'About Us - DwellCasa')

@section('content')
<section class="pt-10 pb-20 bg-[#fbfbf9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl !font-sans font-bold text-slate-900 mt-4 mb-6">{{ $webInfo->about_heading }}</h1>
            <p class="text-lg text-slate-700 max-w-3xl mx-auto">
                {{ $webInfo->about_sub_description }}    
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h2 class="text-3xl md:text-4xl !font-sans font-bold text-slate-900 mb-6 px-4 md:px-0">Our Story</h2>
                <p class="text-gray-600 mb-4 px-4 md:px-0 text-justify md:text-left">
                    {{ $webInfo->about_main_description }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-slate-200 to-slate-300 h-96 rounded-2xl flex items-center justify-center shadow-lg overflow-hidden">
                @if($webInfo->about_image)
                    <img src="{{ asset('storage/' . $webInfo->about_image) }}" alt="About DwellCasa" class="w-full h-full object-cover">
                @else
                    <span class="text-slate-600 font-medium">Hotel Image</span>
                @endif
            </div>
        </div>

        @if(isset($faqs) && $faqs->isNotEmpty())
        <div class="mt-16">
            <div class="text-center mb-10">
                <h2 class="font-serif italic text-3xl md:text-4xl font-bold text-slate-900">Frequently Asked Questions</h2>
            </div>
            <div class="max-w-5xl mx-auto divide-y divide-slate-100 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                @foreach($faqs as $index => $faq)
                <div class="faq-item">
                    <button type="button"
                        class="faq-trigger w-full flex items-center justify-between gap-4 px-6 py-5 text-left hover:bg-slate-50/60 transition-colors cursor-pointer"
                        aria-expanded="false">
                        <span class="font-semibold text-slate-900 text-base leading-snug text-md">{{ $faq->question }}</span>
                        <span class="faq-icon flex-shrink-0 w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                    <div class="faq-answer hidden px-6 pb-5">
                        <p class="text-slate-500 text-md leading-relaxed whitespace-pre-wrap">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.faq-trigger').forEach(trigger => {
        trigger.addEventListener('click', function () {
            const item     = this.closest('.faq-item');
            const answer   = item.querySelector('.faq-answer');
            const icon     = item.querySelector('.faq-icon');
            const isOpen   = this.getAttribute('aria-expanded') === 'true';

            this.setAttribute('aria-expanded', !isOpen);
            answer.classList.toggle('hidden', isOpen);
            icon.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    });
</script>
@endpush