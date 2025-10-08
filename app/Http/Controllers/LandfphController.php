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
use Carbon\Carbon;

class LandfphController extends Controller
{
    public function index(Request $request)
    {
        $callback = array(
            'data' => null,
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $newurl2 = explode(";", trim(str_replace(' ','%20',$request->url_link)));

        foreach ($newurl2 as $show)
        {
            $link[] = $show;
        }

        $total_amt = number_format($request->total_amt, 2, '.', ',');
        $book_amt = number_format($request->book_amt, 2, '.', ',');

        $transaction_date = Carbon::createFromFormat('M  j Y h:iA', $request->transaction_date)->format('d-m-Y');
        // Carbon::createFromFormat('Y-m-d H:i:s.u', $request->transaction_date)->format('d-m-Y');

        $dataArray = array(
            'user_id'           => $request->user_id,
            'level_no'          => $request->level_no,
            'entity_cd'         => $request->entity_cd,
            'doc_no'            => $request->doc_no,
            'nop_no'            => $request->nop_no,
            'entity_name'       => $request->entity_name,
            'name_land'         => $request->name_land,
            'name_owner'        => $request->name_owner,
            'url_link'          => $link,
            'total_amt'         => $total_amt,
            'book_amt'          => $book_amt,
            'email_addr'        => $request->email_addr,
            'transaction_date'  => $transaction_date,
            'user_name'         => $request->user_name,
            'sender_name'       => $request->sender_name,
            'descs'             => $request->descs,
            'link'              => 'LandFph',
        );

        $data2Encrypt = array(
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
        );

        // Melakukan enkripsi pada $dataArray
        $encryptedData = Crypt::encrypt($data2Encrypt);

        try {
            $emailAddress = strtolower($request->email_addr);
            $approveSeq = $request->approve_seq;
            $entityCd = $request->entity_cd;
            $docNo = $request->doc_no;
            $levelNo = $request->level_no;
            $app_url = 'LandFph';
            $type = 'F';
            $module = 'LM';
        
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
}
