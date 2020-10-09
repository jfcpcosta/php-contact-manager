<?php namespace Core\Mvc;

use Core\Persistence\File;

class View {

    public static function render(string $name, array $data = null): void {
        if (!is_null($data)) {
            extract($data);
        }

        $viewPath = "../views/$name.phtml";

        if (File::exists($viewPath)) {

            $helpers = File::folderContent("../views/helpers/", "*.helper.php");
            foreach ($helpers as $helper) {
                require_once $helper;
            }

            include $viewPath;
        }
    }

    public static function renderWithTemplate(string $name, array $data = null): void {
        static::render('templates/header', $data);
        static::render($name, $data);
        static::render('templates/footer', $data);
    }
}