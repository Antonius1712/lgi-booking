class MeetingRoomBookingManager {
    constructor(config) {
        this.timeRanges = config.timeRanges;
        this.booked = config.booked;
        this.LAST_BOOKING_TIME = '17:00';
    }

    init() {
        this.initializeSelect2();
        this.initializeCalendar();
        this.attachModalListeners();
        this.attachTimeChangeListeners();
    }

    // ============================================
    // INITIALIZATION
    // ============================================

    initializeSelect2() {
        $('.select2').select2();
    }

    initializeCalendar() {
        flatpickr(".flatpickr", {
            showMonths: 1,
            dateFormat: "d M Y",
            onChange: (selectedDates) => this.onDateChange(selectedDates)
        });
    }

    onDateChange(selectedDates) {
        if (!selectedDates.length) return;

        const date = selectedDates[0];
        const params = {
            year: date.getFullYear(),
            month: date.getMonth() + 1,
            day: date.getDate()
        };

        $('#loading').show();
        setTimeout(() => {
            window.location.href = '?' + new URLSearchParams(params).toString();
        }, 50);
    }

    attachModalListeners() {
        $('#bookingMeetingRoomModal').on('show.bs.modal', (e) => 
            this.handleNewBookingModal(e)
        );
        
        $('#EditBookingMeetingRoomModal').on('show.bs.modal', (e) => 
            this.handleEditBookingModal(e)
        );
    }

    attachTimeChangeListeners() {
        $('#stime').on('change', () => this.onStartTimeChangeCreate());
        $('#e_stime').on('change', () => this.onStartTimeChangeEdit());
    }

    // ============================================
    // MODAL HANDLING
    // ============================================

    handleNewBookingModal(event) {
        const data = this.extractModalData(event.relatedTarget);
        const modal = $(event.target);

        this.resetModalForCreate();
        this.populateModalForm(modal, data, false);
        this.generateStartTimes(data.time, data.dateStr, data.room_slug);
        this.generateEndTimes(data.time, data.dateStr, data.room_slug);
    }

    handleEditBookingModal(event) {
        const data = this.extractModalDataForEdit(event.relatedTarget);
        const modal = $(event.target);

        this.resetModalForEdit();
        
        const isOwner = this.isBookingOwner(data.nik_booking);
        this.setEditModalPermissions(modal, isOwner, data);

        this.populateModalForm(modal, data, true);
        this.generateStartTimes(data.time, data.dateStr, data.room_slug, data.id, data.room_id);
        this.generateEndTimes(data.time, data.dateStr, data.room_slug, data.id, data.room_id);
    }

    extractModalData(element) {
        const $el = $(element);
        return {
            room_slug: $el.data('r'),
            year: $el.data('y'),
            month: $el.data('m'),
            day: $el.data('d'),
            time: $el.data('t'),
            dateStr: this.formatDateString($el.data('y'), $el.data('m'), $el.data('d')),
            roomName: this.formatRoomName($el.data('r'))
        };
    }

    extractModalDataForEdit(element) {
        const $el = $(element);
        const data = this.extractModalData(element);
        return {
            ...data,
            id: $el.data('i'),
            room_id: $el.data('room_id'),
            start_time: $el.data('sb'),
            end_time: $el.data('eb'),
            description: $el.data('desc'),
            username: $el.data('username_booking'),
            nik_booking: $el.data('nik_booking')
        };
    }

    populateModalForm(modal, data, isEdit) {
        const prefix = isEdit ? 'e_' : '';
        
        modal.find('.modal-title').text(
            isEdit ? `Edit Booking Room ${data.roomName}` : `Booking Room ${data.roomName}`
        );
        modal.find('.room').val(data.room_slug);
        modal.find('.year').val(data.year);
        modal.find('.month').val(data.month);
        modal.find('.day').val(data.day);
        
        if (isEdit) {
            modal.find('.idBooking').val(data.id);
            modal.find(`#${prefix}stime`).val(data.start_time);
            modal.find(`#${prefix}etime`).val(data.end_time);
            modal.find(`#${prefix}description`).val(data.description);

            const routeUpdate = @js(route('booking.meeting-room.update', '__ID__'))
                .replace('__ID__', data.id);
            modal.find('#formEditAction').attr('action', routeUpdate);
        } else {
            modal.find(`#${prefix}stime`).val(data.time);
        }
    }

    resetModalForCreate() {
        $('.modal-footer').show();
        $('#stime, #etime, #description, #participant').prop('disabled', false);
    }

    resetModalForEdit() {
        $('#e_stime, #e_etime, #e_description, #e_participant').prop('disabled', false);
    }

    setEditModalPermissions(modal, isOwner, data) {
        if (!isOwner) {
            $('.modal-footer').hide();
            modal.find('.modal-title').text(`Booked By: ${data.username}`);
            $('#e_stime, #e_etime, #e_description, #e_participant').prop('disabled', true);
        }
    }

    isBookingOwner(nik_booking) {
        const currentUserNIK = parseInt(@js(auth()->user()->NIK));
        return nik_booking === currentUserNIK;
    }

    // ============================================
    // TIME CHANGE HANDLERS
    // ============================================

    onStartTimeChangeCreate() {
        const stime = $('#stime').val();
        const { year, month, day, room_slug } = this.getModalValues('bookingMeetingRoomModal');
        const dateStr = this.formatDateString(year, month, day);
        
        this.generateEndTimes(stime, dateStr, room_slug);
    }

    onStartTimeChangeEdit() {
        const stime = $('#e_stime').val();
        const { year, month, day, room_slug } = this.getModalValues('EditBookingMeetingRoomModal');
        const idBooking = parseInt($('#EditBookingMeetingRoomModal .idBooking').val());
        const dateStr = this.formatDateString(year, month, day);
        
        this.generateEndTimes(stime, dateStr, room_slug, idBooking);
    }

    getModalValues(modalId) {
        const $modal = $(`#${modalId}`);
        return {
            year: $modal.find('.year').val(),
            month: $modal.find('.month').val(),
            day: $modal.find('.day').val(),
            room_slug: $modal.find('.room').val()
        };
    }

    // ============================================
    // TIME GENERATION
    // ============================================

    generateStartTimes(time, selectedDate, room_slug, idBooking = null, room_id = null) {
        const isEdit = idBooking !== null;
        const selectId = isEdit ? '#e_stime' : '#stime';
        const $select = $(selectId);

        $select.empty().append('<option value="">-- Select Time --</option>');

        this.timeRanges.forEach(timeSlot => {
            const hasConflict = this.hasStartTimeConflict(
                timeSlot,
                room_slug,
                idBooking
            );

            if (!hasConflict) {
                $select.append(`<option value="${timeSlot}">${timeSlot}</option>`);
            }
        });
    }

    generateEndTimes(stime, selectedDate, room_slug, idBooking = null, room_id = null) {
        const isEdit = idBooking !== null;
        const selectId = isEdit ? '#e_etime' : '#etime';
        const $select = $(selectId);

        $select.empty().append('<option value="">-- Select Time --</option>');

        if (!stime) return;

        const startMinutes = this.timeToMinutes(stime);
        const availableTimes = isEdit
            ? this.getAvailableEndTimesForEdit(stime, room_slug, idBooking, room_id)
            : this.getAvailableEndTimesForCreate(stime, room_slug);

        availableTimes.forEach(etime => {
            const etimeMinutes = this.timeToMinutes(etime);
            const durationText = this.formatDurationText(etimeMinutes - startMinutes);
            
            $select.append(`
                <option value="${etime}">
                    ${etime} (${durationText})
                </option>
            `);
        });
    }

    getAvailableEndTimesForCreate(stime, room_slug) {
        const startMinutes = this.timeToMinutes(stime);
        
        return this.timeRanges.filter(etime => {
            const etimeMinutes = this.timeToMinutes(etime);

            if (etimeMinutes <= startMinutes) return false;

            return !this.hasEndTimeConflict(etimeMinutes, startMinutes, room_slug);
        });
    }

    getAvailableEndTimesForEdit(stime, room_slug, idBooking, room_id) {
        const startMinutes = this.timeToMinutes(stime);
        const bookedTimes = this.getBookedTimesForRoom(room_id, idBooking);

        return this.timeRanges.filter(etime => {
            const etimeMinutes = this.timeToMinutes(etime);

            if (etimeMinutes <= startMinutes) return false;
            if (bookedTimes.includes(etime)) return false;

            return true;
        });
    }

    getBookedTimesForRoom(room_id, idBooking) {
        const bookedTimes = [];

        this.booked.forEach(booking => {
            if (booking.id === idBooking || booking.meeting_room_id !== room_id) {
                return;
            }

            let currentTime = this.timeToMinutes(booking.start_time);
            const endTime = this.timeToMinutes(booking.end_time);

            while (currentTime < endTime) {
                bookedTimes.push(this.fromMinutes(currentTime));
                currentTime += 30;
            }
        });

        return [...new Set(bookedTimes)];
    }

    // ============================================
    // CONFLICT DETECTION
    // ============================================

    hasStartTimeConflict(timeSlot, room_slug, idBooking = null) {
        const timeMinutes = this.timeToMinutes(timeSlot);

        return this.booked.some(booking => {
            if (booking.meeting_room.slug !== room_slug) return false;
            if (idBooking && booking.id === idBooking) return false;

            const bookingStart = this.timeToMinutes(booking.start_time);
            const bookingEnd = this.timeToMinutes(booking.end_time);

            return timeMinutes >= bookingStart && timeMinutes < bookingEnd;
        });
    }

    hasEndTimeConflict(endTimeMinutes, startTimeMinutes, room_slug) {
        return this.booked.some(booking => {
            if (booking.meeting_room.slug !== room_slug) return false;

            const bookingStart = this.timeToMinutes(booking.start_time);
            const bookingEnd = this.timeToMinutes(booking.end_time);

            // Overlap: end time extends into or past another booking
            return endTimeMinutes > bookingStart && startTimeMinutes < bookingEnd;
        });
    }

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================

    formatRoomName(slug) {
        return slug
            .split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    formatDateString(year, month, day) {
        return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }

    formatDurationText(durationMinutes) {
        const hours = Math.floor(durationMinutes / 60);
        const mins = durationMinutes % 60;

        let text = '';
        if (hours) text += `${hours} Hour${hours > 1 ? 's' : ''} `;
        if (mins) text += `${mins} Minutes`;

        return text.trim();
    }

    timeToMinutes(time) {
        const [h, m] = time.split(':').map(Number);
        return h * 60 + m;
    }

    fromMinutes(minutes) {
        const h = String(Math.floor(minutes / 60)).padStart(2, '0');
        const m = String(minutes % 60).padStart(2, '0');
        return `${h}:${m}`;
    }
}