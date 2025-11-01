<?php

namespace App\Transformers\Admin\Events;

class EventTransformer extends TransformerAbstract
{
    protected $type;
      protected array $availableIncludes = ['host', 'hosts', 'expert', 'partnerChannel', 'categories', 'attendees', 'attachments', 'links', 'ticket', 'channel', 'tags', 'eventAudiences', 'vonageSession', 'participants', 'files', 'userEventActivities', 'eventPartnerChannels', 'seriesEvents', 'eventRecordings', 'handRaisedUsers'];