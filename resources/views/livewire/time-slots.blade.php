<div class="col-md-12">
    <div class="card p-3">
        <h5>Time Slots Test Component</h5>

        <div id="count" class="mb-2">
            Count: {{ $count }}
        </div>

        {{-- <button class="btn btn-primary" wire:click="addCount">
            Add
        </button> --}}

        <button class="btn btn-primary" wire:click.prevent="addCount">Add</button>

    </div>
</div>
