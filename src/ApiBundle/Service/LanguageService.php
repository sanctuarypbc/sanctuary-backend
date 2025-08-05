<?php

namespace App\ApiBundle\Service;

use App\Repository\LanguageRepository;

/**
 * Class OrganizationService
 * @package App\ApiBundle\Service
 */
class LanguageService
{
    /** @var LanguageRepository  */
    private $languageRepository;

    /**
     * OrganizationService constructor.
     * @param LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param null $id
     * @return mixed
     */
    public function getActiveLanguagesData($id = null)
    {
        return $this->languageRepository->getActiveLanguagesData($id);
    }
}
