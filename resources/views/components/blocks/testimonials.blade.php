@props(['block'])

@php
    $data = $block['data'] ?? $block;
    $testimonials = $data['testimonials'] ?? [];
@endphp

<section class="bg-white py-12">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        @if(!empty($data['subtitle']) || !empty($data['title']) || !empty($data['description']))
            <div class="text-center mb-16">
                @if(!empty($data['subtitle']))
                    <div class="inline-block px-4 py-2 mb-4 bg-teal-100 text-teal-800 text-sm font-semibold rounded-full">
                        {{ $data['subtitle'] }}
                    </div>
                @endif
                @if(!empty($data['title']))
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
                        {{ $data['title'] }}
                    </h2>
                @endif
                @if(!empty($data['description']))
                    <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                        {{ $data['description'] }}
                    </p>
                @endif
            </div>
        @endif

        @if(!empty($testimonials))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $index => $testimonial)
                    @php
                        $ringColor = $index % 2 === 0 ? 'ring-blue-200' : 'ring-teal-200';
                    @endphp
                    <div class="bg-slate-50 rounded-2xl p-8 shadow-lg flex flex-col h-full">
                        <div class="flex items-center mb-6">
                            @php
                                $rating = isset($testimonial['rating']) ? (float) $testimonial['rating'] : 5;
                                $rating = max(0, min(5, $rating));
                            @endphp
                            <div class="flex">
                                @for($i = 0; $i < 5; $i++)
                                    @php
                                        $fill = max(0, min(1, $rating - $i));
                                    @endphp
                                    <span class="relative inline-block w-5 h-5">
                                        <svg class="absolute inset-0 w-5 h-5 text-slate-200" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="absolute inset-0 overflow-hidden" style="width: {{ $fill * 100 }}%">
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    </span>
                                @endfor
                            </div>
                        </div>
                        @if(!empty($testimonial['quote']))
                            <p class="text-slate-700 mb-6 leading-relaxed flex-1">
                                "{{ $testimonial['quote'] }}"
                            </p>
                        @endif
                        <div class="flex items-center mt-auto">
                            @if(!empty($testimonial['image']))
                                <div class="w-12 h-12 rounded-full overflow-hidden mr-4 ring-2 {{ $ringColor }}">
                                    <img
                                        src="{{ $testimonial['image'] }}"
                                        alt="{{ $testimonial['name'] ?? '' }}"
                                        class="w-full h-full object-cover"
                                        width="48"
                                        height="48"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                            <div>
                                @if(!empty($testimonial['name']))
                                    <div class="font-semibold text-slate-900">{{ $testimonial['name'] }}</div>
                                @endif
                                @if(!empty($testimonial['role']))
                                    <div class="text-sm text-slate-600">{{ $testimonial['role'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
