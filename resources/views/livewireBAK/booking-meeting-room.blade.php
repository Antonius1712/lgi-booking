<div class="card">
    <div class="card-body">
        <div class="col-12">
            <div class="table-responsive " style="width: 100%;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%" class="fixed-column fixed-header-cell"></th>
                            @foreach ($this->rooms as $room)
                            <th class="fixed-header-cell room-header-cell">
                                <a href="">
                                    {{ $room->location->name }} <br />
                                    {{ $room->name }}
                                </a>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->timeRanges as $range)
                        <tr>
                            <td class="fixed-column">
                                {{ $range }}
                            </td>
                            @foreach ($this->rooms as $room)
                            <td>
                                <div class="form-check custom-option custom-option-icon">
                                    <label class="form-check-label custom-option-content" for="{{ $room->slug.'_'.$range }}">
                                        <input class="form-check-input" id="{{ $room->slug.'_'.$range }}" type="checkbox" style="display:none;" value="{{ $range }}" />
                                        <span class="custom-option-body">
                                            <small>
                                                <strong>
                                                    Available
                                                </strong>
                                            </small>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-6">
                    dsadsa
                </div>
                <div class="col-6">
                    <button class="btn btn-primary w-100">
                        Book
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>