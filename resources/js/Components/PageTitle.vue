<script setup>
const props = defineProps([
    'title',
    'breadcrumbs'
])
</script>

<template>
    <div class="flex justify-between">
        <div>
            <h1 class="font-bold text-xl">{{props.title}}</h1>    
        </div>
        <div>
            {{props.breadcrumbs}}
        </div>
    </div>
</template>