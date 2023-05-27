@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">Reply to Message</div>
        <div class="card-body">
            <form method="POST" action="{{ route('webmail.sendReply', ['originalMessage' => $message->getUid()]) }}">
                @csrf

                <div class="form-group">
                    <label for="fromEmail">From</label>
                    <input type="email" name="fromEmail" id="fromEmail" class="form-control" value="{{ $fromEmail }}" readonly>
                </div>

                <div class="form-group">
                    <label for="toEmail">To</label>
                    <input type="email" name="toEmail" id="toEmail" class="form-control" value="{{ $message->getFrom()[0]->mail }}" readonly required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control" value="{{ $message->subject }}" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" id="message" class="form-control" rows="5" required><br><hr>@if ($message->hasHTMLBody()){{ $message->getHTMLBody() }}@else{{ $message->getTextBody() }}@endif</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div>
@include('webmail-admin::partials.tinymce._editor', ['selector' => 'message','height' => '500'])
@endsection
