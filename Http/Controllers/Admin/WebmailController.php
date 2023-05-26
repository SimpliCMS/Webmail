<?php

namespace Modules\Webmail\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use Webklex\IMAP\Message;
use Webklex\IMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Support\FolderCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Part\TextPart;
use Swift_Message;
use Konekt\Gears\Facades\Preferences;
use Modules\Core\Http\Controllers\Controller;

class WebmailController extends Controller {

    private $imapClient;
    private $user;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $account = Client::account('default');
            $account->hostname = Preferences::get('webmail.mail_host', $this->user);
            $account->port = Preferences::get('webmail.mail_port', $this->user);
            $account->encryption = false;
            $account->validate_cert = false;
            $account->username = Preferences::get('webmail.mail_username', $this->user);
            $account->password = Preferences::get('webmail.mail_password', $this->user);

            $this->imapClient = $account->connect();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index() {
        return view('webmail-admin::index');
    }

    public function mailbox($folder = 'INBOX', Request $request) {
        // Initialize your IMAP client and get the folder collection

        $folders = $this->imapClient->getFolders();

        $selectedFolder = $folders->where('name', $folder)->first();
        // Sort the folders and move selected mailbox to the top
        $folders = $folders->sortBy(function ($item) use ($selectedFolder) {
            if ($selectedFolder && $item->name === $selectedFolder->name) {
                return 0;
            }
            return 1;
        });

        // Get the selected mailbox folder
        $selectedFolder = $folders->first(function ($item) use ($folder) {
            return $item->name === $folder;
        });

        // Fetch messages for the selected mailbox
        $messages = [];
        if ($selectedFolder) {
            $messages = $selectedFolder->messages()->all()->get();
        }

        // Check if an AJAX request is made for loading the message content

        return view('webmail-admin::mailbox', compact('folders', 'selectedFolder', 'messages', 'folder'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($folder, $messageId) {
        $folders = $this->imapClient->getFolders();
        $selectedFolder = $folders->where('name', $folder)->first();

        foreach ($folders as $folder) {

            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()->all()->get();
            foreach ($messages as $message) {

                //Move the current Message to 'INBOX.read'
                if ($message->message_id == $messageId) {
                    return view('webmail-admin::show', compact('folders', 'selectedFolder', 'message'));
                } else {
                    
                }
            }
        }
        // Handle the case when the message is not found
        // For example, redirect back with an error message
        return redirect()->back()->with('error', 'Message not found');
    }

    public function compose($folder) {
        $folders = $this->imapClient->getFolders();
        $selectedFolder = $folders->where('name', $folder)->first();
        // Get the current user's email
        $user = Auth::user();
        $fromEmail = Preferences::get('webmail.mail_username', $user);

        return view('webmail-admin::compose', compact('folders', 'selectedFolder', 'fromEmail', 'user'));
    }

    public function getMessageContent(Request $request) {
        $messageId = $request->input('messageId');

        // Find the message with the specified message ID
        $message = Message::where('message_id', $messageId)->first();

        if ($message) {
            // Retrieve the message content
            $content = $message->getHTMLBody(); // Adjust the method based on your library or message format (e.g., getHTMLBody, getTextBody, etc.)
            // Return the message content
            return response()->json(['content' => $content]);
        }

        // Handle the case when the message is not found
        return response()->json(['error' => 'Message not found'], 404);
    }

    public function markAsRead($messageId) {
        $message = $this->imapClient->getFolder('INBOX')->query()->find($messageId);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    public function send(Request $request) {
        $this->validate($request, [
            'to' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $to = $request->input('to');
        $subject = $request->input('subject');
        $message = $request->input('message');

        $fromEmail = Preferences::get('webmail.mail_username', $this->user);
        $fromName = $this->user->name;

        $data = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
        ];

        Mail::send('webmail-admin::send', $data, function ($message) use ($to, $subject, $fromEmail, $fromName) {
            $message->to($to)
                    ->subject($subject)
                    ->from($fromEmail, $fromName)
                    ->setBody($data['message'], 'text/html');
        });

        return redirect()->back()->with('success', 'Email sent successfully!');
    }

    public function delete($messageId) {
        $message = $this->imapClient->getFolder('INBOX')->query()->find($messageId);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

}
