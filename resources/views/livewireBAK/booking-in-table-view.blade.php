<div class="card" x-data="pickTime()" x-on:reload-alpine.window="reset()">
    <div wire:loading wire:target="submitBook, date" id="loading">
        Loading..
    </div>

    <div class="card-body">

        <div class="col-12">
            <input type="date" class="form-control" 
                x-ref="searchInput"
                x-init="if ($wire.focusInput) $refs.searchInput.focus()"
                wire:model.live.debounce.500ms="date"
                name="d"
                placeholder="Search for users..."
            >
        </div>

        <div class="col-12 mt-4">
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
                            <td class="fixed-column" style="vertical-align: top;">
                                {{ $range }}
                            </td>
                            @if( $range !== '17:00' )
                            @foreach ($rooms as $room)
                            @php
                            // $arrayBooked = isset($booked[$room->slug]) ? $booked[$room->slug] : [];
                            $arrayBooked = $booked[$room->slug] ?? [];
                            $isBooked = collect($arrayBooked)
                                ->pluck(0)        // take only "08:00", "08:30", etc
                                ->contains($range);


                            $booking = collect($arrayBooked)
                                ->firstWhere(0, $range); // match time

                            $name = $booking[1] ?? null;

                            // dd($arrayBooked, $isBooked, $range, in_array($range, $arrayBooked), $booked);
                            // dd($room);
                            @endphp
                            <td>
                                <div class="form-check custom-option custom-option-icon {{ $isBooked ? 'bg-disabled' : null }}">
                                    <label class="form-check-label custom-option-content"
                                        for="{{ $room->slug.'_'.$range }}">
                                        <input class="form-check-input" id="{{ $room->slug.'_'.$range }}"
                                            type="checkbox" style="display:none;" value="{{ $range }}"
                                            x-on:click="addRange(@js($room->slug), @js($range), $event.target.checked)"
                                            {{ $isBooked ? 'disabled' : null }} />

                                        <span class="custom-option-body">
                                            <small>
                                                @if( $isBooked )
                                                <strong>
                                                    Booked
                                                </strong> 
                                                <br/>
                                                <span>
                                                    {{ $name }}
                                                </span>
                                                @else
                                                <strong>
                                                    Available
                                                </strong>
                                                @endif
                                            </small>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            @endforeach
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="row">
                <div class="col-6">
                    <div x-show="Object.keys(range).length">
                        <ul>
                            <template x-for="(times, room) in range" :key="room">
                                <template x-if="Array.isArray(times) && times.length">
                                    <li>
                                        <strong x-data="{
                                                ucwords(str) {
                                                    return str.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                                                }                                    
                                            }" x-text="ucwords(room)">
                                        </strong>:

                                        <span x-text="times.join(', ')"></span>
                                    </li>
                                </template>
                            </template>
                        </ul>
                    </div>
                    <div x-show="!Object.keys(range).length">No time selected</div>

                    <div class="form-group mt-4">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" cols="30" rows="10" class="form-control" wire:model="description"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <button class="btn btn-primary w-100" wire:click="submitBook(range)">
                        Book
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>