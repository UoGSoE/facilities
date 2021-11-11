<?php

namespace App\Models;

trait GenericAllocationLogic
{
    public function scopeRecentlyAllocated($query, int $numberOfDays = 28)
    {
        return $query->where('allocated_at', '>=', now()->subDays($numberOfDays));
    }

    public function scopeUnallocated($query)
    {
        return $query->whereNull('people_id');
    }

    public function scopeAllocated($query)
    {
        return $query->whereNotNull('people_id');
    }

    public function allocateTo(People $person, ?string $avantiTicketId = '')
    {
        $this->allocateToId($person->id, $avantiTicketId);
    }

    public function allocateToId(int $personId, ?string $avantiTicketId = '')
    {
        $this->update(['people_id' => $personId, 'avanti_ticket_id' => $avantiTicketId]);
    }

    public function deallocate()
    {
        $this->update(['people_id' => null, 'avanti_ticket_id' => null]);
    }

    public function isAllocated(): bool
    {
        return $this->people_id != null;
    }

    public function isUnallocated(): bool
    {
        return $this->people_id == null;
    }
}
