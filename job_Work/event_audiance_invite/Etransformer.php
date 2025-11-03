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
