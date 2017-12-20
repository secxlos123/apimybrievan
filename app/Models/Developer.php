<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Developer extends Model implements AuditableContract
{
    use Auditable;
    
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'city_id', 'company_name', 'address', 'summary', 'pks_description',
        'created_by', 'approved_by', 'is_approved', 'pks_number', 'plafond', 'dev_id_bri'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'user' ];

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }

    /**
     * The relation to properties.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany( Property::class );
    }

    /**
     * Get all of the property items for the property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function propertyTypes()
    {
        return $this->hasManyThrough( PropertyType::class, Property::class );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['dev_id', 'asc'];

        return $query->from('developers_view_table')
            ->where(function ($developer) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($developer) use ($request) {

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('city_id')) $developer->where('city_id', $request->input('city_id'));

                /**
                 * Query for for without independent
                 */
                if ($request->has('without_independent')) {
                    if ($request->without_independent) {
                        // $developer->where('bri', '=', NULL);
                        $developer->where('bri', '!=', '1');
                    }
                }

            })
            ->select('*')
            ->selectRaw('(select users.image from users where users.id = developers_view_table.dev_id) as image')
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query for search user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        return $query
            ->where('company_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('email', 'ilike', "%{$request->input('search')}%")
            ->orWhere('phone_number', 'ilike', "%{$request->input('search')}%")
            ->orWhere('city_name', 'ilike', "%{$request->input('search')}%");
    }

    public function getListUserProperties($startList = null, $endList = null, $user_id)
    {
        $developer = Developer::select('id')->where('user_id', $user_id)->firstOrFail();
        if(!empty($startList) && !empty($endList)){
            $startList = date("01-m-Y",strtotime($startList));
            $endList   = date("t-m-Y", strtotime($endList));
     
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
            $startList = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
            $endList = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($startList) && !empty($endList)){
            $now        = new \DateTime();
            $startList = $now->format('Y-m-d h:i:s');

            $endList   = date("t-m-Y", strtotime($endList));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
            $endList = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($endList) && !empty($startList)){
            $now      = new \DateTime();
            $endList = $now->format('Y-m-d h:i:s');

            $startList = date("01-m-Y",strtotime($startList));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
            $startList = $dateStart->format('Y-m-d h:i:s');

            $filter = true;
        }else{
            $filter = false;
        }

        if($filter){
            $query = DB::select("
                        SELECT DISTINCT ON (developers.id)
                               users.first_name as name, properties.slug as slug, properties.name as property_name,
                               property_types.building_area as building_area, property_types.price as price, property_items.address as address,
                               eforms.is_approved as is_approved, properties.created_at as created_at
                        FROM developers developers
                        JOIN properties properties USING (id)
                        JOIN users users USING (id)
                        JOIN eforms eforms USING (id)
                        JOIN property_types property_types USING (id)
                        JOIN property_items property_items USING (id)
                        WHERE properties.developer_id = ".$developer['id']."
                        AND properties.created_at between '".$startList."' and '".$endList."'
                        ORDER  BY developers.id, properties.name, users.id, users.first_name
            ");

            $data = collect($query)->map(function($item, $key){
                return [
                    'name'            => $item->name,
                    'slug'            => $item->slug,
                    'property_name'   => $item->name,
                    'unit_price'      => "Rp. ".$item->price,
                    'unit_type'       => "Type ".$item->building_area,
                    'address'         => $item->address,
                    'approved_status' => ($item->is_approved) ? 'Approved' : 'Rejected',
                    'created_at'      => $item->created_at,
                    'created'         => strtotime($item->created_at),
                ];
            })->toArray();

            usort($data, function($a, $b) {
                return $b['created'] - $a['created'];
            });
            $data = collect($data)->map(function($item, $key){
                return [
                    'name'            => $item['name'],
                    'slug'            => $item['slug'],
                    'property_name'   => $item['property_name'],
                    'unit_price'      => $item['unit_price'],
                    'unit_type'       => $item['unit_type'],
                    'address'         => $item['address'],
                    'approved_status' => $item['approved_status'],
                ];
            })->slice(0, 5);
        }else{
            $query = DB::select("
                        SELECT DISTINCT ON (developers.id)
                               users.first_name as name, properties.slug as slug, properties.name as property_name,
                               property_types.building_area as building_area, property_types.price as price, property_items.address as address,
                               eforms.is_approved as is_approved, properties.created_at as created_at
                        FROM developers developers
                        JOIN properties properties USING (id)
                        JOIN users users USING (id)
                        JOIN eforms eforms USING (id)
                        JOIN property_types property_types USING (id)
                        JOIN property_items property_items USING (id)
                        WHERE properties.developer_id = ".$developer['id']."
                        ORDER  BY developers.id, properties.name, users.id, users.first_name
                        LIMIT 5
            ");

            $data = collect($query)->map(function($item, $key){
                return [
                    'name'            => $item->name,
                    'slug'            => $item->slug,
                    'property_name'   => $item->name,
                    'unit_price'      => "Rp. ".$item->price,
                    'unit_type'       => "Type ".$item->building_area,
                    'address'         => $item->address,
                    'approved_status' => ($item->is_approved) ? 'Approved' : 'Rejected',
                    'created'         => strtotime($item->created_at),
                ];
            })->toArray();

            usort($data, function($a, $b) {
                return $b['created'] - $a['created'];
            });

            $data = collect($data)->map(function($item, $key){
                return [
                    'name'            => $item['name'],
                    'slug'            => $item['slug'],
                    'property_name'   => $item['property_name'],
                    'unit_price'      => $item['unit_price'],
                    'unit_type'       => $item['unit_type'],
                    'address'         => $item['address'],
                    'approved_status' => $item['approved_status'],
                ];
            })->slice(0, 5);
        }

        return $data;
    }
}