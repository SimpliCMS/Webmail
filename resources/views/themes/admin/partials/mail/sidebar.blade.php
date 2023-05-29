<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Folders</h5>
        <div class="add-folder-container">
            <a href="#" class="add-folder-link">Add Folder</a>
            <form class="add-folder-form d-none" action="{{ route('webmail.addFolder') }}" method="POST">
                @csrf
                <div class="input-group ms-2">
                    <input type="text" name="folderName" class="form-control" placeholder="Enter folder name" required>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <div class="sidebar">
        @if($folders->count() > 0)
        <ul class="list-group">
            <!-- Sort the folders by name -->
            @php
            $sortedFolders = $folders->sortBy('name');
            @endphp

            <!-- INBOX -->
            @php
            $inboxFolder = $sortedFolders->where('name', 'INBOX')->first();
            @endphp
            @if ($inboxFolder)
            <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'INBOX' ? 'active' : '' }}">
                <a href="{{ route('webmail.mailbox', 'INBOX') }}" class="{{ $selectedFolder && $selectedFolder->name === 'INBOX' ? 'text-white' : '' }}">
                    {{ ucfirst(strtolower($inboxFolder->name)) }}
                </a>
                <span class="badge bg-primary folder-count" data-folder="{{ $inboxFolder->name }}"></span>
            </li>
            @endif

            <!-- Sent -->
            @php
            $sentFolder = $sortedFolders->where('name', 'Sent')->first();
            @endphp
            @if ($sentFolder)
            <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Sent' ? 'active' : '' }}">
                <a href="{{ route('webmail.mailbox', 'Sent') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Sent' ? 'text-white' : '' }}">
                    {{ $sentFolder->name }}
                </a>
                <span class="badge bg-primary folder-count" data-folder="{{ $sentFolder->name }}"></span>
            </li>
            @endif

            <!-- Drafts -->
            @php
            $draftsFolder = $sortedFolders->where('name', 'Drafts')->first();
            @endphp
            @if ($draftsFolder)
            <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Drafts' ? 'active' : '' }}">
                <a href="{{ route('webmail.mailbox', 'Drafts') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Drafts' ? 'text-white' : '' }}">
                    {{ $draftsFolder->name }}
                </a>
                <span class="badge bg-primary folder-count" data-folder="{{ $draftsFolder->name }}"></span>
            </li>
            @endif

            <!-- Junk -->
            @php
            $junkFolder = $sortedFolders->where('name', 'Junk')->first();
            @endphp
            @if ($junkFolder)
            <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Junk' ? 'active' : '' }}">
                <a href="{{ route('webmail.mailbox', 'Junk') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Junk' ? 'text-white' : '' }}">
                    {{ $junkFolder->name }}
                </a>
                <span class="badge bg-primary folder-count" data-folder="{{ $junkFolder->name }}"></span>
            </li>
            @endif

            <!-- Trash -->
            @php
            $trashFolder = $sortedFolders->where('name', 'Trash')->first();
            @endphp
            @if ($trashFolder)
            <li class="list-group-item {{ $selectedFolder && $selectedFolder->name === 'Trash' ? 'active' : '' }}">
                <a href="{{ route('webmail.mailbox', 'Trash') }}" class="{{ $selectedFolder && $selectedFolder->name === 'Trash' ? 'text-white' : '' }}">
                    {{ $trashFolder->name }}
                </a>
                <span class="badge bg-primary folder-count" data-folder="{{ $trashFolder->name }}"></span>
            </li>
            @endif

            <!-- Other folders -->
            @foreach ($sortedFolders->sortBy('name') as $folder)
            @if (!in_array($folder->name, ['INBOX', 'Sent', 'Drafts', 'Junk', 'Trash']))
            <li class="list-group-item {{ $selectedFolder && $folder->name === $selectedFolder->name ? 'active' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('webmail.mailbox', $folder->name) }}" class="{{ $selectedFolder && $folder->name === $selectedFolder->name ? 'text-white' : '' }}">
                            {{ $folder->name }}
                        </a>
                        <span class="badge bg-primary folder-count" data-folder="{{ $folder->name }}"></span>
                    </div>
                    @if($folder->name !== 'Archive')
                    <form id="delete-form" action="{{ route('webmail.deleteFolder', ['targetFolder' => $folder->name]) }}" method="POST" style="display:inline;">
                        @csrf
                        <button href="#" type="submit" name="targetFolder" value="{{ $folder->name }}" class="btn btn-link p-0 m-0 border-0 shadow-none delete-link {{ $selectedFolder && $folder->name === $selectedFolder->name ? 'text-white' : '' }}">Delete</button>
                    </form>
                    @endif
                </div>
            </li>
            @endif
            @endforeach


        </ul>
        @else
        <p>No folders found</p>
        @endif
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function () {
  fetchFolderCounts();

  // Fetch folder counts
  function fetchFolderCounts() {
    $('.folder-count').each(function () {
      var folderCountElement = $(this);
      var folderName = folderCountElement.data('folder');
      var currentCount = parseInt(folderCountElement.text());

      $.ajax({
        url: '/admin/mail/mailbox/' + folderName + '/count',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          var newCount = response.count;

          // Only update the count if it's a valid number and not zero
          if (!isNaN(newCount) && newCount !== 0) {
            folderCountElement.text(newCount);
          }
        },
        error: function () {
          // Keep the previous count if there's an error
          folderCountElement.text(currentCount);
        }
      });
    });
  }

  // Update folder counts every 5 seconds
  setInterval(fetchFolderCounts, 20000);
});

    $('.add-folder-link').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $('.add-folder-form').removeClass('d-none');
    });
</script>
@endpush
