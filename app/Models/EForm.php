<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\UserNotification;
use App\Models\Developer;
use App\Models\PropertyItem;
use App\Models\Collateral;
use App\Models\Appointment;
use App\Models\BRIGUNA;
use Carbon\Carbon;
use Sentinel;
use Asmx;
use RestwsHc;
use DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Models\KartuKredit;

class EForm extends Model implements AuditableContract
{
    use Auditable;

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
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address', 'token', 'status', 'response_status', 'recommended', 'recommendation', 'is_screening', 'pefindo_score', 'uploadscore', 'ket_risk', 'dhn_detail', 'sicd_detail', 'status_eform', 'branch', 'ao_name', 'ao_position', 'pinca_name', 'pinca_position', 'prescreening_name', 'prescreening_position', 'selected_sicd','ref_number', 'sales_dev_id', 'send_clas_date', 'selected_dhn', 'clas_position', 'pefindo_detail', 'selected_pefindo', 'vip_sent','IsFinish','pinca_note','delay_prescreening','tgl_disposisi', 'pefindo_score_all','kk_details'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'status', 'aging', 'is_visited', 'pefindo_color', 'is_recontest', 'is_clas_ready', 'selected_pefindo_json' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'updated_at', 'ao' ];

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
            $path = public_path( 'uploads/' . $this->nik . '/' );
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
        if ($this->customer) {
            return str_replace('"', '', str_replace("'", '', $this->customer->fullname));
        }

        return '';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getMobilePhoneAttribute()
    {
        if ($this->customer) {
            return $this->customer->mobile_phone;
        }

        return '';
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
     * Get selected pefindo information.
     *
     * @return Array
     */
    public function getSelectedPefindoJsonAttribute()
    {
        if( $this->selected_pefindo ) {
            return json_decode($this->selected_pefindo);
        }
        return ['individual'=>0];
    }

    /**
     * Get Status information.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if ( (!$this->is_approved && $this->recommended) || ($this->status_eform == 'Rejected') ) {
            return 'Kredit Ditolak';

        } elseif ( $this->status_eform == 'Approval1' ) {
            return 'Kredit Disetujui';

        } elseif ( $this->status_eform == 'Approval2' ) {
            return 'Rekontes Kredit';

        } elseif ( $this->status_eform == 'Disbursed' ) {
            return 'Disbursed Briguna';

        } elseif ( $this->status_eform == 'Menunggu Putusan' ) {
            return 'Menunggu Putusan';

        } elseif ( $this->status_eform == 'Pencairan' ) {
            return 'Pencairan';

        } elseif( $this->is_approved ) {
            if ( $this->visit_report ) {
                if ( $this->visit_report->use_reason == 13 && !$this->vip_sent ) {
                    return 'Kirim Ulang VIP';
                }
            }

            return 'Proses CLS';

        } elseif( $this->visit_report ) {
            return 'Prakarsa';

        } elseif( $this->ao_id != null || $this->ao_id != '' ) {
            return 'Disposisi Pengajuan';

        }

        return 'Pengajuan Kredit';
    }

    /**
     * Get Prescreening color information.
     *
     * @return string
     */
    public function getPrescreeningStatusAttribute( $value )
    {
        if ( $value == 1 ) {
            return 'Hijau';

        } elseif ( $value == 2 ) {
            return 'Kuning';

        } elseif ( $value == 3 ) {
            return 'Merah';

        }

        return '-';
    }

    /**
     * Get Pefindo color information.
     *
     * @return string
     */
    public function getPefindoColorAttribute( $value )
    {
        $value = $this->pefindo_score;
        if ( $value >= 250 && $value <= 529 ) {
            return 'Merah';

        } elseif ( ( $value >= 677 && $value <= 900 ) ) {
            return 'Hijau';

        } else {
            return 'Kuning';
        }
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        $days = 0;
        $date = Carbon::now();

        if ($this->created_at) {
            $stopAge = $this->detail_actions()->aging()->first();
            if ( $stopAge ) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $stopAge->execute_at);

            } else if ($this->send_clas_date) {
                $date = Carbon::createFromFormat('Y-m-d', $this->send_clas_date);

            }

            $days = $this->created_at->diffInDays($date);
        }
        return $days . ' hari ';
    }

    /**
     * Get Visited status ( LKN ).
     *
     * @return string
     */
    public function getIsVisitedAttribute()
    {
        if ($this->product_type == 'kpr') {
            if ( $this->visit_report ) {
                return true;
            }

        } else {
            if ($this->briguna) {
                if ($this->briguna->score) {
                    return true;
                }
            }

        }

        return false;
    }

    /**
     * Get recontest status.
     *
     * @return string
     */
    public function getIsRecontestAttribute()
    {
        if( $this->recontest ) {
            return true;
        }

        return false;
    }

    /**
     * Get depedencies VIP CLAS status.
     *
     * @return string
     */
    public function getIsClasReadyAttribute()
    {
        if ( $this->customer ) {
            if ( $this->is_visited && $this->customer->is_verified && $this->is_screening && !$this->vip_sent ) {
                if ( $this->visit_report ) {
                    if ( $this->visit_report->use_reason == 13 ) {
                        return true;
                    }
                }
            }
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
        $name = preg_replace("/[^A-Za-z]/", '',$customer->first_name.$customer->last_name.'XXX');
        $ref_number = strtoupper( substr( $name, 0, 3 ) );
        $ref_number .= date( 'y' );
        $ref_number .= date( 'm' );
        $ref_number_check = static::whereRaw( 'ref_number ILIKE ?', [ $ref_number . '%' ] );
        $ref_number_check = $ref_number_check->max( 'ref_number' );

        if( count($ref_number_check) > 0 ) {

            $ref_number .= ((integer) substr($ref_number_check, 7)) + 1;
        } else {
            $ref_number .= '1';
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
        $eform = static::findOrFail( $eform_id );
        $developer_id = env('DEVELOPER_KEY',1);
        $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');
        $collateral = Collateral::where('developer_id',$eform->kpr->developer_id)->where('property_id',$eform->kpr->property_id)->first();

        // Default result value
        $result = array(
            'status' => false
        );

        // KPR value
        $kprValue = array(
            'is_sent'=> true
        );

        // Default value for update eform
        $defaultValue = $eform->generateVIPData( $request );

        // Approve function
        if ( $request->is_approved ) {
            if ( $eform->status_eform == 'Approval2' ) {
                // Recontest
                $result = $eform->insertRecontestBRI( '21' );
                $kprValue = null;

            } else if ($eform->kpr->developer_id != $developer_id && $eform->kpr->developer_name != $developer_name) {
                $result = $eform->insertCoreBRI(10);

            } else if ($eform->kpr->developer_id == $developer_id && $eform->kpr->developer_name == $developer_name && $collateral->approved_by != null ) {
                $result = $eform->insertCoreBRI(10);

            } else {
                $result = $eform->insertCoreBRI(8);
                $kprValue['is_sent'] = false;

            }

            // VIP function
            if ( !$eform->vip_sent ) {
                if ( $eform->visit_report->use_reason == 13 ) {
                    $result = $eform->insertAnalisVIPBRI(
                        $eform->stepVIP(
                            $eform->additional_parameters
                        )
                    );
                    $eform->update($defaultValue);
                }
            }

            $availableStatus = "sold";

        } else {
            // Reject function
            $defaultValue['status_eform'] = 'Rejected';
            $availableStatus = "available";
            $result['status'] = true;
            $kprValue['is_sent'] = false;

            // Recontest
            if ($eform->status_eform == 'Approval2' ) {
                $result = $eform->insertRecontestBRI( '0' );

            }

        }

        // Success hit CLAS service
        if ( $result['status'] ) {
            if( $kprValue != null ) {
                $eform->kpr()->update( $kprValue );

            }

            if( $eform->status_eform == 'Approval2' ) {
                $eform->updateRecontest( $request );

                $defaultValue = static::unsetRecontest( $defaultValue );
            }

            $eform->update($defaultValue);

            $eform->setAvailibility( $availableStatus );
        }

        return $result;
    }

    /**
     * Function to insert data to core BRI.
     *
     * @return array
     */
    public function insertCoreBRI( $maxStep )
    {
        $endpoint = [
            ['InsertDataCif', 'fid_cif_las']
            , ['InsertDataCifSdn', null]
            , ['InsertDataAplikasi', 'fid_aplikasi']
            , ['InsertDataPrescreening', null]
            , ['InsertDataScoringKpr', null]
            , ['InsertDataTujuanKredit', null]
            , ['InsertDataMaster', null]
            , ['InsertIntoReviewer', 'nama_reviewer']
            , ['InsertDataAgunanModel71', 'id_model_71']
            , ['InsertDataAgunan', 'fid_agunan']
        ];

        $step = $this->clas_position ? (intval($this->clas_position) > 0 ? intval($this->clas_position) : 1) : 1;
        $allRequest = array();
        $return = array(
            'status' => true
            , 'message' => ''
        );

        if ( $step > 1 ) {
            for ($i = 1; $i < $step; $i++) {
                $request = $this->{"step".$i}($this->additional_parameters);
                $allRequest += $request;
            }
        }

        foreach ($endpoint as $key => $value) {
            if ( $key+1 == $step && $step <= $maxStep) {
                \Log::info("Start Step " . $step);

                $request = $this->{"step".$step}($this->additional_parameters);
                $allRequest += $request;

                $sendRequest = ($step == 7 ? $allRequest : $request);

                \Log::info(json_encode($sendRequest));

                if ( $value[0] != 'InsertIntoReviewer' ) {
                    $set = $this->SentToBri( $sendRequest, $value[0], $value[1], $step );

                    if (!$set['status']) {
                        \Log::info('Error Step Ke -'.$step);
                        $return = array(
                            'status' => false
                            , 'message' => $set[ 'message' ]
                        );
                        \Log::info($return);
                        break;
                    }
                }

                \Log::info('Berhasil Step Ke -'.$step);
                $step++;
            }
        }

        if ($step == 10) {
            $this->is_approved = true;
            $this->save();
        }

        return $return;
    }

    /**
     * Send to recontest service BRI
     *
     * @return array
     **/
    public function insertRecontestBRI( $status )
    {
        return $this->SentToBri(
            $this->additional_parameters + [
                "fid_status" => $status
            ]
            , 'UpdateStatusByAplikasi'
            , null
            , 0
        );
    }

    /**
     * Remove unused params for recontest
     *
     * @return void
     **/
    public static function unsetRecontest( $request )
    {
        unset($request['pros']);
        unset($request['cons']);
        unset($request['recommendation']);
        unset($request['recommended']);

        return $request;
    }

    /**
     * Update recontest recommendation
     *
     * @return void
     **/
    public function updateRecontest( $request )
    {
        $this->recontest->update( [
            'pinca_recommendation' => $request->recommendation,
            'pinca_recommended' => $request->recommended == "yes" ? true : false
        ] );
    }

    /**
     * Update property status
     *
     * @return void
     **/
    public function setAvailibility( $status )
    {
        $developer_id = env('DEVELOPER_KEY',1);
        $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');

        if ($this->kpr->developer_id != $developer_id && $this->kpr->developer_name != $developer_name) {
            PropertyItem::setAvailibility( $this->kpr->property_item, $status );
        }
    }

    /**
     * Send to analis VIP service BRI
     *
     * @return array
     * @author
     **/
    public function insertAnalisVIPBRI( $data )
    {
        return $this->SentToBri(
            $data
            , 'InsertIntoAnalis'
            , null
            , 0
        );
    }

    /**
     * Send to analis VIP service BRI
     *
     * @return array
     * @author
     **/
    public function generateVIPData( $request = null )
    {
        if ( !isset($request->auto_approve) ) {
            return array(
                'pros' => isset($request->pros) ? $request->pros : '',
                'cons' => isset($request->cons) ? $request->cons : '',
                'pinca_position' => $request->pinca_position,
                'pinca_name' => $request->pinca_name,
                'recommendation' => $request->recommendation,
                'recommended' => $request->recommended == "yes" ? true : false,
                'is_approved' => $request->is_approved,
                'status_eform' => 'approved'
            );

        }

        $visitReport = $this->visit_report;

        return array(
            'pros' => $visitReport->pros,
            'cons' => $visitReport->cons,
            'pinca_position' => $this->ao_position,
            'pinca_name' => $this->ao_name,
            'recommendation' => $visitReport->recommendation,
            'recommended' => $visitReport->recommended,
            'is_approved' => true,
            'status_eform' => 'approved'
        );
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

        static::created( function( $eform ) {
            $scheduleData = array(
                'title' => $eform->ref_number
                , 'appointment_date' => $eform->appointment_date
                , 'user_id' => $eform->user_id
                , 'ao_id' => $eform->ao_id
                , 'eform_id' => $eform->id
                , 'ref_number' => $eform->ref_number
                , 'address' => $eform->address
                , 'latitude' => $eform->latitude
                , 'longitude' => $eform->longitude
                , 'desc' => $eform->ref_number
                , 'status' => 'waiting'
            );

            $schedule = Appointment::create($scheduleData);
        } );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['created_at', 'asc'];
        $user = \RestwsHc::getUser();

        $eform = $query->where( function( $eform ) use( $request, &$user ) {
            if( $request->has( 'status' ) ) {
                if( $request->status == 'Submit' ) {
                    $eform->whereIsApproved( true );

                } elseif ($request->status == 'Rejected' || $request->status == 'Approval1' || $request->status == 'Approval2' || $request->status == 'Disbursed') {
                    $eform->where('status_eform', $request->status);

                } else if( $request->status=='MenungguPutusan' ){
					$eform->where('status_eform', 'Menunggu Putusan');
				}else if( $request->status == 'Initiate' ) {
                    $eform->has( 'visit_report' )->whereIsApproved( false );

                } else if( $request->status == 'Dispose' ) {
                    $eform->whereNotNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );

                } else if( $request->status == 'Rekomend' ) {
                    $eform->whereNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );

                }
            }
        } );

        if ($request->has('search')) {
            $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
                ->where( function( $eform ) use( $request, &$user ) {
                    $eform->orWhere('users.last_name', 'ilike', '%'.strtolower($request->input('search')).'%')
                        ->orWhere('users.first_name', 'ilike', '%'.strtolower($request->input('search')).'%')
                        // ->orWhere('users.id', '=', $request->input('search'))
                        ->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('search').'%');
                } );

        } else {
            if ($request->has('customer_name')){
                $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
                    ->where( function( $eform ) use( $request, &$user ) {
                        $eform->orWhere(\DB::raw("LOWER(concat(users.first_name,' ', users.last_name))"), "like", "%".strtolower($request->input('customer_name'))."%");
                    } );
            }

            if ($request->has('ref_number')) {
                $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                    $eform->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('ref_number').'%');
                } );
            }
        }


        if ($request->has('prescreening')) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                $prescreening = $request->input('prescreening');
                if (strtolower($prescreening) != 'all') {
                    $eform->Where('eforms.prescreening_status', $prescreening);
                }
            } );
        }

        if ($request->has('name')) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                $name = $request->input('name');
                if (strtolower($name) != 'all') {
                    $eform->Where('eforms.ao_id', substr('000'.$request->input('name'), -8));
                }
            } );
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                $start_date = date('Y-m-d',strtotime($request->input('start_date')));
                $end_date = $request->has('end_date') ? date('Y-m-d',strtotime($request->input('end_date'))) : date('Y-m-d');

                $eform->where('eforms.created_at', '>=', $start_date . ' 00:00:00')
                ->where('eforms.created_at', '<=', $end_date . ' 23:59:59');
            } );
        }

        if ( !$request->has('is_screening') ) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                if ( $user['role'] == 'ao' ) {
                    $eform = $eform->where('eforms.ao_id', substr('000'.$user['pn'], -8));

                }

                if ($request->has('branch_id')) {
                    $eform = $eform->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($request->input('branch_id')));
                }
            } );

            if ( $user['role'] != 'ao' || $request->has('customer_name')) {
                if ( $request->has('customer_name') ) {
                    $eform = $eform->select( ['eforms.*', 'users.first_name', 'users.last_name'] );

                } else {
                    $eform = $eform->select([
                            'eforms.*'
                            , \DB::Raw(" case when status_eform in ('Rejected') then 4 when status_eform in ('Approval2', 'Approval1', 'approved') then 3 when ao_id is not null then 2 else 1 end as new_order ")
                        ]);
                    if ( $sort[0] != "action" ) {
                        $eform = $eform->orderBy('new_order', 'asc');
                    }

                }

            }
        }

        if ( $request->has('is_screening') ) {
            if ( $request->input('is_screening') != 'All' ) {
                $eform = $eform->where('eforms.is_screening', $request->input('is_screening'));

            }
            if ( $user['role'] != 'ao' || $request->has('search')) {
                if ( $request->has('search') ) {
                    $eform = $eform->select( ['eforms.*', 'users.first_name', 'users.last_name'] );

                }
            }
            $eform = $eform->where('response_status', 'approve');
        }

        if ( $request->has('product') ) {
            if ( $request->input('product') != 'All' ) {
                $eform = $eform->where('eforms.product_type', $request->input('product'));

            }
        }

        if ( $sort[0] == "ref_number" || $sort[0] == "action" || $sort[0] == "aging" ) {
            $sort[0] = 'created_at';
        }

        $eform = $eform->orderBy('eforms.'.$sort[0], $sort[1]);

        return $eform;
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
    public function users()
    {
        return $this->belongsTo( User::class, 'user_id' );
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
     * The relation to Recontest.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recontest()
    {
        return $this->hasOne( Recontest::class, 'eform_id' );
    }

    public function briguna()
    {
        return $this->hasOne( BRIGUNA::class, 'eform_id' );
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

    public function kartukredit(){
        return $this->hasOne(KartuKredit::class, 'eform_id');
    }

    /**
     * Update EForm from CLAS.
     *
     * @return array
     */
    public static function updateCLAS( $fid_aplikasi, $status )
    {
        $returnStatus = false;
        $target = static::where(
                DB::Raw("additional_parameters::json->>'fid_aplikasi'")
                , $fid_aplikasi
            )->first();

        if ($target) {
            if ( $status != 'Pencairan' ) {
                $returnStatus = "EForm berhasil di " . ( $status == 'Approval1' ? 'Setujui' : "Tolak" ) . ".";
                $target->update([
                    'is_approved' => ( $status == 'Approval1' ? true : false )
                    , 'status_eform' => ( $status == 'Approval2' ? 'Rejected' : $status )
                ]);

                // Recontest
                if ( $status == 'Approval2' ) {
                    if ( !$target->recontest ) {
                        $target->recontest()->create( [
                            'expired_date' => Carbon::now()->addMonths(1)
                        ] );
                        $returnStatus = "EForm berhasil di Rekontes.";
                    } else {
                        $returnStatus = "EForm sudah pernah di Rekontes.";

                    }
                }

                if ($target->kpr) {
                    $target->kpr->update([
                        'is_sent' => ( $status == 'Approval1' ? true : false )
                    ]);

                    if ( $status != 'Approval2' ) {
                        $target->setAvailibility( $status == 'Approval1' ? "sold" : "available" );

                    }
                }
            } else {
                $target->update(['status_eform' => $status]);
                $returnStatus = "EForm berhasil di cairkan.";

            }
        }

        return array(
            'message' => $returnStatus,
            'contents' => $target,
        );
    }

    /**
     * Verify E-Form customer data.
     *
     * @return array
     */
    public static function verify( $token, $status )
    {
        $returnStatus = false;
        $target = static::where('token', $token)->first();

        if ($target) {
            $lastData = static::where('user_id', $target->user_id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($lastData->token == $target->token) {
                $returnStatus = true;
                $target->update(['response_status' => $status]);
                $verifiedStatus = ($status == "approve" ? true : false);
                $target->customer->detail()->update(['is_verified' => $verifiedStatus]);
            }
        }

        return array(
            'message' => $returnStatus
            , 'contents' => $target
        );
    }

    /**
     * Generate token for verification.
     *
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function generateToken( $user_id )
    {
        $lastData = static::where( 'user_id', $user_id )
            ->orderBy( 'created_at', 'desc' )
            ->first();

        $lastData->update( [
            'token' => strtr(base64_encode(openssl_encrypt(date('y-m-d h:i:s'), 'AES-128-ECB', 'APImyBRI')), '+/=', '-_,')
            , 'response_status' => 'unverified'
        ]);

        return $lastData;
    }

    /**
     * [Sent To 10 End Point Bri]
     * @author erwan.akse@wgs.co.id
     * @param $request  Data User
     * @param $endpoint End Point BRI
     * @param $value    Return Data From Bri
     * @return true|false Is Sent Success|Failed
     */
    public function SentToBri($request, $endpoint, $value = null, $step)
    {
        if ( ENV('APP_ENV') == 'local' ) {
            $post_to_bri = array (
              'code' => '200',
              'descriptions' => 'Success',
              'contents' => $this->ref_number
            );

        } else {
            $post_to_bri = Asmx::setEndpoint( $endpoint )
                ->setBody( [
                    'Request' => json_encode( $request )
                ] )
                ->post( 'form_params' );

            $return = array(
                'status' => false
                , 'message' => isset($post_to_bri[ 'contents' ]) ? $post_to_bri[ 'contents' ] : ''
            );

        }

        \Log::info('============================================================================================');
        \Log::info($endpoint);
        \Log::info($post_to_bri);
        \Log::info('============================================================================================');

        if ( $post_to_bri[ 'code' ] == 200 ) {
            if ( $endpoint != 'UpdateStatusByAplikasi' && $endpoint != 'InsertIntoAnalis' ) {
                if ($value != null) {
                    $this->additional_parameters += [ $value => $post_to_bri[ 'contents' ] ] ;
                }

                $this->clas_position = $step + 1;
                $this->send_clas_date = date("Y-m-d");
                $this->save();

            } else if ( $endpoint == 'InsertIntoAnalis' ) {
                $this->vip_sent = true;
                $this->save();

            }

            $return = array(
                'status' => true
                , 'message' => ''
            );
        } else {
            if ( $endpoint == 'InsertIntoAnalis' ) {
                // $this->vip_sent = false;
                // $this->save();

            }
        }

        return $return;
    }

    /**
     * Generate Parameters for step 1.
     *
     * @param array $data
     * @return array $request
     */
    public function step1($data)
    {
        \Log::info("step1");
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $customer_work = (object) $customer->work;
        $customer_contact = (object) $customer->contact;
        $customer_finance = (object) $customer->financial;
        $lkn = $this->visit_report;
        $year = !( $customer_work->work_duration ) ? 0 : $customer_work->work_duration;
        $mount = !( $customer_work->work_duration_month ) ? 0 : $customer_work->work_duration_month;
        $lama_usaha = $year *12 + $mount;

        if( $lkn ){
            if ( $lkn->use_reason == 13 ) {
                $npwp = !( $lkn->npwp_number_masking ) ? '99.999.999.9-999.999' : $lkn->npwp_number_masking;

            } else {
                $npwp = !( $lkn->npwp_number ) ? '' : $lkn->npwp_number;

            }
        } else {
            $npwp = '';
        }

        $request = $data + [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "tempat_lahir_pemohon" => $this->reformatString( $customer_detail->birth_place ),
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "alamat_pemohon" => !( $customer_detail->address ) ? '' : substr($customer_detail->address, 0, 40),
            "alamat_domisili" => !( $customer_detail->current_address ) ? '' : substr($customer_detail->current_address, 0, 40),
            "jenis_kelamin_pemohon" => !( $customer->gender_sim ) ? '' : strtolower($customer->gender_sim),
            "kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "pekerjaan_pemohon_value" => !( $customer_work->work_id ) ? '' : $customer_work->work_id,
            "status_pernikahan_pemohon_value" => !( $customer_detail->status_id ) ? '' : $customer_detail->status_id,
            "nik_pasangan" => !( $customer_detail->couple_nik ) ? '' : $customer_detail->couple_nik,
            "nama_pasangan" => !( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "status_tempat_tinggal_value" => !( $customer_detail->address_status_id ) ? '0' : $customer_detail->address_status_id,
            "telepon_pemohon" => !( $customer->phone ) ? '0' : substr($customer->phone, 0, 11),
            "hp_pemohon" => !( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => !( $customer->email ) ? '' : $customer->email,
            "nama_perusahaan" => !( $customer_work->company_name ) ? '' : $customer_work->company_name,
            "lama_usaha" => $lama_usaha,
            "nama_keluarga" => !( $customer_contact->emergency_name ) ? '' : $customer_contact->emergency_name,
            "hubungan_keluarga" => !( $customer_contact->emergency_relation ) ? '' : $customer_contact->emergency_relation,
            "telepon_keluarga" => !( $customer_contact->emergency_contact ) ? '0' : $customer_contact->emergency_contact,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "npwp_pemohon" => $npwp,
            "cif" => !( $customer_detail->cif_number ) ? '' : $customer_detail->cif_number,
            "status_pisah_harta_pemohon" => !( $lkn->source_income ) ? '' : ($lkn->source_income == "Single Income" ? 'Tidak' : 'Pisah Harta'),
            "sektor_ekonomi_value" => !( $lkn->economy_sector ) ? '' : $lkn->economy_sector,
            "Status_gelar_cif" => $this->reformatTitle( $lkn->title ),
            'Kode_pos_cif' => !( $customer_detail->zip_code ) ? '40000' : $customer_detail->zip_code,
            'Kelurahan_cif' => !( $customer_detail->kelurahan ) ? 'kelurahan' : $customer_detail->kelurahan,
            'Kecamatan_cif' => !( $customer_detail->kecamatan ) ? 'kecamatan' : $customer_detail->kecamatan,
            'lokasi_dati_cif' => $this->reformatCity( $customer_detail->city ),
            "Usia_mpp" => !( $lkn->age_of_mpp ) ? '' : $lkn->age_of_mpp,
            "Bidang_usaha_value" => !( $lkn->economy_sector ) ? '' : $lkn->economy_sector,
            "Status_kepegawaian_value" => !( $lkn->employment_status ) ? '' : $lkn->employment_status,
            "agama_value_pemohon" => !( $lkn->religion ) ? '' : $lkn->religion,
            "telepon_tempat_kerja" => !( $lkn->office_phone ) ? '0' : substr($lkn->office_phone, 0, 11 ),
            "jenis_kpp_value" => !( $lkn->kpp_type_name ) ? '' : $lkn->kpp_type_name,
            "jumlah_tanggungan" => !( $customer_finance->dependent_amount ) ? '0' : $customer_finance->dependent_amount,
            "status_pinjam_bank_lain" => 'Tidak'
        ];

        return $request;
    }

    /**
     * Generate Parameters for step 2.
     *
     * @param array $data
     * @return array $request
     */
    public function step2($data)
    {
        \Log::info("step2");
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $customer_work = (object) $customer->work;
        $lkn = $this->visit_report;

        $income = ( $lkn->source ) ? ( $this->validateData( $lkn->source == 'nonfixed' ? $lkn->income : $lkn->income_salary ) ) : 0 ;

        $request = $data + [
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4),
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "jenis_kelamin_pemohon" => !( $customer->gender_sim ) ? '' : strtolower($customer->gender_sim),
            "kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "tempat_lahir_pemohon" => $this->reformatString( $customer_detail->birth_place ),
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "status_pernikahan_pemohon_value" => !( $customer_detail->status_id ) ? '' : $customer_detail->status_id,
            "alamat_pemohon" => !( $customer_detail->address ) ? '' : substr($customer_detail->address, 0, 40),
            "status_tempat_tinggal_value" => !( $customer_detail->address_status_id ) ? '' : $customer_detail->address_status_id,
            "alamat_domisili" => !( $customer_detail->current_address ) ? '' : substr($customer_detail->current_address, 0, 40),
            "telepon_pemohon" => !( $customer->phone ) ? '0' : substr($customer->phone, 0 , 11),
            "hp_pemohon" => !( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => !( $customer->email ) ? '' : $customer->email,
            "jenis_pekerjaan_value" => !( $customer_work->type_id ) ? '' : $customer_work->type_id,
            "nama_perusahaan" => !( $customer_work->company_name ) ? '' : $customer_work->company_name,
            "bidang_usaha_value" => !( $customer_work->work_field_id ) ? '' : $customer_work->work_field_id,
            "jabatan_value" => !( $customer_work->position_id ) ? '' : $customer_work->position_id,
            "npwp_pemohon" => !( $lkn->npwp_number ) ? '' : $lkn->npwp_number,
            'agama_value_pemohon' => !( $lkn->religion ) ? '' : $lkn->religion,
            "alamat_usaha" => !( $customer_work->office_address ) ? '' : $customer_work->office_address,
            'telepon_tempat_kerja' => !( $lkn->office_phone ) ? '0' : substr($lkn->office_phone, 0, 11),
            'tujuan_membuka_rekening_value' => 'T2',
            'sumber_utama_value' => !( $lkn->source ) ? '00099' : ($lkn->source == "fixed" ? '00011' : '00012'),

            'Kode_pos_cif' => !( $customer_detail->zip_code ) ? '' : $customer_detail->zip_code,
            'Kelurahan_cif' => !( $customer_detail->kelurahan ) ? '' : $customer_detail->kelurahan,
            'Kecamatan_cif' => !( $customer_detail->kecamatan ) ? '' : $customer_detail->kecamatan,
            'Kota_cif' => $this->reformatCity( $customer_detail->city ),
            'Propinsi_cif' => 'propinsi',

            'Kode_pos_domisili' => !( $customer_detail->zip_code_current ) ? '' : $customer_detail->zip_code_current,
            'Kelurahan_domisili' => !( $customer_detail->kelurahan_current ) ? '' : $customer_detail->kelurahan_current,
            'Kecamatan_domisili' => !( $customer_detail->kecamatan_current ) ? '' : $customer_detail->kecamatan_current,
            'Kota_domisili' => $this->reformatCity( $customer_detail->kabupaten_current ),
            'Propinsi_domisili' => 'propinsi',

            'Kode_pos_perusahaan' => !( $customer_work->zip_code_office ) ? '' : $customer_work->zip_code_office,
            'Kelurahan_perusahaan' => !( $customer_work->kelurahan_office ) ? '' : $customer_work->kelurahan_office,
            'Kecamatan_perusahaan' => !( $customer_work->kecamatan_office ) ? '' : $customer_work->kecamatan_office,
            'Kota_perusahaan' => $this->reformatCity( $customer_work->kabupaten_office ),
            'Propinsi_perusahaan' => 'propinsi',

            'Alamat_surat_menyurat_value' => '1',
            'Penghasilan_perbulan_value' => $income,
            'Pendidikan_terakhir_value' => !( $lkn->title ) ? '' : $lkn->title,
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 3.
     *
     * @param array $data
     * @return array $request
     */
    public function step3($data)
    {
        \Log::info("step3");
        $kpr = $this->kpr;
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $lkn = $this->visit_report;

        $developer = Developer::find($kpr->developer_id);

        $request = $data + [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "jenis_kredit" => strtoupper( $this->product_type ),
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4),
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "nama_pasangan" => !( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "jenis_kpp_value" => !( $lkn->kpp_type_name ) ? '' : $lkn->kpp_type_name,
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "program_value" => !( $lkn->program_list ) ? '' : $lkn->program_list,
            "project_value" => !( $lkn->project_list ) ? '' : $lkn->project_list,
            "pihak_ketiga_value" => !( $developer ) ? '1' : ( $developer->dev_id_bri ? $developer->dev_id_bri : '1' ),
            "sub_pihak_ketiga_value" => '1'
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 4.
     *
     * @param array $data
     * @return array $request
     */
    public function step4($data)
    {
        \Log::info("step4");
        return $data;
    }

    /**
     * Generate Parameters for step 5.
     *
     * @param array $data
     * @return array $request
     */
    public function step5($data)
    {
        \Log::info("step5");
        $kpr = $this->kpr;
        $lkn = $this->visit_report;
        $customer = clone $this->customer;
        $customer_finance = (object) $customer->financial;

        $income = $this->validateData( $lkn->source == 'nonfixed' ? $lkn->income : $lkn->income_salary );
        $otherIncome = $lkn->source == 'nonfixed' ? 0 : $this->validateData( $lkn->income_allowance ) ;
        $incomeCouple = $this->validateData( $customer_finance->salary_couple );
        $otherIncomeCouple = $this->validateData( $customer_finance->other_salary_couple );
        $loan = $this->validateData( $customer_finance->loan_installment );
        $thp = $income + $otherIncome + ( $lkn->source_income == 'joint' ? $incomeCouple + $otherIncomeCouple : 0 );
        $dir = $this->getDIR( $thp, 40 );
        $maxInstallment = ( $dir * $thp ) / 100;
        $interest = $this->getInterest( $data['fid_aplikasi'] );
        $installment = $this->getInstallment( $interest );
        $maxPlafond = $this->getMaxPlafond( $interest, $maxInstallment - $loan );

        $request = $data + [
            "jenis_kredit" => strtoupper( $this->product_type ),
            "angsuran" => $loan,
            "jangka_waktu" => $kpr->year,
            "Jenis_dibiayai_value" => !( $lkn->type_financed ) ? '0' : $lkn->type_financed,
            "permohonan_pinjaman" => !( $kpr->request_amount ) ? '0' : $kpr->request_amount,
            "uang_muka" => round( ( $kpr->dp / 100 ) * $kpr->price ),
            "persen_uang_muka" => $kpr->dp,
            "gaji_bulanan_pemohon" => $income,
            "pendapatan_lain_pemohon" => $otherIncome,
            "jenis_penghasilan" =>  !( $lkn->source_income ) ? 'Single Income' : ( $lkn->source_income == 'single' ) ? 'Single Income' : 'Joint Income',
            "gaji_bulanan_pasangan" => $incomeCouple,
            "pendapatan_lain_pasangan" => $otherIncomeCouple,
            "harga_agunan" => !($kpr->price) ? '0' : $this->reformatCurrency($kpr->price),
            "maksimal_angsuran" => $maxInstallment,
            "kelonggaran_angsuran_kredit" => $maxInstallment - $loan,
            "suku_bunga_bulan" => number_format( $interest, 4 ),
            "maksimum_plafond" => $maxPlafond,
            "angsuran_sesuai_jumlah_plafond" => $installment,
            "Pernah_pinjam_bank_lain_value" => !( $lkn->loan_history_accounts ) ? '' : $lkn->loan_history_accounts
        ];

        return $request;
    }

    /**
     * Generate Parameters for step 6.
     *
     * @param array $data
     * @return array $request
     */
    public function step6($data)
    {
        \Log::info("step6");
        $lkn = $this->visit_report;

        $request = $data + [
            "tujuan_penggunaan_value" => !( $lkn->use_reason ) ? '' : $lkn->use_reason,
            "tujuan_penggunaan" => !( $lkn->use_reason_name ) ? '' : $lkn->use_reason_name
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 7.
     *
     * @param array $data
     * @return array $request
     */
    public function step7($data)
    {
        \Log::info("step7");
        $lkn = $this->visit_report;

        $request = $data + [
            "nama_pengelola" => !($this->ao_name) ? '': $this->ao_name ,
            "pn_pengelola" => !($this->ao_id) ? '': $this->ao_id
        ];
        return $request;
    }

     /**
     * Generate Parameters for step 8.
     *
     * @param array $data
     * @return array $request
     */
    public function step8($data)
    {
        \Log::info("step8");
        return $data + [
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4)
        ];

    }

    /**
     * Generate Parameters for step 9.
     *
     * @param array $data
     * @return array $request
     */
    public function step9($data)
    {
        \Log::info("step9");
        $kpr = $this->kpr;
        $collateral = Collateral::WithAll()->where('property_id',$kpr->property_id)->firstOrFail();
        $otsInArea = $collateral->otsInArea;
        $otsLetter = $collateral->otsLetter;
        $otsBuilding = $collateral->otsBuilding;
        $otsEnvironment = $collateral->otsEnvironment;
        $otsValuation = $collateral->otsValuation;
        $otsOther = $collateral->otsOther;
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;

        $request = $data + [
            //ots Area
            "id_model_71" => (isset($this->additional_parameters['id_model_71']))? $this->additional_parameters['id_model_71'] : 0,
            "Lokasi_tanah_agunan" => !($otsInArea->location) ? '0' : $otsInArea->location,
            "Rt_agunan" => !($otsInArea->rt) ? '0' : $otsInArea->rt,
            "Rw_agunan" => !($otsInArea->rw) ? '0' : $otsInArea->rw,
            "Kelurahan_agunan"=> !($otsInArea->sub_district) ? '0' : $otsInArea->sub_district,
            "Kecamatan_agunan"=> !($otsInArea->district) ? '0' : $otsInArea->district,
            "Kabupaten_kotamadya_agunan" => !($otsInArea->city) ? '0' : ( !($otsInArea->city->name) ? '0' : $otsInArea->city->name ),
            "Jarak_agunan" => !($otsInArea->distance) ? '0' : intval( $otsInArea->distance ),
            "Jarak_satuan_agunan" => !($otsInArea->unit_type_name) ? 'Kilometer' : $otsInArea->unit_type_name,
            "Jarak_dari_agunan" => !($otsInArea->distance_from) ? 'Pusat Kota' : $otsInArea->distance_from,
            "Kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '0' : $customer_detail->citizenship_id,
            "Posisi_terhadap_jalan_agunan_value"=> !($otsInArea->position_from_road) ? '0' : $otsInArea->position_from_road,
            "Posisi_terhadap_jalan_agunan" => !($otsInArea->position_from_road) ? '0' : $otsInArea->position_from_road,
            "Batas_utara_tanah_agunan" => !($otsInArea->north_limit) ? '0' :  $otsInArea->north_limit ,
            "Batas_timur_tanah_agunan" => !($otsInArea->east_limit) ? '0' :  $otsInArea->east_limit ,
            "Batas_selatan_tanah_agunan" => !($otsInArea->south_limit) ? '0' : $otsInArea->south_limit ,
            "Batas_barat_tanah_agunan" => !($otsInArea->west_limit) ? '0' :  $otsInArea->west_limit ,
            "Keterangan_lain_agunan" => !($otsInArea->another_information) ? '0' : $otsInArea->another_information,
            "Bentuk_tanah_value" => !($otsInArea->ground_type) ? '0' : $otsInArea->ground_type,
            "Permukaan_tanah_agunan_value" => !($otsInArea->ground_level) ? '0' : $otsInArea->ground_level,
            "Luas_tanah_sesuai_identifikasi_lapangan_agunan" => !($otsInArea->surface_area) ? '0' : intval( $otsInArea->surface_area ),
            //ots Letter
            "Jenis_surat_tanah_agunan_value" => !($otsLetter->type) ? '0' : $otsLetter->type,
            "No_surat_tanah" => !($otsLetter->number) ? '0' : $otsLetter->number,
            "Tanggal_surat_tanah_agunan" => $this->reformatDate($otsLetter->date),
            "Atas_nama_agunan" => !($otsLetter->on_behalf_of) ? '0' : $otsLetter->on_behalf_of,
            "Hak_atas_tanah_agunan_value" => !($otsLetter->authorization_land) ? '0' : $otsLetter->authorization_land,
            "Masa_hak_atas_tanah_agunan" => $this->reformatDate($otsLetter->duration_land_authorization),
            "Kemampuan_perpanjangan_hak_atas_tanah_agunan_value" => '0',//tidak ada di table
            "Kecocokan_data_kantor_agraniabpn_agunan" => !($otsLetter->match_bpn) ? '0' : $otsLetter->match_bpn,
            "Kecocokan_data_kantor_agraniabpn_agunan_value" => !($otsLetter->match_bpn) ? '0' : $otsLetter->match_bpn,
            "Nama_kantor_agrariabpn_agunan"=> !($otsLetter->bpn_name) ? '0' : $otsLetter->bpn_name,
            "Kecocokan_pemeriksaan_lokasi_tanah_lapangan_agunan_value" => !($otsLetter->match_area) ? '0' : $otsLetter->match_area,
            "Kecocokan_batas_tanah_lapangan_agunan_value" => !($otsLetter->match_limit_in_area) ? '0' : $otsLetter->match_limit_in_area,
            "Luas_tanah_berdasarkan_surat_tanah_agunan" => !($otsLetter->surface_area_by_letter) ? '0' : intval( $otsLetter->surface_area_by_letter ),
            //otsBuilding
            "No_ijin_mendirikan_bangunan_agunan" => !($otsBuilding->permit_number) ? '0' : $otsBuilding->permit_number,
            "Tanggal_ijin_mendirikan_bangunan_agunan" => $this->reformatDate($otsBuilding->permit_date),
            "Atas_nama_ijin_mendirikan_bangunan_agunan" => !($otsBuilding->on_behalf_of) ? '0' : $otsBuilding->on_behalf_of,
            "Jenis_bangunan_agunan_value" => !($otsBuilding->type) ? '3' : $otsBuilding->type,
            "Jumlah_bangunan_agunan" => !($otsBuilding->count) ? '0' : $otsBuilding->count,
            "Luas_bangunan_agunan" => !($otsBuilding->spacious) ? '0' : $otsBuilding->spacious,
            "Tahun_bangunan_agunan" => !($otsBuilding->year) ? '0' : $otsBuilding->year,
            "Uraian_bangunan_agunan" => !($otsBuilding->description) ? '0' : $otsBuilding->description,
            "Batas_utara_bangunan_agunan" => !($otsBuilding->north_limit) ? '0' : intval( $otsBuilding->north_limit ),
            "Batas_utara_bangunan_agunan1" => !($otsBuilding->north_limit_from) ? '0' :  $otsBuilding->north_limit_from ,
            "Batas_timur_bangunan_agunan" => !($otsBuilding->east_limit) ? '0' : intval( $otsBuilding->east_limit ),
            "Batas_timur_bangunan_agunan1" => !($otsBuilding->east_limit_from) ? '0' :  $otsBuilding->east_limit_from ,
            "Batas_selatan_bangunan_agunan" => !($otsBuilding->south_limit) ? '0' : intval( $otsBuilding->south_limit ),
            "Batas_selatan_bangunan_agunan1" => !($otsBuilding->south_limit_from) ? '0' : $otsBuilding->south_limit_from ,
            "Batas_barat_bangunan_agunan" => !($otsBuilding->west_limit) ? '0' : intval( $otsBuilding->west_limit ),
            "Batas_barat_bangunan_agunan1" => !($otsBuilding->west_limit_from) ? '0' :  $otsBuilding->west_limit_from ,
            //otsEnvironment
            "Peruntukan_bangunan_agunan_value" => !($otsOther->building_exchange) ? '0' : $otsOther->building_exchange,
            "Fasilitas_umum_yang_ada_agunan_pln" => !($otsEnvironment->designated_pln) ? '0' : $otsEnvironment->designated_pln,
            "Fasilitas_umum_yang_ada_agunan_pam" => !($otsEnvironment->designated_pam) ? '0' : $otsEnvironment->designated_pam,
            "Fasilitas_umum_yang_ada_agunan_telepon" => !($otsEnvironment->designated_phone) ? '0' : $otsEnvironment->designated_phone,
            "Fasilitas_umum_yang_ada_agunan_telex" => !($otsEnvironment->designated_telex) ? '0' : $otsEnvironment->designated_telex,
            "Fasilitas_umum_lain_agunan" => !($otsEnvironment->other_designated) ? '0' : $otsEnvironment->other_designated,
            "Saran_transportasi_agunan" => !($otsEnvironment->transportation) ? '0' : $otsEnvironment->transportation,
            "jarak_sarana_transportasi" => !($otsEnvironment->distance_from_transportation) ? '0' : intval( $otsEnvironment->distance_from_transportation ),
            "Lain_lain_agunan_value" => '-',
            "Petunjuk_lain_agunan" => !($otsEnvironment->other_guide) ? '0' : $otsEnvironment->other_guide,
            //valuation
            "Tanggal_penilaian_npw_tanah_agunan" => $this->reformatDate($otsValuation->scoring_land_date),
            "Npw_tanah_agunan" => !($otsValuation->npw_land) ? '0' : $this->reformatCurrency( $otsValuation->npw_land ),
            "Nl_tanah_agunan" => !($otsValuation->nl_land) ? '0' : $this->reformatCurrency( $otsValuation->nl_land ),
            "Pnpw_tanah_agunan" => !($otsValuation->pnpw_land) ? '0' : $this->reformatCurrency( $otsValuation->pnpw_land ),
            "Pnl_tanah_agunan" => !($otsValuation->pnl_land) ? '0' : $this->reformatCurrency( $otsValuation->pnl_land ),
            "Tanggal_penilaian_npw_bangunan_agunan" => $this->reformatDate($otsValuation->scoring_building_date),
            "Npw_bangunan_agunan" => !($otsValuation->npw_building) ? '0' : $this->reformatCurrency( $otsValuation->npw_building ),
            "Nl_bangunan_agunan" => !($otsValuation->nl_building) ? '0' : $this->reformatCurrency( $otsValuation->nl_building ),
            "Pnpw_bangunan_agunan" => !($otsValuation->pnpw_building) ? '0' : $this->reformatCurrency( $otsValuation->pnpw_building ),
            "Pnl_bangunan_agunan" => !($otsValuation->pnl_building) ? '0' : $this->reformatCurrency( $otsValuation->pnl_building ),
            "Tanggal_penilaian_npw_tanah_bangunan_agunan"=> $this->reformatDate($otsValuation->scoring_all_date),
            "Npw_tanah_bangunan_agunan" => !($otsValuation->npw_all) ? '0' : $this->reformatCurrency( $otsValuation->npw_all ),
            "Nl_tanah_bangunan_agunan" => !($otsValuation->nl_all) ? '0' : $this->reformatCurrency( $otsValuation->nl_all ),
            "Pnpw_tanah_bangunan_agunan" => !($otsValuation->pnpw_all) ? '0' : $this->reformatCurrency( $otsValuation->pnpw_all ),
            "Pnl_tanah_bangunan_agunan" => !($otsValuation->pnl_all) ? '0' : $this->reformatCurrency( $otsValuation->pnl_all ),
            //otsOther
            "Jenis_ikatan_agunan_value" => !($otsOther->bond_type) ? '0' : $otsOther->bond_type,
            "Penggunaan_bangunan_sesuai_fungsinya_agunan_value" => !($otsOther->use_of_building_function) ? '0' : $otsOther->use_of_building_function,
            "Penggunaan_bangunan_sesuai_optimal_agunan_value" => !($otsOther->optimal_building_use) ? '0' : $otsOther->optimal_building_use,
            "Peruntukan_tanah_agunan_value" => !($otsEnvironment->designated_land) ? '0' : $otsEnvironment->designated_land,
            "jarak_posisi_terhadap_jalan"=>!($otsInArea->distance_of_position) ? '0' : intval($otsInArea->distance_of_position),
            "Nama_debitur_agunan" => !( $this->customer_name ) ? '' : $this->customer_name,
            "Biaya_sewa_agunan" => '0',//tidak ada di table
            "Hal_perludiketahui_bank_agunan" => !($otsOther->things_bank_must_know) ? '0' : $otsOther->things_bank_must_know,
            "uid_ao"=> '0'

        ];
        return $request;
    }

    /**
     * Generate Parameters for step 10.
     *
     * @param array $data
     * @return array $request
     */
    public function step10($data)
    {
       \Log::info("step10");
        $kpr = $this->kpr;
        $collateral = Collateral::WithAll()->where('property_id',$kpr->property_id)->firstOrFail();
        $otsInArea = $collateral->otsInArea;
        $otsLetter = $collateral->otsLetter;
        $otsBuilding = $collateral->otsBuilding;
        $otsEnvironment = $collateral->otsEnvironment;
        $otsValuation = $collateral->otsValuation;
        $otsOther = $collateral->otsOther;
        $otsSeven = $collateral->otsSeven;
        $otsEight = $collateral->otsEight;
        $otsNine = $collateral->otsNine;
        $otsTen = $collateral->otsTen;

        $request = $data + [
            "Fid_agunan" => (isset($this->additional_parameters['fid_agunan']))? $this->additional_parameters['fid_agunan'] : '0',
            "Nama_debitur_agunan_rt" => !( $this->customer_name ) ? '' : $this->customer_name,
            "Jenis_agunan_value_rt" => !($otsBuilding->type) ? '3' : $otsBuilding->type,
            "Status_agunan_value_agunan_rt" => !($otsSeven->collateral_status) ? 'Ditempati Sendiri' : $otsSeven->collateral_status,
            "Deskripsi_agunan_rt" => !($otsSeven->description) ? '0' : $otsSeven->description,
            "Jenis_mata_uang_agunan_rt" => 'IDR',
            "Nama_pemilik_agunan_rt" => !($otsSeven->on_behalf_of) ? '0' : $otsSeven->on_behalf_of,
            "Status_bukti_kepemilikan_value_agunan_rt" => !($otsSeven->ownership_status) ? '0' : $otsSeven->ownership_status,
            "Nomor_bukti_kepemilikan_agunan_rt" => !($otsSeven->ownership_number) ? '0' : $otsSeven->ownership_number,
            "Tanggal_bukti_kepemilikan_agunan_rt" => $this->reformatDate($otsSeven->date_evidence),
            "Tanggal_jatuh_tempo_agunan_rt"=> $this->reformatDate($otsLetter->duration_land_authorization),
            "Alamat_agunan_rt" => !($otsSeven->address_collateral) ? '0': str_replace("'", "",$otsSeven->address_collateral),
            "Kelurahan_agunan_rt" => !($otsSeven->village) ? '0' : $otsSeven->village,
            "Kecamatan_agunan_rt" => !($otsSeven->districts) ? '0' : $otsSeven->districts,
            "Lokasi_agunan_rt" =>!($otsSeven->city) ? '0' : ( !($otsSeven->city->name) ? '0' : $otsSeven->city->name ),
            "Nilai_pasar_wajar_agunan_rt"=>!($otsEight->fair_market) ? '0' : $this->reformatCurrency( $otsEight->fair_market ),
            "Nilai_likuidasi_agunan_rt"=>!($otsEight->liquidation) ? '0' : $this->reformatCurrency( $otsEight->liquidation ),
            "Proyeksi_nilai_pasar_wajar_agunan_rt"=>!($otsEight->fair_market_projection) ? '0' : $this->reformatCurrency( $otsEight->fair_market_projection ),
            "Proyeksi_nilai_likuidasi_agunan_rt" => !($otsEight->liquidation_projection) ? '0' : $this->reformatCurrency( $otsEight->liquidation_projection ),
            "Nilai_likuidasi_saat_realisasi_agunan_rt"=>!($otsEight->liquidation_realization) ? '0' : $this->reformatCurrency( $otsEight->liquidation_realization ),
            "Nilai_jual_obyek_pajak_agunan_rt" =>!($otsEight->njop) ? '0' : $this->reformatCurrency( $otsEight->njop ),// no pokok wajib pajak
            "Penilaian_appraisal_dilakukan_oleh_value_agunan_rt"=>!($otsEight->appraisal_by) ? 'bank' : $otsEight->appraisal_by,// bank and independent
            "Penilai_independent_agunan_rt"=>!($otsEight->independent_appraiser) ? '0' : $otsEight->independent_appraiser,
            "Tanggal_penilaian_terakhir_agunan_rt" => $this->reformatDate($otsEight->date_assessment),
            "Jenis_pengikatan_value_agunan_rt" => !($otsEight->type_binding)?'0':$otsEight->type_binding,
            "No_bukti_pengikatan_agunan_rt" => !($otsEight->binding_number)?'0': $otsEight->binding_number,
            "Nilai_pengikatan_agunan_rt" => !($otsEight->binding_value) ? '0' : $this->reformatCurrency( $otsEight->binding_value ),
            "Paripasu_value_agunan_rt" => !($otsTen->paripasu) ? 'false' : ($otsTen->paripasu == 'Ya' ? 'true':'false' ),
            "Nilai_paripasu_agunan_bank_rt" => !($otsTen->paripasu_bank) ? '0' : $this->reformatCurrency( $otsTen->paripasu_bank ),
            "Flag_asuransi_value_agunan_rt" => !($otsTen->insurance)? 'Tidak': $otsTen->insurance,
            "Nama_perusahaan_asuransi_agunan_rt" =>!($otsTen->insurance_company)?"IJK":$otsTen->insurance_company,
            "Nilai_asuransi_agunan_rt" => !($otsTen->insurance_value) ? '0' : $this->reformatCurrency( $otsTen->insurance_value ),
            "Eligibility_value_agunan_rt" => !($otsTen->eligibility)? '0' : $otsTen->eligibility,
            // Field Tambahan
            "Pemecah_sertifikat_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date)?'':$this->reformatDate($otsNine->receipt_date),//string kosong apabila tidak di berikan
            "Pemecah_sertifikat_status_value_agunan_rt"=>!($otsNine->certificate_status)? '' : ($otsNine->certificate_status == "Sudah Diberikan" ? '1' : '0'),
            "Pemecah_sertifikat_keterangan_agunan_rt"=>!($otsNine->information)? '' : $otsNine->information,
            "Dokumen_notaris_delevoper_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_notary)?'':$this->reformatDate($otsNine->receipt_date_notary),
            "Dokumen_notaris_developer_status_value_agunan_rt"=>!($otsNine->notary_status)? '' : ($otsNine->notary_status == "Sudah Diberikan" ? '1' : '0'),
            "Dokumen_notaris_developer_keterangan_agunan_rt"=>!($otsNine->information_notary)? '' : $otsNine->information_notary,
            "Dok_take_over_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_takeover)?'':$this->reformatDate($otsNine->receipt_date_takeover),
            "Dok_take_over_value_agunan_rt"=>!($otsNine->takeover_status)? '' : ($otsNine->takeover_status == "Sudah Diberikan" ? '1' : '0'),
            "Dok_take_over_keterangan_agunan_rt"=>!($otsNine->information_takeover)? '' : $otsNine->information_takeover,
            "Perjanjian_kredit_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_credit)?'':$this->reformatDate($otsNine->receipt_date_credit),
            "Perjanjian_kredit_status_value_agunan_rt"=>!($otsNine->credit_status)? '' : ($otsNine->credit_status == "Sudah Diberikan" ? '1' : '0'),
            "Perjanjian_kredit_keterangan_agunan_rt"=>!($otsNine->information_credit)? '' : $otsNine->information_credit,
            "Skmht_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_skmht)?'':$this->reformatDate($otsNine->receipt_date_skmht),
            "Skmht_status_value_agunan_rt"=>!($otsNine->skmht_status)? '' : ($otsNine->skmht_status == "Sudah Diberikan" ? '1' : '0'),
            "Skmht_keterangan_agunan_rt"=>!($otsNine->information_skmht)? '' : $otsNine->information_skmht,
            "Imb_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_imb)?'':$this->reformatDate($otsNine->receipt_date_imb),
            "Imb_status_value_agunan_rt"=>!($otsNine->imb_status)? '' : ($otsNine->imb_status == "Sudah Diberikan" ? '1' : '0'),
            "Imb_keterangan_agunan_rt"=>!($otsNine->information_imb)? '' : $otsNine->information_imb,
            "Shgb_tanggal_penerimaan_agunan_rt"=>!($otsNine->receipt_date_shgb)?'':$this->reformatDate($otsNine->receipt_date_shgb),
            "Shgb_status_value_agunan_rt"=>!($otsNine->shgb_status)? '' : ($otsNine->shgb_status == "Sudah Diberikan" ? '1' : '0'),
            "Shgb_keterangan_agunan_rt"=>!($otsNine->information_shgb)? '' : $otsNine->information_shgb
        ];
        return $request;
    }

    /**
     * Generate Parameters for step VIP.
     *
     * @param array $data
     * @return array $request
     */
    public function stepVIP($data)
    {
        \Log::info("stepVIP");
        $request = $data + [
            "nama_pengelola" => !($this->ao_name) ? '': $this->ao_name ,
            "pn_pengelola" => !($this->ao_id) ? '': $this->ao_id,
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4)
        ];
        return $request;
    }

    /**
     * Get eform date action.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail_actions()
    {
        return $this->hasMany('App\Models\ActionDate', 'eform_id');
    }

    public function user_notifications()
    {
        return $this->hasMany('App\Models\UserNotification', 'notifiable_id');
    }

    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Validate data.
     *
     * @param string $money
     * @return string $return
     */
    public function validateData( $variable )
    {
        if ( $variable ) {
            return $this->reformatCurrency( number_format($variable, 0, '', '') );
        }
        return '0';
    }

    /**
     * Get DIR.
     *
     * @param string $money
     * @return string $return
     */
    public function getDIR( $thp, $dir )
    {
        if ( $thp > 25000000 ) {
            $dir = 50;

        } else if ( $thp >= 15000000 && $thp <= 25000000 ) {
            $dir = 45;

        }
        return $dir;
    }

    /**
     * Max Plafond.
     *
     * @return string $return
     */
    public function getMaxPlafond( $interestInYear, $installment )
    {
        $rate = $this->kpr->year;

        $interestInMonth = $interestInYear / 12; //suku bunga per tahun dibagi 12
        $interestInMonthPercentage = $interestInMonth / 100; //suku bunga per bulan dibagi %
        $x = 1 + $interestInMonthPercentage;
        $y = pow($x, $rate); //di pangkatkan jangka waktu

        return intval(( $installment / $interestInMonthPercentage ) * ( 1 - ( 1 / $y ) )); //maksimum plafond
    }

    /**
     * Get installment based on plafond.
     *
     * @return string $return
     */
    public function getInstallment( $interestInYear )
    {
        $rate = $this->kpr->year;
        $requested_amount = $this->kpr->request_amount;

        $interestInMonth = $interestInYear / 12; //suku bunga per tahun dibagi 12
        $interestInMonthPercentage = $interestInMonth / 100; //suku bunga per bulan dibagi %
        $x = 1 + $interestInMonthPercentage;
        $y = pow($x, $rate); //di pangkatkan jangka waktu

        return intval(( $requested_amount * $interestInMonthPercentage ) / ( 1 - ( 1 / $y ) )); //jumlah plafon kredit yang diusulkan
    }

    /**
     * Get interest from service.
     *
     * @return string $return
     */
    public function getInterest( $fid_aplikasi )
    {
        if ( !isset($this->additional_parameters['gimmick_rate']) ) {
            if ( ENV('APP_ENV') == 'local' ) {
                $getGimmick = array(
                    "code" => "200"
                    , "descriptions" => "Success"
                    , "contents" => "7.11"
                );

            } else {
                $getGimmick = Asmx::setEndpoint( 'GetGimmickRate' )
                    ->setBody([
                        'Request' => json_encode( array(
                            'fid_aplikasi' => $fid_aplikasi
                        ) )
                    ])
                    ->post( 'form_params' );
            }

            $this->additional_parameters += [ "gimmick_rate" => $getGimmick[ 'contents' ] ] ;
            $this->save();

            $return = $getGimmick[ 'contents' ];

        } else {
            $return = $this->additional_parameters['gimmick_rate'];

        }

        return floatval($return);
    }

    /**
     * Reformat currency.
     *
     * @param string $money
     * @return string $return
     */
    public function reformatCurrency( $money )
    {
        return $money ? round( str_replace(',', '.', str_replace('.', '', $money))) : '0';
    }

    /**
     * Remove comma and dot.
     *
     * @param string $place
     * @return string $return
     */
    public function reformatString( $place )
    {
        return $place ? str_replace(',', '', str_replace('.', '', $place)) : '';
    }

    /**
     * Reformat Title
     *
     * @param string $place
     * @return string $return
     */
    public function reformatTitle( $title )
    {
        if ( $title ) {
            switch ($title) {
                case 'SD':
                    return 2;
                    break;

                case 'SE':
                    return 2;
                    break;

                case 'SM':
                    return 3;
                    break;

                case 'SU':
                    return 4;
                    break;

                case 'S1':
                    return 8;
                    break;

                case 'S2':
                    return 9;
                    break;

                case 'S3':
                    return 10;
                    break;

                case 'ZZ':
                    return 7;
                    break;

                default:
                    return 7;
                    break;
            }
        }

        return 7;
    }

    /**
     * Reformat City.
     *
     * @param string $place
     * @return string $return
     */
    public function reformatCity( $city )
    {
        return $city;
    }

    /**
     * Reformat Date ddmmyyyy.
     *
     * @param string $place
     * @return string $return
     */
    public function reformatDate( $date )
    {
        if ( $date ) {
            $dates = explode("-", $date);
            return $dates[2].$dates[1].$dates[0];
        }

        return '01019999';
    }

    /**
     * Get chart attribute
     */

    public function getChartAttribute()
    {
        return [
            'month'  => $this->month,
            'month2' => $this->month2,
            'value'  => $this->value,
        ];
    }


    /**
        * Get list count for chart
        *
        * @param  string $startChart
        * @param  string $endChart
        * @return array
    */
    public function getChartEForm($startChart, $endChart, $user_id = null)
    {
        $filter = false;
        if(!empty($startChart) && !empty($endChart)){
            $startChart = date("01-m-Y",strtotime($startChart));
            $endChart   = date("t-m-Y", strtotime($endChart));

            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;

        }else if(empty($startChart) && !empty($endChart)){
            $now        = new \DateTime();
            $startChart = $now->format('Y-m-d h:i:s');

            $endChart   = date("t-m-Y", strtotime($endChart));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;

        }else if(empty($endChart) && !empty($startChart)){
            $now      = new \DateTime();
            $endChart = $now->format('Y-m-d h:i:s');

            $startList = date("01-m-Y",strtotime($startList));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $filter = true;

        }

        $data = Eform::select(
                DB::raw("count(eforms.id) as value"),
                DB::raw("to_char(eforms.created_at, 'TMMonth YYYY') as month"),
                DB::raw("to_char(eforms.created_at, 'MM YYYY') as month2"),
                DB::raw("to_char(eforms.created_at, 'YYYY MM') as order")
            )
            ->with("kpr");

        if ( $user_id ) {
            $data = $data->whereHas("kpr", function ($query) use ($user_id) {
                return $query->join('properties', 'properties.id', 'property_id')
                    ->where('kpr.developer_id', $user_id);
            });
        }
        $user = \RestwsHc::getUser();
        if (count($user)>0) {
            if ($user['role'] == 'ao') {
                $data->where('ao_id',substr('000'.$user['pn'], -8));
            }elseif ($user['role'] == 'mp' || $user['role'] == 'amp' || $user['role'] == 'pinca') {
                $data->where('branch_id',intval($user['branch_id']));
            }
        }

        $data = $data->when($filter, function ($query) use ($startChart, $endChart){
                return $query->whereBetween('eforms.created_at', [$startChart, $endChart]);
            })
            ->groupBy('month', 'month2', 'order')
            ->orderBy("order", "asc")
            ->get()
            ->pluck("chart");

        return $data;
    }

    public function getNewestEFormAttribute()
    {
        // Set language to Bahasa
        Carbon::setLocale('id');
		$nominal = 0;
		if($this->product_type == 'kpr'){
			$nominal = $this->nominal;
		}elseif($this->product_type=='briguna'){
			$nominal = $this->briguna->request_amount;
		}else{
			$nominal = '0';
		}
        // return custom collection
        return [
            'no_ref'            => $this->ref_number,
            'nasabah'           => $this->customer['personal']['name'],
            'nominal'           => $nominal,
            'product_type'      => $this->product_type,
            'tanggal_pengajuan' => date('d-M-Y', strtotime($this->created_at)),
            'no_telepon'        => empty($this->mobile_phone) ? null : $this->mobile_phone,
            'prescreening'      => $this->prescreening_status,
            'status'            => $this->status,
            'aging'             => Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans()
        ];
    }

    public function getNewestEForm($startList, $endList)
    {
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
        $user = \RestwsHc::getUser();
        $data = EForm::select();

		$BRANCH_CODE = intval($user['branch_id']);
								$branchcis ='';
								if(strlen($BRANCH_CODE)=='5'){
									$branchcis = $BRANCH_CODE;
									$p = '';
									for($l=0;$l<5;$l++){
										if(substr($BRANCH_CODE,$l,1)!='0'){
											$p = $l;
											goto tangkep;
										}
									}
									tangkep : $branchcis = substr($BRANCH_CODE,$p,5);
									/* for($i=0;$i<5;$i++){
										$cek = substr($BRANCH_CODE,$i,1);
										if($cek!=0){
											$branchcis = substr($BRANCH_CODE,$i,4);
											$i = 5;
										}
									} */
								}else{										
										$o = strlen($BRANCH_CODE);
										$branchut = '';
										for($y=$o;$y<5;$y++){
											if($y==$o){
												$branchut = '0'.$BRANCH_CODE;
											}else{
												$branchut = '0'.$branchut;
											}
										} 
										$branchcis = $branchut;	
								}
        if (count($user)>0) {
            if ($user['role'] == 'ao') {
                $data->where('ao_id',$user['pn']);
            }elseif ($user['role'] == 'mp' || $user['role'] == 'amp' || $user['role'] == 'pinca') {
                $data->whereRaw('(eforms."branch_id"='."'".$branchcis."'".' OR eforms."branch_id"='."'".$BRANCH_CODE."'".')');
				//$data->where('branch_id',$branchcis)->orWhere('branch_id',$BRANCH_CODE);
            }
        }

        $data = $data->when($filter, function($query) use ($startList, $endList){
                    return $query->whereBetween('eforms.created_at', [$startList, $endList]);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->pluck('newestEForm');

        return $data;
    }
}
