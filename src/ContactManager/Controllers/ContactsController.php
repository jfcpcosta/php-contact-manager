<?php namespace ContactManager\Controllers;

use ContactManager\Models\Repositories\ContactsRepository;
use Core\Http\FlashBag;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\SecuredController;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ContactsController extends SecuredController {

    private $contacts;

    public function __construct() {
        parent::__construct();
        $this->contacts = new ContactsRepository();
    }

    public function list(): void {
        $searchTerm = Request::get('search');

        $this->render('list', [
            'contacts' => $this->contacts->getAllContacts($searchTerm)
        ]);
    }
    
    public function listAsJson(): void {
        $searchTerm = Request::get('search');
        $contacts = $this->contacts->getAllContacts($searchTerm);

        Response::json($contacts);
    }

    public function detail($id): void {
        $contact = $this->contacts->getContactById($id);

        if (is_null($contact)) {
            FlashBag::add('Contact not found', 'danger');
            $this->redirect('/contacts');
        }

        $this->render('detail', [
            'contact' => $contact
        ]);
    }

    public function add(): void {
        if ($this->isPost()) {
            $imageFile = $this->contacts->uploadImage(Request::file('image'));
            $this->contacts->createContact(Request::post('name'), Request::post('email'), Request::post('phone'), $imageFile);
            FlashBag::add('Contact created');
            $this->redirect('/contacts');
        }
        $this->render('form');
    }
    
    public function update($id): void {
        $contact = $this->contacts->getContactById($id);

        if (is_null($contact)) {
            FlashBag::add('Contact not found', 'danger');
            $this->redirect('/contacts');
        }

        if ($this->isPost()) {
            $this->contacts->updateContact($id, Request::post('name'), Request::post('email'), Request::post('phone'), Request::file('image'));
            FlashBag::add('Contact updated', 'success');
            $this->redirect('/contacts');
        }

        $this->render('form', [
            'contact' => $contact
        ]);
    }
    
    public function remove($id): void {
        if ($this->contacts->removeContact($id)) {
            FlashBag::add('Contact removed', 'success');
            $this->redirect('/contacts');
        }

        FlashBag::add('Contact not found', 'danger');
        $this->redirect('/contacts');
    }
    
    public function export(): void {
        $file = $this->contacts->exportContacts();
        $this->redirect($file);
    }

    public function send($id): void {
        if ($this->isPost()) {
            $contact = $this->contacts->getContactById($id);

            if (is_null($contact)) {
                FlashBag::add('Contact not found', 'danger');
                $this->redirect('/contacts');
            }

            $subject = Request::post('subject');
            $message = Request::post('message');
            
            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host = 'CHANGE ME: smtp server address';
                $mail->SMTPAuth = true;
                $mail->Username = 'CHANGE ME: smtp server username';
                $mail->Password = 'CHANGE ME: smto server asswird';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                $mail->setFrom('flag@reativ.dev', 'Altice Contact Manager');
                $mail->addAddress($contact->email, $contact->name);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = $message;

                $mail->send();
                FlashBag::add('Sent mail to ' . $contact->name);
            } catch (Exception $e) {
                FlashBag::add('Error sending email: ' . $mail->ErrorInfo, 'danger');
            }

            $this->redirect('/contacts');
        }

        throw new Exception('Only post request');
    }
}
