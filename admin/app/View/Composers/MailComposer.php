<?php

namespace App\View\Composers;

use App\Models\User;
use Illuminate\View\View;

class MailComposer
{
    /**
     * @param  View  $view
     */
    public function compose(View $view)
    {
        $mails          = User::GetAdmin()
                                ->loadCount(['inbox' => fn($qry) => $qry->where('read_status', 0)->where('inbox_delete_status', 0)])
                                ->loadCount(['contacts' => fn($qry) => $qry->where('read_msg', 0)]);
        $toalUnRead       = $mails->inbox_count + $mails->contacts_count;
        $view->with('toalUnRead', $toalUnRead);

    }
}
