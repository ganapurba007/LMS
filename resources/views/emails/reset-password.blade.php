<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Reset Kata Sandi — {{ config('app.name', 'RuangTerra') }}</title>
  <!--[if mso]>
  <style type="text/css">
    body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }
  </style>
  <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
  
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; table-layout: fixed; padding: 40px 15px;">
    <tr>
      <td align="center" valign="top">
        
        <!-- Main Email Container (max 580px) -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
          
          <!-- Top Gradient Accent Bar -->
          <tr>
            <td height="6" style="background: linear-gradient(90deg, #206bc4 0%, #0ca678 100%); line-height: 6px; font-size: 6px;">&nbsp;</td>
          </tr>

          @php
            $appIcon = asset('images/icon.png');
            if (isset($message) && is_object($message) && method_exists($message, 'embed') && file_exists(public_path('images/icon.png'))) {
                $appIcon = $message->embed(public_path('images/icon.png'));
            }
          @endphp

          <!-- Header / Brand Section -->
          <tr>
            <td align="center" style="padding: 36px 30px 24px 30px; background: #ffffff;">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" valign="middle">
                    <div style="display: inline-block; width: 62px; height: 62px; line-height: 62px; text-align: center; border-radius: 16px; background-color: #3368A0; background: linear-gradient(135deg, #3368A0 0%, #206bc4 100%); padding: 8px; box-sizing: border-box; box-shadow: 0 6px 16px rgba(32, 107, 196, 0.32);">
                      <img src="{{ $appIcon }}" alt="{{ config('app.name', 'RuangTerra') }}" width="46" height="46" style="width: 46px; height: 46px; max-width: 46px; object-fit: contain; display: block; margin: 0 auto; border: 0;" />
                    </div>
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding-top: 14px;">
                    <span style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                      {{ config('app.name', 'RuangTerra') }}
                    </span>
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 3px;">
                      Platform Pembelajaran Online
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Divider -->
          <tr>
            <td style="padding: 0 32px;">
              <div style="height: 1px; background-color: #f1f5f9;"></div>
            </td>
          </tr>

          <!-- Content Body -->
          <tr>
            <td style="padding: 32px 36px;">
              
              <!-- Icon & Badge -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td>
                    <div style="display: inline-block; padding: 4px 12px; background-color: rgba(32, 107, 196, 0.08); border-radius: 20px; color: #206bc4; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                      &#128272; Permintaan Keamanan Akun
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Greeting -->
              <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 18px 0 12px 0; line-height: 1.3;">
                Halo, {{ $user->name }}!
              </h1>

              <p style="font-size: 14px; line-height: 1.65; color: #475569; margin: 0 0 24px 0;">
                Kami menerima permintaan untuk mereset kata sandi akun LMS Anda di <strong>{{ config('app.name', 'RuangTerra') }}</strong>. Klik tombol di bawah ini untuk membuat kata sandi baru:
              </p>

              <!-- CTA Button -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 28px 0;">
                <tr>
                  <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" style="border-radius: 10px; background: linear-gradient(135deg, #206bc4 0%, #1a569d 100%); box-shadow: 0 4px 14px rgba(32, 107, 196, 0.35);">
                          <a href="{{ $url }}" target="_blank" style="display: inline-block; padding: 14px 34px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 10px; letter-spacing: 0.2px;">
                            Atur Ulang Kata Sandi &rarr;
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Security Info Box -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin: 24px 0 20px 0;">
                <tr>
                  <td style="padding: 16px 18px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td width="24" valign="top" style="font-size: 16px; line-height: 20px;">
                          &#9200;
                        </td>
                        <td style="padding-left: 10px; font-size: 12px; line-height: 1.5; color: #64748b;">
                          <strong style="color: #334155;">Penting:</strong> Tautan ini hanya berlaku selama <strong style="color: #206bc4;">{{ $count }} menit</strong>. Jika tautan kedaluwarsa, Anda dapat mengajukan permintaan baru.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <p style="font-size: 13px; line-height: 1.6; color: #64748b; margin: 0 0 24px 0;">
                Jika Anda <strong>tidak meminta</strong> perubahan kata sandi ini, tidak ada tindakan lebih lanjut yang perlu dilakukan. Kata sandi Anda tetap aman dan akun Anda tidak akan berubah.
              </p>

              <!-- Troubleshooting Link Box -->
              <div style="border-top: 1px dashed #e2e8f0; padding-top: 20px; margin-top: 20px;">
                <p style="font-size: 11px; line-height: 1.5; color: #94a3b8; margin: 0 0 6px 0;">
                  Jika tombol di atas tidak dapat diklik, salin dan tempelkan tautan berikut ke browser Anda:
                </p>
                <div style="background-color: #f1f5f9; padding: 10px 12px; border-radius: 8px; font-size: 11px; word-break: break-all; color: #206bc4; font-family: monospace; line-height: 1.4;">
                  {{ $url }}
                </div>
              </div>

            </td>
          </tr>

          <!-- Footer Section -->
          <tr>
            <td style="background-color: #f8fafc; padding: 24px 36px; border-top: 1px solid #e2e8f0; text-align: center;">
              <p style="font-size: 12px; color: #64748b; margin: 0 0 6px 0; font-weight: 500;">
                &copy; {{ date('Y') }} <strong>{{ config('app.name', 'RuangTerra') }}</strong>. Hak cipta dilindungi undang-undang.
              </p>
              <p style="font-size: 11px; color: #94a3b8; margin: 0; line-height: 1.4;">
                Email otomatis ini dikirim oleh sistem LMS RuangTerra. Harap jangan membalas email ini secara langsung.
              </p>
            </td>
          </tr>

        </table>
        <!-- End Email Container -->

      </td>
    </tr>
  </table>

</body>
</html>
