<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Mail\SendLandFphMail;
use Exception;
use Carbon\Carbon;

class LandfphController extends Controller
{
    public function index(Request $request)
    {
        $callback = [
            'data'  => null,
            'Error' => false,
            'Pesan' => '',
            'Status'=> 200
        ];

        try {
            // ====== Persiapan data ======
            $newurl2 = explode(";", trim(str_replace(' ','%20',$request->url_link)));
            $link = [];
            foreach ($newurl2 as $show) {
                $link[] = $show;
            }

            $file_name_data = explode(";", trim($request->file_name));
            $link_file_name = [];
            foreach ($file_name_data as $show_file_name) {
                $link_file_name[] = $show_file_name;
            }

            // dd($request->transaction_date);

            $total_amt = number_format($request->total_amt, 2, '.', ',');
            $book_amt  = number_format($request->book_amt, 2, '.', ',');

            // $transaction_date = Carbon::createFromFormat('M  j Y h:iA', $request->transaction_date)->format('d-m-Y');

            $list_of_approve = explode('; ', $request->approve_exist);
            $approve_data = [];
            foreach ($list_of_approve as $approve) {
                $approve_data[] = $approve;
            }

            $dataArray = [
                'user_id'           => $request->user_id,
                'level_no'          => $request->level_no,
                'entity_cd'         => $request->entity_cd,
                'doc_no'            => $request->doc_no,
                'no_alas_hak'       => $request->no_alas_hak,
                'name_alas_hak'       => $request->name_alas_hak,
                'luas_alas_hak'       => $request->luas_alas_hak,
                'entity_name'       => $request->entity_name,
                'name_owner'        => $request->name_owner,
                'url_link'          => $link,
                'file_name'          => $link_file_name,
                'total_amt'         => $total_amt,
                'book_amt'          => $book_amt,
                'email_addr'        => $request->email_addr,
                'transaction_date'  => $request->transaction_date,
                'user_name'         => $request->user_name,
                'sender_name'       => $request->sender_name,
                'sender_addr'       => $request->sender_addr,
                'descs'             => $request->descs,
                "clarify_user"		=> $request->clarify_user,
                "clarify_email"		=> $request->clarify_email,
                'approve_list'      => $approve_data,
                'subject'           => "Need Approval for Land FPH No.  ".$request->doc_no,
                'link'              => 'LandFph',
            ];

            // dd($dataArray);

            $data2Encrypt = [
                'entity_cd'     => $request->entity_cd,
                'project_no'    => $request->project_no,
                'email_address' => $request->email_addr,
                'level_no'      => $request->level_no,
                'approve_seq'   => $request->approve_seq,
                'doc_no'        => $request->doc_no,
                'usergroup'     => $request->usergroup,
                'user_id'       => $request->user_id,
                'supervisor'    => $request->supervisor,
                'type'          => 'F',
                'type_module'   => 'LM',
                'text'          => 'Land FPH',
            ];

            $encryptedData = Crypt::encrypt($data2Encrypt);

            // isi callback data secara konsisten
            $callback['data'] = [
                'payload'   => $dataArray,
                'encrypted' => $encryptedData
            ];

            // ====== Proses kirim email ======
            $emailAddress = strtolower($request->email_addr);
            $approveSeq = $request->approve_seq;
            $entityCd   = $request->entity_cd;
            $docNo      = $request->doc_no;
            $levelNo    = $request->level_no;

            if (!empty($emailAddress)) {
                $cacheFile = 'email_sent_' . $approveSeq . '_' . $entityCd . '_' . $docNo . '_' . $levelNo . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_LandFph/' . date('Ymd') . '/' . $cacheFile);
                $cacheDirectory = dirname($cacheFilePath);

                if (!file_exists($cacheDirectory)) {
                    mkdir($cacheDirectory, 0755, true);
                }

                $lockFile = $cacheFilePath . '.lock';
                $lockHandle = fopen($lockFile, 'w');
                if (!flock($lockHandle, LOCK_EX)) {
                    fclose($lockHandle);
                    throw new Exception('Failed to acquire lock');
                }

                if (!file_exists($cacheFilePath)) {
                    // kirim email
                    Mail::to($emailAddress)->send(new SendLandFphMail($encryptedData, $dataArray));

                    file_put_contents($cacheFilePath, 'sent');
                    Log::channel('sendmailapproval')->info("Email Land FPH doc_no $docNo Entity $entityCd berhasil dikirim ke: $emailAddress");

                    $callback['Pesan'] = "Email berhasil dikirim ke: $emailAddress";
                    $callback['Error'] = false;
                    $callback['Status']= 200;

                } else {
                    Log::channel('sendmailapproval')->info("Email Land FPH doc_no $docNo Entity $entityCd sudah pernah dikirim ke: $emailAddress");

                    $callback['Pesan'] = "Email sudah pernah dikirim ke: $emailAddress";
                    $callback['Error'] = false;
                    $callback['Status']= 201;
                }
            } else {
                Log::channel('sendmail')->warning("No email address provided for document $docNo");

                $callback['Pesan'] = "No email address provided";
                $callback['Error'] = true;
                $callback['Status']= 400;
            }

        } catch (\Exception $e) {
            Log::channel('sendmail')->error("Gagal mengirim email: " . $e->getMessage());

            $callback['Pesan'] = "Gagal mengirim email: " . $e->getMessage();
            $callback['Error'] = true;
            $callback['Status']= 500;
        }

        return response()->json($callback, $callback['Status']);
    }

    public function processData($status='', $encrypt='')
    {
        Artisan::call('config:cache');
        Artisan::call('cache:clear');
        Cache::flush();

        $cacheKey = 'processData_' . $encrypt;

        // Check if the data is already cached
        if (Cache::has($cacheKey)) {
            // If cached data exists, clear it
            Cache::forget($cacheKey);
        }

        $query = 0;
        $query2 = 0;
        
        $data = Crypt::decrypt($encrypt);

        $msg = " ";
        $msg1 = " ";
        $notif = " ";
        $st = " ";
        $image = " ";

        Log::info('Decrypted data: ' . json_encode($data));

        $where = array(
            'doc_no'        => $data["doc_no"],
            'entity_cd'     => $data["entity_cd"],
            'approve_seq'   => $data["approve_seq"],
            'level_no'      => $data["level_no"],
            'type'          => $data["type"],
            'module'        => $data["type_module"],
        );

        $query = DB::connection('pakuwon')
        ->table('mgr.cb_cash_request_appr')
        ->where($where)
        ->whereIn('status', ["A", "R", "C"])
        ->get();

        Log::info('First query result: ' . json_encode($query));

        if (count($query)>0) {
            $msg = 'You Have Already Made a Request to '.$data["text"].' No. '.$data["doc_no"] ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
            return view("email.after", $msg1);
        } else {
            $where2 = array(
                'doc_no'        => $data["doc_no"],
                'status'        => 'P',
                'entity_cd'     => $data["entity_cd"],
                'approve_seq'   => $data["approve_seq"],
                'level_no'      => $data["level_no"],
                'type'          => $data["type"],
                'module'        => $data["type_module"],
            );
    
            $query2 = DB::connection('pakuwon')
            ->table('mgr.cb_cash_request_appr')
            ->where($where2)
            ->get();
    
            Log::info('Second query result: ' . json_encode($query2));

            if (count($query2) == 0) {
                $msg = 'There is no '.$data["text"].' with No. '.$data["doc_no"] ;
                $notif = 'Restricted !';
                $st  = 'OK';
                $image = "double_approve.png";
                $msg1 = array(
                    "Pesan" => $msg,
                    "St" => $st,
                    "notif" => $notif,
                    "image" => $image
                );
                return view("email.after", $msg1);
            } else {
                $name   = " ";
                $bgcolor = " ";
                $valuebt  = " ";
                if ($status == 'A') {
                    $name   = 'Approval';
                    $bgcolor = '#40de1d';
                    $valuebt  = 'Approve';
                } else if ($status == 'R') {
                    $name   = 'Revision';
                    $bgcolor = '#f4bd0e';
                    $valuebt  = 'Revise';
                } else {
                    $name   = 'Cancellation';
                    $bgcolor = '#e85347';
                    $valuebt  = 'Cancel';
                }
                $dataArray = Crypt::decrypt($encrypt);
                $data = array(
                    "status"    => $status,
                    "doc_no"    => $dataArray["doc_no"],
                    "email"     => $dataArray["email_address"],
                    "encrypt"   => $encrypt,
                    "name"      => $name,
                    "bgcolor"   => $bgcolor,
                    "valuebt"   => $valuebt
                );
                if ($status === "A") {
                    return view('email/landfphMail/passchecknoremark', $data);
                } else {
                    return view('email/landfphMail/passcheckwithremark', $data);
                }
            }
        }
    }

    public function getaccess(Request $request)
    {
        $data = Crypt::decrypt($request->encrypt);

        $status = $request->status;

        $reasonget = $request->reason;

        $descstatus = " ";
        $imagestatus = " ";

        $msg = " ";
        $msg1 = " ";
        $notif = " ";
        $st = " ";
        $image = " ";

        if (trim($reasonget) == '') {
            $reason = 'no reason (just Level 1)';
        } else {
            $reason = $reasonget;
        }

        if ($status == "A") {
            $descstatus = "Approved";
            $imagestatus = "approved.png";
        } else if ($status == "R") {
            $descstatus = "Revised";
            $imagestatus = "revise.png";
        } else {
            $descstatus = "Cancelled";
            $imagestatus = "reject.png";
        }
        $pdo = DB::connection('pakuwon')->getPdo();
        $sth = $pdo->prepare("EXEC mgr.xrl_send_mail_approval_land_fph ?, ?, ?, ?, ?");
        $success = $sth->execute([
            $data["entity_cd"],
            $data["doc_no"],
            $status,
            $data["level_no"],
            $reason
        ]);
        if ($success) {
            $msg = "You Have Successfully ".$descstatus." the Purchase Requisition No. ".$data["doc_no"];
            $notif = $descstatus." !";
            $st = 'OK';
            $image = $imagestatus;
        } else {
            $msg = "You Failed to ".$descstatus." the Purchase Requisition No.".$data["doc_no"];
            $notif = 'Fail to '.$descstatus.' !';
            $st = 'FAIL';
            $image = "reject.png";
        }
        $msg1 = array(
            "Pesan" => $msg,
            "St" => $st,
            "notif" => $notif,
            "image" => $image
        );
        return view("email.after", $msg1);
    }
}
