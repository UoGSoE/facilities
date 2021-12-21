<div>
    <button class="btn" wire:click.prevent="toggleVisible"><i class="bi bi-card-checklist"></i> Notes</button>
    @if ($visible)
    <div id="notes-box-{{ $model->html_id }}">
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">New Note</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" wire:model="body"></textarea>
        </div>
        <div class="mb-3"><button class="btn btn-secondary" wire:click="save">Save</button></div>
        <div class="accordion" id="accordionPanelsStayOpen-{{ $model->html_id }}">
            @foreach ($notes as $note)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-heading-{{ $note->html_id }}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse-{{ $note->html_id }}" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                            {{ $note->created_at->format('d/m/Y H:i') }} {{ optional($note->user)->full_name }} {{ substr($note->body, 0, 30) }}...
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapse-{{ $note->html_id }}" class="accordion-collapse collapse @if ($loop->first) show @endif" aria-labelledby="panelsStayOpen-heading-{{ $note->html_id }}">
                        <div class="accordion-body">
                            {{ $note->body }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
