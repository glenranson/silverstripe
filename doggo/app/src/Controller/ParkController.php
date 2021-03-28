<?php

namespace Doggo\Controller;

use Doggo\Model\Park;
use League\Csv\Exception;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Upload;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\MimeValidator\MimeUploadValidator;


class ParkController extends Controller 
{
    private static $allowed_actions = [
        'index',
	      'imageupload',
    ];


	/**
	 * Custom Controller action  - To receive uploaded Image from front end.
	 *
	 * @param HTTPRequest $request
	 * @return HTTPResponse|void
	 * @throws \SilverStripe\Control\HTTPResponse_Exception
	 * @throws \SilverStripe\ORM\ValidationException
	 */
		public function imageupload(HTTPRequest $request) {

			if (!$request->isPOST()) {
				return $this->json(['error' => 'Method not allowed'], 405);
			}

			$file = $request->postVar('file');

			if (!isset($file)) {
				return $this->httpError(500, 'File upload error');
			}
			else {

				// File uploading
				$Uploads = 'client/uploads';

				Folder::find_or_make($Uploads);

				$uploaded = Upload::create();
				$uploaded->setValidator(MimeUploadValidator::create());

				// Create new file instance
				$newfile = File::create();
				$newfile->validate();

				try {
					$uploaded->loadIntoFile($file, $newfile, $Uploads);
				}catch (\Exception $e) {
					return $this->httpError(400, 'Error file');
				}

				// Upload check
				if ($uploaded->isError() ) {
					return $this->httpError(400, 'Error file');
				}
				else {
					// Relate the file to the park photo field.
					$Photo = $uploaded->getFile()->ID;

					$id = $request->postVar('park');

					// Update park
					$park = Park::get_by_id($id);
					$park->setField('PhotoID', $Photo);
					$park->write();

					return $this->json($park);
				}
			}
		}

    public function index(HTTPRequest $request) 
    {
        if (!$request->isGET()) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $id = $request->param('ID');

        if (empty($id)) {
            $parks = Park::get()->toArray();
            return $this->json($parks);
        }

        $park = Park::get_by_id($id);

        if (!$park) {
            return $this->json(['error' => 'Park does not exist'], 404);
        }

        return $this->json($park);
    }

    /**
     * @param $data
     * @param int $status
     * @param bool $forceObject
     * @return HTTPResponse
     */
    public function json($data, $status = 200, $forceObject = false)
    {
        $flags = null;

        if ($forceObject) {
            $flags = JSON_FORCE_OBJECT;
        }

        $response = (new HTTPResponse())
            ->setStatusCode($status)
            ->setBody(json_encode($data, $flags))
            ->addHeader('Content-Type', 'application/json');

        return $response;
    }
}