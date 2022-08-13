
<script setup>
import { Link } from '@inertiajs/inertia-vue3';

</script>

<template>
    <div class="px-6">
        <div class="mt-6">
            <div class="w-24 h-24 rounded-full bg-gray-500 mb-4 mx-auto"></div>
            <Link :href="route('dashboard')" class="py-3 text-lg text-white font-bold">{{ $page.props.auth.user.name }} ({{$page.props.auth.role.role_name}})</Link>
        </div>

        <div class="mt-7">
            <ul id="menu">                
                <li class="text-white">
                    User Management
                    <ul>
                        <li v-if="$page.props.auth.user.role_id == 1" ><Link :href="route('roles')" class="">Roles</Link></li>
                        <li v-if="$page.props.auth.user.role_id == 1" ><Link :href="route('users')" class="">User</Link></li>
                    </ul>
                </li>            

                <li class="text-white">
                    Expense Management
                    <ul>
                        <li v-if="$page.props.auth.user.role_id == 1"><Link :href="route('categories')" class="">Expense Categories</Link></li>
                        <li><Link :href="route('expenses')" class="">Expenses</Link></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
ul#menu li{
    @apply text-lg font-bold py-1;
}

ul#menu li a{
    @apply pl-6;
}
</style>