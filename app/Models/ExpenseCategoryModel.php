<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExpensesModel as Expenses;


class ExpenseCategoryModel extends Model
{
    use HasFactory;
    protected $table = 'expense_category';
    protected $fillable = ['category_name','category_description'];

    public function listed()
    {
        return $this->hasmany(Expenses::class,'id','expense_category_id');

    }    
}
