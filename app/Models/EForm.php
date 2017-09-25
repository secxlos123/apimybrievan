<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use App\Models\Customer;
use Sentinel;

class EForm extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'eforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'office_id', 'product_type', 'prescreening_status', 'is_approved'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'office', 'ao_name', 'status', 'aging' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'ao_id', 'created_at', 'updated_at', 'branch', 'ao', 'kpr' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function saveImages( $images )
    {
        foreach ( $images as $key => $image ) {
            $path = public_path( 'uploads/eforms/' . $this->id . '/' );
            $filename = $key . '.' . $image->getClientOriginalExtension();
            $image->move( $path, $filename );
        }
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getCustomerNameAttribute()
    {
        return $this->customer->fullname;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getMobilePhoneAttribute()
    {
        return $this->customer->mobile_phone;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getNominalAttribute()
    {
        if( $this->kpr ) {
            return $this->kpr->request_amount;
        }
        return 0;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getOfficeAttribute()
    {
        if( $office = $this->branch ) {
            return $this->branch->name;
        }
        return '-';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getAoNameAttribute()
    {
        if( $ao = $this->ao ) {
            return $ao->fullname;
        }

        return '-';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if( $this->is_approved ) {
            return 'Diterima';
        }
        return 'Pengajuan Baru';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        $days = $this->created_at->diffInDays();
        $weeks = (integer) ( $days / 7 );
        $days = $days % 7;
        $months = (integer) ( $weeks / 4 );
        $weeks = $weeks % 4;
        $result = '';
        if( $months != 0 ) {
            $result .= $months . ' bulan ';
        }
        if( $weeks != 0 ) {
            $result .= $weeks . ' minggu ';
        }
        if( $days != 0 ) {
            $result .= $days . ' hari ';
        } else {
            $result = 'Baru';
        }
        return $result;
    }

    /**
     * Set user id information.
     *
     * @return string
     */
    public function setUserIdAttribute( $value )
    {
        $this->attributes[ 'user_id' ] = $value;
        $customer = $this->customer;
        $ref_number = strtoupper( substr( $customer->first_name, 0, 3 ) );
        $ref_number .= date( 'y' );
        $ref_number .= date( 'm' );
        $ref_number_check = static::whereRaw( 'ref_number ILIKE ?', [ $ref_number . '%' ] )->max( 'ref_number' );
        if( $ref_number_check ) {
            $ref_number .= substr( ( '00' . ( integer ) substr( $ref_number_check, -2 ) + 1 ), -2 );
        } else {
            $ref_number .= '01';
        }
        $this->attributes[ 'ref_number' ] = $ref_number;
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating( function( $eform ) {
            $customer_detail = CustomerDetail::whereNik( $eform->nik )->first();
            $eform->user_id = $customer_detail->user_id;

            if( $user_input = Sentinel::getUser() ) {
                if( $user_input->roles->first()->slug == 'ao' ) {
                    $eform->ao_id = $user_input->id;
                }
            }
        } );

        // static::addGlobalScope( 'role', function( Builder $builder ) {
        //     $login_usr = Sentinel::getUser();
        //     if( $login_usr ) {
        //         $role_slug = $login_usr->roles()->first()->slug;
        //         if( $role_slug == 'ao' ) {
        //             // $builder->whereAoId( $login_usr->id )->has( 'visit_report', '<', 1 );
        //             $builder->whereAoId( $login_usr->id );
        //         } else if( $role_slug == 'mp' || $role_slug == 'pinca' ) {
        //             if( $login_usr->detail ) {
        //                 // $builder->where( [
        //                 //     'office_id' => $login_usr->detail->office_id,
        //                 //     'prescreening_status' => 0
        //                 // ] )->has( 'visit_report' );
        //                 $builder->where( [
        //                     'office_id' => $login_usr->detail->office_id
        //                 ] );
        //             }
        //         }
        //     }
        // } );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo( Customer::class, 'user_id' );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo( Office::class, 'office_id' );
    }

    /**
     * The relation to visit report.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function visit_report()
    {
        return $this->hasOne( VisitReport::class, 'eform_id' );
    }

    /**
     * The relation to visit report.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kpr()
    {
        return $this->hasOne( KPR::class, 'eform_id' );
    }
}