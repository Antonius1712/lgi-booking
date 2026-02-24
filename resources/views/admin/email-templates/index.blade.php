@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Email Templates</h4>
        <p class="text-muted mb-0 small">Manage email templates for booking notifications.</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    @foreach (['Email Type', 'Subject', 'Status', 'Actions'] as $h)
                        <th class="text-uppercase text-muted fw-semibold small">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($allTypes as $type)
                    @php $tpl = $templates->firstWhere('email_type', $type); @endphp
                    <tr>
                        <td class="align-middle">
                            <div class="fw-semibold small" style="color:#2c2c5e">
                                {{ \App\Models\EmailTemplate::typeLabel($type) }}
                            </div>
                            <div class="text-muted" style="font-size:.7rem">{{ $type }}</div>
                        </td>
                        <td class="align-middle small text-body">
                            {{ $tpl?->subject ?? '—' }}
                        </td>
                        <td class="align-middle">
                            @if ($tpl)
                                @if ($tpl->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            @else
                                <span class="text-muted small">Not configured</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <a href="{{ route('admin.email-templates.edit', $type) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="icon-base bx bx-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
