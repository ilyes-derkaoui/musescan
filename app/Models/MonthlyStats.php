<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MonthlyStats extends Model {
    protected $fillable = ['month', 'year', 'total_scans', 'total_visits', 'total_feedback'];
}
