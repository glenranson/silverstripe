<?php

namespace Doggo\Model;

use http\Env\Url;
use JsonSerializable;
use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\Image;

class Park extends DataObject implements JsonSerializable
{
    private static $table_name = 'Park';

    private static $db = [
        'Title' => 'Varchar',
        'Latitude' => 'Decimal(9,6)',
        'Longitude' => 'Decimal(9,6)',
        'Notes' => 'Text',
        'Provider' => "Enum(array('Wellington City Council', 'Palmerston North City Council'))",
        'ProviderCode' => 'Varchar(100)',
        'GeoJson' => 'Text',
        'FeatureOnOffLeash' => "Enum(array('On-leash', 'Off-leash'), 'On-leash')",
        'IsToPurge' => 'Boolean',
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    private static $indexes = [
        'Provider' => [
            'columns' => ['Provider'],
        ],
        'ProviderCode' => [
            'columns' => ['Provider', 'ProviderCode'],
        ],
    ];

    private static $default_sort = "Title";

		/**
		* Photo has one to many relationship with file table.
		*
		* @var array
		*/
		private static $has_one = [
			'Photo' => Image::class
		];

    public function validate()
    {
        $validate = parent::validate();

        if (empty($this->Title)) {
            $validate->addFieldError('Title', 'Title is required');
        }

        return $validate;
    }

	  /**
	  * Probably an easier way to do this, but this helper function returns the park photo image URL.
	  *
	  * @param $ID
	  * @return string
	  */
		public function photoURL($ID) {
				$image = File::get_by_id($ID);
				$url = $image->getAbsoluteURL();
				return $url;
		}

    public function jsonSerialize()
    {
        return [
            'ID' => $this->ID,
            'Title' => $this->Title,
            'Latitude' => $this->Latitude,
            'Longitude' => $this->Longitude,
            'Notes' => $this->Notes,
            'Provider' => $this->Provider,
            'ProviderCode' => $this->ProviderCode,
            'GeoJson' => $this->GeoJson,
            'FeatureOnOffLeash' => $this->FeatureOnOffLeash,
            'LastEdited' => $this->LastEdited,
            'Created' => $this->Created,
	          'PhotoURL' => !empty($this->PhotoID)? $this->photoURL($this->PhotoID) : 'DogParkDefault.jpg',
        ];
    }
}
