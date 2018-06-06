<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\UserNotification;
use App\Models\Developer;
use App\Models\UserDevelopers;
use App\Models\PropertyItem;
use App\Models\Collateral;
use App\Models\Appointment;
use App\Models\BRIGUNA;
use Carbon\Carbon;
use Sentinel;
use Asmx;
use RestwsHc;
use DB;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Models\KartuKredit;

class EFormMonitoring extends Model implements AuditableContract
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
    protected $appends = [ 'recontestdata', 'customer_name', 'mobile_phone', 'catatan_analis', 'catatan_reviewer', 'penilaian_agunan', 'plafond_usulan', 'catatan_tolak', 'disbushr', 'nominal',  'status', 'aging', 'is_visited', 'pefindo_color', 'is_recontest', 'is_clas_ready', 'selected_pefindo_json', 'kanwils' ];

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
	 
	public function getCatatanAnalisAttribute()
		{
			$catatan_analis = array();
			if(count($this->additional_parameters)>0){
				$fid_aplikasi = $this->additional_parameters['fid_aplikasi'];
			\Log::info("-------------------connect to clas-----------------");
				$servernyaclas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='http://apimybridev.bri.co.id/'){	
					$servernyaclas = 'sqlsrv_clas';
			}else{
					$servernyaclas = 'sqlsrv_clas_prod';
			  }
			  
					$catatan_analis = DB::connection($servernyaclas)->table('CLS_T_HISTORY_ANALIS')->select('CATATAN_ANALIS')->where('fid_aplikasi',$fid_aplikasi)->get();
				$analisa = array();
			if(count($catatan_analis)>=1){
				foreach ($catatan_analis as $data) {	
					$analisa[] = $data->CATATAN_ANALIS;
				}
					return $analisa; die();
			}
			}
			return '';die();
		}
		
		
	public function getCatatanReviewerAttribute()
		{
			$catatan_pemutus = array();
			if(count($this->additional_parameters)>0){
				$fid_aplikasi = $this->additional_parameters['fid_aplikasi'];
			\Log::info("-------------------connect to clas-----------------");
				$servernyaclas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='http://apimybridev.bri.co.id/'){	
					$servernyaclas = 'sqlsrv_clas';
			}else{
					$servernyaclas = 'sqlsrv_clas_prod';
			  }
			  
					$catatan_pemutus = DB::connection($servernyaclas)->table('CLS_T_HISTORY_PUTUSAN')->select('CATATAN_PEMUTUS')->where('jenis_putusan','reviewer')->where('putusan_pemutus','Belum Diputus')->where('fid_aplikasi',$fid_aplikasi)->get();
				$analisa = array();
			if(count($catatan_pemutus)>=1){
				foreach ($catatan_pemutus as $data) {
					$analisa[] = $data->CATATAN_PEMUTUS;
				}
					return $analisa; die();
			}
			}
			return '';die();
		}
		
	public function getPenilaianAgunanAttribute()
		{
			$penilaian_agunan = array();
			if(count($this->additional_parameters)>0){
				$fid_aplikasi = $this->additional_parameters['fid_aplikasi'];
			\Log::info("-------------------connect to clas-----------------");
				$servernyaclas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='http://apimybridev.bri.co.id/'){	
					$servernyaclas = 'sqlsrv_clas';
			}else{
					$servernyaclas = 'sqlsrv_clas_prod';
			  }
			  
					$penilaian_agunan = DB::connection($servernyaclas)->table('CLS_KPR_MODEL71')->select('SEBESAR')->where('fid_aplikasi',$fid_aplikasi)->get();
				$analisa = array();
			if(count($penilaian_agunan)>=1){
				foreach ($penilaian_agunan as $data) {
					$analisa[] = $data->SEBESAR;
				}
					return $analisa; die();
			}
			}
			return '';die();
		}
		
			public function getPlafondUsulanAttribute()
		{
			$plafond_usulan = array();
			if(count($this->additional_parameters)>0){
				$fid_cif_las = $this->additional_parameters['fid_cif_las'];
			\Log::info("-------------------connect to clas-----------------");
				$servernyaclas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='http://apimybridev.bri.co.id/'){	
					$servernyaclas = 'sqlsrv_clas';
			}else{
					$servernyaclas = 'sqlsrv_clas_prod';
			  }
			  
					$plafond_usulan = DB::connection($servernyaclas)->table('LAS_T_APLIKASI')->select('PLAFOND_INDUK')->where('FID_CIF_LAS',$fid_cif_las)->get();
				$putusan = array();
			if(count($plafond_usulan)>=1){
				foreach ($plafond_usulan as $data) {
					$putusan[] = $data->PLAFOND_INDUK;
				}
					return $putusan; die();
			}
			}
			return '';die();
		}
		
		
			public function getCatatanTolakAttribute()
		{
			$catatan_tolak = array();
			if(count($this->additional_parameters)>0){
				$fid_aplikasi = $this->additional_parameters['fid_aplikasi'];
			\Log::info("-------------------connect to clas-----------------");
				$servernyaclas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='http://apimybridev.bri.co.id/'){	
					$servernyaclas = 'sqlsrv_clas';
			}else{
					$servernyaclas = 'sqlsrv_clas_prod';
			  }
			  
					$catatan_tolak = DB::connection($servernyaclas)->table('CLS_T_HISTORY_PUTUSAN')->select('CLS_T_HISTORY_PUTUSAN.CATATAN_PEMUTUS')
								->join('LAS_T_STATUS_APLIKASI','LAS_T_STATUS_APLIKASI.fid_aplikasi','=','CLS_T_HISTORY_PUTUSAN.fid_aplikasi')
								->where('CLS_T_HISTORY_PUTUSAN.jenis_putusan','putusan kredit')
								->where('LAS_T_STATUS_APLIKASI.fid_st_aplikasi','17')
								->where('LAS_T_STATUS_APLIKASI.fid_aplikasi',$fid_aplikasi)->get();
				$putusan = array();
			if(count($catatan_tolak)>=1){
				foreach ($catatan_tolak as $data) {
					$putusan[] = $data->CATATAN_PEMUTUS;
				}
					return $putusan; die();
			}
			}
			return '';die();
		}
	
	public function getDisbushrAttribute()
		{
			 $disbursed = DB::table('action_dates')
                         ->select('action_dates.created_at')
                         ->where('action_dates.action', 'eform-Disbursed')
						 ->where('action_dates.eform_id', $this->eform_id)
                         ->get();
                $disbursed = $disbursed->toArray();
                $disbursed = json_decode(json_encode($disbursed), True);
				return $disbursed;
		}
    
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

        } elseif ( ( $value >= 677 && $value <= 900 ) || $value == 999 ) {
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
		$x = $this->recontest();
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
		$x['waktu_aging'] = $days . ' hari ';
        return $x;
    }

	
    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getKanwilsAttribute()
    {
		$kode_uker = $this->branch_id;
		$start = '';
		if(strlen($kode_uker)=='5'){
			for($i=0;$i<5;$i++){
				if(substr($kode_uker,$i,1)!=0){
					$start = $i;
					goto uker;
				}
			}
			uker:
			$kode_uker = substr($kode_uker,$start,4);
		}
		
		$dati2 = DB::table('uker_tables')
                ->select('uker_tables.dati2')
                ->where('uker_tables.kode_uker', $kode_uker)
                ->get();
		 $dati2 = $dati2->toArray();
         $dati2 = json_decode(json_encode($dati2), True);
        return $dati2[0]['dati2'].' / '.$this->branch;
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
        if( $this->recontest() ) {
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
        
        // $eform = $query->where( function( $eform ) );

        \Log::info("------- FILTERING EFORMS RESULT -------");
        \Log::info(print_r($request->source,true).' '.print_r($request->dev_id,true));

        $eform = $query->from('eforms')->join('kpr', 'kpr.eform_id', '=', 'eforms.id')
            ->where( function( $eform ) use( $request ) {
                if( $request->has('product_type') &&  $request->product_type!='-') {
                    \Log::info("------- FILTERING PRODUCT TYPE RESULT -------");
                    $eform->where('eforms.product_type', $request->product_type);
                }

                if( $request->product_type=='kpr' && $request->has('source') &&  $request->source!='-') {
                    \Log::info("------- FILTERING SOURCE RESULT -------");
                    if($request->source=='nondev' || $request->dev_id=='nondev'){
                        \Log::info("------- FILTERING NONDEV RESULT -------");
                        $eform->whereNull('kpr.developer_id');
                    }
                    else if($request->source=='rumah.com' || $request->dev_id=='rumah.com'){
                        \Log::info("------- FILTERING rumah.com RESULT -------");
                        //$eform->where('kpr.developer_id', 2706);
            			$agen = UserDeveloper::select('user_id')->where('admin_developer_id',2706);
            			$eform->whereIn('sales_dev_id',$agen);
                    }
                    else if($request->source=='dev' &&  $request->dev_id!='-'){
                        \Log::info("------- FILTERING DEV RESULT -------");
                        $eform->where('kpr.developer_id', $request->dev_id);
                    }
                }
                if( $request->has('branch_id') &&  $request->branch_id!='-') {
                    $eform->where('eforms.branch_id', $request->branch_id);
                }
            })
            ->orderBy('created_at', 'asc');

        // $eform->join('kpr', 'kpr.eform_id', '=', 'eforms.id');

        // if($request->product_type=='kpr' && $request->has('source') &&  $request->source!='-'){
        //     \Log::info("------- FILTERING KPR RESULT -------");
        //     \Log::info(print_r($request->source,true).' '.print_r($request->dev_id,true));

        //     if($request->source=='nondev' || $request->dev_id=='nondev'){
        //         \Log::info("------- FILTERING NONDEV RESULT -------");
        //         $eform->whereNull('kpr.developer_id');
        //     }
        //     else if($request->source=='rumah.com' || $request->dev_id=='rumah.com'){
        //         \Log::info("------- FILTERING rumah.com RESULT -------");
        //         $eform->where('kpr.developer_id', 2706);
        //     }
        //     else if($request->source=='dev' &&  $request->dev_id!='-'){
        //         \Log::info("------- FILTERING DEV RESULT -------");
        //         $eform->where('kpr.developer_id', $request->dev_id);
        //     }
        // }

            // if( $request->has('source') &&  $request->source!='-' ){
            //     if($request->source=='nondev')
            //         $eform->whereNull('kpr.developer_id');
            //     else if($request->source=='rumah.com')
            //         $eform->where('kpr.developer_id', 2706);
            // }
            // else if($request->has('dev_id') &&  $request->dev_id!='-') {
            //     $eform->where('kpr.developer_id', $request->dev_id);
            // }

        // if( $request->has('branch_id') && $request->branch_id!=''  &&  $request->branch_id!='-') {
        //     $eform->where('eforms.branch_id', $request->branch_id);
        // }
        
        // if ( $sort[0] == "ref_number" || $sort[0] == "action" || $sort[0] == "aging" ) {
        //     $sort[0] = 'created_at';
        // }

        // $eform = $eform->orderBy('eforms.'.$sort[0], $sort[1]);

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
	  public function getRecontestdataAttribute()
    {
		$recontest =  $this->hasOne( Recontest::class, 'eform_id' )->get();
        return $recontest;
    }
    public function recontest()
    {
		$aging = $this->getAtributeAging($this->hasMany( Monitoring\Action_dates::class, 'eform_id' )->get());
		$return = ['aging'=>$aging];
        return $return;
//        return $this->hasOne( Recontest::class, 'eform_id' );
    }
	public function getAtributeAging($value){
		$return = array();
		$i = 0;
		$go = array();
		foreach ($value as $data){
			$data_action = str_replace('eform-','',$data['action']);
			$return['id'] = $data['id'];
			$return['eform_id'] = $data['eform_id'];
			$return['data_action'] = $data_action;
			$return['created_at'] = $data['created_at'];
			$return['updated_at'] = $data['updated_at'];
			$go['x'.$i] = $return;
			$i = (int) $i+1;
		}
		return $go;
	}
	public function getAtributeDeveloper($value){
		$return = array();
		foreach ($value as $data){
			$return = ['id'=>$data['id'],'company_name'=>$data['company_name']];
		}
		return $return;
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

        // return custom collection
        return [
            'no_ref'            => $this->ref_number,
            'nasabah'           => $this->customer['personal']['name'],
            'nominal'           => $this->nominal,
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

        if (count($user)>0) {
            if ($user['role'] == 'ao') {
                $data->where('ao_id',$user['pn']);
            }elseif ($user['role'] == 'mp' || $user['role'] == 'amp' || $user['role'] == 'pinca') {
                $data->where('branch_id',intval($user['branch_id']));
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
