@extends('layouts.app')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.email-templates.index') }}" class="text-primary small text-decoration-none">
        ← Back to Email Templates
    </a>
    <h4 class="fw-bold mb-0 mt-1" style="color:#2c2c5e">
        {{ \App\Models\EmailTemplate::typeLabel($trigger) }}
    </h4>
    <div class="text-muted small">{{ $trigger }}</div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-error me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.email-templates.update', $trigger) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="subject" class="form-control"
                               value="{{ old('subject', $template?->subject) }}"
                               placeholder="Email subject line..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">
                            Email Body <span class="text-danger">*</span>
                        </label>
                        <textarea id="email-body" name="email_body">{{ old('email_body', $template?->email_body) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="is_active" value="1"
                                   {{ old('is_active', $template?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="is_active">
                                Active (send this email)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base bx bx-save me-1"></i>Save Template
                        </button>
                        <a href="{{ route('admin.email-templates.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold">Available Variables</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody class="small">
                        @php
                            $vars = [
                                '{{booking_number}}' => 'Booking number',
                                '{{employee_name}}'  => 'Employee full name',
                                '{{driver_name}}'    => 'Driver full name',
                                '{{date}}'           => 'Booking date',
                                '{{time}}'           => 'Time slot',
                                '{{destination}}'    => 'Destination',
                                '{{purpose}}'        => 'Purpose of trip',
                                '{{room_name}}'      => 'Meeting room name',
                                '{{location}}'       => 'Room location',
                                '{{cancel_reason}}'  => 'Cancellation reason',
                            ];
                        @endphp
                        @foreach ($vars as $var => $desc)
                            <tr>
                                <td><code class="text-primary">{{ $var }}</code></td>
                                <td class="text-muted">{{ $desc }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css">
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
<script>
$('#email-body').summernote({
    height: 450,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link', 'hr']],
        ['view', ['codeview', 'fullscreen']],
    ],
});
</script>
@endsection
