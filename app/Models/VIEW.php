<?php

namespace App\Models;;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class VIEW extends Model
{
	  protected $table = 'view_table';
	  public function scopeFilter($query, $request) {
        $view = $query->where( function( $view ) use( $request, &$user ) {
          $form = $request['form'];
          $view->Where('view','=', $form);
        });
		$view->join('view_div', 'view_div.index', '=', 'view_table.div');
		$view->join('view_label', 'view_label.index', '=', 'view_table.div');
		$view->orderBy('index', 'ASC');
			
				$view = $view->select([
          'view_table.index','view_table.view','view_table.type','view_table.class','view_table.name','view_table.id as id_table',
		  'view_table.etc','view_table.value','view_table.div','view_table.label','view_table.kolom','view_table.first','view_table.last',
		  'view_div.class as div_class','view_div.id as div_id','view_div.name as div_name',
		  'view_label.value as label_value','view_label.class as label_class'
        ]);

        \Log::info($view->toSql());
        return $view;

    }
}