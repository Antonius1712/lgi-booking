@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div
                class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                <h5 class="card-title mb-sm-0 me-2">Setting Location</h5>
                <div class="action-btns">
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-success">
                        Create
                    </a>
                </div>
            </div>
            <div class="card-body pt-6">
                <div class="row">
                    <div class="col-lg-12 mx-auto">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $location)
                                <tr>
                                    <td width="90%">{{ $location->name }}</td>
                                    <td width="10%" class="td-action">
                                        <div class="btn-group" role="group"
                                            aria-label="Button group with nested dropdown">
                                            <a href="#" id="BtnActionGroup" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" style="">
                                                <i class="icon-base bx bx-menu"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="BtnActionGroup">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.locations.edit', $location) }}">
                                                    <i class="icon-base bx bx-edit bg-warning"></i>
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.locations.destroy', $location) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="icon-base bx bx-trash bg-danger"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection