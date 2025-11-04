<?php

namespace App\Transformers\Admin\Events;

class EventTransformer extends TransformerAbstract
{
    protected $type;
      protected array $availableIncludes = [ 'eventAudiences'];
        protected $graceStartMin = 10;
    protected $graceEndMin = 0;
    public function __construct($type)
    {
        $this->type = $type;
    }

      public function transform(Event $event): array
    {

        $page = '/events/detail';
        $url = url($page) . '/' . $event->slug;

         return [
            'id' => $event->id,
            'uuid' => $event->uuid,
            ];
    }

     public function includeEventAudiences(Event $event): Collection|NullResource
    {
        return $this->collection($event->eventAudiences, new \App\Transformers\Admin\Events\EventAudienceTransformer());
    }
