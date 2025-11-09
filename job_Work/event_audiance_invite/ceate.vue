<script setup>

const props = defineProps({
    audiences: Array
})
const form = useForm({
    audiance:[]

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