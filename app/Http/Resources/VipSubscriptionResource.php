<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VipSubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package' => [
                'id' => $this->package->id,
                'name' => $this->package->name,
                'duration_days' => $this->package->duration_days,
                'price' => $this->package->price,
            ],
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'payment_status' => $this->payment_status,
            'vnpay_transaction_id' => $this->vnpay_transaction_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}