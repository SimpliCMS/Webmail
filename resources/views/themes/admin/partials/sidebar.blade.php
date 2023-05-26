<div class="sidebar">
    @if($folders->count() > 0)
    <ul class="list-group">
        <!-- INBOX -->
        @php
        $inboxFolder = $folders->where('name', 'INBOX')->first();
        @endphp
        @if ($inboxFolder)
        <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'INBOX' ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', 'INBOX') }}" class="{{ $selectedFolder && $selectedFolder->name === 'INBOX' ? 'text-white' : '' }}">
                {{ ucfirst(strtolower($inboxFolder->name)) }}
            </a>
            <span class="badge bg-primary">{{ $inboxFolder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif

        <!-- Sent -->
        @php
        $sentFolder = $folders->where('name', 'Sent')->first();
        @endphp
        @if ($sentFolder)
        <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Sent' ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', 'Sent') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Sent' ? 'text-white' : '' }}">
                {{ $sentFolder->name }}
            </a>
            <span class="badge bg-primary">{{ $sentFolder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif

        <!-- Drafts -->
        @php
        $draftsFolder = $folders->where('name', 'Drafts')->first();
        @endphp
        @if ($draftsFolder)
        <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Drafts' ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', 'Drafts') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Drafts' ? 'text-white' : '' }}">
                {{ $draftsFolder->name }}
            </a>
            <span class="badge bg-primary">{{ $draftsFolder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif

        <!-- Junk -->
        @php
        $junkFolder = $folders->where('name', 'Junk')->first();
        @endphp
        @if ($junkFolder)
        <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Junk' ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', 'Junk') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Junk' ? 'text-white' : '' }}">
                {{ $junkFolder->name }}
            </a>
            <span class="badge bg-primary">{{ $junkFolder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif

        <!-- Trash -->
        @php
        $trashFolder = $folders->where('name', 'Trash')->first();
        @endphp
        @if ($trashFolder)
        <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Trash' ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', 'Trash') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Trash' ? 'text-white' : '' }}">
                {{ $trashFolder->name }}
            </a>
            <span class="badge bg-primary">{{ $trashFolder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif

        <!-- Other folders -->
        @foreach ($folders as $folder)
        @if (!in_array($folder->name, ['INBOX', 'Sent', 'Drafts', 'Junk', 'Trash']))
        <li class="list-group-item {{ $selectedFolder && $folder->name === $selectedFolder->name ? 'active' : '' }}">
            <a href="{{ route('webmail.mailbox', $folder->name) }}" class="{{ $selectedFolder && $folder->name === $selectedFolder->name ? 'text-white' : '' }}">
                {{ $folder->name }}
            </a>
            <span class="badge bg-primary">{{ $folder->search()->unseen()->setFetchBody(false)->count() }}</span>
        </li>
        @endif
        @endforeach
    </ul>
    @else
    <p>No folders found</p>
    @endif
</div>

