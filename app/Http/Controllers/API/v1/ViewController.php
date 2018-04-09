<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VIEW;
use DB;

class ViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        \Log::info($request->all());
		$view = '<div class="panel-body">';
		$view1 = '';
		$view2 = '';
		$view3 = '';
		$view4 = '';
		$divid='';
		$divname='';
        $newForm = VIEW::filter( $request )->get();
		$table_view = $newForm->toArray();
		$c = count($table_view);
		for($i=0;$i<$c;$i++){
			if(!empty($table_view[$i]['div_name'])){
				$divname = 'name="'.$table_view[$i]['div_name'].'"';
			}

			if(!empty($table_view[$i]['div_id'])){
				$divid = 'id="'.$table_view[$i]['div_id'].'"';
			}
			if($table_view[$i]['kolom']=='1'){
				if($table_view[$i]['first']=='1'){
					$view1 .= '<div class="row"><div class="col-md-6"><div class="form-horizontal" '.$divid.' '.$divname.'>';
				}
					$view1 .= $this->generate_table($table_view,$i);
				if($table_view[$i]['last']=='1'){
					$view1 .= '</div></div></div>';
				}
			
			}elseif($table_view[$i]['kolom']=='button_modify'){
				if($table_view[$i]['first']=='1'){
					$view1 .= '<div class="row"><div class="col-md-12"><div class="form-horizontal" '.$divid.' '.$divname.'>';
				}
					$view1 .= $this->generate_table($table_view,$i);
				if($table_view[$i]['last']=='1'){
					$view1 .= '</div></div></div>';
				}
			
			}elseif($table_view[$i]['kolom']=='2'){
				if($table_view[$i]['first']=='1'){
					$view1 .= '<div class="row"><div class="'.$table_view[$i]['div_class'].'" '.$divid.' '.$divname.'><div class="form-horizontal">';
					$view1 .= $this->generate_table($table_view,$i);
					$view1 .= '</div></div>';
				}
				if($table_view[$i]['last']=='1'){
					$view1 .= '<div class="'.$table_view[$i]['div_class'].'" '.$divid.' '.$divname.'><div class="form-horizontal">';
					$view1 .= $this->generate_table($table_view,$i);
					$view1 .= '</div></div></div>';
				}
			}elseif($table_view[$i]['kolom']=='3'){
				if($table_view[$i]['first']=='1'){
					$view1 .= '<div class="row"><div class="'.$table_view[$i]['div_class'].'" '.$divid.' '.$divname.'><div class="form-horizontal">';
				}
				if($table_view[$i]['last']=='1'){
					$view1 .= '<div class="'.$table_view[$i]['div_class'].'" '.$divid.' '.$divname.'><div class="form-horizontal">';
				}
				if($table_view[$i]['last']=='0'&&$table_view[$i]['first']=='0'){
					$view1 .= '<div class="'.$table_view[$i]['div_class'].'" '.$divid.' '.$divname.'><div class="form-horizontal">';
				}
				$view1 .= $this->generate_table($table_view,$i);
				$view1 .= '</div></div>';
			}elseif($table_view[$i]['kolom']=='0'){
				$view1 .= '<div class="row"><div class="col-md-6" '.$divid.' '.$divname.'><div class="form-horizontal"></div></div></div>';
			}elseif($table_view[$i]['kolom']=='single'){
				if($table_view[$i]['first']=='1'){
					$view1 .= '<div class="row"><div class="col-md-12" '.$divid.' '.$divname.'><div class="form-horizontal">';
				}
				$view1 .= $this->generate_table($table_view,$i);
				if($table_view[$i]['last']=='1'){
					$view1 .= '</div></div></div>';
				}
			}
		}
		$view .= $view1.'</div>';
		return response()->success( [
            'message' => 'Sukses',
            'contents' => $view
        ], 200 );
    }

	public function generate_table($table_view,$i){
		$view1 = '';
				if($table_view[$i]['type']=='select'){
					$view1 .= $this->select($table_view[$i]);
				}elseif($table_view[$i]['type']=='text'){
					$view1 .= $this->text($table_view[$i]);
				}elseif($table_view[$i]['type']=='table'){
					$view1 .= $this->table($table_view[$i]);
				}elseif($table_view[$i]['type']=='button'){
					$view1 .= $this->button($table_view[$i]);
				}elseif($table_view[$i]['type']=='button_modify'){
					$view1 .= $this->button_modify($table_view[$i]);
				}elseif($table_view[$i]['type']=='label'){
					$view1 .= $this->labeling($table_view[$i]);
				}elseif($table_view[$i]['type']=='radio'){
					$view1 .= $this->radio($table_view[$i]);
				}elseif($table_view[$i]['type']=='file'){
					$view1 .= $this->files($table_view[$i]);
				}elseif($table_view[$i]['type']=='hidden'){
					$view1 .= $this->hidden($table_view[$i]);
				}elseif($table_view[$i]['type']=='number'){
					$view1 .= $this->number($table_view[$i]);
				}elseif($table_view[$i]['type']=='password'){
					$view1 .= $this->password($table_view[$i]);
				}
				return $view1;
	}
    public function label($class,$value)
    {
			$label = '<label class="'.$class.'">'.$value.'</label>';
			return $label;
    }

    public function labeling($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			$form .= '</div>';

		return $form;
    }
    public function text($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .=	'<input type="text" class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"
						value="'.$view['value'].'" '.$view['etc'].'>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	public function password($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .=	'<input type="password" class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"
						value="'.$view['value'].'" '.$view['etc'].'>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	 public function files($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .=	'<input type="file" class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"
						value="'.$view['value'].'" '.$view['etc'].'>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }

	 public function hidden($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .=	'<input type="hidden" class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"
						value="'.$view['value'].'" '.$view['etc'].'>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	 public function number($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .=	'<input type="number" class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"
						value="'.$view['value'].'" '.$view['etc'].'>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
    public function table($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$isitable = explode(";",$view['value']);
			$table = '';
			foreach ($isitable as $value) {
				$table .= '<th>'.$value.'</th>';
			}
			$form .=	'<table id="'.$view['id_table'].'" name="'.$view['name'].'" class="'.$view['class'].'"><thead class="bg-primary"><tr>'
						.$table.'</tr></thead><tbody></tbody></table>';

			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	    public function select($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .= 	'<select class="'.$view['class'].'" name="'.$view['name'].'" id="'.$view['id_table'].'"'.$view['etc'].'>
						'.$view['value'].'</select>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	   public function radio($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$radio = explode(";",$view['value']);
			$x = count($radio);
			for($i=0;$i<$x;$i++){
				$radios = explode(",",$radio[$i]);
				$form .= '<div class="col-md-3"><input type="radio" name="'.$view['name'].'" id="'.$view['id_table'].'" value="'.$radios['0'].'"'.$view['etc'].'>'.$radios['1'].'</div>';
			}
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	    public function button($view)
    {
			$form = '<div class="form-group '.$view['name'].'">';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .= '<button type="button" class="'.$view['class'].'" id="'.$view['id_table'].'" name="'.$view['name'].'" '.
						$view['etc'].$view['value'].' </button>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }
	   public function button_modify($view)
    {
			$form = '<div>';
			if(!empty($view['label'])){
			$form .= $this->label($view['label_class'],$view['label_value']);
			}
			if(!empty($view['div'])){
			$form .= '<div class="'.$view['div_class'].'">';
			}
			$form .= '<button type="button" class="'.$view['class'].'" id="'.$view['id_table'].'" name="'.$view['name'].'" '.
						$view['etc'].$view['value'].' </button>';
			if(!empty($view['div'])){
			$form .= '</div>';
			}
			$form .= '</div>';
		return $form;
    }



}
