<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assigned_to' => $this->assignedToUser->name, 
            'created_by' => $this->createdByUser->name,
            'previous_status' => $this->previous_status,
            'new_status' => $this->new_status,
            'updated_by' => $this->updatedByUser->name, 
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
