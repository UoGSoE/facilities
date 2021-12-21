<?php

namespace App\Http\Livewire;

use Livewire\Component;

class NotesEditor extends Component
{
    public $model;
    public $visible = false;
    public $body = '';

    public function mount($model)
    {
        $this->model = $model;
    }

    public function render()
    {
        return view('livewire.notes-editor', [
            'notes' => $this->model->fresh()->notes()->latest()->get(),
        ]);
    }

    public function toggleVisible()
    {
        $this->visible = ! $this->visible;
    }

    public function save()
    {
        if (! trim($this->body)) {
            return;
        }

        $this->model->notes()->create([
            'body' => $this->body,
            'user_id' => auth()->id(),
        ]);

        $this->body = '';
    }
}
