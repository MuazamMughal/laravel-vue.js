if (!function_exists('prepareEditData')) {
    function prepareEditData($event, $type, $typeBaseUrl)
    {
          $event->load([ 'eventAudiences'])

            $eventData = fractal(
            $event,
            new \App\Transformers\Admin\Events\EventTransformer(EventTypeEnum::EVENT)
        )->parseIncludes(['hosts', 'categories', 'attendees', 'attachments', 'links', 'ticket', 'channel', 'tags', 'vonageSession', 'participants', 'eventPartnerChannels', 'eventRecordings', 'eventAudiences']);

          $transformedData = $eventData->toArray();

             $data = [
            'event' => $eventData,
            ]
