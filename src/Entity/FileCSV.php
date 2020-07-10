<?php
// src/Entity/Product.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class FileCSV
{
    // ...

    /**
     * @ORM\Column(type="string")
     */
    private $csvFilename;

    public function getCsvFilename()
    {
        return $this->csvFilename;
    }

    public function setCsvFilename($csvFilename)
    {
        $this->csvFilename = $csvFilename;

        return $this;
    }
}
