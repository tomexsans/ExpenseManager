<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
import ModalVue from '@/Components/Modal.vue';
import { ref,onMounted, reactive, watch } from '@vue/runtime-core';
import { Inertia } from '@inertiajs/inertia';
import { Axios } from 'axios';

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position:'top-right' });


const state = reactive({
    usersData:{},
    openModal : false,
    modalTitle:"",    
    resultmessage:"",
    process:"",
    roles:{},
    form:{
        name:'',
        email:'',
        role_id:0,
        id:0
    }
})


const getRoles = () => {
    axios.get('/emgr/roles')
        .then((response)=>{
            if(response.status == 200){ 
                state.roles = response.data

            }
        })
        .catch(()=>{
            
        })
        .finally()

}

const getData = () => {
    axios.get('/emgr/users')
        .then((response)=>{
            if(response.status == 200){ 
                state.usersData = response.data.data
            }
        })
        .catch()
        .finally()
}

const editUser = (user) => {
    axios.get('/emgr/users/'+user.id)
        .then((response)=>{
            if(response.status == 200){ 
                state.form ={
                    name    :response.data.name,
                    email   :response.data.email,
                    role_id :response.data.role_id,
                    id      :response.data.id
                }

                state.openModal = true
                state.modalTitle = 'Update User'
                state.process = 'edit'
            }
        })
        .catch()
        .finally()
}

const addUser = () => {
    state.openModal = true
    state.modalTitle = 'Add User'
    state.process = 'add'
    
    state.form ={
        name    :'',
        email   :'',
        role_id :'',
        id      :''
    }
}


const addUserSubmit = () => {
    axios.post('/emgr/users',state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`User was successfully added`,{type:'success'});
                getData()
        }
    }).catch(()=>{
        toaster.show(`An Error was encountered`,{type:'danger'});

    })
}
const updateUserSubmit = () => {
        axios.put('/emgr/users/'+state.form.id,state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`User was successfully Updated`,{type:'success'});
                getData()
        }
    }).catch(function(){
        toaster.error(`An Error was encountered why deleting user`);

    })
}
const delUserSubmit = () => {
    axios.delete('/emgr/users/'+state.form.id,state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`User was successfully Deleted`,{type:'success'});
                getData()
        }else{
            toaster.warning(`An Error was encountered why deleting user`);

        }
    }).catch(error => {
        console.log(error.response)
        state.resultmessage = error.response.data.message
        toaster.error(`An Error was encountered while deleting user`);

    })
}

const handler = () => {

    switch(state.process){
        case 'add':
            addUserSubmit()
            break;
            case 'edit':
                updateUserSubmit()
                break;                
    }
    
}
onMounted(()=>{
    /*
        Get Users on mounted
    */
    getData()
    /*
        When updating users we need the Roles on the dropdown
        we can either fetch it when the Update modal is triggered
        Or Prefetch it on mount.
        for this project we will prefetch it
    */
    getRoles()
});

watch(state.openModal, (newX) => {
    console.log(newX)
})
</script>

<template>
    <Head title="Dashboard" />

    <ModalVue :open="state.openModal" :title="state.modalTitle">
        <p class="text-center text-gray-700 p-4">{{state.resultmessage}}</p>
        <form action="" @submit.prevent="handler">
            <div class="flex flex-col">
                <div class="w-full text-center">
                    <label class="float-left ml-20 mt-3">Name</label>
                    <input type="text" class="ml-10 w-2/4 float-right" v-model="state.form.name" required>
                </div>
                <div class="w-full text-center mt-5">
                    <label class="float-left ml-20 mt-3">Email Address</label>
                    <input type="email" class="ml-10 w-2/4 float-right" v-model="state.form.email" required>
                </div>            
                <div class="w-full text-center mt-5">
                    <label class="float-left ml-20 mt-3">Role</label>
                    <select  class="ml-10 w-2/4 float-right" v-model="state.form.role_id" required>
                        <option v-for="(role,i) in state.roles" :key="i" :value="role.id">{{role.name}}</option>
                    </select>
                </div>            
                <input type="hidden" v-model="state.form.id">
            </div>
            <div class="flex justify-between mt-6">
                <div>
                    <button v-if="state.process == 'edit'" @click.prevent="delUserSubmit" type="button" class="btn">Delete</button>        
                </div>
                <div>
                    <button class="btn mr-5" @click.prevent="state.openModal = false">Cancel</button>        
                    <button class="btn" type="submit">Save</button>        
                </div>
                
            </div>
        </form>
    </ModalVue>

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="User" breadcrumbs="User Management > User" />
            <div class="mt-20 w-4/5">
                <table class="table border w-full">
                    <thead>
                        <tr class="bg-gray-400">
                            <th class="text-left px-2">Name</th>
                            <th class="text-left px-2">Email Address</th>
                            <th class="text-left px-2">Role</th>
                            <th class="text-left px-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody v-if="state.usersData">
                        <tr @click.prevent="editUser(user)" v-for="(user,index) in state.usersData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ user.name }}</td>
                            <td class="px-2">{{ user.email }}</td>
                            <td class="px-2">{{ user.role.role_name }}</td>
                            <td class="px-2">{{ user.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="addUser">
                    ADD USER
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
