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
    categoryData:{},
    openModal : false,
    modalTitle:"",    
    resultmessage:"",
    process:"",
    roles:{},
    errors:{
        name:'',
        desc:''
    },
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
    axios.get('/emgr/expensescat')
        .then((response)=>{
            if(response.status == 200){ 
                state.categoryData = response.data.data
            }
        })
        .catch()
        .finally()
}

const editCategory = (cat) => {
    axios.get('/emgr/expensescat/'+cat.id)
        .then((response)=>{
            if(response.status == 200){ 
                state.form ={
                    name    :response.data.category_name,
                    desc   :response.data.category_description,
                    id      :response.data.id
                }

                state.openModal = true
                state.modalTitle = 'Update Category'
                state.process = 'edit'
            }
        })
        .catch(errors => {
            toaster.warning(`An Error was encountered`);
            promptErrors(errors)
        })
        .finally()
}

const promptErrors = (response) => {

    if(response.response.data.errors){
        const err = response.response.data.errors
        state.errors.name          = err.name ? err.name.join('<br>') : ''
        state.errors.desc   = err.desc ? err.desc.join('<br>') : ''
    }
}

const addCategorySubmit = () => {
    axios.post('/emgr/expensescat',state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`Category was successfully added`,{type:'success'});
                getData()
        }
    }).catch(errors=>{
        toaster.warning(`An Error was encountered`);
        promptErrors(errors)
    })
}
const updateCategorySubmit = () => {
    axios.put('/emgr/expensescat/'+state.form.id,state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`Category was successfully Updated`,{type:'success'});
                getData()
        }
    }).catch(errors => {
        toaster.error(`An Error was encountered why Updating category`);
        promptErrors(errors)
    })
}
const delUserSubmit = () => {
    axios.delete('/emgr/expensescat/'+state.form.id,state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`Category was successfully Deleted`,{type:'success'});
                getData()
        }else{
            toaster.warning(`An Error was encountered why deleting category`);

        }
    }).catch(errors => {        
        toaster.error(errors.response.data.message ?? 'An Error was encountered while deleting category');
    })
}

const handler = () => {

    switch(state.process){
        case 'add':
            addCategorySubmit()
            break;
            case 'edit':
                updateCategorySubmit()
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

const modalHandler = (type,data) => {

    state.errors = {
        name:'',desc:''
    }

    if(type == 'new'){  
        state.openModal = true
        state.modalTitle = 'Add Category'
        state.process = 'add'
        
        state.form ={
            name    :'',
            desc   :'',
            id      :''
        }
    }

    if(type == 'edit'){
        editCategory(data)
    }   
}

</script>

<template>
    <Head title="Dashboard" />

    <ModalVue :open="state.openModal" :title="state.modalTitle">
        <p class="text-center text-gray-700 p-4">{{state.resultmessage}}</p>
        <form action="" @submit.prevent="handler">
            <div class="flex flex-col">
                <div class="flex flex-row w-full justify-between">
                    <div>
                        <label class="">Name</label>
                    </div>
                    <div class="w-3/6">
                        <input type="text" class="w-full" v-model="state.form.name" required/>
                        <span class="text-red-500">{{ state.errors.name }}</span>
                    </div>
                </div>
                <div class="flex flex-row w-full justify-between mt-5">
                    <div>
                        <label class="">Description</label>
                    </div>
                    <div class="w-3/6">
                        <input type="text" class="w-full" v-model="state.form.desc" required/>
                        <span class="text-red-500">{{ state.errors.desc }}</span>
                    </div>
                </div>                            
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
            <input type="hidden" v-model="state.form.id"/>
        </form>
    </ModalVue>

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="Expense Categories" breadcrumbs="Expense Management > Categories" />
            <div class="mt-20 w-4/5">
                <table class="table border w-full">
                    <thead>
                        <tr class="bg-gray-400">
                            <th class="text-left px-2">Display Name</th>
                            <th class="text-left px-2">Description</th>
                            <th class="text-left px-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody v-if="state.categoryData">
                        <tr @click.prevent="modalHandler('edit',cat)" v-for="(cat,index) in state.categoryData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ cat.name }}</td>
                            <td class="px-2">{{ cat.desc }}</td>
                            <td class="px-2">{{ cat.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="modalHandler('new')">
                    ADD Category
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
