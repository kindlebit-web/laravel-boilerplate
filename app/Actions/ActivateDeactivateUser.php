<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ActivateDeactivateUser extends AbstractAction
{

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'users';
    }

    public function getTitle()
    {
        return ($this->data->is_active) ? 'Active' : 'Deactivated';
    }

    public function getIcon()
    {
        return ($this->data->is_active) ? 'voyager-check-circle' : 'voyager-warning';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => ($this->data->is_active) ? 'btn btn-sm btn-success pull-right' : 'btn btn-sm btn-danger pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('activate_deactivate', ['user_id' => $this->data->id]);
    }
}
