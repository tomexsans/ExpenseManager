<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::truncate();
        \App\Models\RolesModel::truncate();
        \App\Models\ExpenseCategoryModel::truncate();
        \App\Models\ExpensesModel::truncate();

        \App\Models\User::factory()->create([
            'name' => 'Admin John',
            'email' => 'admin@system.com',
            'role_id' => 1
        ]);
    
        \App\Models\User::factory()->create([
            'name' => 'User John',
            'email' => 'user@system.com',
            'role_id' => 2
        ]);
    
        \App\Models\RolesModel::upsert([
            ['role_description'=>'Super User','role_name' => 'Administrator'],
            ['role_description'=>'Can Add Expenses','role_name' => 'User']], ['role_name'],[]);
    

        \App\Models\ExpenseCategoryModel::upsert([
            ['category_description'=>'' ,'category_name' => 'Utilities'],
            ['category_description'=>'' ,'category_name' => 'Needs'],
            ['category_description'=>'' ,'category_name' => 'Debts']],[]);

        \App\Models\ExpensesModel::upsert([
            ['amount' => 4353.43,'user_id'=>1,'expense_category_id'=>1,'entry_date'=>'2022-03-04'],
            ['amount' => 5353.43,'user_id'=>1,'expense_category_id'=>1,'entry_date'=>'2022-03-04'],
            ['amount' => 843.43,'user_id'=>1,'expense_category_id'=>1,'entry_date'=>'2022-03-04'],
            ['amount' => 1843.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 1843.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 1843.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 4253.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 234.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 3.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 123.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 23.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
            ['amount' => 53.43,'user_id'=>1,'expense_category_id'=>3,'entry_date'=>'2022-03-04'],
        ],[]);

        
    }
}
