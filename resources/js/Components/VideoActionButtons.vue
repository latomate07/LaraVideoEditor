<script setup>
const emits = defineEmits([
    'backwardEvent',
    'forwardEvent',
    'playVideo',
    'replayVideo'
]);
</script>

<template>
    <div class="flex flex-row justify-around whitespace-nowrap overflow-x-scroll mt-5">
        <i @click="$emit('backwardEvent')"
            class="fa fa-backward cursor-pointer bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold p-3 rounded"></i>
        <i @click="$emit('playVideo', $event)"
            class="fa fa-play cursor-pointer bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold p-3 rounded"></i>
        <i @click="$emit('forwardEvent')"
            class="fa fa-forward cursor-pointer bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold p-3 rounded"></i>
        <i @click="$emit('replayVideo')"
            class="fa fa-repeat cursor-pointer bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold p-3 rounded"></i>
        <i class="fa fa-download cursor-pointer bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold p-3 rounded"></i>
    </div>
</template>