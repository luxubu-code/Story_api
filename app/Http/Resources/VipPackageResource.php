<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VipPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Chuyển đổi thông tin gói VIP thành dạng JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'features' => $this->features,
            'is_popular' => $this->is_popular,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}