<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Carbon\Carbon;
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'user_name'=>optional($this->user)->name ?? 'Unknown User',
            'created_at'=>Carbon::parse($this->created_at)->format('Y-m-d h:i:s A'),
            'created_at_readable'=>Carbon::parse($this->created_at)->diffForHumans(),
            'category_name'=>optional($this->category)->name ?? 'Unknown Category',
            'title'=>$this->title,
            'description'=> Str::limit($this->description,100),
            'image_path'=>$this->image ? asset('storage/media/'. $this->image->filename):null,
        ];
    }
}
