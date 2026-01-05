<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>IFCA - PWON</title>

  <style type="text/css">
    /* Reset */
    body, table, td, p {
      margin: 0;
      padding: 0;
    }
    img {
      border: 0;
      display: block;
      line-height: 0;
    }
    table {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }
    /* Mobile responsive */
    @media screen and (max-width: 600px) {
    .container {
        width: 100% !important;
    }
    .content {
        padding: 20px !important;
    }
    .button {
        display:block !important;
        width:100% !important;
        margin-bottom:10px !important;
    }
}
  </style>
</head>

<body style="margin:0; padding:0; background-color:#ffffff; font-family: Arial, Helvetica, sans-serif;">

  <!-- Background Table -->
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;">
    <tr>
      <td align="center" style="padding:40px 0;">

        <!-- Outlook Wrapper Fixed 600px --> <!--[if mso]> <table role="presentation" width="1024" cellpadding="0" cellspacing="0" border="0" align="center"> <tr><td> <![endif]-->

        <!-- Main Container -->
        <table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0"
  width="600"
  style="width:600px; max-width:2048px; background-color:#ffffff; border-collapse:collapse;">

          
          <!-- Header -->
          <tr>
            <td align="center" style="padding-bottom:25px;">
              <img src="{{ url('public/images/PWON-logo.png') }}" alt="logo" width="130" style="display:block;">
              <p style="font-size:16px; color:#026735; margin:10px 0 0;">{{ $dataArray['entity_name'] }}</p>
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td class="content" style="background-color:#e0e0e0; padding:30px; color:#000000; font-size:14px; line-height:22px;">
              <h5 style="font-size:20px; font-weight:400; margin:0 0 15px;">Dear {{ $dataArray['user_name'] }},</h5>
              <p style="margin:0 0 15px;">Tolong berikan persetujuan untuk proses Pemecahan SHGB {{ $dataArray['shgb_no_split'] }} dengan detail :</p>

              @php
                  // Helper kecil untuk menghindari htmlspecialchars error
                  function safeVal($val) {
                      return is_array($val) ? implode(', ', $val) : ($val ?? '-');
                  }
              @endphp

              <!-- Detail Table -->
              <table role="presentation"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    width="100%"
                    style="font-size:14px;
                            color:#000000;
                            border-collapse:collapse;">

                <!-- OUTLOOK FRIENDLY COLUMN WIDTH -->
                <colgroup>
                  <col width="140">
                  <col width="180">
                  <col width="180">
                  <col width="140">
                  <col width="110">
                  <col width="110">
                  <col width="90">
                  <col width="90">
                  <col width="110">
                </colgroup>

                <tr>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">No. SHGB</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">No. SHGB BPN</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">No. SPPT</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">No. NIB</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">Tanggal Terbit</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">Tanggal Expired</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">Luas Awal (M²)</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">Luas Akhir (M²)</th>
                  <th style="border:1px solid #000;padding:8px;text-align:center;">Induk / Pecahan</th>
                </tr>

                <!-- INDUK -->
                <tr>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['shgb_no_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['shgb_no_bpn_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['nop_no_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['nib_no_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_date_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_expired_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_area_split']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['remaining_area']) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['split_status_hdstr']) }}
                  </td>
                </tr>

                <!-- PECAHAN -->
                @foreach($dataArray['shgb_no'] as $i => $no)
                <tr>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($no) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['shgb_no_bpn'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['nop_no'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;white-space:nowrap;">
                    {{ safeVal($dataArray['nib_no'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_date'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_expired'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;"></td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['shgb_area'][$i]) }}
                  </td>
                  <td style="border:1px solid #000;padding:8px;text-align:center;">
                    {{ safeVal($dataArray['split_status_dtstr'][$i]) }}
                  </td>
                </tr>
                @endforeach
              </table>

              <!-- Attachments -->
              @if (!empty($dataArray['attachments']) && count($dataArray['attachments']) > 0)
                  <p style="margin:20px 0 10px;">To view detail transaction, please click the link below:</p>
                  @foreach($dataArray['attachments'] as $attachment)
                      <a href="{{ $attachment['url'] }}" target="_blank" style="color:#026735; text-decoration:none;">
                          {{ $attachment['file_name'] }}
                      </a><br>
                  @endforeach
              @endif

              <!-- Buttons -->
              <div style="text-align:center; margin:20px 0; font-size:0;">
                <a href="{{ config('app.url') }}/api/{{ $dataArray['link'] }}/A/{{ $encryptedData }}"
                  style="display:inline-block; width:32%; max-width:32%;
                          font-size:13px; font-weight:600; text-transform:uppercase;
                          text-decoration:none; background-color:#1ee0ac; color:#ffffff;
                          padding:12px 0; border-radius:3px; margin:0 0.5%;">
                  Approve
                </a>

                <a href="{{ config('app.url') }}/api/{{ $dataArray['link'] }}/R/{{ $encryptedData }}"
                  style="display:inline-block; width:32%; max-width:32%;
                          font-size:13px; font-weight:600; text-transform:uppercase;
                          text-decoration:none; background-color:#f4bd0e; color:#ffffff;
                          padding:12px 0; border-radius:3px; margin:0 0.5%;">
                  Revise
                </a>

                <a href="{{ config('app.url') }}/api/{{ $dataArray['link'] }}/C/{{ $encryptedData }}"
                  style="display:inline-block; width:32%; max-width:32%;
                          font-size:13px; font-weight:600; text-transform:uppercase;
                          text-decoration:none; background-color:#e85347; color:#ffffff;
                          padding:12px 0; border-radius:3px; margin:0 0.5%;">
                  Reject
                </a>
              </div>



              <p style="margin:15px 0;">In case you need some clarification, kindly approach:<br>
                <a href="mailto:{{ $dataArray['clarify_email'] }}" style="color:#026735;">{{ $dataArray['clarify_user'] }}</a>
              </p>

              <p style="margin:15px 0;">
                <b>Thank you,</b><br>
                <a href="mailto:{{ $dataArray['sender_addr'] }}" style="color:#026735;">{{ $dataArray['sender_name'] }}</a>
              </p>

              <!-- Approval List -->
              @php $hasApproval = false; $counter = 0; @endphp
              @foreach($dataArray['approve_list'] as $key => $approve_list)
                @if($approve_list && $approve_list != 'EMPTY')
                  @if(!$hasApproval)
                    @php $hasApproval = true; @endphp
                    <p style="margin:15px 0;">This request approval has been approved by:<br>
                  @endif
                  {{ ++$counter }}. {{ $approve_list }}<br>
                @endif
              @endforeach
              @if($hasApproval)</p>@endif

              <p style="margin:15px 0;"><b>Please do not reply, this is an automated email.</b></p>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding:25px 10px 0; font-size:13px; color:#555555;">
              Copyright © 2023 IFCA Software. All rights reserved.
            </td>
          </tr>
        </table>

        <!--[if mso]>
</td></tr></table>
<![endif]-->

      </td>
    </tr>
  </table>
</body>
</html>