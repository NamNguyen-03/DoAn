<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEmails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'customer_name',
        'email',
        'sent',
        'message',
        'created_at'

    ];
    protected $primaryKey = 'email_id';
    protected $table = 'tbl_contact_emails';
}
