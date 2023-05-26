<div class="card mb-5">
    <div class="card-header">Message</div>

    <div class="card-body">
        <h4>{{ $message->subject }}</h4>
        <p>From: {{ $message->getFrom()[0]->mail }}</p>
        <p>To: {{ $message->getTo()[0]->mail }}</p>
        <p>           @php
            $messageDate = \Carbon\Carbon::parse($message->getDate());
            $formattedDate = $messageDate->isToday() ? $messageDate->format('h:i A') : $messageDate->format('F j, Y h:i A');
            @endphp
            {{ $formattedDate }}</p>

        <hr>
        @if ($message->hasHTMLBody())
        <div class="message-container">
            <iframe id="message-iframe" srcdoc="{{ $message->getHTMLBody() }}"
                    style="overflow: hidden; height: 100%; width: 100%; position: absolute;"></iframe>
        </div>
        <script>
            var iframe = document.getElementById("message-iframe");
            if (iframe) {
                iframe.addEventListener("load", function () {
                    var links = iframe.contentWindow.document.getElementsByTagName("a");
                    for (var i = 0; i < links.length; i++) {
                        links[i].setAttribute("target", "_blank");
                    }
                });
            }
        </script>
        @else
        <div>{!! $message->getTextBody() !!}</div>
        @endif
    </div>
</div>