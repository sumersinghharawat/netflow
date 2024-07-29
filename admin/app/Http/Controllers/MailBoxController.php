<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendMail;
use App\Models\MailBox;
use App\Models\Contacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RequestsMailBox;
use Config;
use Notification;
use App\Notifications\AllUserMailNotification;

class MailBoxController extends Controller
{
    public function viewmailbox()
    {
        $inboxes        = collect([]);
        $contacts       = collect([]);
        $results        = collect([]);
        $mails          = User::GetAdmin()->load(['inbox' => fn($qry) => $qry->with('fromUser')->where('inbox_delete_status', 0)], 'contacts.sent_user')
            ->load(['contacts' => fn($qry) => $qry ->where('status', 1)]);
        foreach ($mails->inbox as $key => $inbox) {
            $inboxes[] = [
                        'id' => $inbox->id,
                        'subject' => $inbox->subject,
                        'username' => $inbox->fromUser->username,
                        'message' => $inbox->message,
                        'date' => $inbox->created_at->diffForHumans(),
                        'read_status' => $inbox->read_status,
                        'type' => 'admin_message',
                        'replystatus' => 1,
                        'created_at' => $inbox->created_at,
                    ];
        }
        foreach ($mails->contacts as $key => $contact) {
            $contacts[] = [
                        'id' => $contact->id,
                        'subject' => $contact->name,
                        'username' => $contact->sent_user->username,
                        'message' => $contact->contact_info,
                        'date' => $contact->created_at->diffForHumans(),
                        'read_status' => $contact->read_msg,
                        'type' => 'replica_message',
                        'replystatus' => 1,
                        'created_at' => $contact->created_at,
                    ];
        }
        $result = $contacts->concat($inboxes);
        $results = $result->sortByDesc('created_at');
        // dd($results);
        return view('mailBox.mail-box', compact('results'));
    }

    public function viewAutoResponder()
    {
        $inboxes        = collect([]);
        $contacts       = collect([]);
        $results        = collect([]);
        $mails          = User::GetAdmin()->load(['inbox' => fn($qry) => $qry->with('fromUser')->where('thread', null)->where('inbox_delete_status', 0)], 'contacts.sent_user')
                                ->loadCount(['inbox' => fn($qry) => $qry->where('read_status', 0)])
                                ->loadCount(['contacts' => fn($qry) => $qry->where('read_msg', 'no')]);
        $toalUnRead       = $mails->inbox_count + $mails->contacts_count;
        foreach ($mails->inbox as $key => $inbox) {
            $inboxes[] = [
                        'id' => $inbox->id,
                        'subject' => $inbox->subject,
                        'username' => $inbox->fromUser->username,
                        'message' => $inbox->message,
                        'date' => $inbox->created_at->diffForHumans(),
                        'read_status' => $inbox->read_status,
                        'type' => 'admin_message',
                        'replystatus' => 1,
                    ];
        }
        foreach ($mails->contacts as $key => $contact) {
            $contacts[] = [
                        'id' => $contact->id,
                        'subject' => $contact->name ." contacted You",
                        'username' => $contact->sent_user->username,
                        'message' => $contact->message,
                        'date' => $contact->created_at->diffForHumans(),
                        'read_status' => $contact->read_msg,
                        'type' => 'replica_message',
                        'replystatus' => 1,
                    ];
        }
        $results = $contacts->concat($inboxes);
        return view('mailBox.auto-responder', compact('results', 'toalUnRead'));
    }

    public function readSingleMail(Request $request, $id, $type, $page = null)
    {
        $replyStatus = false;

        if ($type == 'replica_message') {
            $mail = Contacts::with('sent_user')->findOrFail($id);
            $mail['type'] = $type;
            $result = Contacts::find($id)->update(['read_msg' => 1]);
        } else {
            $replyStatus = true;
            $mail = MailBox::with('fromUser', 'toUser', 'replys','reply')->orderBy('created_at', 'desc')->findOrFail($id);
            $mail['type'] = $type;
            $result = MailBox::find($id)->update(['read_status' => '1']);
            // dd($mail);
        }
        // dd($mail);
        if($page == "sent-mail") $replyStatus = false;
        return view('mailBox.single-mail', compact('mail','replyStatus', 'page'));
    }

    public function compose()
    {
        return view('mailBox.compose-mail');
    }

    public function storeCompose(RequestsMailBox $request)
    {
        $validatedData  = $request->validated();
        // dd($request->all());
        try {
            if ($request->send_status == 'all') {
                $send_status = 'yes';
            } else {
                $send_status = 'no';
            }
            if (Auth::user()->id != $request->user_id) {
                MailBox::create([
                    'from_user_id' => Auth::user()->id,
                    'to_user_id' => $request->user_id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'date' => Carbon::now(),
                    'inbox_delete_status' => 0,
                    'sent_delete_status' => 0,
                    'thread'    => $request->reply,
                    'to_all' => ($send_status == 'yes' ? 1 : 0),

                ]);
                if ($request->has('reply')) {
                    if($send_status == 'yes'){
                        $this->sendAllUserMailNotification();
                    }
                    return redirect(route('mailBox'))->with('success', __('mail.Message_sent_successfully'));
                }
            } else {
                return redirect(route('mailBox'))->with('error', __('mail.you_cant_sent_msg_to_yourself'));
            }

            return back()->with('success', __('mail.Message_sent_successfully'));
        } catch (\Throwable $th) {
            return back()
                ->with('error', $th->getMessage());
        }
    }

    public function sent()
    {
        $messages = MailBox::where('from_user_id', Auth::user()->id)->where('sent_delete_status', false)->orderBy('created_at', 'DESC')->with('toUser')->paginate(10);
        $result['type'] = 'admin_messages';

        return view('mailBox.mail-sent-items', compact('messages'));
    }

    public function delete(Request $request, $id, $type)
    {

        try {
            $mail = MailBox::find($id);

            $contacts = Contacts::find($id);
            if ($request->mail_type == 'sent_mail') {
                if ($type == 'admin_message') {
                    $mail->sent_delete_status = 1;
                    $mail->save();

                    return back()->with(
                        'success',
                        __('mail.message_status_change')
                    );
                } else {
                    $contacts->status = 0;
                    $contacts->save();

                    return back()->with(
                        'success',
                        __('mail.message_status_change')
                    );
                }
            } elseif ($request->mail_type == 'inbox_mail') {
                if ($type == 'admin_message') {
                    $mail->inbox_delete_status = 1;
                    $mail->save();

                    return back()->with(
                        'success',
                        __('mail.message_status_change')
                    );
                } else {
                    $contacts->status = 0;
                    $contacts->save();

                    return back()->with(
                        'success',
                        __('mail.message_status_change')
                    );
                }
            }
        } catch (\Throwable $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function replyMail($id)
    {
        $mail = MailBox::with('fromUser')->find($id);
        $reply = 1;
        return view('mailBox.replyMail', compact('mail', 'reply'));
    }
    public function sendTest(Request $request)
    {
        $mailData = [
            "from"    => ['email' => 'test@mail.com', 'name' => ''],
            "to"      => $request->tomail,
            "subject" => 'test mail',
            "content" => 'test mail protocol',
            "footer"  => '',
            "logo"    => '',
            "prefix"  => session('prefix')
        ];

        $mail = DB::table('mail_settings')->first();
        if ($mail) //checking if table is not empty
        {
            $config = array(
                'driver'     => 'smtp',
                'host'       => $mail->smtp_host,
                'port'       => $mail->smtp_port,
                'from'       => array('address' => $mail->from_email, 'name' => $mail->from_name),
                'encryption' => $mail->smtp_protocol,
                'username'   => $mail->smtp_username,
                'password'   => $mail->smtp_password,
                'timeout'    => $mail->smtp_timeout,
                'auth_mode'  => null,
                'verify_peer' => false,
            );
            Config::set('mail', $config);
        }
        try {
            Mail::to($mailData['to'])->send(new SendMail($mailData));
            return redirect()->back()->withSuccess(trans('common.mail_successfully_delivered'));
        } catch (\Throwable $th) {
            $messageStatus = $th->getMessage();
            return redirect()->back()->withErrors($messageStatus);
        }
    }

    public function sendAllUserMailNotification() {  //todo

        $userSchema = User::find(1);           //todo
        $allMailData = [
            // 'username' => $userSchema->username,
            // 'userId' => $userId,
            // 'requestId' => $requestId,
            // 'url' => url('/epin'),
        ];

        return Notification::send($userSchema, new AllUserMailNotification($allMailData));
        dd('Task completed!');
    }
}
