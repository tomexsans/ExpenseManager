<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="My Expenses" breadcrumbs="Dashboard" />



            <div>
                

            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
