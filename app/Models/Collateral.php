<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Collateral extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The fillable columns
     * @var array
     */
    protected $fillable = ['property_id', 'developer_id', 'staff_id', 'staff_name', 'status', 'remark', 'approved_by','is_staff', 'manager_id', 'manager_name'];

    /**
     * The hidden columns
     * @var [type]
     */
    protected $hidden = [];

    CONST STATUS = [
        'baru',
        'sedang di proses',
        'menunggu persetujuan',
        'disetujui',
        'ditolak'
    ];


    /**
     * Relation with developer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    /**
     * Relation with developer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Relation with ots in area
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsInArea()
    {
        return $this->hasOne(OtsInArea::class, 'collateral_id');
    }

    /**
     * Relation with ots letter
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsLetter()
    {
        return $this->hasOne(OtsAccordingLetterLand::class, 'collateral_id');
    }

    /**
     * Relation with ots building
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsBuilding()
    {
        return $this->hasOne(OtsBuildingDesc::class, 'collateral_id');
    }

    /**
     * Relation with ots environment
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsEnvironment()
    {
        return $this->hasOne(OtsEnvironment::class, 'collateral_id');
    }

    /**
     * Relation with ots valuation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsValuation()
    {
        return $this->hasOne(OtsValuation::class, 'collateral_id');
    }

    /**
     * Relation with ots other
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsOther()
    {
        return $this->hasOne(OtsAnotherData::class, 'collateral_id');
    }

    /**
     * Relation with otsSeven
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsSeven()
    {
        return $this->hasOne(OtsSeven::class, 'collateral_id');
    }

    /**
     * Relation with otsEight
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsEight()
    {
        return $this->hasOne(OtsEight::class, 'collateral_id');
    }

    /**
     * Relation with otsNine
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsNine()
    {
        return $this->hasOne(OtsNine::class, 'collateral_id');
    }

    /**
     * Relation with otsTen
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsTen()
    {
        return $this->hasOne(OtsTen::class, 'collateral_id');
    }

    /**
     * Relation with otsDoc
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function otsDoc()
    {
        return $this->hasOne(OtsDoc::class, 'collateral_id');
    }

    public function scopeWithAll($query)
    {
      return $query
        ->with('property')
        ->with('developer')
        ->with('otsInArea')
        ->with('otsLetter')
        ->with('otsBuilding')
        ->with('otsEnvironment')
        ->with('otsValuation')
        ->with('otsOther.images')
        ->with('otsSeven')
        ->with('otsEight')
        ->with('otsNine')
        ->with('otsTen')
        ->with('otsDoc');
    }

    /**
     * Eloquent lifecyle
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // static::creating(function($model) {
        //   $model->status = 'pending';
        // });
        static::updating(function($model) {
            if (!$model->approved_by) {
                unset($model->approved_by);
            }
        });
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['eform_id', 'asc'];

        return $query->from('collateral_view_table')
            ->where(function ($collaterals) use ($request) {

                if ($request->has('status')) $collaterals->where('status', $request->input('status'));

                if ($request->has('search')) {
                    $collaterals->Where('last_name', 'ilike', '%'.$request->input('search').'%')
                    ->orWhere('first_name', 'ilike', '%'.$request->input('search').'%')
                    ->orWhere('ref_number', 'ilike', '%'.$request->input('search').'%');
                }
            })
            ->select('*')
            ->where('developer_id',1)
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetDetails($query, $developerId,$propertyId)
    {
        return $query->from('collateral_view_table')
            ->where('developer_id',$developerId)->where('property_id',$propertyId);
    }
}
