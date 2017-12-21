<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ApprovalDataChange extends Model implements AuditableContract
{
    use Auditable;

    /**
     * Status of approval data change
     * @var [type]
     */
    CONST WAITING = 'menunggu persetujuan';

    /**
     * The fillable columns
     * @var array
     */
    protected $fillable = [
      'related_id',
      'related_type',
      'city_id',
      'company_name',
      'summary',
      'logo',
      'phone',
      'mobile_phone',
      'status',
      'remark',
      'approval_by'
    ];

    /**
     * the name of approved developer change
     * @var string
     */
    CONST APPROVED = 'approved';

    /**
     * the name of rejected developer change
     * @var string
     */
    CONST REJECTED = 'rejected';


    /**
     * Boot lifecyle model
     * @return void
     */
    protected static function boot()
    {
      parent::boot();
      static::creating(function($model) {
        $model->status = static::WAITING;
      });
    }

    /**
     * Relation with related id and related type
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function related()
    {
      return $this->morphTo();
    }

    /**
     * Relation with city
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function city()
    {
      return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Scope for get list approval data change
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeGetList($query)
    {
      return $query->with('related')->with('city');
    }

    /**
     * Scope for get approval by params
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOnly($query, $approvalType)
    {
      return $query->where('related_type', ($approvalType === 'developer' ? Developer::class : ThirdParty::class));
    }

    /**
     * for create approval
     * @param  array $data
     * @return ApprovalDataChange
     */
    public function createApproval($data)
    {
      return $this->create($data);
    }

    /**
     * for approve data change developer or pihak 3
     * @var boolean
     */
    public function approve($id, $approvalType, $approvalId)
    {
      return $this->updateStatus($id, $approvalType, ['status' => static::APPROVED, 'approval_by' => $approvalId]);
    }

    /**
     * for reject data change developer or pihak 3
     * @var boolean
     */
    public function reject($id, $approvalType, $reason)
    {
      return $this->updateStatus($id, $approvalType, ['status' => static::REJECTED, 'remark' => $reason]);
    }

    /**
     * for update statuc data change
     * @param  integer $id
     * @param  string $status
     * @return boolean
     */
    public function updateStatus($id, $approvalType, $data)
    {
      $this->only($approvalType)->findOrFail($id)->update($data);
      return $this->getList()->findOrFail($id);
    }

    /**
     * Check is developer
     * @return boolean
     */
    public function isDeveloper()
    {
      return $this->related_type === Developer::class;
    }

    /**
     * Check is developer
     * @return boolean
     */
    public function isThirdParty()
    {
      return $this->related_type === ThirdParty::class;
    }
}
