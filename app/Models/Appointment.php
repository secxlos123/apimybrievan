<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Appointment extends Model implements AuditableContract
{
    use Auditable;

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['title', 'appointment_date', 'appointment_date_res', 'user_id', 'ao_id','eform_id', 'ref_number', 'longitude', 'latitude', 'address', 'status', 'desc'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    public function eform()
    {
        return $this->belongsTo( EForm::class, 'eform_id');
    }

    /**
     * Scope for get appointment by ao, month and year
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string $month
     * @param  string $year
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAo($query, $aoId, $month, $year)
    {
      return $query->where($this->getTable() . '.ao_id', $aoId)
              ->atTime($month, $year)
              ->ascAppointment();
    }

    /**
     * Scope for get appointment by branch, month and year
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string $month
     * @param  string $year
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopePinca($query, $branchId, $month, $year)
    {
        return $query->where(\DB::Raw("TRIM(LEADING '0' FROM ".$this->getTable().".branch_id)"), (string) intval($branchId))
            ->whereNotNull($this->getTable() . '.ao_id')
            ->atTime($month, $year)
            ->ascAppointment();
    }

    /**
     * Scope for get appointment by customer, month and year
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string $month
     * @param  string $year
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeCustomer($query, $memberId, $month, $year)
    {
      return $query->where($this->getTable() . '.user_id', $memberId)
              ->atTime($month, $year)
              ->ascAppointment();
    }

    public function scopeAtTime($query, $month, $year)
    {
      return $query->whereMonth($this->getTable() . '.appointment_date', $month)
              ->whereYear($this->getTable() . '.appointment_date', $year);
    }

    /**
     * sorting appoinment by date
     * @param  \Illuminate\Dtabase\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAscAppointment($query)
    {
      return $query->orderBy($this->getTable() . '.appointment_date', $this->getTable() . '.asc');
    }

    public function scopeVisibleColumn($query)
    {
      $columns = collect($this->getFillable());
      $columns = $columns->push('id')
        ->reject(function($column) {
          return $column === 'appointment_date_res';
        })
        ->map(function($column) {
          return $this->getTable(). '.' . $column;
        });
      return $query->select($columns->all());
    }

    public function scopeWithEform($query)
    {
      return $query->leftJoin('eforms', 'eforms.id', '=', $this->getTable() . '.eform_id')
                  ->leftJoin('users', 'users.id', '=', 'eforms.user_id')
                  ->addSelect(\DB::raw("CONCAT(users.first_name, ' ',users.last_name) as guest_name"));
    }

}
