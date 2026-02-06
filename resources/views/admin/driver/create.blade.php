@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.drivers.store') }}" method="post">
            @csrf
            <div class="card">
                <div
                    class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                    <h5 class="card-title mb-sm-0 me-2">Setting Driver</h5>
                    <div class="action-btns">
                        <button type="submit" class="btn btn-success">
                            Create
                        </button>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <div class="row">
                        <div class="col-lg-12 mx-auto">
                            <div class="form-group mt-4">
                                <label for="nik">NIK</label>
                                <input type="text" name="nik" id="nik" class="form-control">
                                @error('nik')
                                <span class="text-danger text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
    <script>
        const searchUserByNikUrl = @js(route('services.search-user-by-nik'));

        // $('body').on('keyup', '#nik', function(){
        //     let thisValue = $(this).val();
        //     console.log(thisValue);
        // });

        $('#nik').autocomplete({
            source: function(req, res){
                $.ajax({
                    url: searchUserByNikUrl,
                    type: 'POST',
                    data: {
                        keywords: req.term,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function( data ) {
                        // console.log(data);
                        res($.map(data, function (item) {
                            return {
                                label: `${item.NIK} - ${item.Name}`,
                                value: item.NIK,
                                data: item
                            };
                        }));
                    },
                });
            },
            minLength: 4,
            select: function( event, ui ) {
                let data = ui.item.data;
                console.log(data);
            },
        });
    </script>
@endsection