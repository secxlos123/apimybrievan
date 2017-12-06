<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Collateral extends Model
{

    /**
     * The fillable columns
     * @var array
     */
    protected $fillable = ['property_id', 'developer_id', 'staff_id', 'staff_name', 'status', 'remark', 'approved_by','is_staff'];

    /**
     * The hidden columns
     * @var [type]
     */
    protected $hidden = ['developer_id'];

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
          ->with('otsOther');
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['prop_id', 'asc'];

        return $query->from('developer_properties_view_table')
            ->where(function ($property) use ($request) {

                if ($request->has('city_id')) $developer->where('prop_city_id', $request->input('city_id'));

            })
            ->select('*')
            ->where('prop_id', '!=', '1')
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetDetails($query, $id)
    {

        return $query->from('developer_properties_view_table')
            ->where('prop_id', '=', $id)->get();
    }
}
