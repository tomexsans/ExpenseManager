<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import PageTitleVue from '@/Components/PageTitle.vue';

import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale
} from 'chart.js'
import { reactive } from '@vue/reactivity';
import { onMounted } from '@vue/runtime-core';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)
ChartJS.defaults.scale.ticks.display = false;


const data = reactive({
    chartOptions: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        elements: {
            arc: {
                backgroundColor: ['#a3be8c','#fcc419','#3F69AA','#ffff4e'],
                hoverBackgroundColor: '#44DE28'
            }
        } 
        
    },
    chartData: {
        labels: [],
        datasets: [ { data: [] } ]
    },
    tableData:{}
})


const getData = () => {
    axios.get('/myexpenses')
    .then(response => {

        data.tableData = response.data ?? []

        let labels = []
        let dataSets = []

        response.data.forEach(element => {
             labels.push(element.category.category)
             dataSets.push(element.amount)
        });

        data.chartData.labels = labels
        data.chartData.datasets = [ {data : dataSets }]
    })
}
onMounted(()=>{
    getData();

});

</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <div class="px-16 py-16">
            <PageTitleVue title="My Expenses" breadcrumbs="Dashboard" />



            <div class="flex">                
                <div class="w-5/12 pt-32 pl-10 pr-32">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th align="center">Expenses Categories</th>
                                    <th align="right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenses,index) in data.tableData" :key="index">
                                    <td align="center">{{ expenses.category.category }}</td>
                                    <td align="right">{{ expenses.amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                <div class="w-5/12 pt-20">
                    <Pie
                        :chart-options="data.chartOptions"
                        :chart-data="data.chartData"
                        chart-id="bar-chart"
                        dataset-id-key="label"
                        plugins="plugins"
                        css-classes=""
                        styles=""
                        width="400"
                        height="400"
                    />
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
