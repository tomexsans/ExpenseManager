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
    openModal : false,
    process : '',
    form:{
        role_name:'',
        role_description:'',
        id:0
    },
    errors:{
        role_name : '',
        role_description : '',
    }
})


const promptErrors = (err) => {
    state.errors.role_name = err.role_name[0] ?? ''
    state.errors.role_description = err.role_description[0] ?? ''
}
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

const updateRole = () => {
    axios.put('/emgr/roles/'+state.form.id,state.form)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openModal = false
                toaster.success(`Role was successfully Updated`)
            }
        }).catch(error => {
            toaster.warning(error.response.data.message ?? 'An error has occured, unable to update role')
            if(error.response.data.errors){
                promptErrors(error.response.data.errors)
            }
        })
}

const deleteRole = () => {
    axios.delete('/emgr/roles/'+state.form.id)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openModal = false
                toaster.success(`Role was successfully Deleted`)
            }
        }).catch(error => {


            toaster.warning(error.response.data.message ?? 'An error has occured, unable to Delete role')
            if(error.response.data.errors){
                promptErrors(error.response.data.errors)
            }
        })
}

const newRole = () => {
        axios.post('/emgr/roles/',state.form)
        .then((response)=>{
            if(response.status == 200){
                getData()
                state.openModal = false
                toaster.success(`Role was successfully Added`)
                
            }
        }).catch(error => {
            toaster.warning(error.response.data.message ?? 'An error has occured, unable to Add role')
            if(error.response.data.errors){
                state.errors.role_name = error.response.data.errors.role_name[0] ?? ''
                state.errors.role_description = error.response.data.errors.role_description[0] ?? ''
            }
        })
}



const openModalHandler = (type,data) => {

    state.process = type
    state.openModal = true

    //clear any errors
    state.errors= {
        role_name : '',
        role_description : '',
    }

    if(type == 'new'){
        
        state.form = {
            role_name: '',
            role_description:'',
            id:0
        }
    }

    if(type == 'edit'){
        /*
            Example of using data that was alreay loaded.
            Can be used for none criticial mission data.
        */
        state.form = {
            role_name: data.name,
            role_description:data.description,
            id:data.id
        }
    }
}

const submitHandler = () => {
    if(state.process == 'new'){
        newRole()
    }

    if(state.process == 'edit'){
        updateRole()
    }
}
onMounted(()=>{
    getData()
});
</script>

<template>
    <Head title="Dashboard" />

    <ModalVue :open="state.openModal" title="Add Role">
        <form @submit.prevent="submitHandler">
            <div class="">
                <div class="w-full flex justify-between mb-5">
                    <div>
                        <label class="">Display Name</label>
                    </div>
                    <div class="w-7/12">
                        <input type="text" v-model="state.form.role_name" class="w-full" required>
                        <span class="text-red-400">{{ state.errors.role_name}}</span>
                    </div>
                </div>
                <div class="w-full flex justify-between mb-5">
                    <div>
                        <label class="">Description</label>
                    </div>
                    <div class="w-7/12">
                       <input type="text" v-model="state.form.role_description" class="w-full" required>
                        <span  class="text-red-400">{{ state.errors.role_description}}</span>
                    </div>
                </div>                        
            </div>
            <div class="w-full flex justify-between mb-5">
                <div>
                    <button v-if="state.process == 'edit'" type="button" @click.prevent="deleteRole" class="btn mr-5">Delete</button>        
                </div>
                <div>
                    <button class="btn mr-5" @click.prevent="state.openModal = false">Cancel</button>        
                    <button class="btn">Save</button>        
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
                        <tr @click.prevent="openModalHandler('edit',roles)" v-for="(roles,index) in state.rolesData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ roles.name }}</td>
                            <td class="px-2">{{ roles.description }}</td>
                            <td class="px-2">{{ roles.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="openModalHandler('new')">
                    ADD Role
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
