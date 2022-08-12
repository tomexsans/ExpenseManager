<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head,usePage } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
import ModalVue from '@/Components/Modal.vue';
import { onMounted, computed,reactive, watch } from 'vue';

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position:'top-right' });
const props = defineProps(['ziggy','props','pages'])
const user = computed(() => usePage().props.value.auth.user)

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
        amount    :'',
        entry_date   :'',
        category :'',
        id      : ''
    }

    state.error = {
        amount    :'',
        entry_date   :''        
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
    }).catch(errors =>{
        toaster.warning(`An Error was encountered`);
        
        state.error.amount = errors.response.data.errors.amount ?? ''
        state.error.entry_date = errors.response.data.errors.entry_date ?? ''
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
    }).catch(function(){
        toaster.error(`An Error was encountered why deleting user`);

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
        console.log(error.response)
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
            <div class="flex flex-col">
                <div class="w-full text-center ">
                    <label class="float-left ml-9 mt-3">Expense Category</label>
                    
                    <select  class="ml-10 w-2/4 float-right" v-model="state.form.category" required>
                        <option v-for="(cat,i) in state.categories" :key="i" :value="cat.id">{{cat.name}}</option>
                    </select>
                </div>                  
                <div class="w-full text-center mt-5">
                    <label class="float-left ml-9 mt-3">Amount</label>
                     <div class="ml-10 w-2/4 float-right">
                        <input type="text" class="w-full" v-model="state.form.amount" required>
                        <span>{{ state.error.amount }}</span>
                    </div>
                </div>
                <div class="w-full text-center mt-5">
                    <label class="float-left ml-9 mt-3">Entry Date</label>
                    <div class="ml-10 w-2/4 float-right">
                        <input type="date" class="w-full" v-model="state.form.entry_date" required>
                        <span>{{ state.error.entry_date }}</span>
                    </div>
                    

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
                        <tr @click.prevent="editExpenses(expense)" v-for="(expense,index) in state.expensesData" :key="index"
                            class="odd:bg-gray-100 even:bg-gray-300 hover:cursor-pointer hover:bg-slate-400">
                            <td class="px-2">{{ expense.category.category_name }}</td>
                            <td class="px-2">{{ expense.amount }}</td>
                            <td class="px-2">{{ expense.entry_date }}</td>
                            <td class="px-2">{{ expense.created }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <button class="btn float-right mt-5" @click.prevent="addUser">
                    ADD EXPENSES {{ $page.props.userid}}
                </button>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
