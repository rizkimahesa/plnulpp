<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;

class GoogleDriveService
{
    protected $client;
    protected $drive;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->addScope(Google_Service_Drive::DRIVE_READONLY);
        $this->drive = new Google_Service_Drive($this->client);
    }

    public function findFolderByDate($parentFolderId, $dateFormatted)
    {
        $query = sprintf(
            "'%s' in parents and mimeType = 'application/vnd.google-apps.folder' and name = '%s' and trashed = false",
            $parentFolderId,
            $dateFormatted
        );

        $response = $this->drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, webViewLink)',
        ]);

        return $response->getFiles()[0] ?? null;
    }
}
