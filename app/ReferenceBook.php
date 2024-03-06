<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceBook extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function pages()
    {
        return $this->hasMany(ReferenceBookPage::class)->orderBy('page_no');
    }
    public function reference_book_page()
    {
        return $this->hasOne(ReferenceBookPage::class);
    }
}
