<?php namespace ContactManager\Models\Repositories;

use Core\Http\Request;
use Core\Mvc\Model;

class ContactsRepository extends Model {

    const TABLE = 'contacts';

    public function getAllContacts(string $filter = null): array {
        $user = Request::session('user')->id;

        if ($filter) {
            $where = "(name LIKE '%$filter%' OR email LIKE '%$filter%' OR phone LIKE '%$filter%') AND created_by = $user";
        } else {
            $where = [
                'created_by' => $user
            ];
        } 

        return $this->database->where(self::TABLE, $where);
    }

    public function getContactById(int $id): object {
        $user = Request::session('user')->id;
        $result = $this->database->where(self::TABLE, [
            'id' => $id,
            'created_by' => $user
        ]);
        return isset($result[0]) ? $result[0] : null;
    }

    public function createContact(string $name, string $email, string $phone = null, string $image = null): bool {
        $user = Request::session('user')->id;
        $result = $this->database->insert(self::TABLE, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'image' => $image,
            'created_by' => $user
        ]);

        return $result['stmt']->rowCount() > 0;
    }

    public function updateContact(int $id, string $name, string $email, string $phone = null, string $image = null): bool {
        $user = Request::session('user')->id;
        
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];

        if (!is_null($image)) {
            $contact = $this->getContactById($id);
            if ($contact->image) {
                unlink($contact->image);
            }

            $filePath = $this->uploadImage($image);
            $data['image'] = $filePath;
        }

        $stmt = $this->database->update(self::TABLE, $data, ['id' => $id, 'created_by' => $user]);

        return $stmt->rowCount() > 0;
    }

    public function removeContact(int $id): bool {
        $user = Request::session('user')->id;

        $contact = $this->getContactById($id);

        if ($contact->image) {
            unlink($contact->image);
        }

        $stmt = $this->database->delete(self::TABLE, ['id' => $id, 'created_by' => $user]);
        return $stmt->rowCount() > 0;
    }

    public function exportContacts(): string {
        $contacts = $this->getAllContacts();

        $content = "sep=,\n";
        $content .= "ID,NAME,EMAIL\n";

        foreach ($contacts as $contact) {
            $content .= sprintf("%s,%s,%s\n", $contact->id, $contact->name, $contact->email);
        }

        $fileName = '/export.csv';
        file_put_contents($fileName, $content);

        return $fileName;
    }

    public function uploadImage(array $file): string {
        $path = 'images/';
        $hash = md5(time());
        $date = date('Y-m-d_H:i:s');
        $targetFile = sprintf("%s%s_%s_%s", $path, $date, $hash, $file['name']);

        if (copy($file['tmp_name'], $targetFile)) {
            return $targetFile;
        }

        return null;
    }
}