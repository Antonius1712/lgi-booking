@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#2c2c5e">Berikan Ulasan Perjalanan</h4>
        <p class="text-muted mb-0" style="font-size:.85rem">{{ $driverBooking->booking_number }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        {{-- Trip Summary --}}
        <div class="card mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="home-booker-avatar flex-shrink-0">
                        {{ $driverBooking->driver?->initials() }}
                    </div>
                    <div>
                        <div style="font-size:.88rem;font-weight:600;color:#2c2c5e">{{ $driverBooking->driver?->Name ?? '-' }}</div>
                        <div style="font-size:.75rem;color:#82868b">Driver</div>
                    </div>
                    <div class="ms-auto text-end">
                        <div style="font-size:.82rem;font-weight:600;color:#2c2c5e">
                            {{ $driverBooking->scheduled_pickup_date?->format('d M Y') }}
                        </div>
                        <div style="font-size:.75rem;color:#82868b">
                            {{ $driverBooking->scheduled_pickup_time?->format('H:i') }}–{{ $driverBooking->scheduled_end_time?->format('H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Feedback Form --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('feedback.driver.store', $driverBooking) }}" method="POST">
                    @csrf

                    {{-- Star Rating --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Rating Perjalanan</label>
                        <div class="d-flex gap-2" id="starRating">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="star-label" style="cursor:pointer;font-size:2rem;color:#dee2e6;transition:color .15s" data-value="{{ $i }}">
                                    <input type="radio" name="rating" value="{{ $i }}" class="d-none" {{ old('rating') == $i ? 'checked' : '' }}>
                                    &#9733;
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tags --}}
                    @if ($tags->isNotEmpty())
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Kesan Perjalanan <span class="text-muted fw-normal">(opsional)</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <label class="feedback-tag-btn">
                                    <input type="checkbox" name="feedback_tag_ids[]" value="{{ $tag->id }}"
                                           class="d-none feedback-tag-input"
                                           {{ in_array($tag->id, old('feedback_tag_ids', [])) ? 'checked' : '' }}>
                                    <span class="btn btn-outline-secondary btn-sm feedback-tag-label {{ in_array($tag->id, old('feedback_tag_ids', [])) ? 'active' : '' }}">
                                        {{ $tag->label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea class="form-control" name="notes" rows="3"
                                  placeholder="Ceritakan pengalaman perjalanan Anda...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base bx bx-send me-1"></i> Kirim Ulasan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@section('script')
<script>
    // Star rating interaction
    const stars = $('#starRating .star-label');

    function highlightStars(val) {
        stars.each(function () {
            const v = parseInt($(this).data('value'));
            $(this).css('color', v <= val ? '#fd7e14' : '#dee2e6');
        });
    }

    // Restore old value on page load
    const checked = $('input[name="rating"]:checked').val();
    if (checked) { highlightStars(parseInt(checked)); }

    stars.on('mouseenter', function () {
        highlightStars(parseInt($(this).data('value')));
    }).on('mouseleave', function () {
        const selected = $('input[name="rating"]:checked').val();
        highlightStars(selected ? parseInt(selected) : 0);
    }).on('click', function () {
        const val = parseInt($(this).data('value'));
        $(this).find('input[type=radio]').prop('checked', true);
        highlightStars(val);
    });

    // Tag toggle
    $(document).on('change', '.feedback-tag-input', function () {
        $(this).next('.feedback-tag-label').toggleClass('active', this.checked);
    });
</script>
@endsection
