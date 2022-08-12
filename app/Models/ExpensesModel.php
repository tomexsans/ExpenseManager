<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExpenseCategoryModel;

class ExpensesModel extends Model
{
    use HasFactory;
    protected $table = 'expenses';
    protected $fillable = ['amount','user_id','expense_category_id','entry_date'];


    public function category()
    {
        return $this->hasOne(ExpenseCategoryModel::class,'id','expense_category_id');
    }
}
