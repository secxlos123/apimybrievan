<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use Sentinel;
use Asmx;

class Screening extends Model
{
    /**
     * The table name.
     *`
     * @var string
     */
    protected $table = 'eforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'tujuan_penggunaan','jenis_pinjaman','mitra', 'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'branch', 'ao_name', 'status', 'aging', 'is_visited' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'ao_id', 'updated_at', 'branch', 'ao' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [ 'additional_parameters' => 'array' ];

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
    public function getBranchAttribute()
    {
        // if( $branch = $this->branch ) {
        //     return $this->branch->name;
        // }
        return 'Branch Name';
    }

    /**
     * Get AO detail information.
     *      
     * @return string
     */
    public function getAoNameAttribute()
    {
        $AO = \RestwsHc::getUser( $this->ao_id );
        if( $AO ) {
            return $AO[ 'name' ];
        }
        return null;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if( $this->is_approved ) {
            return 'Submit';
        }
        if( $this->visit_report ) {
            return 'Initiate';
        }
        if( $this->ao_id ) {
            return 'Dispose';
        }
        return 'Rekomend';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getPrescreeningStatusAttribute()
    {
        return 'Hijau';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        //before
		//$days = $this->created_at->diffInDays();
        //after
		$days = $this->created_at;
        // $weeks = (integer) ( $days / 7 );
        // $days = $days % 7;
        // $months = (integer) ( $weeks / 4 );
        // $weeks = $weeks % 4;
        // $result = '';
        // if( $months != 0 ) {
        //     $result .= $months . ' bulan ';
        // }
        // if( $weeks != 0 ) {
        //     $result .= $weeks . ' minggu ';
        // }
        // if( $days != 0 ) {
        //     $result .= $days . ' hari ';
        // } else {
        //     $result = 'Baru';
        // }
        return $days . ' hari ';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getIsVisitedAttribute()
    {
        if( $this->visit_report ) {
            return true;
        }
        return false;
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
     * Approve E-Form function.
     *
     * @return string
     */
    public static function approve( $eform_id, $request )
    {
        $eform = static::find( $eform_id );
        for ( $i=1; $i <= 7; $i++ ) {
            $result = $eform->insertCoreBRI( $i );
            if( $result === false ) {
                \Log::info( 'Error step ' . $i );
                return $result;
                // $i--;
            }
            \Log::info( 'Step ' . $i . ' Berhasil.' );
        }
        $eform->update( [
            'pros' => $request->pros,
            'cons' => $request->cons,
            'is_approved' => true
        ] );
        return $eform;
    }

    /**
     * Function to insert data to core BRI.
     *
     * @return string
     */

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
     * Scope a query to filter eform.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['appointment_date', 'asc'];
        return $query->leftJoin('users','eforms.user_id','=','users.id')
        ->where( function( $eform ) use( $request ) {
            if( $request->has( 'status' ) ) {
                if( $request->status == 'Submit' ) {
                    $eform->whereIsApproved( true );
                } else if( $request->status == 'Initiate' ) {
                    $eform->has( 'visit_report' )->whereIsApproved( false );
                } else if( $request->status == 'Dispose' ) {
                    $eform->whereNotNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                } else if( $request->status == 'Rekomend' ) {
                    $eform->whereNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                }
            }
            if ($request->has('search')) {
                 $eform->where('eforms.ref_number', '=', $request->input('search'))
                  ->orWhere('users.first_name', 'ilike', '%' . $request->input('search') . '%')
                  ->orWhere('users.last_name', 'ilike', '%' . $request->input('search') . '%');
            }
            if ($request->has('start_date') || $request->has('end_date')) {
                $start_date= date('Y-m-d',strtotime($request->input('start_date')));
                $end_date = $request->has('end_date') ? date('Y-m-d',strtotime($request->input('end_date'))) : date('Y-m-d');
                $eform->whereBetween('eforms.created_at',array($start_date,$end_date));
            }
        } )->orderBy('eforms.'.$sort[0], $sort[1]);
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