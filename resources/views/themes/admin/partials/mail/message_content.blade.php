<div class="card d-flex flex-column">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>{{ $message->subject }}</h5>

        <div class="d-flex align-items-center">
            <div class="dropdown me-2">
                <button class="btn btn-primary dropdown-toggle" type="button" id="moveToFolderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
            </div>


            <a href="{{ route('webmail.reply', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" class="btn btn-primary me-2"><i class="fa-sharp fa-solid fa-reply"></i> Reply</a>
            <a href="{{ route('webmail.forward', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" class="btn btn-primary me-2"><i class="fa-sharp fa-solid fa-share"></i> Forward</a>

            @if(request()->route('folder') !== 'Trash')
            <form action="{{ route('webmail.trash', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" method="POST" style="display:inline;">
                @csrf
                @method('POST')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Trash
                </button>
            </form>
            @else
            <form action="{{ route('webmail.delete', ['folder' => request()->route('folder'), 'messageId' => $message->getUid()]) }}" method="POST" style="display:inline;">
                @csrf
                @method('POST')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card-body flex-grow-1">
        <p>From: {{ $message->getFrom()[0]->personal }}&nbsp;{{ '<' . $message->getFrom()[0]->mail . '>' }}&nbsp;<a href="{{ route('webmail.address-book.create', ['email' => $message->getFrom()[0]->mail, 'name' => $message->getFrom()[0]->personal]) }}">
    <i class="fas fa-address-book"></i> Add to Contacts
</a></p>
        <p>To: {{ $message->getTo()[0]->personal }}&nbsp;{{ '<' . $message->getTo()[0]->mail . '>' }}</p>
        <p>
            @php
            $messageDate = \Carbon\Carbon::parse($message->getDate());
            $formattedDate = $messageDate->isToday() ? $messageDate->format('h:i A') : $messageDate->format('F j, Y h:i A');
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

