<?php

namespace Modules\Webmail\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Webklex\IMAP\Facades\Client;
use Webklex\IMAP\Message;
use Webklex\IMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Support\FolderCollection;
use Webklex\IMAP\Folder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Modules\Webmail\Models\Account;
use Modules\Webmail\Models\AddressBook;
use Konekt\Gears\Facades\Preferences;
use Laravolt\Avatar\Facade as Avatar;
use Modules\Core\Http\Controllers\Controller;

class WebmailController extends Controller {

    protected $user;
    protected $imapClient;

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

            $webmailAccount = Account::where('email', Preferences::get('webmail.mail_username', $this->user))->first();
            // If the account doesn't exist, create a new one
            if (!$webmailAccount) {
                $webmailAccount = Account::create(['email' => Preferences::get('webmail.mail_username', $this->user), 'user_id' => $this->user->id]);
                $addressBook = $webmailAccount->addressBook()->create([
                    'account_id' => $webmailAccount->id,
                ]);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index() {
        return view('webmail-admin::mail.index');
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

// Check if Archive folder already exists
        $archiveFolderName = 'Archive';
        $existingFolder = $folders->first(function ($folder) use ($archiveFolderName) {
            return strtolower($folder->name) === strtolower($archiveFolderName);
        });

// Create Archive folder if it doesn't exist
        if (!$existingFolder) {
            $this->imapClient->createFolder($archiveFolderName);
        }

        // Get the selected mailbox folder
        $selectedFolder = $folders->first(function ($item) use ($folder) {
            return $item->name === $folder;
        });

        // Fetch messages for the selected mailbox
        $messages = [];
        if ($selectedFolder) {
            $messages = $selectedFolder->messages()->all()->get();
        }

        // Set the active message ID
        $activeMessageId = null;
        if ($request->has('messageId')) {
            $activeMessageId = $request->input('messageId');
        }

        // Check if an AJAX request is made for loading the message content
        if ($request->ajax()) {
            return view('webmail-admin::partials.mail.mailbox_content', compact('folders', 'selectedFolder', 'messages', 'folder', 'activeMessageId'));
        } else {
            return view('webmail-admin::mail.mailbox', compact('folders', 'selectedFolder', 'messages', 'folder', 'activeMessageId'));
        }
    }

    public function pollFolders($folderName) {
        // Get all the folders
        $folders = $this->imapClient->getFolders();

        $folderCounts = [];

        // Iterate over the folders
        foreach ($folders as $folder) {
            $name = $folder->name;

            // Get the unseen message count for each folder
            $messageCount = $folder->query()->unseen()->setFetchBody(false)->count();

            // Store the folder name and count in the array
            $folderCounts[$name] = $messageCount;
        }

        // Return the folder counts for the requested folder as JSON response
        return response()->json(['count' => $folderCounts[$folderName] ?? 0]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($folder = 'INBOX', $messageId, Request $request) {
        $folders = $this->imapClient->getFolders();
        $folder = $this->imapClient->getFolder($folder);
        $selectedFolder = $folders->where('name', $folder)->first();
        //Get all Messages of the current Mailbox $folder
        /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
        $message = $folder->messages()->getMessageByUid($messageId);
        if ($message->getUid() == $messageId) {
            if ($request->ajax()) {
                return view('webmail-admin::partials.mail.message_content', compact('folders', 'selectedFolder', 'folder', 'message'));
            } else {
                return view('webmail-admin::mail.show', compact('folders', 'selectedFolder', 'folder', 'message'));
            }
        } else {
            
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

        return view('webmail-admin::mail.compose', compact('folders', 'selectedFolder', 'fromEmail', 'user'));
    }

    public function reply($folder, $messageId) {
        $folders = $this->imapClient->getFolders();
        $folder = $this->imapClient->getFolder($folder);
        $selectedFolder = $folders->where('name', $folder)->first();

        // Get the original message
        $message = $folder->messages()->getMessageByUid($messageId);

        // Get the current user's email
        $user = Auth::user();
        $fromEmail = Preferences::get('webmail.mail_username', $user);

        return view('webmail-admin::mail.reply', compact('folders', 'selectedFolder', 'fromEmail', 'user', 'message'));
    }

    public function forward($folder, $messageId) {
        $folders = $this->imapClient->getFolders();
        $folder = $this->imapClient->getFolder($folder);
        $selectedFolder = $folders->where('name', $folder)->first();

        // Get the original message
        $message = $folder->messages()->getMessageByUid($messageId);

        // Get the current user's email
        $user = Auth::user();
        $fromEmail = Preferences::get('webmail.mail_username', $user);

        return view('webmail-admin::mail.forward', compact('folders', 'selectedFolder', 'fromEmail', 'user', 'message'));
    }

    public function move(Request $request) {
        $folder = $this->imapClient->getFolder($request->folder);

        // Get the target folder
        // Get the message
        $message = $folder->messages()->getMessageByUid($request->messageId);

        $message->move($request->targetFolder);
        if ($request->ajax()) {
            // Return a JSON response for Ajax requests
            return response()->json(['message' => 'Message moved successfully!']);
        } else {
            // Redirect back for regular form submissions
           return redirect()->back()->with('success', 'Message moved successfully!');
        }
    }

    public function addFolder(Request $request) {
        $this->validate($request, [
            'folderName' => 'required'
        ]);

        $folderName = $request->input('folderName');

        try {

            // Create the folder
            $folder = $this->imapClient->createFolder($folderName);

            // Close the connection
            $this->imapClient->disconnect();

            // Redirect or display success message
            return redirect()->back()->with('success', 'Folder created successfully');
        } catch (\Exception $e) {
            // Handle connection or folder creation error
            return redirect()->back()->with('error', 'Failed to create the folder');
        }
    }

    public function deleteFolder($targetFolder) {
        try {


            $folder = $this->imapClient->deleteFolder($targetFolder);

            // Close the connection
            $this->imapClient->disconnect();

            // Redirect or display success message
            return redirect()->back()->with('success', 'Folder deleted successfully');
        } catch (\Exception $e) {
            // Handle connection or folder creation error
            return redirect()->route('webmail.index')->with('error', 'Failed to delete the folder');
        }
    }

    public function markAsRead($folder, $messageId) {
        $folder = $this->imapClient->getFolder($folder);

        // Get the message by UID
        $message = $folder->messages()->getMessageByUid($messageId);

        $message->setFlag('SEEN');

        return response()->json(['success' => true]);
    }

    public function markAsUnRead($folder, $messageId) {
        $folder = $this->imapClient->getFolder($folder);

        // Get the message by UID
        $message = $folder->messages()->getMessageByUid($messageId);

        $message->setFlag('UNSEEN');

        return response()->json(['success' => true]);
    }

    public function send(Request $request) {
        $this->validate($request, [
            'toEmail' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->isHTML(true);
            $mail->Host = Config::get('mail.host');
            $mail->Port = Config::get('mail.port');
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('mail.username');
            $mail->Password = Config::get('mail.password');

            // Set the email details
            $mail->setFrom(Preferences::get('webmail.mail_username', $this->user), $this->user->name);
            $mail->addAddress($request->input('toEmail'));
            $mail->Subject = $request->input('subject');
            $mail->Body = $request->input('message');

            // Send the email
            $mail->send();

            // Save the sent email to the "Sent" folder
            $path = "Sent"; // Specify the folder where you want to save the sent email
            $message = $mail->getSentMIMEMessage();

            // Open the connection to the IMAP server
            $imapStream = imap_open("{" . Preferences::get('webmail.mail_host', $this->user) . ":" . Preferences::get('webmail.mail_port', $this->user) . "/imap/notls}" . $path, Preferences::get('webmail.mail_username', $this->user), Preferences::get('webmail.mail_password', $this->user));

            // Check if the connection was successful
            if ($imapStream) {
                // Save the email to the specified folder
                imap_append($imapStream, "{" . Preferences::get('webmail.mail_host', $this->user) . ":" . Preferences::get('webmail.mail_port', $this->user) . "/imap/notls}" . $path, $message);

                // Close the IMAP connection
                imap_close($imapStream);

                return redirect()->back()->with('success', 'Email sent and saved to Sent folder!');
            } else {
                // Failed to connect to the IMAP server
                return redirect()->back()->with('error', 'Failed to connect to the IMAP server');
            }
        } catch (Exception $e) {
            echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
        }
    }

    public function sendReply(Request $request) {
        $this->validate($request, [
            'toEmail' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->isHTML(true);
            $mail->Host = Config::get('mail.host');
            $mail->Port = Config::get('mail.port');
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('mail.username');
            $mail->Password = Config::get('mail.password');

            // Set the email details
            $mail->setFrom(Preferences::get('webmail.mail_username', $this->user), $this->user->name);
            $mail->addAddress($request->input('toEmail'));
            $mail->Subject = $request->input('subject');
            $mail->Body = $request->input('message');

            // Set the appropriate headers for replying
            $mail->addCustomHeader('In-Reply-To', $originalMessage->getMessageId());
            $mail->addCustomHeader('References', $originalMessage->getMessageId());

            // Send the email
            $mail->send();

            // Save the sent email to the "Sent" folder
            $path = "Sent"; // Specify the folder where you want to save the sent email
            $message = $mail->getSentMIMEMessage();

            // Open the connection to the IMAP server
            $imapStream = imap_open("{" . Preferences::get('webmail.mail_host', $this->user) . ":" . Preferences::get('webmail.mail_port', $this->user) . "/imap/notls}" . $path, Preferences::get('webmail.mail_username', $this->user), Preferences::get('webmail.mail_password', $this->user));

            // Check if the connection was successful
            if ($imapStream) {
                // Save the email to the specified folder
                imap_append($imapStream, "{" . Preferences::get('webmail.mail_host', $this->user) . ":" . Preferences::get('webmail.mail_port', $this->user) . "/imap/notls}" . $path, $message);

                // Close the IMAP connection
                imap_close($imapStream);

                return redirect()->back()->with('success', 'Email sent and saved to Sent folder!');
            } else {
                // Failed to connect to the IMAP server
                return redirect()->back()->with('error', 'Failed to connect to the IMAP server');
            }
        } catch (Exception $e) {
            echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
        }
    }

    public function trash(Request $request) {
        $folder = $this->imapClient->getFolder($request->folder);

        // Get the message by UID
        $message = $folder->messages()->getMessageByUid($request->messageId);

        $message->move($folder_path = 'Trash');

        if ($request->ajax()) {
            // Return a JSON response for Ajax requests
            return response()->json(['message' => 'Email moved to Trash!']);
        } else {
            // Redirect back for regular form submissions
            return redirect()->back()->with('success', 'Email moved to Trash!');
        }
    }

    public function delete(Request $request) {
        $folder = $this->imapClient->getFolder($request->folder);

        $message = $folder->messages()->getMessageByUid($request->messageId);

        $message->delete();

        if ($request->ajax()) {
            // Return a JSON response for Ajax requests
            return response()->json(['message' => 'Email deleted']);
        } else {
            // Redirect back for regular form submissions
            return redirect()->back()->with('success', 'Email deleted');
        }
    }

}
