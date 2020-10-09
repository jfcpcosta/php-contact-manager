<?php return [
    '/'                     => 'ContactManager\Controllers\AuthController::login',
    '/logout'               => 'ContactManager\Controllers\AuthController::logout',
    '/sign-up'              => 'ContactManager\Controllers\AuthController::signUp',
    '/contacts'             => 'ContactManager\Controllers\ContactsController::list',
    '/contacts/json'        => 'ContactManager\Controllers\ContactsController::listAsJson',
    '/contacts/add'         => 'ContactManager\Controllers\ContactsController::add',
    '/contacts/:id'         => 'ContactManager\Controllers\ContactsController::detail',
    '/contacts/:id/update'  => 'ContactManager\Controllers\ContactsController::update',
    '/contacts/:id/remove'  => 'ContactManager\Controllers\ContactsController::remove',
    '/contacts/export'      => 'ContactManager\Controllers\ContactsController::export',
    '/contacts/:id/send'    => 'ContactManager\Controllers\ContactsController::send',
];
