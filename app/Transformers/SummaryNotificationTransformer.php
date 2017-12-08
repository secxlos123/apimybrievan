<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SummaryNotificationTransformer extends TransformerAbstract
{
    public function transform($notification)
    {
    	return $notification;
    }
}