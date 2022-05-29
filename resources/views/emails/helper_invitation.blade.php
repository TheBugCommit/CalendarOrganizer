<h4>
    Hello {{ $data['owner_name'] }} has invited you to join on {{ $data['calendar_title'] }}, click on the link.
    <br>
    If you are not registered, register.
</h4>

<a href="{{ $data['token'] }}">Click To become Helper</a>
<p>Or copy paste this link: </p>
<a href="{{ $data['token'] }}">{{ $data['token'] }}</a>
