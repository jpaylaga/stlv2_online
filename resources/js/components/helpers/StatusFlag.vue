<template>
    <div style="display:inline;">
        <i v-if="value > 0" class="ft-arrow-up teal accent-3"></i>
        <i v-else-if="value < 0" class="ft-arrow-down red accent-3"></i>
    </div>
</template>
<script>
    export default {
        props: ['value'],
        created () {

        },
    }
</script>
