<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserVipSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Chuyển đổi thông tin subscription thành dạng JSON với các thông tin cần thiết
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package' => new VipPackageResource($this->whenLoaded('vipPackage')),
            'status' => [
                'is_active' => $this->isActive(),
                'payment_status' => $this->payment_status,
                'days_remaining' => $this->getDaysRemaining(),
            ],
            'dates' => [
                'start_date' => $this->start_date->format('Y-m-d H:i:s'),
                'end_date' => $this->end_date->format('Y-m-d H:i:s'),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            'payment' => [
                'transaction_id' => $this->vnpay_transaction_id,
                'amount' => $this->whenLoaded('vipPackage', function () {
                    return $this->vipPackage->price;
                }),
            ],
        ];
    }
}