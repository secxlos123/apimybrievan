<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lang;
use DB;
use App\Models\ApprovalDataChange;

class UserNotification extends Model
{
	protected $table = 'notifications';
	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	public $incrementing = false;

	protected $casts = [
		'id' => 'string',
		'data' => 'array',
		'type' => 'string',
		'type_module' => 'string',
	];

	public function scopeUnreads($query) {
		return $query->whereNull('read_at');
	}

	public function notifiable() {
		return $this->morphTo();
	}

	/**
	 * Mark the notification as read.
	 *
	 * @return void
	 */
	public function markAsRead($is_read = null) {
		if (is_null($this->read_at)) {
			$this->forceFill(['read_at' => $this->freshTimestamp(),
								'is_read' => $is_read ])->save();
		}
	}

	public function getIsReadAttribute() {
		return (bool) $this->read_at;
	}

	public function getSubject($status_eform, $ref_number, $user_id) {

		$typeModuleEform = getTypeModule(EForm::class);
		$typeModuleCollateral = getTypeModule(Collateral::class);
		$approvalDataChange = ApprovalDataChange::where('related_id',$this->slug)->first();
		$approval_data_changes_id = $approvalDataChange ? $approvalDataChange->id : 0;
		$internalurl = env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/');
		$externalurl =  env('MAIN_APP_URL', 'http://mybri.bri.co.id/');
		$url = $internalurl . 'eform?ref_number=' . $ref_number . '&slug=' . $this->slug.'&type='.$this->type_module;
		if ($user_id) {
			$url = $externalurl.'tracking/detail/'.$this->slug;
		} else {
			$url = $url;
		}
		$tipeNotif = $this->type;
		if($tipeNotif == 'App\Notifications\CollateralManagerApprove' || $tipeNotif  == 'App\Notifications\CollateralManagerRejected'){
			$slug = !empty($this->data['prop_slug']) ? $this->data['prop_slug'] : null;
		}

		if($tipeNotif == 'App\Notifications\CollateralOTS' || $tipeNotif == 'App\Notifications\CollateralStafRejectOTS'){
				$collateralAppraisal =!empty($this->data['staff_name']) ? $this->data['staff_name'] : null;
				$debitur = !empty($this->data['user_name']) ? $this->data['user_name'] : null;
		}
		switch ($this->type) {
		/* eform  */
		case 'App\Notifications\PengajuanKprNotification':
			$subjectNotif = ['message' => 'Pengajuan Aplikasi KPR Baru',
				'message_external' => 'Selamat, pengajuan kredit anda sukses dilakukan. petugas BRI akan segera menghubungi no HP yang telah anda daftarkan.',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\EFormPenugasanDisposisi':
			$subjectNotif = ['message' => 'Disposisi Pengajuan',
				'message_external' => '',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\ApproveEFormCustomer':
			if ($status_eform == 'approved') {
				$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Setujui',
					'message_external' => 'Permohonan KPR an. '.$this->data['user_name'].' no : '.$this->data['ref_number'].' dalam proses analisa oleh BRI.',
					'url' => $url,
					'url_mobile' => '#',
				];
			} else {
				$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
					'message_external' => '',
					'url' => $url,
					'url_mobile' => '#',
				];
			}
			break;
		case 'App\Notifications\ApproveEFormCLAS':
			$getKPR = KPR::where('eform_id',$this->slug)->first();
			$plafondKredit =  !($getKPR->request_amount) ? $getKPR->request_amount : 0;

			$subjectNotif = ['message' => 'Selamat Permohonan KPR an. '.$this->data['user_name'].' no : '.$this->data['ref_number'].' telah disetujui sebesar RP. '.number_format($plafondKredit,2).' Mohon siapkan dokumen yang diperlukan untuk penandatanganan akad kredit. Informasi lebih lanjut harap hubungi tenaga pemasar BRI',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\RejectEFormCustomer':
			$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Tolak',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\RejectEFormCLAS':
			$subjectNotif = ['message' => 'Mohon maaf pengajuan KPR an. '.$this->data['user_name'].' no : '.$this->data['ref_number'].' belum dapat kami setujui. Mohon hubungi tenaga pemasar kami untuk keterangan lebih lanjut.',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\LKNEFormCustomer':
			$subjectNotif = ['message' => 'Prakarsa LKN.',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\LKNEFormRecontest':
			$subjectNotif = ['message' => 'LKN Recontest.',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\LKNEFormCLAS':
			$subjectNotif = ['message' => 'Prakarsa LKN',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\ScorePefindoPreScreening':
			$subjectNotif = ['message' => 'Eform Pengajuan Telah Diisi Score Pefindo',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationApproveFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationRejectFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menolak Form KPR',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationDataNasabah':
			$subjectNotif = ['message' => 'Verifikasi Pengajuan KPR',
				'url' => env('MAIN_APP_URL', 'http://mybri.bri.co.id/') . 'verification?ref_number=' . $ref_number . '&slug=' . $this->slug,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\PropertyNotification':
			$subjectNotif = ['message' => 'Proyek Data Baru',
				'url' => '',
				'message_external' => 'Terdapat permohohonan penilaian agunan baru dari '.$this->data['user_name'].' untuk Developer PKS BRI. Harap segera lakukan penilaian agunan sesuai ketentuan yang berlaku.',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\NewSchedulerCustomer':
			$subjectNotif = ['message' => 'Schedule Data Baru',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
				'message_external' => 'Schedule Data Baru',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\UpdateSchedulerCustomer':
			$subjectNotif = ['message' => 'Update Data Schedule',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
				'message_external' => 'Mohon maaf terjadi perubahan jadwal pertemuan dengan tenaga pemasar kami. Detail informasi dapat dilihat di fitur penjadwalan dalam aplikasi MyBRI.',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\EditDeveloper':
			$subjectNotif = ['message' => 'Perbaharui Data Profile',
				'url' => '/approval-data/developer?related_id=' .@$approval_data_changes_id.'&slug=' . $this->slug.'&type='.$this->type_module,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\ApproveDeveloperProfile':

			$subjectNotif = ['message' => 'Data Profile Telah Disetujui ',
				'url' => '/dev/profile/personal?related_id=' .@$approval_data_changes_id.'&slug=' . $this->slug.'&type='.$this->type_module,
				'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan.',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\RejectDeveloperProfile':
			$subjectNotif = ['message' => 'Data Profile Telah Disetujui ',
				'url' => '/dev/profile/personal?related_id=' .@$approval_data_changes_id.'&slug=' . $this->slug.'&type='.$this->type_module,
				'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI',
				'url_mobile' => '#',
			];
			break;

		case 'App\Notifications\RecontestEFormNotification':
			$subjectNotif = ['message' => 'Pengajuan Anda Telah di Rekontest.',
				'url' => $url,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		//collateral notif
		case 'App\Notifications\CollateralDisposition':
			$subjectNotif = ['message' => 'Penugasan Staff Collateral',
				'url' => '#',
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateraAODisposition':
			$subjectNotif = ['message' => 'Penugasan AO Collateral',
				'url' => $internalurl.'staff-collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralOTS':
			$subjectNotif = ['message' => 'Penilaian agunan debitur an. ['.$debitur.'] sedang dilakukan oleh ['.$collateralAppraisal.']',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralStafRejectOTS':
			$subjectNotif = ['message' => 'Collateral appraisal an ['.$collateralAppraisal.'] menolak permintaan penilaian, harap lakukan penugasan ke staff collateral lainnya',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralStafPenilaianAnggunan':
			$subjectNotif = ['message' => 'Form Penilaian Agunan',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralStafChecklist':
			$subjectNotif = ['message' => 'Collateral Checklist',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralManagerRejected':
			$subjectNotif = [
			  'message' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI',
			  'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI',
				'url' => $externalurl.'dev/proyek?slug='.$slug. '&type=collateral_manager_approving',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralManagerApprove':
			$subjectNotif = [
			  'message' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan',
			  'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan',
				'url' => $externalurl.'dev/proyek?slug='.$slug. '&type=collateral_manager_approving',
				'url_mobile' => '#',
			];
			break;
		default:
			$subjectNotif = ['message' => 'Type undefined',
				'url' => '',
				'message_external' => '',
				'url_mobile' => '#',
			];
			break;
		}

		return $subjectNotif;
	}

	/**
	 * Get the notification all.
	 *
	 * @return void
	 */
	public function getNotifications($branch_name) {
		return $this->where('role_name', $branch_name)->orderBy('created_at', 'DESC');

	}

	public function getUnreads($branch_id, $role, $pn, $user_id) {
		$pn = substr( $pn, -8 );
		$query = $this->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
			// ->where('eforms.branch_id', @$branch_id)
			//->Where('eforms.ao_id', @$pn)
			->orderBy('notifications.created_at', 'DESC');

		if (@$role == 'pinca') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\PengajuanKprNotification')) {
				$query->unreads()->Orwhere('eforms.branch_id', @$branch_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\LKNEFormCustomer')) {
				$query->leftJoin('visit_reports', 'eforms.id', '=', 'visit_reports.eform_id')
					->whereNotNull('visit_reports.created_at')
					->unreads()->Orwhere('eforms.branch_id', @$branch_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\LKNEFormRecontest')) {
				$query->whereNotNull('visit_reports.created_at')
					->unreads()->Orwhere('eforms.branch_id', @$branch_id);
			}
		}

		if (@$role == 'ao') {

			if ($query->Orwhere('notifications.type', 'App\Notifications\EFormPenugasanDisposisi')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				/*is rejected*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationApproveFormNasabah')) {
				/*verifiy app*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationRejectFormNasabah')) {
				/*verifiy app*/
				$query->unreads();
			}
			if ($query->Orwhere('notifications.type', 'App\Notifications\EditDeveloper')) {
				$query->unreads();
            }

            if ($query->Orwhere('notifications.type', 'App\Notifications\CollateraAODisposition')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ScorePefindoPreScreening')) {
				$query->unreads();
			}
			$query->Where('eforms.ao_id', @$pn);
		}

		if (@$role == 'customer') {

			if ($query->Orwhere('notifications.type', 'App\Notifications\PengajuanKprNotification')) {
				// $query->where('notifications.notifiable_id', @$user_id);
				$query->whereNull('eforms.ao_id')->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RecontestEFormNotification')) {
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\NewSchedulerCustomer')) {
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\UpdateSchedulerCustomer')) {
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCLAS')) {
				/*is is_approved*/
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCLAS')) {
				$query->unreads()->where('notifications.notifiable_id', @$user_id);
			}

			/*if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationDataNasabah')) {
				$query->unreads();
			}*/
		}

		if (@$role == 'staff') {
			$query->whereNull('notifications.created_at');
		}

		if (@$role == 'collateral-appraisal') {
                    if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralDisposition')) {
                            $query->unreads();
                    }
                }

		if (@$role == 'collateral') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\PropertyNotification')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralOTS')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralStafPenilaianAnggunan')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralStafRejectOTS')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralStafChecklist')) {
				$query->unreads();
			}

		}

		if (@$role == 'developer') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralManagerRejected')) {
				$query->where('notifications.notifiable_id', @$user_id);
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralManagerApprove')) {
				$query->where('notifications.notifiable_id', @$user_id);
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveDeveloperProfile')) {
				$query->where('notifications.notifiable_id', @$user_id);
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectDeveloperProfile')) {
				$query->where('notifications.notifiable_id', @$user_id);
				$query->unreads();
			}

		}

		$query->select('notifications.id', 'notifications.type', 'notifications.notifiable_id', 'notifications.notifiable_type', 'notifications.data', 'notifications.read_at', 'notifications.created_at', 'notifications.updated_at', 'notifications.branch_id', 'notifications.role_name', 'notifications.slug','notifications.type_module','notifications.is_read', 'eforms.is_approved', 'eforms.ao_id', 'eforms.ref_number');

		return $query;

	}

	public function getUnreadsMobile($branch_id, $role, $pn, $user_id, $limit, $count = null) {
		$query = new UserNotification;
		if (empty($pn)) {
			if ($role == 'customer') {
				$data = $query->where('notifiable_id', $user_id)
							  ->whereIn('type', [
								'App\Notifications\PengajuanKprNotification',
								'App\Notifications\RecontestEFormNotification',
								'App\Notifications\NewSchedulerCustomer',
								'App\Notifications\UpdateSchedulerCustomer',
								'App\Notifications\ApproveEFormCustomer',
								'App\Notifications\ApproveEFormCLAS',
								'App\Notifications\RejectEFormCustomer',
								'App\Notifications\RejectEFormCLAS'
   					   ]);
			}else if($role == 'developer') {
				$data = $query->where('notifiable_id', $user_id)
							  ->whereIn('type', [
								'App\Notifications\CollateralManagerRejected',
								'App\Notifications\CollateralManagerApprove',
								'App\Notifications\ApproveDeveloperProfile',
								'App\Notifications\RejectDeveloperProfile'
					   ]);
			}
		}else{
			// $query = $this->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
			// 	->where('eforms.branch_id', $branch_id)
			// 	->Where('eforms.ao_id', $pn)
			// 	->orderBy('notifications.created_at', 'DESC');

			if ($role == 'pinca') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
							'App\Notifications\PengajuanKprNotification',
							'App\Notifications\LKNEFormCustomer',
							'App\Notifications\LKNEFormRecontest',
						]);
				// var_dump(json_encode($data->get()));
				// die();
			}

			if ($role == 'ao') {
				$data = $query
				->select('notifications.id', 'notifications.type', 'notifications.notifiable_id',
									   'notifications.notifiable_type', 'notifications.data', 'notifications.read_at',
									   'notifications.created_at', 'notifications.updated_at',
									   'notifications.branch_id', 'notifications.role_name', 'notifications.slug',
									   'notifications.type_module','notifications.is_read', 'eforms.is_approved',
									   'eforms.ao_id', 'eforms.ref_number')
							  ->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
				  	          ->where('eforms.branch_id', 'like', '%'.$branch_id)
							  ->where('eforms.ao_id', $pn)
							  ->whereIn('notifications.type', [
							  		'App\Notifications\EFormPenugasanDisposisi',
									'App\Notifications\ApproveEFormCustomer',
									'App\Notifications\VerificationApproveFormNasabah',
									'App\Notifications\VerificationRejectFormNasabah',
									'App\Notifications\EditDeveloper',
									'App\Notifications\CollateraAODisposition',
									'App\Notifications\ScorePefindoPreScreening',
					  	]);
			}

			if ($role == 'staff') {
				$data = $query->where('branch_id', $branch_id);
			}

			if ($role == 'collateral-appraisal') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
							  		'App\Notifications\CollateralDisposition',
					    ]);
            }

			if (@$role == 'collateral') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
									'App\Notifications\PropertyNotification',
									'App\Notifications\CollateralOTS',
									'App\Notifications\CollateralStafPenilaianAnggunan',
									'App\Notifications\CollateralStafRejectOTS',
									'App\Notifications\CollateralStafChecklist',
				]);
			}
		}

		if (empty($count)) {
			return $data->paginate($limit);
		}else {
			if(empty($data)){
				$counter = count($data);
			}else {
				$counter = $data->get()->count();
			}

			return $counter;
		}
	}
}