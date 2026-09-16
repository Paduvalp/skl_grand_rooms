{{--
    Common questions, answered from Site Settings.

    The answers are printed as ordinary visible text on purpose. Google only
    accepts FAQ structured data when the visitor can read the same answer on
    the page, and the plain text is what gets matched against questions
    people type.

    The block disappears when there is nothing to answer with.
--}}
@php $faqs = \App\Support\Seo::faqs($settings); @endphp

@if ($faqs)
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Common questions</h2>

            <div class="accordion" id="faqAccordion">
                @foreach ($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button @if ($i > 0) collapsed @endif"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $i }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                    aria-controls="faq{{ $i }}">
                                {{ $faq['question'] }}
                            </button>
                        </h3>
                        <div id="faq{{ $i }}"
                             class="accordion-collapse collapse @if ($i === 0) show @endif"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">{{ $faq['answer'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::faqPage($faqs)) !!}</script>
    @endpush
@endif
