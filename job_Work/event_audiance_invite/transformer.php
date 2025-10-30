<?php

namespace App\Transformers\Admin\Events;

use App\Models\EventAudience;
use League\Fractal\TransformerAbstract;

class EventAudienceTransformer extends TransformerAbstract
{
   
    public function transform(EventAudience $eventAudience)
    {
        return [
            'id' => $eventAudience->audience->id,
            'name' => $eventAudience->audience->name,
        ];
    }
}