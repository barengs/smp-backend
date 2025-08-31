<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffStudyResource extends JsonResource
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
            'staff_id' => $this->staff_id,
            'study_id' => $this->study_id,
            'staff' => new StaffResource($this->whenLoaded('staff')),
            'study' => new SimpleStudyResource($this->whenLoaded('study')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
