@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div
                class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                <h5 class="card-title mb-sm-0 me-2">Setting Driver</h5>
                <div class="action-btns">
                    <a href="{{ route('admin.drivers.create') }}" class="btn btn-success">
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
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drivers as $driver)
                                <tr>
                                    <td>{{ $driver->Name }}</td>
                                    <td width="10%" class="td-action">
                                        <form action="{{ route('admin.drivers.destroy', $driver) }}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="dropdown-item">
                                                <i class="icon-base bx bx-trash bg-danger"></i>
                                                Delete
                                            </button>
                                        </form>
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