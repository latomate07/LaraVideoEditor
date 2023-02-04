<script setup>
defineProps({
    title: String
});
</script>

<template>
    <main class="grid gap-2 grid-cols-[20%_60%_20%]">
        <slot name="filesOrganisation"></slot>
        <slot name="display"></slot>
        <slot name="edit"></slot>
    </main>
</template>