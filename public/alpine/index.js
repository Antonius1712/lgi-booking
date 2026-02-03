document.addEventListener('alpine:init', () => {
    Alpine.data('pickTime', () => ({
        range: {},
        time_slot: '',
        addRange(room, time, checked) {
            this.time_slot = `${time} - ${moment(time, 'HH:mm').add(30, 'minutes').format('HH:mm')}`;
            if (checked) {
                // Add time to the room array
                this.range[room] ??= [];

                if (!this.range[room].includes(this.time_slot)) this.range[room].push(this.time_slot);
            } else {
                // Remove time if unchecked
                if (this.range[room]) {
                    this.range[room] = this.range[room].filter(t => t !== this.time_slot);
                    if (!this.range[room].length) delete this.range[room];
                }
            }

            if (this.range[room]) {
                this.range[room].sort((a, b) => {
                    const startA = moment(a.split(' - ')[0], 'HH:mm');
                    const startB = moment(b.split(' - ')[0], 'HH:mm');
                    return startA - startB;
                });
            }

            // console.log(this.time_slot, this.range);
        },
        reset() {
            this.range = {};  // <-- clear all selected times
            this.time_slot = '';
        }
    }));
});