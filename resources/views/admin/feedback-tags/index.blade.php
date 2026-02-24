@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h4 class="mb-1 fw-bold" style="color:#2c2c5e">Feedback Tags</h4>
    <p class="text-muted mb-0" style="font-size:.85rem">
        Manage feedback bubble tags for driver booking ratings.
    </p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="icon-base bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">

    {{-- Add new tag --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold">Add New Tag</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.feedback-tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem">
                            Tag Label <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                               value="{{ old('label') }}"
                               placeholder="e.g. Clean Car, Friendly Driver..."
                               maxlength="100" required>
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="icon-base bx bx-plus me-1"></i>Add Tag
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tag list --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">Tags
                    <span class="badge ms-1" style="background:#e8e5ff;color:#7367f0;font-size:.7rem">
                        {{ $tags->count() }}
                    </span>
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background:#fafafa">
                            @foreach (['Label', 'Sort Order', 'Status', 'Actions'] as $h)
                                <th style="font-size:.72rem;color:#82868b;text-transform:uppercase;
                                           letter-spacing:.5px;font-weight:700;padding:.75rem 1rem">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tags as $tag)
                            <tr>
                                <td style="vertical-align:middle;padding:.75rem 1rem">
                                    <span style="display:inline-block;font-size:.82rem;font-weight:600;
                                                 background:rgba(115,103,240,.1);color:#7367f0;
                                                 padding:4px 12px;border-radius:20px">
                                        {{ $tag->label }}
                                    </span>
                                </td>
                                <td style="vertical-align:middle;padding:.75rem 1rem;font-size:.82rem;color:#82868b">
                                    {{ $tag->sort_order }}
                                </td>
                                <td style="vertical-align:middle;padding:.75rem 1rem">
                                    @if ($tag->is_active)
                                        <span style="font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                                     background:rgba(40,199,111,.12);color:#28c76f">Active</span>
                                    @else
                                        <span style="font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                                     background:rgba(130,134,139,.1);color:#82868b">Inactive</span>
                                    @endif
                                </td>
                                <td style="vertical-align:middle;padding:.75rem 1rem">
                                    <div class="d-flex gap-1">
                                        <button type="button" class="adm-act-btn adm-act-blue"
                                                title="Edit"
                                                onclick="openEdit({{ $tag->id }}, '{{ addslashes($tag->label) }}', {{ $tag->sort_order }}, {{ $tag->is_active ? 'true' : 'false' }})">
                                            <i class="icon-base bx bx-edit"></i>
                                        </button>

                                        <form action="{{ route('admin.feedback-tags.destroy', $tag) }}"
                                              method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="adm-act-btn adm-act-red"
                                                    title="Delete"
                                                    onclick="return confirm('Delete tag &quot;{{ addslashes($tag->label) }}&quot;?')">
                                                <i class="icon-base bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5" style="font-size:.82rem">
                                    No feedback tags yet. Add the first one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Edit Feedback Tag</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-form" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem">Label</label>
                        <input type="text" name="label" id="edit-label" class="form-control"
                               maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem">Sort Order</label>
                        <input type="number" name="sort_order" id="edit-sort-order"
                               class="form-control" min="0">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="edit-is-active" value="1">
                        <label class="form-check-label fw-semibold" for="edit-is-active"
                               style="font-size:.83rem">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.adm-act-btn {
    width: 30px; height: 30px; border-radius: 7px; font-size: .85rem;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; border: 1px solid; transition: all .15s;
    background: transparent;
}
.adm-act-blue { border-color: #00cfe8; color: #00cfe8; }
.adm-act-blue:hover { background: #00cfe8; color: #fff; }
.adm-act-red  { border-color: #ea5455; color: #ea5455; }
.adm-act-red:hover  { background: #ea5455; color: #fff; }
</style>

<script>
function openEdit(id, label, sortOrder, isActive) {
    $('#edit-form').attr('action', '/admin/feedback-tags/' + id);
    $('#edit-label').val(label);
    $('#edit-sort-order').val(sortOrder);
    $('#edit-is-active').prop('checked', isActive);
    $('#editModal').modal('show');
}
</script>

@endsection
