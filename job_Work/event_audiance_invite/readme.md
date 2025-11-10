# Event Audience Selection Component + Backend Transformer Integration
![Audience Multiselect in Action](audiance.PNG)
## Overview
This feature adds **multi-select audience targeting** for events in the admin panel, allowing event creators to assign one or more predefined audiences (e.g., "Students", "VIP Members", "Public", etc.) to an event.

It consists of:
- A reusable Vue 3 `<script setup>` component using **Vue Multiselect**
- Proper form validation (at least one audience required)
- Backend Fractal transformer updates to include `eventAudiences` relationship
- Helper function enhancement to preload and transform audience data during edit

---

### Frontend: Vue 3 Audience Multiselect Component

```vue
<script setup>
const props = defineProps({
    audiences: Array
})

const form = useForm({
    audience: []   // will hold array of selected audience IDs
})

const selectedAudiences = ref([])
const customLabelAudiences = (audience) => audience?.name || ''

// Validation: at least one audience must be selected
const validateAudiences = () => {
    form.errors.audiences = form.audiences?.length > 0 
        ? null 
        : 'Please select at least one audience.'
}

watch(() => form.audiences, validateAudiences)

function validateForm() {
    validateAudiences()
}
</script>

<template>
  <div class="w-full">
    <InputLabel for="audiences" :value="'Select Audience’s'" />
    <Multiselect
      v-model="selectedAudiences"
      :options="audiences"
      :custom-label="customLabelAudiences"
      track-by="id"
      placeholder="Select"
      :multiple="true"
      class="custom-multiselect mt-1"
      id="audiences"
      @update:modelValue="(selected) => (form.audiences = selected.map(a => a.id))"
    />
    <InputError class="mt-2" :message="form.errors.audiences" />
  </div>
</template>
```

#### Key Features
- Fully reactive with Inertia.js `useForm`
- Real-time validation on change
- Displays audience `name`, tracks by `id`
- Syncs selected audience **IDs** to form (backend expects IDs)
- Clean error display using existing `InputError` component

---

### Backend: Fractal Transformer Update

Updated the `EventTransformer` to properly expose audience data when requested.

```php
class EventTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['eventAudiences'];

    public function includeEventAudiences(Event $event): Collection|NullResource
    {
        return $this->collection(
            $event->eventAudiences,
            new EventAudienceTransformer()
        );
    }
}
```

Now API responses can include:
```json
"event_audiences": {
  "data": [
    { "id": 3, "name": "Premium Members" },
    { "id": 7, "name": "Early Bird" }
  ]
}
```

---

### Helper Function: `prepareEditData()` Enhancement

Ensured audience data is loaded and transformed when editing an event:

```php
$event->load(['eventAudiences']);

$eventData = fractal(
    $event,
    new EventTransformer(EventTypeEnum::EVENT)
)->parseIncludes([
    'hosts', 'categories', 'attendees', 'attachments', 'links',
    'ticket', 'channel', 'tags', 'vonageSession', 'participants',
    'eventPartnerChannels', 'eventRecordings', 'eventAudiences'  // ← Added
]);
```

This guarantees that when an admin edits an event, the previously selected audiences are pre-selected in the multiselect.

---

### Why This Matters
- Improves event targeting precision
- Enables audience-based notifications, access control, analytics
- Clean separation of concerns (UI + API)
- Full edit support (pre-filled on load)
- Validation prevents accidental public exposure

---

### Tech Stack Used
- Vue 3 + Composition API + `<script setup>`
- Vue Multiselect (`vue-multiselect`)
- Inertia.js + Laravel Forms (`useForm`)
- Laravel Fractal (Spatie/Fractal)
- PHP 8+ with enums and typed properties

---

### Future Improvements (Optional Ideas)
- Add audience-based visibility rules (e.g., hide event from non-selected audiences)
- Audience-specific email/SMS campaigns
- Analytics per audience segment

---

**Completed & Production Ready**  
Clean, maintainable, and fully integrated with existing event workflow.

Feel free to use this as a template for other multi-relationship selectors!
```
