<script setup>

const props = defineProps({
    audiences: Array
})
const form = useForm({
    audiance: []

})
const selectedAudiences = ref([]);
const customLabelAudiences = (audience) => audience?.name || '';
const validateAudiences = () => {
    form.errors.audiences = form.audiences?.length > 0 ? null : 'Please select at least one audience.';
}

watch(() => form.audiences, validateAudiences);
function validateForm() {
    validateAudiences();
}

</script>

<template>
    <div class="w-full">
        <div>
            <InputLabel for="audiences" :value="'Select Audience’s'" />
            <Multiselect v-model="selectedAudiences" :options="audiences" :custom-label="customLabelAudiences"
                track-by="id" placeholder="Select" :taggable="false" class="custom-multiselect mt-1" id="audiences"
                @update:modelValue="
                    (selected) => (form.audiences = selected.map((audience) => audience.id))"
                :multiple="true" />
            <InputError class="mt-2" :message="form.errors.audiences" />
        </div>
    </div>
</template>