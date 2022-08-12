<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Users;
class RolesModel extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['role_name','role_description'];
}
