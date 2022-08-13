<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';
import { reactive } from '@vue/reactivity';

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position:'top-right' });

const state = reactive({
    errors:{oldpassword:'',newpassword:'',newpassword_confirmation:''},
    form:{oldpassword:'',newpassword:'',newpassword_confirmation:''},
    status:""
})

const changePassword = () => {
    axios.put('changepassword',state.form)
        .then(response => {
            if(response.status == 200){
                state.status = "Successfully Updated Password, you will be logged out in a few seconds"
                toaster.success(`Successfully Updated Password, you will be logged out in a few seconds`)

                setTimeout(()=>{
                    window.location = route('logout')
                },4000)
            }

        })
        .catch(errors => {
            console.log(errors)
            toaster.error(`Error has occured while changing your password`)
            if(typeof errors.response.data.errors != "undefined"){
                const err = errors.response.data.errors
                state.errors.oldpassword = err.oldpassword ? err.oldpassword[0] : ''
                state.errors.newpassword = err.newpassword ? err.newpassword[0] : ''
                state.errors.newpassword_confirmation = err.newpassword_confirmation ? err.newpassword_confirmation[0] : ''
            }
        })
}

</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="Change Password" breadcrumbs="User Management > Change Password" />

            <div>
                <div class="lg:w-1/3 flex flex-col mx-auto border border-gray-300 p-20 mt-20">

                    <p class="text-green-400 p-2 text-center">{{state.status}}</p>
                    <form @submit.prevent="changePassword">
                    <div class="flex flex-col">
                        <div>
                            <label class="mt-3">Old Password</label>
                        </div>
                        <div class="w-full">                    
                            <input type="text" class="w-full" v-model="state.form.oldpassword" required>
                            <span class="text-red-400">{{ state.errors.oldpassword }}</span>
                        </div>
                    </div>   
                    <div class="flex flex-col mt-5">
                        <div>
                            <label class="mt-3">New Password</label>
                        </div>
                        <div class="w-full">                    
                            <input type="text" class="w-full" v-model="state.form.newpassword" required>
                            <span class="text-red-400">{{ state.errors.newpassword }}</span>
                        </div>
                    </div>    
                    <div class="flex flex-col mt-5">
                        <div>
                            <label class="mt-3">Confirm New Password</label>
                        </div>
                        <div class="w-full">                    
                            <input type="text" class="w-full" v-model="state.form.newpassword_confirmation" required>
                            <span class="text-red-400">{{ state.errors.newpassword_confirmation }}</span>
                        </div>
                    </div>    
                    <div class="flex flex-col mt-5">
                        <button class="btn" type="submit">Change Password</button>        
                    </div>                        
                    </form>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
