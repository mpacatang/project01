@component('mail::message')

Hi, {{ $user->name }}

We just receive reset password request to your Yuup account {{ $user->email }}. If this is truly yours, please click on the following link. If you don't request it, please ignore this email. Maybe someone entered their email incorrectly. Your password will not be reset until you click the link and create a new password.

@component('mail::button', ['url' => $url, 'color' => 'green'])
RESET MY PASSWORD
@endcomponent

If clicking the URL above does not work, copy and paste this URL into a browser window.

{{ $url }}

@endcomponent