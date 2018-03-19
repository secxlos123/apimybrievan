<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\EForm;
use App\Models\User;
use App\Models\Developer;
use App\Models\PropertyItem;
use App\Notifications\PengajuanKprNotification;
use App\Models\Collateral;
use Asmx;
use DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class KPR extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'kpr';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    public function eform()
    {
        return $this->belongsTo('App\Models\EForm', 'eform_id');
    }

    public function property()
    {
        return $this->hasMany('App\Models\Property', 'developer_id', 'developer_id');
    }

    public function propertyType()
    {
        return $this->hasMany('App\Models\PropertyType', 'property_id');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'status_property', 'eform_id', 'developer_id', 'property_id', 'price', 'building_area', 'home_location', 'year', 'active_kpr', 'dp', 'request_amount', 'developer_name', 'property_name', 'kpr_type_property','property_type','property_type_name','property_item','property_item_name','is_sent' ];

    protected $appends = ['status_property_name','kpr_type_property_name','down_payment', 'active_kpr_preview'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'eform_id' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->eform_id;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        try {
            $eform = EForm::create( $data );
            $data[ 'developer_id' ] = $data[ 'developer' ];
            $data[ 'property_id' ] = isset($data[ 'property' ]) ? $data[ 'property' ] : null;
            $kpr = ( new static )->newQuery()->create( [ 'eform_id' => $eform->id ] + $data );

            if ( isset($data[ 'property_item' ]) ) {
                PropertyItem::setAvailibility( $data[ 'property_item' ], "book" );
            }
            $data = [
                'kpr'   => $kpr,
                'eform' => $eform,
            ];

            set_action_date($eform->id, 'eform-create');

            return $data;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getStatusPropertyNameAttribute()
    {
        $property_name = $this->status_property;

        switch ($property_name) {
            case '1':
                return 'Baru';
                break;
            case '2':
                return 'Secondary';
                break;
            case '3':
                return 'Refinancing';
                break;
            case '4':
                return 'Renovasi';
                break;
            case '5':
                return 'Top Up';
                break;
            case '6':
                return 'Take Over';
                break;
            case '7':
                return 'Take Over Top Up';
                break;
            case '8':
                return 'Take Over Account In House (Cash Bertahap)';
                break;
            default:
                return '';
                break;
        }
    }

    public function getKprTypePropertyNameAttribute()
    {
        $kpr_property = $this->kpr_type_property;

        switch ($kpr_property) {
            case '1':
                return 'Rumah Tapak';
                break;
            case '2':
                return 'Rumah Susun/Apartment';
                break;
            case '3':
                return 'Rumah Toko';
                break;

            default:
                return '';
                break;
        }

    }

    public function getDownPaymentAttribute()
    {
        $down_payment = 0;
        $dp = $this->dp ? $this->dp : 0;
        $price = $this->price ? $this->price : 0;
        $down_payment =  ($dp / 100) * $price ;

        return $down_payment;
    }

    /**
     * active KPR handling
     *
     * @return void
     * @author
     **/
    public function getActiveKprAttribute()
    {
        if ( in_array($this->attributes['active_kpr'], array(1,2,3)) ) {
            return $this->attributes['active_kpr'];
        }

        return 1;
    }

    /**
     * active KPR preview
     *
     * @return void
     * @author
     **/
    public function getActiveKprPreviewAttribute()
    {
        if ( $this->attributes['active_kpr'] == 3 ) {
            return '> 2';
        }

        return $this->attributes['active_kpr'];
    }

    public function getListPropertyAgenDev($agenDevId, $userId)
    {
        $developer = Developer::select('id')->where('user_id', $userId)->first();
        $data = DB::select("SELECT * FROM
                            (SELECT * FROM user_developers
                             JOIN developers ON developers.id = user_developers.admin_developer_id
                             JOIN eforms ON eforms.sales_dev_id = user_developers.user_id
                             JOIN kpr ON kpr.eform_id = eforms.id
                             LEFT JOIN properties ON kpr.property_id = properties.id
                             WHERE eforms.sales_dev_id = ".$agenDevId."
                               AND kpr.developer_id = ".$developer['id']."
                               AND eforms.status_eform = 'Approval1') as a");
        return $data;
    }
}