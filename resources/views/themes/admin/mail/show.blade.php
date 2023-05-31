@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')
<div class="card d-flex flex-column" id="message-content">
    <div class="card-header d-flex justify-content-end align-items-center">
        <div class="ms-auto">
            <button class="btn btn-primary dropdown-toggle me-2" type="button" id="moveToFolderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Move to Folder
            </button>
            <ul class="dropdown-menu" aria-labelledby="moveToFolderDropdown">
                {{-- Sort the folders --}}
                @php
                $currentFolder = request()->route('folder');
                $sortedFolders = $folders->reject(function ($folder) use ($currentFolder) {
                return in_array($folder->name, [$currentFolder, 'Sent', 'Trash']);
                })->sortBy(function ($folder) {
                switch ($folder->name) {
                case 'INBOX':
                return 1; // Sort INBOX first
                case 'Drafts':
                case 'Junk':
                return 2; // Sort Drafts and Junk next
                default:
                return 3; // Sort other folders last
                }
                });
                @endphp

                {{-- Display the sorted folders --}}
                {{-- Display INBOX, Drafts, and Junk folders first --}}
                @foreach ($sortedFolders->where('name', 'INBOX')->sortBy('name') as $folder)
                <form action="{{ route('webmail.move', ['folder' => $currentFolder, 'messageId' => $message->getUid(), 'targetFolder' => $folder->name]) }}" method="POST" style="display:inline;">
                    @csrf
                    <li>
                        <button class="dropdown-item" type="submit" name="targetFolder" value="{{ $folder->name }}">{{ ucfirst(strtolower($folder->name)) }}</button>
                    </li>
                </form>
                @endforeach

                @foreach ($sortedFolders->where('name', 'Drafts')->sortBy('name') as $folder)
                <form action="{{ route('webmail.move', ['folder' => $currentFolder, 'messageId' => $message->getUid(), 'targetFolder' => $folder->name]) }}" method="POST" style="display:inline;">
                    @csrf
                    <li>
                        <button class="dropdown-item" type="submit" name="targetFolder" value="{{ $folder->name }}">{{ ucfirst(strtolower($folder->name)) }}</button>
                    </li>
                </form>
                @endforeach

                @foreach ($sortedFolders->where('name', 'Junk')->sortBy('name') as $folder)
                <form action="{{ route('webmail.move', ['folder' => $currentFolder, 'messageId' => $message->getUid(), 'targetFolder' => $folder->name]) }}" method="POST" style="display:inline;">
                    @csrf
                    <li>
                        <button class="dropdown-item" type="submit" name="targetFolder" value="{{ $folder->name }}">{{ ucfirst(strtolower($folder->name)) }}</button>
                    </li>
                </form>
                @endforeach

                {{-- Display other folders --}}
                @foreach ($sortedFolders->reject(function ($folder) {
                return in_array($folder->name, ['INBOX', 'Drafts', 'Junk']);
                })->sortBy('name') as $folder)
                <form action="{{ route('webmail.move', ['folder' => $currentFolder, 'messageId' => $message->getUid(), 'targetFolder' => $folder->name]) }}" method="POST" style="display:inline;">
                    @csrf
                    <li>
                        <button class="dropdown-item" type="submit" name="targetFolder" value="{{ $folder->name }}">{{ ucfirst(strtolower($folder->name)) }}</button>
                    </li>
                </form>
                @endforeach
            </ul>
            <a href="{{ route('webmail.reply', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" class="btn btn-primary me-2" style="order: 2;"><i class="fas fa-reply"></i> Reply</a>
            <a href="{{ route('webmail.forward', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" class="btn btn-primary me-2" style="order: 3;"><i class="fas fa-share"></i> Forward</a>

            @if(request()->route('folder') !== 'Trash')
            <form id="trash-form" data-action="{{ route('webmail.trash') }}" method="POST" style="display:inline;" class="trash-form">
                @csrf
                <input type="hidden" name="folder" value="{{ request()->route('folder') }}" />
                <input type="hidden" name="messageId" value="{{ $message->getUid() }}" />
                <button id="trash-button" type="button" class="btn btn-danger trash-button">
                    <i class="fas fa-trash"></i> Trash
                </button>
            </form>
            @else
            <form id="delete-form" data-action="{{ route('webmail.delete') }}" method="POST" style="display:inline;" class="delete-form">
                @csrf
                <input type="hidden" name="folder" value="{{ request()->route('folder') }}" />
                <input type="hidden" name="messageId" value="{{ $message->getUid() }}" />
                <button id="delete-button" type="button" class="btn btn-danger delete-button">
                    <i class="fas fa-trash"></i> Trash
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card-body flex-grow-1 mt-1">
        <div class="details-container">
            <div class="avatar-container">
                <img class="avatar" src="{{ getLogo($message->getFrom()[0]->mail, $message->getFrom()[0]->personal) }}" alt="Avatar">
            </div>
            <div class="details">
                <p><h5>{{ $message->subject }}</h5></p>
                <p>From: {{ $message->getFrom()[0]->full }}&nbsp;<a href="{{ route('webmail.address-book.create', ['email' => $message->getFrom()[0]->mail, 'name' => $message->getFrom()[0]->personal]) }}">
                        <i class="fas fa-address-book"></i> Add to Contacts
                    </a></p>
                <p>To: {{ $message->getTo()[0]->full }}</p>
            </div>
        </div>
        <p>
            @php
            $messageDate = \Carbon\Carbon::parse($message->getDate());
            $formattedDate = $messageDate->isToday() ? $messageDate->format('h:i A') : $messageDate->format('F j');
            if ($messageDate->isCurrentYear()) {
            $formattedDate .= ', ' . $messageDate->format('h:i A');
            } else {
            $formattedDate .= ', ' . $messageDate->format('Y h:i A');
            }
            @endphp
            {{ $formattedDate }}
        </p>
        <hr>
        @if ($message->hasHTMLBody())
        <div class="message-container" style="max-height: 605px; overflow-y: auto;">
            <iframe id="message-iframe" class="d-flex flex-column" srcdoc="{{ $message->getHTMLBody() }}" style="width:100%;height:100%"></iframe>
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
        <div class="message-container" style="max-height: 605px; overflow-y: auto;">
            <iframe id="message-iframe" class="d-flex flex-column" srcdoc="{{ $message->getTextBody() }}" style="width:100%;height:100%"></iframe>
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
        @endif
    </div>
</div>
@endsection