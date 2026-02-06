<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Select Date</h5>
            </div>
            <div class="card-body">
                <input type="date" class="form-control" 
                    min="{{ request()->sdate }}"
                    max="{{ request()->edate }}"
                    {{-- value="{{ now()->format('Y-m-d') }}" --}}
                    wire:model="date"
                    wire:change="selectDate($event.target.value, {{ $meetingRoom->id }})"
                >
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Available Time Slots</h5>
                <div>
                    <span class="badge bg-label-success me-2">
                        <i class="bx bxs-circle"></i> Available
                    </span>
                    <span class="badge bg-label-danger">
                        <i class="bx bxs-circle"></i> Booked
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($timeRanges as $slot)
                    @php
                    $isBooked = in_array($slot, $bookedSlots);
                    // $isSelected = $selectedSlot === $slot;
                    $isSelected[$slot] = in_array($slot, $selectedSlot);
                    $safeId = md5($slot);
                    @endphp
                    <div class="col-md-3 col-sm-6">
                        <div class="form-check custom-option custom-option-icon 
                            @if( $isBooked ) bg-disabled @endif 
                            @if( $date === '') bg-disabled @endif"
                        x-data="{ 
                                localSlots: @entangle('timeSlots').live,
                                bookedSlots: @entangle('bookedSlots'),
                                
                                toggleSlot(slot) {
                                    const index = this.localSlots.indexOf(slot);                                    
                                    if (index > -1) {
                                        // Uncheck - just remove it
                                        this.localSlots.splice(index, 1);
                                        
                                        if (this.localSlots.length > 0) {
                                            this.validateAndRemoveAfterGap();
                                        }

                                    } else {
                                        // Check - add and validate
                                        this.localSlots.push(slot);
                                        this.validateConsecutive();
                                    }
                                },
                                
                                validateConsecutive() {
                                    if (this.localSlots.length <= 1) return;
                                    
                                    const sorted = [...this.localSlots].sort();
                                    const valid = [sorted[0]];
                                    let gapFound = false;
                                    
                                    for (let i = 0; i < sorted.length - 1; i++) {
                                        const end1 = sorted[i].split(' - ')[1].trim();
                                        const start2 = sorted[i + 1].split(' - ')[0].trim();
                                        
                                        if (end1 === start2) {
                                            valid.push(sorted[i + 1]);
                                        } else {
                                            // Found gap - show error and keep only valid slots
                                            gapFound = true;
                                            break;
                                        }
                                    }

                                    if (gapFound && valid.length < sorted.length) {
                                        this.localSlots = valid;
                                        $wire.dispatch('show-error', [{ message: 'Time slots must be consecutive. Slots after the gap have been removed.' }]);
                                    }
                                },

                                validateAndRemoveAfterGap() {
                                    if (this.localSlots.length <= 1) return;
                                    
                                    const sorted = [...this.localSlots].sort();
                                    const valid = [sorted[0]];
                                    let removed = [];
                                    
                                    for (let i = 0; i < sorted.length - 1; i++) {
                                        const end1 = sorted[i].split(' - ')[1].trim();
                                        const start2 = sorted[i + 1].split(' - ')[0].trim();
                                        
                                        if (end1 === start2) {
                                            valid.push(sorted[i + 1]);
                                        } else {
                                            // Found gap - collect all slots after this gap
                                            removed = sorted.slice(i + 1);
                                            break;
                                        }
                                    }

                                    // If we found slots after a gap, remove them and show error
                                    if (removed.length > 0) {
                                        this.localSlots = valid;
                                        $wire.dispatch('show-error', [{ message: 'Time slots must be consecutive. Slots after the gap have been removed.' }]);
                                    }
                                }
                        }">
                            <label class="form-check-label custom-option-content" for="slot_{{ $safeId }}">
                                <span class="custom-option-body">
                                    <span class="custom-option-title">
                                        {{ $slot }}
                                    </span>
                                    <small> 60 Minutes </small>
                                </span>

                                {{-- <input class="form-check-input" type="checkbox" id="slot_{{ $safeId }}"
                                    value="{{ $slot }}" wire:key="slot_{{ $safeId }}" wire:model.live="timeSlots"
                                    wire:click="checkValidConsecutive" @if( $isBooked ) disabled @endif @if( $date===''
                                    ) disabled @endif /> --}}

                                <input class="form-check-input" type="checkbox" id="slot_{{ $safeId }}" value="{{ $slot }}" 
                                    wire:key="slot_{{ $safeId }}"
                                    :checked="localSlots.includes('{{ $slot }}')" 
                                    @click="toggleSlot('{{ $slot }}')"
                                    @if($isBooked) disabled @endif 
                                    @if($date==='' ) disabled @endif
                                />

                                <span class="custom-option-body mt-4">
                                    <small>
                                        <strong>
                                            @if( $isBooked )
                                            Booked
                                            @else
                                            Available
                                            @endif
                                        </strong>
                                    </small>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="button" wire:click="bookTimeSlot({{ $meetingRoom->id }})"
                        class="btn btn-primary btn-lg">
                        <i class="bx bx-check-circle me-2"></i>
                        Book Selected Time Slot
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- <div id="loading" wire:loading></div> --}}
</div>