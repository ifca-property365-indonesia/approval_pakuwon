<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Services\PoModuleService;
use App\Services\CbModuleService;
use App\Services\CmModuleService;


class GetApprControllers extends Controller
{
    public function Index(Request $request, PoModuleService $poModuleService, CbModuleService $cbModuleService, CmModuleService $cmModuleService)
    {
        try {
            // ✅ Daftar field yang diperbolehkan
            $allowedKeys = ['user_id'];

            // 🚨 Cek kalau ada field di luar allowedKeys
            $requestKeys = array_keys($request->all());
            $extraKeys = array_diff($requestKeys, $allowedKeys);

            if (!empty($extraKeys)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request hanya boleh berisi user_id',
                    'invalid_fields' => array_values($extraKeys)
                ], 400);
            }

            $entity_cd = $request->entity_cd;
            $user_id = $request->user_id;

            // 🚨 Blokir juga kalau kosong
            if (empty($entity_cd) || empty($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id wajib diisi'
                ], 400);
            }

            // 🌟 Query database
            $approvals = DB::connection('pakuwon')
            ->table('mgr.cb_cash_request_appr_azure as a')
            ->join('mgr.cf_approval_type as t', function($join) {
                $join->on('a.type', '=', 't.type')
                     ->on('a.module', '=', 't.module');
            })
            ->select(
                'a.doc_no',
                'a.entity_cd',
                'a.level_no',
                'a.type',
                'a.module',
                'a.ref_no', 
                'a.trx_type',
                't.descs as approval_descs',
                DB::raw("MAX(CASE WHEN a.app_status = 'A' THEN a.app_url END) as link_approval"),
                DB::raw("MAX(CASE WHEN a.app_status = 'R' THEN a.app_url END) as link_revise"),
                DB::raw("MAX(CASE WHEN a.app_status = 'C' THEN a.app_url END) as link_reject")
            )
            ->where('a.status','P')
            ->where('a.user_id',$user_id)
            ->where('a.entity_cd',$entity_cd)
            ->whereRaw('a.level_no = (
                select min(b.level_no)
                from mgr.cb_cash_request_appr_azure b
                where b.doc_no = a.doc_no
                and b.entity_cd = a.entity_cd
                and b.user_id = a.user_id
                and b.status = \'P\'
            )')
            ->groupBy(
                'a.doc_no',
                'a.entity_cd',
                'a.level_no',
                'a.type',
                'a.module',
                'a.ref_no',
                'a.trx_type',
                't.descs'
            )
            ->get();

            $data = $approvals->map(function($item) use ($poModuleService, $cbModuleService, $cmModuleService) {
                $item->additional = collect([]);
                try {
                    if($item->module === 'PO') {
                        $item->additional = $poModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->ref_no);
                    } else if($item->module === 'CB') {
                        $item->additional = $cbModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->trx_type);
                    } else if($item->module === 'CM') {
                        $item->additional = $cmModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->ref_no);
                    }
                } catch (\Exception $e) {
                    \Log::error("Detail sub-query error for doc_no {$item->doc_no}: ".$e->getMessage());
                }
                return $item;
            });

            return response()->json(['success'=>true, 'data'=>$data], 200);

        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>'Terjadi kesalahan server','error'=>$e->getMessage()],500);
        }
    }


    public function Detail(Request $request, PoModuleService $poModuleService, CbModuleService $cbModuleService, CmModuleService $cmModuleService)
    {
        try {
            // validasi request (sama seperti sebelumnya)
            $entity_cd = $request->entity_cd;
            $user_id = $request->user_id;
            $doc_no = $request->doc_no;
            $level_no = $request->level_no;

            $approvals = DB::connection('pakuwon')
            ->table('mgr.cb_cash_request_appr_azure as a')
            ->select(
                'a.doc_no',
                'a.entity_cd',
                'a.level_no',
                'a.type',
                'a.module',
                'a.ref_no', 
                'a.trx_type',
                DB::raw("MAX(CASE WHEN a.app_status = 'A' THEN a.app_url END) as link_approval"),
                DB::raw("MAX(CASE WHEN a.app_status = 'R' THEN a.app_url END) as link_revise"),
                DB::raw("MAX(CASE WHEN a.app_status = 'C' THEN a.app_url END) as link_reject")
            )
            // ->where('a.status','P')
            ->where('a.user_id',$user_id)
            ->where('a.entity_cd',$entity_cd)
            ->where('a.doc_no',$doc_no)
            ->where('a.level_no',$level_no)
            ->whereRaw('a.level_no = (
                select min(b.level_no)
                from mgr.cb_cash_request_appr_azure b
                where b.doc_no = a.doc_no
                and b.entity_cd = a.entity_cd
                and b.email_addr = a.email_addr
                
            )')
            ->groupBy(
                'a.doc_no',
                'a.entity_cd',
                'a.level_no',
                'a.type',
                'a.module',
                'a.ref_no',
                'a.trx_type'
            )
            ->get();

            $data = $approvals->map(function($item) use ($poModuleService, $cbModuleService, $cmModuleService) {
                $item->additional = collect([]);
                try {
                    if($item->module === 'PO') {
                        $item->additional = $poModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->ref_no);
                    } else if($item->module === 'CB') {
                        $item->additional = $cbModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->trx_type);
                    } else if($item->module === 'CM') {
                        $item->additional = $cmModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no);
                    } else  {
                        $item->additional = $cmModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no);
                    }
                } catch (\Exception $e) {
                    \Log::error("Detail sub-query error for doc_no {$item->doc_no}: ".$e->getMessage());
                }
                return $item;
            });

            return response()->json(['success'=>true, 'data'=>$data], 200);

        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>'Terjadi kesalahan server','error'=>$e->getMessage()],500);
        }
    }

    public function GetTotalData(Request $request)
    {
        try {
            // Validasi wajib
            if (!$request->has('user_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id wajib dikirim',
                ], 400);
            }
            $user_id = $request->user_id;

            $result = DB::connection('pakuwon')->select("
                SELECT status, COUNT(*) AS total
                FROM (
                    SELECT status FROM mgr.cb_cash_request_appr_azure WHERE user_id = ?
                    UNION ALL
                    SELECT status FROM mgr.cb_cash_request_appr_his WHERE user_id = ?
                ) AS t
                GROUP BY status
            ", [$user_id, $user_id]);

            // Default nilai
            $output = [
                'total_A' => 0,
                'total_R' => 0,
                'total_C' => 0,
                'total_P' => 0,
            ];

            // Mapping hasil SQL ke output
            foreach ($result as $row) {
                $statusKey = 'total_' . $row->status;
                if (isset($output[$statusKey])) {
                    $output[$statusKey] = $row->total;
                }
            }

            return response()->json(['success' => true, 'total' => $output], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan server', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function GetHistory(Request $request, PoModuleService $poModuleService, CbModuleService $cbModuleService, CmModuleService $cmModuleService)
    {
        try {
            // Validasi wajib
            if (!$request->has('user_id') || !$request->has('status')) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id dan status wajib dikirim',
                ], 400);
            }

            $user_id = $request->user_id;
            $status = $request->status;

            // Tentukan tabel
            $table = ($status === "A")
                ? 'mgr.cb_cash_request_appr'
                : 'mgr.cb_cash_request_appr_his';

            // Ambil data utama
            $approvals = DB::connection('pakuwon')
                ->table($table.' as a')
                ->select(
                    'a.doc_no',
                    'a.entity_cd',
                    'a.level_no',
                    'a.type',
                    'a.status',
                    'a.module',
                    'a.ref_no',
                    'a.trx_type',
                )
                ->where('a.user_id', $user_id)
                ->where('a.status', $status)
                ->distinct()
                ->get();

            // Isi data tambahan
            $data = $approvals->map(function($item) use ($poModuleService, $cbModuleService, $cmModuleService) {
                $item->additional = collect([]);

                try {
                    if ($item->module === 'PO') {
                        $item->additional = $poModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->ref_no);
                    } elseif ($item->module === 'CB') {
                        $item->additional = $cbModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no, $item->trx_type);
                    } else {
                        $item->additional = $cmModuleService->getDetails($item->type, $item->entity_cd, $item->doc_no);
                    }
                } catch (\Exception $e) {
                    \Log::error("Detail sub-query error for doc_no {$item->doc_no}: ".$e->getMessage());
                }

                return $item;
            });

            return response()->json([
                'success' => true,
                'total' => $data->count(), // 🔥 Tambahkan total disini
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
