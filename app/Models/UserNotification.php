<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lang;

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
		$url = env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/') . 'eform?ref_number=' . $ref_number . '&slug=' . $this->slug.'&type='.$typeModuleEform;
		$internalurl = env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/');
		$externalurl =  env('MAIN_APP_URL', 'http://mybri.bri.co.id/');
		if ($user_id) {
			$url = 'eform?slug=' . $this->slug.'&type='.$typeModuleEform;
		} else {
			$url = $url;
		}
		switch ($this->type) {
		/* eform  */
		case 'App\Notifications\PengajuanKprNotification':
			$subjectNotif = ['message' => 'Pengajuan Aplikasi KPR Baru',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\EFormPenugasanDisposisi':
			$subjectNotif = ['message' => 'Disposisi Pengajuan',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\ApproveEFormCustomer':
			if ($status_eform == 'approved') {
				$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Setujui',
					'url' => $url,
					'url_mobile' => '#',
				];
			} else {
				$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
					'url' => $url,
					'url_mobile' => '#',
				];
			}
			break;
		case 'App\Notifications\RejectEFormCustomer':
			$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Tolak',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\LKNEFormCustomer':
			$subjectNotif = ['message' => 'Prakarsa LKN',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationApproveFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationRejectFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menolak Form KPR',
				'url' => $url,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\VerificationDataNasabah':
			$subjectNotif = ['message' => 'Verifikasi Pengajuan KPR',
				'url' => env('MAIN_APP_URL', 'http://mybri.bri.co.id/') . 'verification?ref_number=' . $ref_number . '&slug=' . $this->slug,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\PropertyNotification':
			$subjectNotif = ['message' => 'Proyek Data Baru',
				'url' => '',
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\NewSchedulerCustomer':
			$subjectNotif = ['message' => 'Schedule Data Baru',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\UpdateSchedulerCustomer':
			$subjectNotif = ['message' => 'Update Data Schedule',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\EditDeveloper':
			$subjectNotif = ['message' => 'Perbaharui Data Profile',
				'url' => '/approval-data/developer?related_id=' .$this->data['approval_data_changes_id'].'&slug=' . $this->slug.'&type='.$this->type_module,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\ApproveDeveloperProfile':
			$subjectNotif = ['message' => 'Data Profile Telah Disetujui ',
				'url' => '/dev/profile/personal?related_id=' .$this->data['approval_data_changes_id'].'&slug=' . $this->slug.'&type='.$this->type_module,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\RejectDeveloperProfile':
			$subjectNotif = ['message' => 'Data Profile Telah Disetujui ',
				'url' => '/dev/profile/personal?related_id=' .$this->data['approval_data_changes_id'].'&slug=' . $this->slug.'&type='.$this->type_module,
				'url_mobile' => '#',
			];
			break;
		
		//collateral notif
		case 'App\Notifications\CollateralDisposition':
			$subjectNotif = ['message' => 'Penugasan Staff Collateral',
				'url' => '#',
				'url_mobile' => '#',
			];
			break;	
		case 'App\Notifications\CollateraAODisposition':
			$subjectNotif = ['message' => 'Penugasan AO Collateral',
				'url' => $internalurl.'staff-collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'url_mobile' => '#',
			];
			break;	
		case 'App\Notifications\CollateralOTS':
			$subjectNotif = ['message' => 'OTS menilai anggunan',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'url_mobile' => '#',
			];
			break;
		case 'App\Notifications\CollateralStafRejectOTS':
			$subjectNotif = ['message' => 'menolak menilai agunan',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'url_mobile' => '#',
			];
			break;	
		case 'App\Notifications\CollateralStafPenilaianAnggunan':
			$subjectNotif = ['message' => 'Form Penilaian Agunan',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'url_mobile' => '#',
			];
			break;	
		case 'App\Notifications\CollateralStafChecklist':
			$subjectNotif = ['message' => 'Collateral Checklist',
				'url' => $internalurl.'collateral?slug='.$this->slug. '&type=' . $typeModuleCollateral,
				'url_mobile' => '#',
			];
			break;			
		case 'App\Notifications\CollateralManagerRejected':
			$subjectNotif = ['message' => 'reject collateral',
				'url' => '#',
				'url_mobile' => '#',
			];
			break;	
		case 'App\Notifications\CollateralManagerApprove':
			$subjectNotif = ['message' => 'Approved collateral',
				'url' => '#',
				'url_mobile' => '#',
			];
			break;
		default:
			$subjectNotif = ['message' => 'Type undefined',
				'url' => '',
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

	public function getUnreadsMobile($branch_id, $role, $pn, $user_id, $type) 
	{
		if ($type == "eks") {
			$query = $this->select();
			$query->where('notifications.notifiable_id', @$user_id);

			if ($query->Orwhere('notifications.type', 'App\Notifications\NewSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\UpdateSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				/*is rejected*/
				$query->unreads();
				if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationDataNasabah')) {
					$query->unreads()->where('notifications.notifiable_id', @$user_id);
				}
			}
			return $query->paginate();
		}else{

		}
	}

	protected function getTestAttribute()
	{
		var_dump(json_encode($this));
		die();
	}

	public function getUnreads($branch_id, $role, $pn, $user_id) {
		$query = $this->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
			->where('eforms.branch_id', @$branch_id)
			->Where('eforms.ao_id', @$pn)
			->orderBy('notifications.created_at', 'DESC');

		if (@$role == 'pinca') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\PengajuanKprNotification')) {
				$query->whereNull('eforms.ao_id')->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\LKNEFormCustomer')) {
				$query->leftJoin('visit_reports', 'eforms.id', '=', 'visit_reports.eform_id')
					->whereNotNull('visit_reports.created_at')
					->unreads();
			}

		}

		if (@$role == 'ao') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\EFormPenugasanDisposisi')) {
				$query->whereNotNull('eforms.ao_id')
					->whereNotNull('eforms.ao_name')
					->whereNotNull('eforms.ao_position')
					->unreads();
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
		}

		if (@$role == 'customer') {
			$query->where('notifications.notifiable_id', @$user_id);

			if ($query->Orwhere('notifications.type', 'App\Notifications\NewSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\UpdateSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				/*is rejected*/
				$query->unreads();
				if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationDataNasabah')) {
					$query->unreads()->where('notifications.notifiable_id', @$user_id);
				}
			}
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
				$query->unreads();
			}
			
			if ($query->Orwhere('notifications.type', 'App\Notifications\CollateralManagerApprove')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveDeveloperProfile')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectDeveloperProfile')) {
				$query->unreads();
			}

		}

		$query->select('notifications.id', 'notifications.type', 'notifications.notifiable_id', 'notifications.notifiable_type', 'notifications.data', 'notifications.read_at', 'notifications.created_at', 'notifications.updated_at', 'notifications.branch_id', 'notifications.role_name', 'notifications.slug','notifications.type_module','notifications.is_read', 'eforms.is_approved', 'eforms.ao_id', 'eforms.ref_number');

		return $query;
		
	}
}