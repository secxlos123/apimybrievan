<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\EForm;
use App\Models\User;
use App\Models\PropertyItem;
use App\Notifications\PengajuanKprNotification;
use App\Models\Collateral;
use Asmx;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'status_property', 'eform_id', 'developer_id', 'property_id', 'price', 'building_area', 'home_location', 'year', 'active_kpr', 'dp', 'request_amount', 'developer_name', 'property_name', 'kpr_type_property','property_type','property_type_name','property_item','property_item_name','is_sent' ];

    protected $appends = ['status_property_name','kpr_type_property_name','down_payment'];

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
        \DB::beginTransaction();
        try {
            $eform = EForm::create( $data );
            $data[ 'developer_id' ] = $data[ 'developer' ];
            $data[ 'property_id' ] = isset($data[ 'property' ]) ? $data[ 'property' ] : null;
            $kpr = ( new static )->newQuery()->create( [ 'eform_id' => $eform->id ] + $data );

            if ( isset($data[ 'property_item' ]) ) {
                PropertyItem::setAvailibility( $data[ 'property_item' ], "book" );
            }

            $usersModel = User::FindOrFail($eform->user_id);
            $usersModel->notify(new PengajuanKprNotification($eform)); /*send notification to pinca*/
            
            return $kpr;
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollback();
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

}