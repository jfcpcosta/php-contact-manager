<?php namespace Core\Persistence;

class File {

    public static function exists(string $path): bool {
        return file_exists($path);
    }

    public static function add(string $file, string $content, bool $append = true): void {
        if ($append) {
            file_put_contents($file, $content, FILE_APPEND);
        } else {
            file_put_contents($file, $content);
        }
    }

    public static function get(string $file): string {
        return static::exists($file) ? file_get_contents($file) : null;
    }

    public static function folderContent(string $path, string $filter): array {
        return glob($path . "/" . $filter);
    }
}