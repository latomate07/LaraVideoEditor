<script setup>
    defineProps({
        videoTitle: String,
        timelineTitle: String,
        settings: String
    });
</script>

<template>
    <nav>
        <ul class="list-none flex justify-between border-b-1 shadow-lg shadow-indigo-500/50">
            <li class="text-center p-3 pl-32 cursor-pointer hover:scale-125 ease-in-out duration-300">{{ videoTitle }}</li>
            <li class="p-3 cursor-pointer hover:scale-125 ease-in-out duration-300">{{ timelineTitle }}</li>
            <li class="p-3 pr-32 cursor-pointer hover:scale-125 ease-in-out duration-300">{{ settings }}</li>
        </ul>
    </nav>
</template>