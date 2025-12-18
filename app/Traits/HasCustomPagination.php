<?php

namespace App\Traits;

trait HasCustomPagination
{
    /**
     * Set the pagination theme.
     *
     * @return string
     */
    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }
}
