<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
import ModalVue from '@/Components/Modal.vue';
import { ref,onMounted, reactive } from '@vue/runtime-core';

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position:'top-right' });

const state = reactive({
    rolesData:{},
    openAddRole : false,
    openUpdateRole : false,
    form:{
        role_name:'',
        role_description:'',
        id:0
    }
})

const getData = () => {
    axios.get('/emgr/roles')
        .then((response)=>{
            if(response.status == 200){ 
                state.rolesData = response.data 
            }
        })
        .catch()
        .finally()
}

const updateForm = (d) => {
    state.openUpdateRole = true
    state.form = {
        role_name: d.name,
        role_description:d.description,
        id:d.id
    }
}

const updateRole = () => {
    axios.put('/emgr/roles/'+state.form.id,state.form)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openUpdateRole = false
                toaster.success(`Role was successfully Updated`)
            }
        }).catch(error => {
            toaster.warning(error.response.data.message ?? 'An error has occured, unable to update role')
        })
}

const deleteRole = () => {
    axios.delete('/emgr/roles/'+state.form.id)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openUpdateRole = false
                toaster.success(`Role was successfully Deleted`)
            }
        }).catch(error => {
            toaster.warning(error.response.data.message ?? 'An error has occured, unable to Delete role')
        })
}

const newRole = () => {
        axios.post('/emgr/roles/',state.form)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openAddRole = false
                toaster.success(`Role was successfully Added`)
            }
        }).catch(error => {
            toaster.warning(error.response.data.message ?? 'An error has occured, unable to Add role')
        })
}

const addNewRole = () => {
    state.openAddRole = true
        state.form = {
        role_name: '',
        role_description:'',
        id:0
    }
}

onMounted(()=>{
    getData()
});
</script>

<template>
    <Head title="Dashboard" />

    <ModalVue :open="state.openAddRole" title="Add Role">
        <form @submit.prevent="newRole">
            <div class="flex flex-col">
                <div class="w-full text-center">
                    <label class="float-left ml-20 mt-3">Display Name</label>
                    <input type="text" v-model="state.form.role_name" class="ml-10 w-2/4 float-right">
                </div>
                <div class="w-full text-center mt-5">
                    <label class="float-left ml-20 mt-3">Description</label>
                    <input type="text"  v-model="state.form.role_description" class="ml-10 w-2/4 float-right">
                </div>            
            </div>
            <div class="float-right mt-5">
                <button class="btn mr-5" @click.prevent="state.openAddRole = false">Cancel</button>        
                <button class="btn">Save</button>        
            </div>
        </form>

    </ModalVue>

    <ModalVue :open="state.openUpdateRole" title="Update Role">
        <form @submit.prevent="updateRole">
        <div class="flex flex-col">
            <div class="w-full text-center">
                <label class="float-left ml-20 mt-3">Display Name</label>
                <input type="text" v-model="state.form.role_name" class="ml-10 w-2/4 float-right">
            </div>
            <div class="w-full text-center mt-5">
                <label class="float-left ml-20 mt-3">Description</label>
                <input type="text" v-model="state.form.role_description" class="ml-10 w-2/4 float-right">
                <input type="hidden" v-model="state.form.id" class="ml-10 w-2/4 float-right">
            </div>            
        </div>
        
        <div class="flex justify-between mt-5">
            <div>
                <button type="button" @click.prevent="deleteRole" class="btn mr-5">Delete</button>        
            </div>
            <div>
                <button class="btn mr-5" @click.prevent="state.openUpdateRole = false">Cancel</button>        
                <button type="submit" class="btn">Update</button>        
            </div>            
        </div>
        </form>
    </ModalVue>

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="Roles" breadcrumbs="User Management > Roles" />
            <div class="mt-20 w-4/5">
                <table class="table border w-full">
                    <thead>
                        <tr class="bg-gray-400">
                            <th class="text-left px-2">Display Name</th>
                            <th class="text-left px-2">Display Description</th>
                            <th class="text-left px-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody v-if="state.rolesData">
                        <tr @click.prevent="updateForm(roles)" v-for="(roles,index) in state.rolesData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ roles.name }}</td>
                            <td class="px-2">{{ roles.description }}</td>
                            <td class="px-2">{{ roles.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="addNewRole">
                    ADD Role
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
