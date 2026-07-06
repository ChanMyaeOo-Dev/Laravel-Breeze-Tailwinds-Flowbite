<?php

namespace App\Models;

use Database\Factories\TableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    /** @use HasFactory<TableFactory> */
    use HasFactory;
}
