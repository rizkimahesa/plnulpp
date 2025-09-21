<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;

class DriveController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

    public function index()
    {
        $parentFolderId = '1HxADw0gRVmb5xuq3bomTi5Mhri1knvi0'; // ID folder utama di Google Drive
        $folder = $this->drive->findFolderByDate($parentFolderId, now()->format('d-m-Y'));

        if (!$folder) {
            return "Folder tanggal hari ini tidak ditemukan.";
        }

        $files = $this->drive->listFilesInFolder($folder['id']);

        return view('drive.index', compact('files', 'folder'));
    }
}