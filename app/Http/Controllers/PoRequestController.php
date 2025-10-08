<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Mail\SendPoRMail;
use Exception;

class PoRequestController extends Controller
{
    public function processModule($data) 
    {
        if (strpos($data["req_hd_descs"], "\n") !== false) {
            $req_hd_descs = str_replace("\n", ' (', $data["req_hd_descs"]) . ')';
        } else {
            $req_hd_descs = $data["req_hd_descs"];
        }

        if (strpos($data["source"], "\n") !== false) {
            $source = str_replace("\n", ' (', $data["source"]) . ')';
        } else {
            $source = $data["source"];
        }

        $list_of_urls = explode('; ', $data["url_file"]);
        $list_of_files = explode('; ', $data["file_name"]);
        $list_of_doc = explode('; ', $data["document_link"]);
        $list_of_approve = explode('; ', $data["approve_exist"]);

        $url_data = [];
        $file_data = [];
        $doc_data = [];
        $approve_data = [];

        foreach ($list_of_urls as $url) {
            $url_data[] = $url;
        }

        foreach ($list_of_files as $file) {
            $file_data[] = $file;
        }

        foreach ($list_of_doc as $doc) {
            $doc_data[] = $doc;
        }

        foreach ($list_of_approve as $approve) {
            $approve_data[] = $approve;
        }

        $formattedNumber = number_format($data["total_price"], 2, '.', ',');
        
        $dataArray = array(
            'sender'        => $data["sender"],
            'entity_name'   => $data["entity_name"],
            'descs'         => $data["descs"],
            'email_address' => $data["email_addr"],
            'sender_addr'   => $data["sender_addr"],
            'user_name'     => $data["user_name"],
            'clarify_user'  => $data["clarify_user"],
            'clarify_email' => $data["clarify_email"],
            'req_hd_descs'  => $data["req_hd_descs"],
            'source'	    => $data["source"],
            'req_hd_no'     => $data["req_hd_no"],
            'curr_cd'       => $data["curr_cd"],
            'total_price'   => $formattedNumber,
            'url_file'      => $url_data,
            'file_name'     => $file_data,
            'doc_link'      => $doc_data,
            'approve_list'  => $approve_data,
            'module'        => "PoRequest",
            'subject'       => "Need Approval for Purchase Requisition No.  ".$data['req_hd_no'],
            'approve_seq'   => $data["approve_seq"],
        );

        $data2Encrypt = array(
            'entity_cd'     => $data["entity_cd"],
            'project_no'    => $data["project_no"],
            'email_address' => $data["email_addr"],
            'level_no'      => $data["level_no"],
	        'approve_seq'   => $data["approve_seq"],
            'doc_no'        => $data["doc_no"],
            'usergroup'     => $data["usergroup"],
            'user_id'       => $data["user_id"],
            'supervisor'    => $data["supervisor"],
            'type'          => 'Q',
            'type_module'   => 'PO',
            'text'          => 'Purchase Requisition'
        );

        // Melakukan enkripsi pada $dataArray
        $encryptedData = Crypt::encrypt($data2Encrypt);
    
        try {
            $emailAddress = strtolower($data["email_addr"]);
            $approveSeq = $data["approve_seq"];
            $entityCd = $data["entity_cd"];
            $docNo = $data["doc_no"];
            $levelNo = $data["level_no"];
            $app_url = 'porequest';
            $type = 'Q';
            $module = 'PO';
        
            if (!empty($emailAddress)) {
                // Check if the email has been sent before for this document
                $cacheFile = 'email_sent_' . $approveSeq . '_' . $entityCd . '_' . $docNo . '_' . $levelNo . '.txt';
                $cacheFilePath = storage_path('app/mail_cache/send_porequeset/' . date('Ymd') . '/' . $cacheFile);
                $cacheDirectory = dirname($cacheFilePath);
        
                // Ensure the directory exists
                if (!file_exists($cacheDirectory)) {
                    mkdir($cacheDirectory, 0755, true);
                }
        
                // Acquire an exclusive lock
                $lockFile = $cacheFilePath . '.lock';
                $lockHandle = fopen($lockFile, 'w');
                if (!flock($lockHandle, LOCK_EX)) {
                    // Failed to acquire lock, handle appropriately
                    fclose($lockHandle);
                    throw new Exception('Failed to acquire lock');
                }
        
                if (!file_exists($cacheFilePath)) {
                    // Send email only if it has not been sent before
                    Mail::to($emailAddress)->send(new SendPoRMail($encryptedData, $dataArray));
        
                    // Mark email as sent
                    file_put_contents($cacheFilePath, 'sent');
        
                    // Log the success
                    Log::channel('sendmailapproval')->info('Email Purchase Requisition doc_no '.$docNo.' Entity ' . $entityCd.' berhasil dikirim ke: ' . $emailAddress);

                    // return 'Email berhasil dikirim ke: ' . $emailAddress;
                    return 'success ' . $encryptedData;
                } else {
                    // Email was already sent
                    Log::channel('sendmailapproval')->info('Email Purchase Requisition doc_no '.$docNo.' Entity ' . $entityCd.' already sent to: ' . $emailAddress);
                    return 'Email has already been sent to: ' . $emailAddress;
                }
            } else {
                // No email address provided
                Log::channel('sendmail')->warning("No email address provided for document " . $docNo);
                return "No email address provided";
            }
        } catch (\Exception $e) {
            // Error occurred
            Log::channel('sendmail')->error('Gagal mengirim email: ' . $e->getMessage());
            return "Gagal mengirim email: " . $e->getMessage();
        }
        
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

        $query = DB::connection('BFIE')
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
    
            $query2 = DB::connection('BFIE')
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
                if ($dataArray["level_no"] > 1 && $status === "A") {
                    return view('email/por/passchecknoremark', $data);
                } else {
                    return view('email/por/passcheckwithremark', $data);
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
        $pdo = DB::connection('BFIE')->getPdo();
        $sth = $pdo->prepare("EXEC mgr.x_send_mail_approval_po_request ?, ?, ?, ?, ?, ?, ?, ?, ?");
        $success = $sth->execute([
            $data["entity_cd"],
            $data["project_no"],
            $data["doc_no"],
            $status,
            $data["level_no"],
            $data["usergroup"],
            $data["user_id"],
            $data["supervisor"],
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
