@component('mail::message')

Thank you for joining Yuup!

Please click the following URL to activate your account:

@component('mail::button', ['url' => $url, 'color' => 'blue'])
ACTIVATE ACCOUNT
@endcomponent

If clicking the URL above does not work, copy and paste this URL into a browser window.

{{ $url }}

@endcomponent