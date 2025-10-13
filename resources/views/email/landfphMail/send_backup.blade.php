<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="application/pdf">
    <meta name="x-apple-disable-message-reformatting">
    <title>IFCA - PWON</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('public/images/pakuwon_icon.ico') }}">
    
    <style>
        body {
            font-family: Arial;
            margin: 0;
            padding: 0 !important;
            mso-line-height-rule: exactly;
            background-color: #ffffff;
            font-family: Arial;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table {
                background-color:#e0e0e0;"
            }

        td {
            padding: 8px;
        }

        @media only screen and (max-width: 620px) {
            table {
                width: 100% !important;
            }

            td {
                display: block;
                width: 100% !important;
                box-sizing: border-box;
            }
            .custom-table {
                background-color:#ffffff;"
            }
            
        }
    </style>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff;font-family: Arial;">
	<div style="width: 100%; background-color: #ffffff; text-align: center;">
        <table width="80%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-left: auto;margin-right: auto;" >
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <img width = "130" src="{{ url('public/images/PWON-logo.png') }}" alt="logo">
                                        <p style="font-size: 16px; color: #026735; padding-top: 0px;">{{ $dataArray['entity_name'] }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#e0e0e0;" class="custom-table">
                        <tbody>
                            <tr>
                                <td style="padding: 30px 30px">
                                    <h5 style="text-align:left;margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Dear {{ $dataArray['user_name'] }}, </h5>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">Tolong berikan persetujuan untuk Form Penawaran Harga dengan detail :</p>
                                    <table cellpadding="0" cellspacing="0" style="text-align:left;width:100%;max-width:800px;margin:0 auto;font-size: 14px;background-color:#e0e0e0; color: #000000;">
                                        <tr>
                                            <td>Nomor Dokumen</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['doc_no'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nama PT</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['entity_name'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>No. ALas Hak</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['no_alas_hak'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nama Alas Hak</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['name_alas_hak'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Luas Alas Hak</td>
                                            <td> : </td>
                                            <td> {{ $dataArray['luas_alas_hak'] }} </td>
                                        </tr>
                                        <tr>
                                            <td>Nama Pemilik</td>
                                            <td>:</td>
                                            <td>{{ $dataArray['name_owner'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal FPH</td>
                                            <td>:</td>
                                            <td>{{ $dataArray['transaction_date'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Harga yang disepakati</td>
                                            <td>:</td>
                                            <td style="text-align: right;">Rp. {{ $dataArray['total_amt'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Uang Tanda Jadi</td>
                                            <td>:</td>
                                            <td style="text-align: right;">Rp. {{ $dataArray['book_amt'] }}</td>
                                        </tr>
                                    </table>
                
                    
                                    <a href="{{ url('api') }}/fph/A/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #1ee0ac; border-radius: 4px; color: #ffffff;">Approve</a>
                                    <a href="{{ url('api') }}/fph/R/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #f4bd0e; border-radius: 4px; color: #ffffff;">Revise</a>
                                    <a href="{{ url('api') }}/fph/C/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #e85347; border-radius: 4px; color: #ffffff;">Reject</a>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        In case you need some clarification, kindly approach : <br>
                                        <a href="mailto:{{ $dataArray['clarify_email'] }}" style="text-decoration: none; color: inherit;">
                                            {{ $dataArray['clarify_user'] }}
                                        </a>
                                    </p>
                    
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        <b>Thank you,</b><br>
                                        <a href="mailto:{{ $dataArray['sender_addr'] }}">
                                            {{ $dataArray['sender_name'] }}
                                        </a>
                                    </p>

                                    @php
                                        $hasApproval = false;
                                        $counter = 0;
                                    @endphp
                    
                                    @foreach($dataArray['approve_list'] as $key => $approve_list)
                                        @if($approve_list !== '' && $approve_list !== 'EMPTY')
                                            @if(!$hasApproval)
                                                @php
                                                    $hasApproval = true;
                                                @endphp
                                                <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 16px;">
                                                    <span>This request approval has been approved by :</span><br>
                                            @endif
                                            {{ ++$counter }}. {{ $approve_list }}<br>
                                        @endif
                                    @endforeach
                    
                                    @if($hasApproval)
                                        </p>
                                    @endif
                    
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        <b>Please do not reply, as this is an automated-generated email.</b><br>
                                    </p>
                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="font-size: 13px;">Copyright © 2023 IFCA Software. All rights reserved.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
               </td>
            </tr>
        </table>
        </div>
</body>
</html>
