<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Notification')</title>
</head>
@php(
    $accent = isset($accent) && is_string($accent) ? $accent : '#F25B0A'
)
@php(
    $company = \Smartisan\Settings\Facades\Settings::group('company')
)
@php(
    $companyName = (string) ($company->get('company_name') ?: config('app.name'))
)
@php(
    $companyEmail = (string) $company->get('company_email')
)
@php(
    $companyPhone = (string) $company->get('company_phone')
)
@php(
    $companyWebsite = (string) $company->get('company_website')
)
@php(
    $companyAddress = (string) $company->get('company_address')
)
@php(
    $logo = optional(\App\Models\ThemeSetting::where('key','theme_logo')->first())->logo
)
<body style="margin:0;padding:0;background:#f6f9fc;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f9fc;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #eef2f7;">
                <tr>
                    <td style="background:{{ $accent }};height:6px;line-height:6px;font-size:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" style="padding:18px 18px 0 18px;">
                        <a href="{{ config('app.url') }}" style="text-decoration:none;border:0;display:inline-block;">
                            <img src="{{ $logo }}" alt="{{ $companyName }}" style="display:block;height:48px;width:auto;border:0;outline:none;" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 28px 4px 28px;">
                        @yield('preheader')
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 28px 0 28px;">
                        @yield('content')
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 28px 28px 28px;border-top:1px solid #f0f4f9;">
                        <p style="margin:0 0 8px 0;font-size:13px;color:#6b7280;">Thanks,<br/>
                            <span style="color:#22C55E;font-weight:600;">{{ $companyName }}</span>
                        </p>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;font-size:12px;color:#6b7280;">
                            <tr>
                                <td style="padding:6px 0;vertical-align:top;">
                                    @if($companyAddress)
                                        <div style="margin:0 0 4px 0;">{{ $companyAddress }}</div>
                                    @endif
                                    <div>
                                        @if($companyEmail)
                                            <a href="mailto:{{ $companyEmail }}" style="color:#6b7280;text-decoration:none;">{{ $companyEmail }}</a>
                                        @endif
                                        @if($companyEmail && $companyPhone)
                                            <span style="margin:0 6px;">•</span>
                                        @endif
                                        @if($companyPhone)
                                            <a href="tel:{{ $companyPhone }}" style="color:#6b7280;text-decoration:none;">{{ $companyPhone }}</a>
                                        @endif
                                        @if(($companyEmail || $companyPhone) && $companyWebsite)
                                            <span style="margin:0 6px;">•</span>
                                        @endif
                                        @if($companyWebsite)
                                            <a href="{{ $companyWebsite }}" style="color:#6b7280;text-decoration:none;" target="_blank" rel="noopener">{{ $companyWebsite }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
