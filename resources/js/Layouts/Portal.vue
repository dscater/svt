<script setup>
import { ref, provide, onMounted } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
const { props: props_page } = usePage();
const url_asset = ref(props_page.url_assets);

onMounted(() => {});
</script>
<template>
    <div id="app" class="fade show">
        <slot></slot>
    </div>
</template>
<style scoped></style>
