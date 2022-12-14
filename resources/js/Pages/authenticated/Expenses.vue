<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
import ModalVue from '@/Components/Modal.vue';
import { onMounted , reactive } from 'vue';

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position:'top-right' });

const state = reactive({
    expensesData:{},
    openModal : false,
    modalTitle:"",    
    resultmessage:"",
    process:"",
    categories:{},
    error:{
        amount    :'',
        entry_date   :''        
    },
    form:{
        name:'',
        email:'',
        role_id:0,
        id:0
    }
})

const getCategories = () => {
    axios.get('/emgr/expensescat')
        .then((response)=>{
            if(response.status == 200){ 
                state.categories = response.data.data
            }
        })
        .catch(()=>{
            
        })
        .finally()

}

const getData = () => {
    axios.get('/emgr/expenses')
        .then((response)=>{
            if(response.status == 200){ 
                state.expensesData = response.data.data
            }
        })
        .catch()
        .finally()
}

const editExpenses = (exopense) => {
    state.error = {
        amount    :'',
        entry_date   :''        
    }
    
    axios.get('/emgr/expenses/'+exopense.id)
        .then((response)=>{
            if(response.status == 200){ 
                state.form ={
                    amount      :response.data.amount,
                    entry_date  :response.data.entry_date,
                    category    :response.data.expense_category_id,
                    id          :response.data.id
                }

                state.openModal = true
                state.modalTitle = 'Update Expenses'
                state.process = 'edit'
            }
        })
        .catch(errors =>{
            toaster.error('Unable to fetch Expenses')
            promptErrors(errors)
        })
        .finally()
}


const modalHandler = (type,data) => {
    state.error = {
        amount    :'',
        entry_date   :''        
    }
    if( type =='new' ){

        state.openModal = true
        state.modalTitle = 'Add Expenses'
        state.process = 'add'
                
        state.form ={
            amount    :'',
            entry_date   :'',
            category :'',
            id      : ''
        }
    }

    if( type =='edit' ){
        editExpenses(data)
    }
}

const promptErrors = (response) => {

    if(response.response.data.errors){
        const err = response.response.data.errors
        state.error.amount = err.amount ? err.amount[0] : ''
        state.error.entry_date = err.entry_date ? err.entry_date[0] : ''        
    }      
       
}

const addExpensesSubmit = () => {
    axios.post('/emgr/expenses',state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`Expenses was successfully added`,{type:'success'});
                getData()
        }
    }).catch(respErrors =>{
        toaster.warning(`An Error was encountered`);
        promptErrors(respErrors)
    })
}
const updateUserSubmit = () => {
        axios.put('/emgr/expenses/'+state.form.id,state.form)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`User was successfully Updated`,{type:'success'});
                getData()
        }
    }).catch(respErrors => {
        toaster.error(`An Error was encountered why updating expense`);
        promptErrors(respErrors)
    })
}
const delUserSubmit = () => {
    axios.delete('/emgr/expenses/'+state.form.id)
    .then((response)=>{
        if(response.status == 200){
                state.openModal = false
                toaster.show(`Expenses was successfully Deleted`,{type:'success'});
                getData()
        }else{
            toaster.warning(`An Error was encountered why deleting Expenses`);

        }
    }).catch(error => {
        state.resultmessage = error.response.data.message
        toaster.error(`An Error was encountered while deleting user`);

    })
}

const handler = () => {

    switch(state.process){
        case 'add':
            addExpensesSubmit()
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
    getCategories()
});


</script>

<template>
    <Head title="Dashboard" />

    <ModalVue :open="state.openModal" :title="state.modalTitle">
        <p class="text-center text-gray-700 p-4">{{state.resultmessage}}</p>
        <form action="" @submit.prevent="handler">
            <div class="">
                <div class="w-full text-center flex flex-row justify-between">
                    <div>
                        <label class="">Expense Category</label>
                    </div>
                    <div class="w-3/6">
                        <select  class="w-full" v-model="state.form.category" required>
                            <option v-for="(cat,i) in state.categories" :key="i" :value="cat.id">{{cat.name}}</option>
                        </select>
                    </div>
                </div>        

                <div class="w-full text-center flex flex-row justify-between mt-5">
                    <div>
                        <label>Amount</label>
                    </div>
                    <div class="w-3/6">
                        <input type="text" class="w-full" v-model="state.form.amount" required>
                        <span class="text-red-500">{{ state.error.amount }}</span>
                    </div>
                </div>        

                <div class="w-full text-center flex flex-row justify-between mt-5">
                    <div>
                        <label>Entry Date</label>
                    </div>
                    <div class="w-3/6">
                        <input type="date" class="w-full" v-model="state.form.entry_date" required>                
                        <span class="text-red-500">{{ state.error.entry_date }}</span>
                        <input type="hidden" v-model="state.form.id">

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
        </form>
    </ModalVue>

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="Expenses" breadcrumbs="Expense Management > Expenses" />
            <div class="mt-20 w-4/5">
                <table class="table border w-full">
                    <thead>
                        <tr class="bg-gray-400">
                            <th class="text-left px-2">Expense Category</th>
                            <th class="text-left px-2">Amount</th>
                            <th class="text-left px-2">Entry Date</th>
                            <th class="text-left px-2">Created At</th>
                        </tr>
                    </thead>
                    <tbody v-if="state.expensesData">
                        <tr @click.prevent="modalHandler('edit',expense)" v-for="(expense,index) in state.expensesData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ expense.category.category_name }}</td>
                            <td class="px-2">{{ expense.amount }}</td>
                            <td class="px-2">{{ expense.entry_date }}</td>
                            <td class="px-2">{{ expense.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="modalHandler('new')">
                    ADD EXPENSES
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
